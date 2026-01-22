<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'cashier']);
        Role::create(['name' => 'cook']);
        Role::create(['name' => 'chef']);
        Role::create(['name' => 'server']);
        Role::create(['name' => 'barista']);
    }
}
