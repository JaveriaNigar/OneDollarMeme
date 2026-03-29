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
        Schema::create('weekly_challenges', function (Blueprint $table) {
            $table->id();
            $table->string('challenge_id')->unique(); // e.g., '2026-W04'
            $table->string('status')->default('active'); // 'active', 'completed', etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_challenges');
    }
};
