<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // လုပ်ဆောင်သူ
            $table->nullableMorphs('subject'); // Task (သို့မဟုတ်) တခြား Model တွေနဲ့ ချိတ်ရန်
            $table->string('action'); // ဥပမာ - created, updated, status_changed
            $table->text('description'); // ဖော်ပြချက်စာသား
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
