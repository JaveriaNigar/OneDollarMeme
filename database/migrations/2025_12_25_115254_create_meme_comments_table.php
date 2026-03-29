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
        Schema::create('meme_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // User who wrote the comment
            $table->unsignedBigInteger('meme_id'); // Meme on which comment is written
            $table->text('body'); // The comment itself
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('meme_id')->references('id')->on('memes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meme_comments');
    }
};
