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
            'profile.view',
            'profile.manage',
            'document.view',
            'document.manage',
            'credit.view',
            'loan.view',
            'loan.create',
            'installment.view',
            'payment.view',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $nasabah = Role::findOrCreate('Nasabah', 'web');
        $nasabah->syncPermissions($permissions);

        $admin = Role::findOrCreate('Admin', 'web');
        $admin->syncPermissions($permissions);

        $customer = User::updateOrCreate(
            ['email' => 'nasabah@bis.test'],
            [
                'name' => 'Nasabah Demo',
                'nik' => '3204012345678901',
                'phone' => '081234567890',
                'password' => 'password',
            ],
        );
        $customer->syncRoles(['Nasabah']);
    }
}
