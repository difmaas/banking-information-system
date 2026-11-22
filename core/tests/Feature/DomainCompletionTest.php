<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use App\Modules\Cis\Models\Customer;
use App\Modules\Cis\Services\CisService;
use App\Modules\Lns\Models\Loan;
use App\Modules\Lns\Services\LnsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class DomainCompletionTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_customer_profile_update_is_audited(): void
    {
        $customer = Customer::query()->where('channel_user_id', 1)->firstOrFail();

        app(CisService::class)->updateProfile(1, [
            'phone' => '081298765432',
            'occupation' => 'Analis Statistik',
            'monthly_income' => 9000000,
            'address' => 'Jl. Audit No. 1',
        ]);

        $this->assertTrue(
            AuditLog::query()
                ->where('action', 'customer.updated')
                ->where('subject_id', $customer->id)
                ->exists(),
        );
    }

    public function test_disbursement_requires_akad(): void
    {
        $service = app(LnsService::class);
        $loan = $service->createLoan(1, [
            'product_id' => 1,
            'amount' => 10000000,
            'tenor_months' => 6,
        ]);

        $service->verify($loan['id'], User::factory()->create()->id, 'Diteruskan', null);
        $service->analyze($loan['id'], User::factory()->create()->id, [
            'survey_result' => 'Survey lapangan selesai.',
            'collateral' => 'Deposito',
        ]);
        $service->approve($loan['id'], User::factory()->create()->id, 'Setuju');

        try {
            $service->disburse($loan['id']);
            $this->fail('Disbursement should fail without akad.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('Akad belum dicatat', $exception->getMessage());
        }
    }

    public function test_payment_rejects_underpayment(): void
    {
        $service = app(LnsService::class);
        $installment = Loan::query()
            ->findOrFail(1)
            ->installments()
            ->where('status', 'Belum Lunas')
            ->firstOrFail();

        try {
            $service->recordPayment(1, [
                'installment_id' => $installment->id,
                'amount' => (float) $installment->amount - 1,
                'reference' => 'PAY-UNDER-TEST',
            ]);
            $this->fail('Underpayment should be rejected.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('kurang dari tagihan', $exception->getMessage());
        }
    }
}
