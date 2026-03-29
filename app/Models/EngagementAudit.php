<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EngagementAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'meme_id',
        'engagement_type',
        'ip_address',
        'device_fingerprint',
        'is_verified',
        'is_flagged',
        'flag_reason',
        'risk_score',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_flagged' => 'boolean',
        'risk_score' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function meme()
    {
        return $this->belongsTo(Meme::class);
    }

    /**
     * Record an engagement and audit it for fraud detection.
     */
    public static function recordEngagement($userId, $memeId, $engagementType, $ipAddress, $userAgent = null)
    {
        $fingerprint = IpAddress::generateFingerprint($userAgent ?? request()->userAgent());

        return self::create([
            'user_id' => $userId,
            'meme_id' => $memeId,
            'engagement_type' => $engagementType,
            'ip_address' => $ipAddress,
            'device_fingerprint' => $fingerprint,
            'is_verified' => true,
            'is_flagged' => false,
            'risk_score' => 0,
        ]);
    }

    /**
     * Get suspicious engagements for a meme.
     */
    public static function getSuspiciousForMeme($memeId)
    {
        return self::where('meme_id', $memeId)
            ->where('is_flagged', true)
            ->get();
    }

    /**
     * Get verified engagement count for a meme by type.
     */
    public static function getVerifiedCount($memeId, $engagementType = null)
    {
        $query = self::where('meme_id', $memeId)
            ->where('is_verified', true)
            ->where('is_flagged', false);

        if ($engagementType) {
            $query->where('engagement_type', $engagementType);
        }

        return $query->count();
    }

    /**
     * Flag an engagement as fraudulent.
     */
    public function flagAsFraudulent($reason)
    {
        $this->update([
            'is_flagged' => true,
            'flag_reason' => $reason,
            'is_verified' => false,
        ]);
    }

    /**
     * Verify an engagement.
     */
    public function verify()
    {
        $this->update([
            'is_verified' => true,
            'is_flagged' => false,
            'flag_reason' => null,
        ]);
    }
}
