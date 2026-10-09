<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TenantSeeder::class,
        ]);

        $tenant = Tenant::first();

        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_ADMIN,
                'tenant_id' => $tenant?->id ?? 1,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'manager@gmail.com'],
            [
                'name' => 'Team Manager',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_MANAGER,
                'tenant_id' => $tenant?->id ?? 1,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'member@gmail.com'],
            [
                'name' => 'Team Member',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_MEMBER,
                'tenant_id' => $tenant?->id ?? 1,
                'email_verified_at' => now(),
            ]
        );

        $this->call([
            TaskSeeder::class,
        ]);
    }
}
