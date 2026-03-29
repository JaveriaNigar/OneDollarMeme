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
        Schema::create('engagement_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('meme_id')->constrained()->onDelete('cascade');
            $table->string('engagement_type'); // like, comment, share
            $table->string('ip_address', 45);
            $table->string('device_fingerprint')->nullable();
            $table->boolean('is_verified')->default(true);
            $table->boolean('is_flagged')->default(false);
            $table->string('flag_reason')->nullable();
            $table->integer('risk_score')->default(0);
            $table->timestamps();

            $table->index(['meme_id', 'engagement_type']);
            $table->index(['user_id', 'ip_address']);
            $table->index('device_fingerprint');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('engagement_audits');
    }
};
