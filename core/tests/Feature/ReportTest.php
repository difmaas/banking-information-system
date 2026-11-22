<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Gls\Models\ChartOfAccount;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_report_service_aggregates_credit_data(): void
    {
        $service = app(ReportService::class);

        $this->assertGreaterThanOrEqual(1, $service->applicationsByStatus()->count());
        $this->assertGreaterThanOrEqual(1, $service->portfolio()->count());
        $this->assertGreaterThanOrEqual(1, $service->installmentsReceived()->count());
    }

    public function test_trial_balance_is_balanced(): void
    {
        $trial = app(ReportService::class)->trialBalance();

        $this->assertTrue($trial['balanced']);
        $this->assertEquals($trial['total_debit'], $trial['total_credit']);
    }

    public function test_general_ledger_returns_running_balance(): void
    {
        $account = ChartOfAccount::query()->where('code', '1300')->firstOrFail();

        $ledger = app(ReportService::class)->generalLedger($account->id);

        $this->assertGreaterThanOrEqual(1, $ledger->count());
        $this->assertEquals(25000000, $ledger->last()['balance']);
    }

    public function test_financial_pages_render_for_authorized_roles(): void
    {
        $pages = [
            ['/admin/credit-report', 'komite@bis.test'],
            ['/admin/financial-report', 'komite@bis.test'],
            ['/admin/financial-report', 'teller@bis.test'],
        ];

        foreach ($pages as [$uri, $email]) {
            $user = User::query()->where('email', $email)->firstOrFail();

            $this->flushSession();

            $response = $this->actingAs($user)->get($uri);

            $response->assertOk();
        }
    }

    public function test_credit_report_is_hidden_from_staff(): void
    {
        $user = User::query()->where('email', 'staf@bis.test')->firstOrFail();

        $this->actingAs($user)->get('/admin/credit-report')->assertForbidden();
    }

    public function test_admin_dashboard_renders_report_widgets(): void
    {
        $user = User::query()->where('email', 'admin@bis.test')->firstOrFail();

        $this->actingAs($user)->get('/admin')->assertOk();
    }
}
