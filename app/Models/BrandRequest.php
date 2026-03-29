<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'website',
        'contact_email',
        'phone',
        'product_brand',
        'product_category',
        'tags',
        'brand_description',
        'product_content',
        'campaign_title',
        'subject_category',
        'brand_logo',
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
        'social_links',
        'theme_color',
        'dos_guidelines',
        'donts_guidelines',
        'status',
    ];

    protected $casts = [
        'social_links' => 'array',
        'tags' => 'array',
        'product_images' => 'array',
        'other_files' => 'array',
        'creative_assets' => 'array',
        'prize_amount' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
