<?php

namespace Tests\Feature;

use App\Filament\Pages\Approval;
use App\Filament\Pages\VerifyLoan;
use App\Models\AuditLog;
use App\Models\User;
use App\Modules\Lns\Models\Loan;
use App\Modules\Lns\Services\LnsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BackOfficeAccessTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_permissions_follow_the_access_matrix(): void
    {
        $staf = Role::findByName('StafKredit', 'web');
        $komite = Role::findByName('Komite', 'web');
        $teller = Role::findByName('Teller', 'web');
        $admin = Role::findByName('Admin', 'web');

        $this->assertTrue($staf->hasPermissionTo('lns.verify'));
        $this->assertFalse($staf->hasPermissionTo('lns.approve'));
        $this->assertTrue($komite->hasPermissionTo('lns.approve'));
        $this->assertTrue($teller->hasPermissionTo('lns.disburse'));
        $this->assertTrue($teller->hasPermissionTo('lns.payment'));
        $this->assertFalse($admin->hasPermissionTo('lns.approve'));
        $this->assertFalse($admin->hasPermissionTo('lns.disburse'));
        $this->assertTrue($admin->hasPermissionTo('parameters.manage'));
    }

    public function test_workflow_pages_are_role_gated(): void
    {
        $staf = User::query()->where('email', 'staf@bis.test')->firstOrFail();
        $teller = User::query()->where('email', 'teller@bis.test')->firstOrFail();

        $this->actingAs($staf);
        $this->assertTrue(VerifyLoan::canAccess());
        $this->assertFalse(Approval::canAccess());

        $this->actingAs($teller);
        $this->assertFalse(VerifyLoan::canAccess());
    }

    public function test_workflow_page_renders_for_authorized_role(): void
    {
        $staf = User::query()->where('email', 'staf@bis.test')->firstOrFail();

        $this->actingAs($staf)
            ->get('/admin/verify-loan')
            ->assertOk();
    }

    public function test_authorized_backoffice_pages_render(): void
    {
        $pages = [
            ['/admin/analyze-loan', 'analis@bis.test'],
            ['/admin/approval', 'komite@bis.test'],
            ['/admin/disbursement', 'teller@bis.test'],
            ['/admin/payment', 'teller@bis.test'],
            ['/admin/customers', 'staf@bis.test'],
            ['/admin/loans', 'staf@bis.test'],
            ['/admin/journals', 'komite@bis.test'],
            ['/admin/audit-trail', 'admin@bis.test'],
        ];

        foreach ($pages as [$uri, $email]) {
            $user = User::query()->where('email', $email)->firstOrFail();

            $this->flushSession();

            $response = $this->actingAs($user)->get($uri);

            $response->assertOk();
        }
    }

    public function test_workflow_actions_write_audit_logs(): void
    {
        $officer = User::factory()->create();
        $lns = app(LnsService::class);

        $loan = $lns->createLoan(1, [
            'product_id' => 1,
            'amount' => 5000000,
            'tenor_months' => 6,
        ]);

        $lns->verify($loan['id'], $officer->id, 'Diteruskan', null);

        $this->assertTrue(
            AuditLog::query()
                ->where('action', 'loan.verified')
                ->where('subject_type', Loan::class)
                ->where('subject_id', $loan['id'])
                ->exists(),
        );
    }
}
