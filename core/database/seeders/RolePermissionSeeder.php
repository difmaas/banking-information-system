<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Seeds the roles, permissions, and demo users used by the Back-Office panel.
 */
class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'cis.view',
            'cis.manage',
            'lns.view',
            'lns.verify',
            'lns.analyze',
            'lns.approve',
            'lns.disburse',
            'lns.payment',
            'lns.manage',
            'gls.view',
            'gls.manage',
            'users.manage',
            'parameters.manage',
            'audit.view',
            'reports.view',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $roles = [
            'Admin' => [
                'cis.view', 'cis.manage', 'lns.view', 'lns.manage',
                'gls.view', 'gls.manage', 'users.manage', 'parameters.manage', 'audit.view', 'reports.view',
            ],
            'StafKredit' => ['cis.view', 'lns.view', 'lns.verify'],
            'AnalisKredit' => ['cis.view', 'lns.view', 'lns.analyze'],
            'Komite' => ['lns.view', 'lns.approve', 'gls.view', 'reports.view'],
            'Teller' => ['cis.view', 'lns.view', 'lns.disburse', 'lns.payment', 'gls.view'],
        ];

        foreach ($roles as $name => $rolePermissions) {
            $role = Role::findOrCreate($name, 'web');
            $role->syncPermissions($rolePermissions);
        }

        $users = [
            'admin@bis.test' => ['Admin', 'Admin'],
            'staf@bis.test' => ['Staf Kredit', 'StafKredit'],
            'analis@bis.test' => ['Analis Kredit', 'AnalisKredit'],
            'komite@bis.test' => ['Komite', 'Komite'],
            'teller@bis.test' => ['Teller', 'Teller'],
        ];

        foreach ($users as $email => [$name, $role]) {
            $user = User::updateOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => 'password'],
            );

            $user->syncRoles([$role]);
        }
    }
}
