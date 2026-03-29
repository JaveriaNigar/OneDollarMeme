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
        Schema::table('brands', function (Blueprint $table) {
            $table->text('dos')->nullable()->after('rules');
            $table->text('donts')->nullable()->after('dos');
        });

        Schema::table('brand_requests', function (Blueprint $table) {
            $table->text('dos')->nullable()->after('rules');
            $table->text('donts')->nullable()->after('dos');
        });

        Schema::table('draft_campaigns', function (Blueprint $table) {
            $table->text('dos')->nullable()->after('guidelines');
            $table->text('donts')->nullable()->after('dos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn(['dos', 'donts']);
        });

        Schema::table('brand_requests', function (Blueprint $table) {
            $table->dropColumn(['dos', 'donts']);
        });

        Schema::table('draft_campaigns', function (Blueprint $table) {
            $table->dropColumn(['dos', 'donts']);
        });
    }
};
