<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'company_name',
        'slug',
        'website',
        'contact_email',
        'phone',
        'brand_description',
        'product_brand',
        'product_category',
        'tags',
        'product_content',
        'social_links',
        'products',
        'logo',
        'product_images',
        'other_files',
        'campaign_image',
        'slogan',
        'image_description',
        'creative_assets',
        'theme_color',
        'campaign_title',
        'campaign_description',
        'subject_category',
        'campaign_goal',
        'audience_location',
        'start_date',
        'end_date',
        'prize_type',
        'prize_amount',
        'winner_selection',
        'dos_guidelines',
        'donts_guidelines',
        'rules',
        'status',
        'is_completed',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'social_links' => 'array',
        'products' => 'array',
        'tags' => 'array',
        'product_images' => 'array',
        'other_files' => 'array',
        'creative_assets' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'prize_amount' => 'decimal:2',
        'status' => 'string',
    ];

    /**
     * Get the user that owns the brand.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the products for the brand.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the memes for the brand.
     */
    public function memes()
    {
        return $this->hasMany(Meme::class);
    }

    /**
     * Scope a query to only include active (non-expired) campaigns.
     */
    public function scopeActiveCampaigns($query)
    {
        return $query->where('status', 'active')
            ->where('is_completed', false)
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            });
    }

    /**
     * Scope a query to only include completed/expired campaigns.
     */
    public function scopeCompleted($query)
    {
        return $query->where(function($q) {
            $q->where('is_completed', true)
              ->orWhere('status', 'completed')
              ->orWhere('end_date', '<', now());
        });
    }
}