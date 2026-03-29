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
        Schema::create('brand_requests', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('company_name');
            $table->string('website')->nullable();
            $table->text('brand_description')->nullable();
            $table->json('social_links')->nullable();
            $table->string('brand_logo')->nullable();
            $table->string('theme_color')->nullable();
            $table->string('campaign_title');
            $table->string('subject_category');
            $table->text('product_content')->nullable();
            $table->string('product_image')->nullable();
            $table->string('product_file')->nullable();
            $table->json('rules')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_requests');
    }
};
