<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SponsoredCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand_id',
        'title',
        'description',
        'prize_amount',
        'start_date',
        'end_date',
        'status',
        'requirements',
        'slug'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'prize_amount' => 'decimal:2',
    ];

    /**
     * Get the brand associated with this campaign.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the submissions for this campaign.
     */
    public function submissions()
    {
        return $this->hasMany(SponsoredSubmission::class);
    }
}