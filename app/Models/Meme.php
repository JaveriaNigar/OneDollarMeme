<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\MemeLike;
use App\Models\MemeComment;
use App\Models\Reaction;
use App\Models\ShareEvent;
use App\Models\ChallengeEntry;

class Meme extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'brand_id',
        'title',
        'image_path',
        'status',
        'template',
        'shares_count',
        'is_contest',
        'contest_week_id',
        'entry_paid_at',
        'score',
        'price_cents',
        'payment_provider',
        'payment_ref',
        'boosted_until',
    ];

    protected $casts = [
        'image_path' => 'string',
        'shares_count' => 'integer',
    ];

    // Relation to User
    public function user() {
        return $this->belongsTo(User::class);
    }

    // Likes relation
    public function likes() {
        return $this->hasMany(MemeLike::class);
    }

    // Comments relation
    public function comments() {
        return $this->hasMany(MemeComment::class);
    }

    // Comments with replies relation
    public function commentsWithReplies() {
        return $this->comments()->with('replies');
    }

    // Reactions relation
    public function reactions() {
        return $this->hasMany(Reaction::class);
    }

    // Share events relation
    public function shareEvents() {
        return $this->hasMany(ShareEvent::class);
    }

    public function brand()
    {
        return $this->belongsTo(\App\Models\Brand::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function challengeEntry()
    {
        return $this->hasOne(ChallengeEntry::class);
    }

    // Method to increment shares count
    public function incrementShares()
    {
        $this->increment('shares_count');
        return $this;
    }

    // Method to decrement shares count
    public function decrementShares()
    {
        $this->decrement('shares_count');
        return $this;
    }

    /**
     * Recalculate score based on formula: (reactions*2) + (comments*3) + (shares*5)
     * Rule: Only count events occurred within the active week window of the contest.
     */
    public function recalculateScore()
    {
        $reactionsCount = $this->reactions()->count();
        $commentsCount = $this->comments()->count();
        $sharesCount = $this->shareEvents()->count();

        $newScore = ($reactionsCount * 2) + ($commentsCount * 3) + ($sharesCount * 5);

        // Update if the score has changed
        if ($this->score != $newScore) {
            $this->update(['score' => $newScore]);
        }
    }
}
