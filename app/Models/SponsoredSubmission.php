<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SponsoredSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'campaign_id',
        'title',
        'content',
        'image_path',
        'status',
    ];

    /**
     * Get the user who submitted this meme.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the campaign this submission belongs to.
     */
    public function campaign()
    {
        return $this->belongsTo(SponsoredCampaign::class);
    }

    /**
     * Get the likes for this submission.
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /**
     * Get the comments for this submission.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}