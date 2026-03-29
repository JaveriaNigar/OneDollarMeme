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
        Schema::table('memes', function (Blueprint $table) {
            $table->boolean('is_contest')->default(false);
            $table->string('contest_week_id')->nullable();
            $table->timestamp('entry_paid_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('memes', function (Blueprint $table) {
            $table->dropColumn(['is_contest', 'contest_week_id', 'entry_paid_at']);
        });
    }
};
