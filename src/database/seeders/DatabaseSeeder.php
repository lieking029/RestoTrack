<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Core setup (roles, permissions, users)
            RoleSeeder::class,
            PermissionSeeder::class,
            UserSeeder::class,

            // Products and menu
            ProductSeeder::class,
            MenuSeeder::class,

            // Inventory
            InventoryItemSeeder::class,
            InventoryMovementSeeder::class,

            // Orders (includes order items and payments)
            OrderSeeder::class,
        ]);
    }
}
