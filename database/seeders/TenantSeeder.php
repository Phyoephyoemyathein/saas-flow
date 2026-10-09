<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::firstOrCreate(
            ['domain' => 'abc.localhost'],
            [
                'name' => 'ABC Company',
                'subscription_status' => 'active',
            ]
        );

        Task::firstOrCreate(
            ['tenant_id' => $tenant->id, 'title' => 'Design Landing Page'],
            [
                'description' => 'Create a modern UI for the SaaS landing page',
                'status' => 'todo',
                'priority' => 'high',
            ]
        );

        Task::firstOrCreate(
            ['tenant_id' => $tenant->id, 'title' => 'Setup Database'],
            [
                'description' => 'Configure SQLite and migration files',
                'status' => 'completed',
                'priority' => 'medium',
            ]
        );
    }
}
