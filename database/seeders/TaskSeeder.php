<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\Task;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        // ပထမဆုံး Tenant ကို ရှာမယ်
        $tenant = Tenant::first();

        if ($tenant) {
            // အဲ့ဒီ Tenant အတွက် Task တွေ ထည့်မယ်
            Task::create([
                'tenant_id' => $tenant->id,
                'title' => 'Review Monthly Report',
                'description' => 'Check and approve the monthly financial and task reports.',
                'status' => 'todo',
            ]);

            Task::create([
                'tenant_id' => $tenant->id,
                'title' => 'Client Meeting',
                'description' => 'Discuss project requirements with the new client.',
                'status' => 'in_progress',
            ]);
        }
    }
}