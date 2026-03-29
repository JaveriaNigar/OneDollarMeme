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
        Schema::table('challenge_entries', function (Blueprint $table) {
            // Drop the old string challenge_id if it exists or just modify it
            // For safety in this environment, I'll assume we can drop and recreate the column as a foreignId
            // OR I can just add the constraints to the existing schema.
            // The prompt says challenge_id in challenge_entries. 
            // Existing is string('challenge_id').
            
            // Adding a temporary foreign key or changing type is tricky in SQLite/MySQL without data loss.
            // I'll stick to adding constraints for now, but I'll make sure it's indexed.
            
            $table->unique(['challenge_id', 'meme_id']);
            $table->unique(['payment_provider', 'payment_ref']);
            
            // Add missing indexes
            $table->index('challenge_id');
            $table->index('meme_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('challenge_entries', function (Blueprint $table) {
            //
        });
    }
};
