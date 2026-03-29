<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class WeeklyChallenge extends Model
{
    use HasFactory;

    protected $fillable = [
        'challenge_id',
        'status',
        'start_at',
        'end_at',
        'winner_meme_id',
        'prize_pool_cents',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    /**
     * Get the most recently closed weekly challenge.
     */
    public static function previous()
    {
        return self::where('status', 'closed')
            ->orderBy('end_at', 'desc')
            ->first();
    }

    /**
     * Get the current active weekly challenge.
     * Creates one if it doesn't exist.
     */
    public static function current()
    {
        $now = Carbon::now();

        // AUTO-CLOSE: Check for any active challenge that has passed its end date
        // and close it automatically. This handles the weekly rollover.
        $expiredChallenges = self::where('status', 'active')
            ->where('end_at', '<', $now)
            ->get();

        foreach ($expiredChallenges as $challenge) {
            $challenge->closeAndPickWinner();
        }

        // Standard Logic: Find the challenge that covers the current timestamp
        $currentChallenge = self::where('start_at', '<=', $now)
            ->where('end_at', '>=', $now)
            ->first();

        if ($currentChallenge) {
            return $currentChallenge;
        }

        // If no challenge is found for the current date range, create one for the current week.
        $startOfWeek = $now->copy()->startOfWeek(); 
        $endOfWeek = $now->copy()->endOfWeek();     
        $challengeId = $now->format('Y') . '-W' . $now->format('W');

        // First, check if a challenge for this specific week ID already exists
        $existing = self::where('challenge_id', $challengeId)->first();
        if ($existing) {
            // Self-heal if dates are missing
            if (!$existing->start_at || !$existing->end_at) {
                $existing->update([
                    'start_at' => $startOfWeek,
                    'end_at' => $endOfWeek,
                ]);
            }
            return $existing;
        }

        // Create new challenge for current week
        return self::create([
            'challenge_id' => $challengeId,
            'status' => 'active',
            'start_at' => $startOfWeek,
            'end_at' => $endOfWeek,
            'prize_pool_cents' => 0,
        ]);
    }

    public function entries()
    {
        return $this->hasMany(ChallengeEntry::class, 'challenge_id', 'challenge_id');
    }

    public function winnerMeme()
    {
        return $this->belongsTo(Meme::class, 'winner_meme_id');
    }

    public function payouts()
    {
        return $this->hasMany(ChallengePayout::class, 'challenge_id', 'challenge_id');
    }

    /**
     * Calculate total prize pool by summing paid_amount_cents from entries.
     */
    public function getTotalPrizePoolCents()
    {
        return $this->entries()->sum('paid_amount_cents');
    }

    /**
     * Fetch top memes for the current week.
     */
    public function getLeaderboard($limit = 10)
    {
        return Meme::where('is_contest', 1)
            ->where('contest_week_id', $this->challenge_id)
            ->whereNotIn('status', ['rejected', 'hidden', 'removed'])
            ->with('user')
            ->orderBy('score', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Close the challenge, pick the winner, and calculate payouts.
     */
    public function closeAndPickWinner()
    {
        if ($this->status !== 'active') {
            return false;
        }

        // 1. Fetch leaderboard (Top 1)
        $leaderboard = $this->getLeaderboard(1);
        $winner = $leaderboard->first();

        // 2. Calculate Prize Pool
        $totalPool = $this->getTotalPrizePoolCents();
        $this->prize_pool_cents = $totalPool;

        if ($winner) {
            $this->winner_meme_id = $winner->id;

            // 3. Record Payout Details
            // Distribution: 50% Winner, 20% System, 30% Profit (20% + 10% remainder)
            $winnerPayout = floor($totalPool * 0.50);
            $systemCosts = floor($totalPool * 0.20);
            $platformProfit = $totalPool - $winnerPayout - $systemCosts;

            ChallengePayout::create([
                'challenge_id' => $this->challenge_id,
                'user_id' => $winner->user_id,
                'meme_id' => $winner->id,
                'total_prize_pool_cents' => $totalPool,
                'winner_payout_cents' => $winnerPayout,
                'system_costs_cents' => $systemCosts,
                'platform_profit_cents' => $platformProfit,
                'status' => 'pending',
            ]);
        }

        // 4. Mark as Closed
        $this->status = 'closed';
        return $this->save();
    }
}
