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
        Schema::create('trashed_comments', function (Blueprint $table) {
            $table->id();

            // User who wrote the bad comment
            $table->unsignedBigInteger('user_id')->nullable();

            // Meme on which the comment was written
            $table->unsignedBigInteger('meme_id')->nullable();

            // The actual comment text
            $table->text('body')->nullable();

            // (Optional) store original comment id if needed
            $table->unsignedBigInteger('original_comment_id')->nullable();

            $table->timestamps();

            // Foreign keys (optional but better)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('meme_id')->references('id')->on('memes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trashed_comments');
    }
};
