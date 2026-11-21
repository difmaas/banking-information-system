<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Main database seeder for the core banking app.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Run the seeders required to bootstrap the core banking database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
        ]);
    }
}
