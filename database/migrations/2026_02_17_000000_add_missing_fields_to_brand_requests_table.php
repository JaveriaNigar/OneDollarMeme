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
        Schema::table('brand_requests', function (Blueprint $table) {
            // Add new columns for the enhanced brand form
            $table->string('contact_email')->nullable()->after('website');
            $table->string('phone', 20)->nullable()->after('contact_email');
            $table->string('product_brand')->nullable()->after('phone');
            $table->string('product_category')->nullable()->after('product_brand');
            $table->json('tags')->nullable()->after('product_category');
            $table->json('product_images')->nullable()->after('product_content');
            $table->json('other_files')->nullable()->after('product_images');
            $table->string('campaign_image')->nullable()->after('other_files');
            $table->string('slogan')->nullable()->after('campaign_image');
            $table->text('image_description')->nullable()->after('slogan');
            $table->json('creative_assets')->nullable()->after('image_description');
            $table->string('campaign_goal')->nullable()->after('creative_assets');
            $table->string('audience_location')->nullable()->after('campaign_goal');
            $table->date('start_date')->nullable()->after('audience_location');
            $table->date('end_date')->nullable()->after('start_date');
            $table->string('prize_type')->nullable()->after('end_date');
            $table->decimal('prize_amount', 10, 2)->nullable()->after('prize_type');
            $table->string('winner_selection')->nullable()->after('prize_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brand_requests', function (Blueprint $table) {
            $table->dropColumn([
                'contact_email',
                'phone',
                'product_brand',
                'product_category',
                'tags',
                'product_images',
                'other_files',
                'campaign_image',
                'slogan',
                'image_description',
                'creative_assets',
                'campaign_goal',
                'audience_location',
                'start_date',
                'end_date',
                'prize_type',
                'prize_amount',
                'winner_selection',
            ]);
        });
    }
};
