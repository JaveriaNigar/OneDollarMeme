<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChallengePayout extends Model
{
    //
    protected $fillable = [
        'challenge_id',
        'user_id',
        'meme_id',
        'total_prize_pool_cents',
        'winner_payout_cents',
        'system_costs_cents',
        'platform_profit_cents',
        'status',
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
        return $this->belongsTo(WeeklyChallenge::class, 'challenge_id', 'challenge_id');
    }
}
