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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // Nullable for system messages
            $table->text('content');
            $table->enum('sender_type', ['user', 'agent'])->default('user'); // Who sent the message
            $table->string('style')->nullable(); // Optional style for meme generation
            $table->string('tone')->nullable(); // Optional tone for meme generation
            $table->string('template')->nullable(); // Optional template for meme generation
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
