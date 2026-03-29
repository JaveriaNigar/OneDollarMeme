<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChallengeEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'challenge_id',
        'meme_id',
        'user_id',
        'paid_amount_cents',
        'paid_at',
        'payment_provider',
        'payment_ref',
    ];

    public function meme()
    {
        return $this->belongsTo(Meme::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function challenge()
    {
        // Link via the string challenge_id as per current migration schema
        return $this->belongsTo(WeeklyChallenge::class, 'challenge_id', 'challenge_id');
    }
}
