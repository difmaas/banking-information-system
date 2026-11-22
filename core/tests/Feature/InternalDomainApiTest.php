<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Cis\Services\CisService;
use App\Modules\Gls\Models\Journal;
use App\Modules\Lns\Models\Loan;
use App\Modules\Lns\Services\LnsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use RuntimeException;
use Tests\TestCase;

class InternalDomainApiTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.core.service_token' => 'test-token',
            'services.core.hmac_secret' => 'test-secret',
        ]);
    }

    public function test_customer_profile_requires_signature(): void
    {
        $this->getJson('/api/internal/v1/customers/1')->assertUnauthorized();
    }

    public function test_customer_profile_returns_seeded_customer(): void
    {
        $this->signedRequest('GET', '/api/internal/v1/customers/1')
            ->assertOk()
            ->assertJsonPath('name', 'Nasabah Demo')
            ->assertJsonPath('channel_user_id', 1);
    }

    public function test_loan_products_and_customer_loans_are_exposed(): void
    {
        $this->signedRequest('GET', '/api/internal/v1/loan-products')
            ->assertOk()
            ->assertJsonPath('0.code', 'KM-01');

        $this->signedRequest('GET', '/api/internal/v1/customers/1/loans')
            ->assertOk()
            ->assertJsonPath('0.status', 'Aktif');
    }

    public function test_write_endpoint_is_idempotent(): void
    {
        $payload = [
            'product_id' => 1,
            'amount' => 5000000,
            'tenor_months' => 12,
        ];
        $headers = ['HTTP_IDEMPOTENCY_KEY' => (string) Str::uuid()];

        $first = $this->signedRequest('POST', '/api/internal/v1/customers/1/loans', $payload, $headers);
        $second = $this->signedRequest('POST', '/api/internal/v1/customers/1/loans', $payload, $headers);

        $first->assertCreated();
        $second->assertCreated()
            ->assertHeader('Idempotent-Replay', 'true');

        $this->assertSame($first->json('id'), $second->json('id'));
    }

    public function test_approval_workflow_disbursement_and_journal_are_consistent(): void
    {
        $service = app(LnsService::class);
        $created = $service->createLoan(1, [
            'product_id' => 1,
            'amount' => 10000000,
            'tenor_months' => 6,
        ]);

        $loanId = $created['id'];
        $analyst = User::factory()->create();
        $officer = User::factory()->create();
        $approver = User::factory()->create();

        $service->verify($loanId, $officer->id, 'Diteruskan', null);

        $analysis = $service->analyze($loanId, $analyst->id, [
            'survey_result' => 'Survey lapangan selesai.',
            'collateral' => 'Deposito',
        ]);
        $this->assertSame('Layak', $analysis['recommendation']);

        $approval = $service->approve($loanId, $approver->id, 'Setuju');
        $this->assertSame('Disetujui', $approval['status']);

        $service->recordAkad($loanId, [
            'signer_name' => 'Nasabah Demo',
            'signed_at' => now()->toDateTimeString(),
        ]);

        $disbursed = $service->disburse($loanId);
        $this->assertSame('Aktif', $disbursed['status']);

        $loan = Loan::query()->findOrFail($loanId);

        $this->assertSame(6, $loan->installments()->count());
        $this->assertSame('Aktif', $loan->status);

        $journal = Journal::query()->where('reference', 'DISB-'.$loanId)->firstOrFail();
        $debit = $journal->lines()->sum('debit');
        $credit = $journal->lines()->sum('credit');

        $this->assertEquals((float) $loan->amount, (float) $debit);
        $this->assertEquals((float) $debit, (float) $credit);
    }

    public function test_missing_customer_returns_404_instead_of_422(): void
    {
        $this->signedRequest('GET', '/api/internal/v1/customers/999')
            ->assertNotFound()
            ->assertJsonPath('message', 'Data tidak ditemukan.');
    }

    public function test_customer_can_be_created_from_channel_user(): void
    {
        $this->signedRequest('POST', '/api/internal/v1/customers', [
            'channel_user_id' => 99,
            'nik' => '3204999999999999',
            'name' => 'Nasabah Baru',
            'email' => 'baru99@example.com',
        ], ['HTTP_IDEMPOTENCY_KEY' => (string) Str::uuid()])
            ->assertCreated()
            ->assertJsonPath('channel_user_id', 99)
            ->assertJsonPath('name', 'Nasabah Baru');
    }

    public function test_customer_store_validation_still_returns_422(): void
    {
        $this->signedRequest('POST', '/api/internal/v1/customers', [
            'nik' => '123',
            'name' => 'Tanpa Channel User',
            'email' => 'tanpa@example.com',
        ])->assertUnprocessable();
    }

    public function test_loan_simulation_is_exposed(): void
    {
        $this->signedRequest('GET', '/api/internal/v1/parameters/required-documents')
            ->assertOk()
            ->assertJson(['ktp', 'kk', 'slip_gaji']);

        $this->signedRequest('POST', '/api/internal/v1/loan-simulations', [
            'product_id' => 1,
            'amount' => 10000000,
            'tenor_months' => 12,
        ])
            ->assertOk()
            ->assertJsonPath('monthly_installment', 958333.33)
            ->assertJsonPath('total_payment', 11499999.96);
    }

    public function test_create_loan_requires_all_required_documents(): void
    {
        app(CisService::class)->createCustomer(99, [
            'nik' => '3204999999999999',
            'name' => 'Nasabah Tanpa Dokumen',
            'email' => 'tanpa-dokumen@example.com',
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Dokumen wajib belum lengkap');

        app(LnsService::class)->createLoan(99, [
            'product_id' => 1,
            'amount' => 5000000,
            'tenor_months' => 6,
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, string>  $extraHeaders
     */
    private function signedRequest(string $method, string $path, array $payload = [], array $extraHeaders = []): TestResponse
    {
        $body = $payload === [] ? '' : json_encode($payload, JSON_THROW_ON_ERROR);
        $timestamp = (string) now()->timestamp;
        $signature = hash_hmac('sha256', $method.'|'.$path.'|'.$timestamp.'|'.$body, 'test-secret');

        $headers = array_merge([
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_SERVICE_TOKEN' => 'test-token',
            'HTTP_X_TIMESTAMP' => $timestamp,
            'HTTP_X_SIGNATURE' => $signature,
        ], $extraHeaders);

        return $this->call($method, $path, [], [], [], $headers, $body);
    }
}
