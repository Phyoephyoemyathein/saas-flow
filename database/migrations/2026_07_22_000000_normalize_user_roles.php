<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->where('role', 'user')->update(['role' => 'member']);
        DB::table('users')->where('role', 'company_admin')->update(['role' => 'admin']);
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'member')->update(['role' => 'user']);
    }
};
