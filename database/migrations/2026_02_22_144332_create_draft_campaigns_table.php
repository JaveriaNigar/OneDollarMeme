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
        Schema::create('draft_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Brand Account
            $table->string('company_name');
            $table->string('contact_email');
            $table->string('website')->nullable();
            $table->string('tags')->nullable();
            
            // Campaign Details
            $table->string('campaign_title');
            $table->string('product_category')->nullable();
            $table->string('product_category_other')->nullable();
            $table->text('product_content');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('guidelines')->nullable();
            
            // Target & Budget
            $table->string('campaign_goal');
            $table->string('campaign_goal_other')->nullable();
            $table->string('prize_type');
            $table->string('prize_type_other')->nullable();
            $table->decimal('prize_amount', 10, 2)->default(0);
            $table->string('audience_location');
            $table->string('audience_location_other')->nullable();
            $table->string('audience_size')->default('1000-5000');
            $table->integer('estimated_participants')->nullable();
            
            // Campaign Creatives
            $table->string('campaign_image')->nullable();
            $table->json('product_images')->nullable();
            $table->json('brand_assets')->nullable();
            $table->string('theme_color')->default('#6f42c1');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('draft_campaigns');
    }
};
