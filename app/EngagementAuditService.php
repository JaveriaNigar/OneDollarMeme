<?php

namespace App;

use App\Models\EngagementAudit;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EngagementAuditService
{
    /**
     * Configuration thresholds for fraud detection.
     */
    const RAPID_ENGAGEMENT_WINDOW_MINUTES = 5;
    const RAPID_ENGAGEMENT_THRESHOLD = 10;
    const NEW_ACCOUNT_DAYS = 7;
    const NEW_ACCOUNT_ENGAGEMENT_THRESHOLD = 20;

    /**
     * Audit an engagement after it's recorded.
     * Returns true if engagement is legitimate, false if flagged.
     */
    public static function auditEngagement(EngagementAudit $engagement): bool
    {
        $riskScore = 0;
        $flagReasons = [];

        // Check 1: Rapid engagement spike detection
        if (self::detectRapidEngagementSpike($engagement->meme_id, $engagement->ip_address)) {
            $riskScore += 30;
            $flagReasons[] = 'Rapid engagement spike from IP';
        }

        // Check 2: Multiple interactions from identical IP
        if (self::detectMultipleFromSameIp($engagement->meme_id, $engagement->ip_address)) {
            $riskScore += 25;
            $flagReasons[] = 'Multiple engagements from same IP';
        }

        // Check 3: Repeated activity from newly created account
        if (self::detectNewAccountSpam($engagement->user_id, $engagement->ip_address)) {
            $riskScore += 40;
            $flagReasons[] = 'New account with repeated activity';
        }

        // Check 4: Same device fingerprint, different accounts
        if (self::detectMultiAccountSameDevice($engagement->meme_id, $engagement->device_fingerprint)) {
            $riskScore += 35;
            $flagReasons[] = 'Multiple accounts from same device';
        }

        // Check 5: Engagement velocity (too many engagements in short time)
        if (self::detectEngagementVelocity($engagement->user_id)) {
            $riskScore += 20;
            $flagReasons[] = 'Unusual engagement velocity';
        }

        // Update the engagement with audit results
        $engagement->update([
            'risk_score' => $riskScore,
            'is_flagged' => $riskScore >= 50,
            'flag_reason' => !empty($flagReasons) ? implode('; ', $flagReasons) : null,
            'is_verified' => $riskScore < 50,
        ]);

        return $riskScore < 50;
    }

    /**
     * Detect rapid engagement spikes from the same IP.
     * More than threshold engagements within the time window.
     */
    public static function detectRapidEngagementSpike($memeId, $ipAddress): bool
    {
        $count = EngagementAudit::where('meme_id', $memeId)
            ->where('ip_address', $ipAddress)
            ->where('created_at', '>=', now()->subMinutes(self::RAPID_ENGAGEMENT_WINDOW_MINUTES))
            ->count();

        return $count >= self::RAPID_ENGAGEMENT_THRESHOLD;
    }

    /**
     * Detect multiple engagements from the same IP on a meme.
     */
    public static function detectMultipleFromSameIp($memeId, $ipAddress): bool
    {
        $count = EngagementAudit::where('meme_id', $memeId)
            ->where('ip_address', $ipAddress)
            ->distinct('user_id')
            ->count('user_id');

        return $count >= 3;
    }

    /**
     * Detect if a new account is engaging repeatedly (potential spam).
     */
    public static function detectNewAccountSpam($userId, $ipAddress): bool
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        // Check if account is new (created within last NEW_ACCOUNT_DAYS days)
        if ($user->created_at < now()->subDays(self::NEW_ACCOUNT_DAYS)) {
            return false;
        }

        // Count engagements from this new account
        $engagementCount = EngagementAudit::where('user_id', $userId)
            ->where('created_at', '>=', now()->subHours(24))
            ->count();

        return $engagementCount >= self::NEW_ACCOUNT_ENGAGEMENT_THRESHOLD;
    }

    /**
     * Detect multiple accounts using the same device.
     */
    public static function detectMultiAccountSameDevice($memeId, $deviceFingerprint): bool
    {
        $userCount = EngagementAudit::where('meme_id', $memeId)
            ->where('device_fingerprint', $deviceFingerprint)
            ->distinct('user_id')
            ->count('user_id');

        return $userCount >= 2;
    }

    /**
     * Detect unusual engagement velocity from a user.
     */
    public static function detectEngagementVelocity($userId): bool
    {
        // More than 50 engagements in 1 hour is suspicious
        $count = EngagementAudit::where('user_id', $userId)
            ->where('created_at', '>=', now()->subHour())
            ->count();

        return $count >= 50;
    }

    /**
     * Get audit statistics for a meme.
     */
    public static function getMemeAuditStats($memeId): array
    {
        $total = EngagementAudit::where('meme_id', $memeId)->count();
        $verified = EngagementAudit::where('meme_id', $memeId)
            ->where('is_verified', true)
            ->where('is_flagged', false)
            ->count();
        $flagged = EngagementAudit::where('meme_id', $memeId)
            ->where('is_flagged', true)
            ->count();

        $byType = EngagementAudit::where('meme_id', $memeId)
            ->select('engagement_type', DB::raw('COUNT(*) as total'), 
                     DB::raw('SUM(CASE WHEN is_verified = 1 AND is_flagged = 0 THEN 1 ELSE 0 END) as verified'))
            ->groupBy('engagement_type')
            ->get()
            ->pluck('total', 'engagement_type')
            ->toArray();

        $verifiedByType = EngagementAudit::where('meme_id', $memeId)
            ->where('is_verified', true)
            ->where('is_flagged', false)
            ->select('engagement_type', DB::raw('COUNT(*) as count'))
            ->groupBy('engagement_type')
            ->get()
            ->pluck('count', 'engagement_type')
            ->toArray();

        return [
            'total_engagements' => $total,
            'verified_engagements' => $verified,
            'flagged_engagements' => $flagged,
            'fraud_percentage' => $total > 0 ? round(($flagged / $total) * 100, 2) : 0,
            'by_type' => $byType,
            'verified_by_type' => $verifiedByType,
        ];
    }

    /**
     * Get all flagged engagements across all memes.
     */
    public static function getAllFlaggedEngagements($limit = 50)
    {
        return EngagementAudit::with(['user', 'meme'])
            ->where('is_flagged', true)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Clean up flagged engagements (remove their effect from scores).
     */
    public static function cleanupFlaggedEngagements($memeId = null)
    {
        $query = EngagementAudit::where('is_flagged', true);
        
        if ($memeId) {
            $query->where('meme_id', $memeId);
        }

        return $query->delete();
    }

    /**
     * Get suspicious IP addresses based on engagement patterns.
     */
    public static function getSuspiciousIps($threshold = 5)
    {
        return EngagementAudit::select('ip_address', DB::raw('COUNT(*) as engagement_count'), 
                                        DB::raw('COUNT(DISTINCT user_id) as user_count'),
                                        DB::raw('SUM(CASE WHEN is_flagged = 1 THEN 1 ELSE 0 END) as flagged_count'))
            ->groupBy('ip_address')
            ->having('engagement_count', '>=', $threshold)
            ->orderByDesc('engagement_count')
            ->limit(100)
            ->get();
    }

    /**
     * Auto-delete flagged engagements older than specified hours.
     */
    public static function autoDeleteFlaggedEngagements($olderThanHours = 24)
    {
        $deletedCount = EngagementAudit::where('is_flagged', true)
            ->where('created_at', '<', now()->subHours($olderThanHours))
            ->delete();

        return $deletedCount;
    }

    /**
     * Auto-delete old audit records (cleanup database).
     */
    public static function autoDeleteOldRecords($olderThanDays = 30)
    {
        $deletedCount = EngagementAudit::where('created_at', '<', now()->subDays($olderThanDays))
            ->delete();

        return $deletedCount;
    }

    /**
     * Auto-delete engagements from users who were deleted.
     */
    public static function autoDeleteOrphanedEngagements()
    {
        $deletedCount = EngagementAudit::doesntHave('user')->delete();
        return $deletedCount;
    }

    /**
     * Full auto-cleanup routine.
     */
    public static function runAutoCleanup()
    {
        $results = [
            'flagged_deleted' => self::autoDeleteFlaggedEngagements(24),
            'old_records_deleted' => self::autoDeleteOldRecords(30),
            'orphaned_deleted' => self::autoDeleteOrphanedEngagements(),
        ];

        return $results;
    }
}
