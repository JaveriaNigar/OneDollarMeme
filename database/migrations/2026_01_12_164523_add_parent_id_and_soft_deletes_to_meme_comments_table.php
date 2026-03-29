<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('meme_comments', function (Blueprint $table) {
            // Add parent_id column for replies
            $table->unsignedBigInteger('parent_id')->nullable()->after('body');

            // Add soft deletes
            $table->softDeletes();
        });

        // Add foreign key constraint after column is created
        Schema::table('meme_comments', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('meme_comments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meme_comments', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['parent_id']);

            // Drop columns
            $table->dropColumn(['parent_id', 'deleted_at']);
        });
    }
};
