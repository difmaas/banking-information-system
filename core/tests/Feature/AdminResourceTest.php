<?php

namespace Tests\Feature;

use App\Filament\Resources\Users\UserResource;
use App\Models\AuditLog;
use App\Models\SystemParameter;
use App\Models\User;
use App\Modules\Lns\Services\LnsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminResourceTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_admin_can_open_all_admin_resources(): void
    {
        $admin = User::query()->where('email', 'admin@bis.test')->firstOrFail();

        foreach (['/admin/system-parameters', '/admin/users', '/admin/roles', '/admin/permissions'] as $uri) {
            $this->actingAs($admin)->get($uri)->assertOk();
        }
    }

    public function test_committee_cannot_open_admin_resources(): void
    {
        $user = User::query()->where('email', 'komite@bis.test')->firstOrFail();

        $this->actingAs($user)->get('/admin/system-parameters')->assertForbidden();
    }

    public function test_parameter_change_affects_approval_level(): void
    {
        $parameter = SystemParameter::query()->where('key', 'approval_level_1_limit')->firstOrFail();
        $parameter->update(['value' => '10000000']);

        $level = app(LnsService::class)->approvalLevel(15000000);

        $this->assertSame(2, $level['level']);
        $this->assertTrue(
            AuditLog::query()
                ->where('action', 'system_parameter.updated')
                ->where('subject_id', $parameter->id)
                ->exists(),
        );
    }

    public function test_user_creation_writes_audit_log(): void
    {
        $user = User::query()->create([
            'name' => 'User Baru',
            'email' => 'user-baru@bis.test',
            'password' => 'password',
        ]);
        $user->assignRole('Teller');

        $this->assertTrue(
            AuditLog::query()
                ->where('action', 'user.created')
                ->where('subject_id', $user->id)
                ->exists(),
        );
        $this->assertTrue($user->hasRole('Teller'));
    }

    public function test_admin_cannot_delete_own_account_from_resource(): void
    {
        $admin = User::query()->where('email', 'admin@bis.test')->firstOrFail();
        $other = User::query()->where('email', 'teller@bis.test')->firstOrFail();

        $this->actingAs($admin);

        $this->assertFalse(UserResource::canDelete($admin));
        $this->assertTrue(UserResource::canDelete($other));
    }
}
