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
        Schema::table('draft_campaigns', function (Blueprint $table) {
            $table->string('company_name')->nullable()->change();
            $table->string('contact_email')->nullable()->change();
            $table->string('website')->nullable()->change();
            $table->string('tags')->nullable()->change();
            $table->string('campaign_title')->nullable()->change();
            $table->string('product_category')->nullable()->change();
            $table->string('product_category_other')->nullable()->change();
            $table->text('product_content')->nullable()->change();
            $table->date('start_date')->nullable()->change();
            $table->date('end_date')->nullable()->change();
            $table->text('guidelines')->nullable()->change();
            $table->string('campaign_goal')->nullable()->change();
            $table->string('campaign_goal_other')->nullable()->change();
            $table->string('prize_type')->nullable()->change();
            $table->string('prize_type_other')->nullable()->change();
            $table->decimal('prize_amount', 10, 2)->nullable()->change();
            $table->string('audience_location')->nullable()->change();
            $table->string('audience_location_other')->nullable()->change();
            $table->string('audience_size')->nullable()->change();
            $table->integer('estimated_participants')->nullable()->change();
            $table->string('campaign_image')->nullable()->change();
            $table->json('product_images')->nullable()->change();
            $table->json('brand_assets')->nullable()->change();
            $table->string('theme_color')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('draft_campaigns', function (Blueprint $table) {
            $table->string('company_name')->nullable(false)->change();
            $table->string('contact_email')->nullable(false)->change();
            $table->string('website')->nullable(false)->change();
            $table->string('tags')->nullable(false)->change();
            $table->string('campaign_title')->nullable(false)->change();
            $table->string('product_category')->nullable(false)->change();
            $table->string('product_category_other')->nullable(false)->change();
            $table->text('product_content')->nullable(false)->change();
            $table->date('start_date')->nullable(false)->change();
            $table->date('end_date')->nullable(false)->change();
            $table->text('guidelines')->nullable(false)->change();
            $table->string('campaign_goal')->nullable(false)->change();
            $table->string('campaign_goal_other')->nullable(false)->change();
            $table->string('prize_type')->nullable(false)->change();
            $table->string('prize_type_other')->nullable(false)->change();
            $table->decimal('prize_amount', 10, 2)->nullable(false)->change();
            $table->string('audience_location')->nullable(false)->change();
            $table->string('audience_location_other')->nullable(false)->change();
            $table->string('audience_size')->nullable(false)->change();
            $table->integer('estimated_participants')->nullable(false)->change();
            $table->string('campaign_image')->nullable(false)->change();
            $table->json('product_images')->nullable(false)->change();
            $table->json('brand_assets')->nullable(false)->change();
            $table->string('theme_color')->nullable(false)->change();
        });
    }
};
