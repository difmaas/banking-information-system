<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

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

        $admin = User::updateOrCreate(
            ['email' => 'admin@bis.test'],
            ['name' => 'Admin', 'password' => 'password'],
        );

        $admin->syncRoles(['Admin']);
    }
}