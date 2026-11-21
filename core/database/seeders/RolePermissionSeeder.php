<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Seeds the roles, permissions, and demo admin user used by the core banking app.
 */
class RolePermissionSeeder extends Seeder
{
    /**
     * Create permissions and roles, then assign roles to the demo admin.
     */
    public function run(): void
    {
        // Clear cached permissions so newly created roles/permissions are visible.
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Permissions grouped by module and administrative function.
        $permissions = [
            'cis.view',
            'cis.manage',
            'lns.view',
            'lns.manage',
            'gls.view',
            'gls.manage',
            'users.manage',
            'parameters.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // Role-to-permission mapping based on the BIS access matrix.
        $roles = [
            'Admin' => $permissions,
            'StafKredit' => ['cis.view', 'lns.view'],
            'AnalisKredit' => ['cis.view', 'lns.view', 'lns.manage'],
            'Komite' => ['lns.view', 'gls.view'],
            'Teller' => ['cis.view', 'lns.view', 'gls.view'],
        ];

        foreach ($roles as $name => $rolePermissions) {
            $role = Role::findOrCreate($name, 'web');
            $role->syncPermissions($rolePermissions);
        }

        // Demo admin account for local development and Back-Office access.
        $admin = User::updateOrCreate(
            ['email' => 'admin@bis.test'],
            ['name' => 'Admin', 'password' => 'password'],
        );

        $admin->syncRoles(['Admin']);
    }
}
