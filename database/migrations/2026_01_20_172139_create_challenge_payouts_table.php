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
        Schema::create('challenge_payouts', function (Blueprint $table) {
            $table->id();
            $table->string('challenge_id')->index(); // Links to weekly_challenges.challenge_id
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // The winner
            $table->foreignId('meme_id')->constrained()->onDelete('cascade'); // The winning meme
            $table->integer('total_prize_pool_cents');
            $table->integer('winner_payout_cents');
            $table->integer('system_costs_cents');
            $table->integer('platform_profit_cents');
            $table->string('status')->default('pending'); // pending, processed, failed
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenge_payouts');
    }
};
