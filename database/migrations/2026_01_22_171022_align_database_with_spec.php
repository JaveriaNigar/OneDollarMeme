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
            // New fields from spec
            if (!Schema::hasColumn('memes', 'price_cents')) {
                $table->integer('price_cents')->default(100)->after('status');
            }
            if (!Schema::hasColumn('memes', 'payment_provider')) {
                $table->string('payment_provider')->nullable()->after('price_cents');
            }
            if (!Schema::hasColumn('memes', 'payment_ref')) {
                $table->string('payment_ref')->nullable()->after('payment_provider');
            }
            if (!Schema::hasColumn('memes', 'boosted_until')) {
                $table->timestamp('boosted_until')->nullable()->after('payment_ref');
            }

            // Adjust indexes
            $table->index(['user_id', 'created_at']);
            $table->index(['is_contest', 'contest_week_id', 'created_at'], 'memes_contest_lane_index');
            $table->index(['status', 'created_at']);
            
            // Unique constraint for anti-duplicate payments
            // Note: SQLite might have trouble with unique constraints on existing tables without a recreation
            // But we'll try standard Laravel approach.
            $table->unique(['payment_provider', 'payment_ref']);
        });

        Schema::table('share_events', function (Blueprint $table) {
            if (!Schema::hasColumn('share_events', 'channel')) {
                $table->string('channel', 32)->default('copy_link')->after('user_id');
            }
            $table->index(['meme_id', 'channel', 'created_at']);
        });

        Schema::table('meme_comments', function (Blueprint $table) {
            $table->index(['meme_id', 'created_at']);
            if (Schema::hasColumn('meme_comments', 'parent_id')) {
                $table->index('parent_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('memes', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex('memes_contest_lane_index');
            $table->dropIndex(['status', 'created_at']);
            $table->dropUnique(['payment_provider', 'payment_ref']);
            $table->dropColumn(['price_cents', 'payment_provider', 'payment_ref', 'boosted_until']);
        });

        Schema::table('share_events', function (Blueprint $table) {
            $table->dropIndex(['meme_id', 'channel', 'created_at']);
            $table->dropColumn('channel');
        });

        Schema::table('meme_comments', function (Blueprint $table) {
            $table->dropIndex(['meme_id', 'created_at']);
            $table->dropIndex(['parent_id']);
        });
    }
};
