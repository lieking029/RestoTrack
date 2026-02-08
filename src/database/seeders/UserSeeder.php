<?php

namespace Database\Seeders;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'user_type' => UserType::Admin,
            'email' => 'admin@restotrack.com',
        ]);

        User::factory()->create([
            'user_type' => UserType::Manager,
            'email' => 'manager@restotrack.com',
        ]);

        $employeeRoles = ['cashier', 'cook', 'chef', 'server', 'barista'];

        foreach ($employeeRoles as $role) {
            $user = User::factory()->create([
                'user_type' => UserType::Employee,
                'email' => "{$role}@restotrack.com",
            ]);
            $user->assignRole($role);
        }

        foreach (range(1, 5) as $i) {
            $user = User::factory()->create([
                'user_type' => UserType::Employee,
            ]);
            $user->assignRole(fake()->randomElement($employeeRoles));
        }
    }
}