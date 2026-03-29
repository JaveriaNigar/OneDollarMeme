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
        Schema::create('challenge_entries', function (Blueprint $table) {
            $table->id();
            $table->string('challenge_id'); // e.g., '2026-W04'
            $table->foreignId('meme_id')->constrained('memes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('paid_amount_cents');
            $table->timestamp('paid_at');
            $table->string('payment_provider'); // 'dummy'
            $table->string('payment_ref');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenge_entries');
    }
};
