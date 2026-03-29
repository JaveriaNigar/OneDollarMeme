<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DraftCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'contact_email',
        'brand_logo',
        'website',
        'tags',
        'campaign_title',
        'product_category',
        'product_category_other',
        'product_content',
        'start_date',
        'end_date',
        'guidelines',
        'campaign_goal',
        'campaign_goal_other',
        'prize_type',
        'prize_type_other',
        'prize_amount',
        'audience_location',
        'audience_location_other',
        'audience_size',
        'estimated_participants',
        'campaign_image',
        'product_images',
        'brand_assets',
        'theme_color',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'prize_amount' => 'decimal:2',
        'product_images' => 'array',
        'brand_assets' => 'array',
    ];

    /**
     * Get the user that owns the draft campaign.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
