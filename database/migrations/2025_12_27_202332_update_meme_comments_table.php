<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meme_comments', function (Blueprint $table) {
            if (!Schema::hasColumn('meme_comments', 'user_id')) {
                $table->unsignedBigInteger('user_id')->after('id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }

            if (!Schema::hasColumn('meme_comments', 'meme_id')) {
                $table->unsignedBigInteger('meme_id')->after('user_id');
                $table->foreign('meme_id')->references('id')->on('memes')->onDelete('cascade');
            }

            if (!Schema::hasColumn('meme_comments', 'body')) {
                $table->text('body')->after('meme_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('meme_comments', function (Blueprint $table) {
            if (Schema::hasColumn('meme_comments', 'body')) $table->dropColumn('body');
            if (Schema::hasColumn('meme_comments', 'meme_id')) $table->dropForeign(['meme_id']);
            if (Schema::hasColumn('meme_comments', 'meme_id')) $table->dropColumn('meme_id');
            if (Schema::hasColumn('meme_comments', 'user_id')) $table->dropForeign(['user_id']);
            if (Schema::hasColumn('meme_comments', 'user_id')) $table->dropColumn('user_id');
        });
    }
};
