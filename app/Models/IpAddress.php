<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IpAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ip_address',
        'device_fingerprint',
        'browser_version',
        'operating_system',
        'user_agent',
        'last_login_at',
    ];

    protected $casts = [
        'last_login_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all users sharing the same IP address.
     */
    public static function getUsersByIp($ipAddress)
    {
        return self::where('ip_address', $ipAddress)
            ->with('user')
            ->get()
            ->pluck('user')
            ->unique('id');
    }

    /**
     * Get IP addresses with multiple users (potential abuse).
     */
    public static function getSuspiciousIps($threshold = 2)
    {
        return self::select('ip_address')
            ->selectRaw('COUNT(DISTINCT user_id) as user_count')
            ->selectRaw('GROUP_CONCAT(DISTINCT user_id) as user_ids')
            ->groupBy('ip_address')
            ->having('user_count', '>=', $threshold)
            ->orderByDesc('user_count')
            ->get();
    }

    /**
     * Record or update IP login tracking.
     */
    public static function recordLogin($userId, $ipAddress, $userAgent = null)
    {
        $ipRecord = self::where('user_id', $userId)
            ->where('ip_address', $ipAddress)
            ->first();

        $browserInfo = self::parseUserAgent($userAgent ?? request()->userAgent());

        if ($ipRecord) {
            $ipRecord->update([
                'last_login_at' => now(),
                'browser_version' => $browserInfo['browser'],
                'operating_system' => $browserInfo['os'],
                'user_agent' => $userAgent ?? request()->userAgent(),
            ]);
        } else {
            self::create([
                'user_id' => $userId,
                'ip_address' => $ipAddress,
                'device_fingerprint' => self::generateFingerprint($userAgent ?? request()->userAgent()),
                'browser_version' => $browserInfo['browser'],
                'operating_system' => $browserInfo['os'],
                'user_agent' => $userAgent ?? request()->userAgent(),
                'last_login_at' => now(),
            ]);
        }

        return $ipRecord;
    }

    /**
     * Parse user agent to extract browser and OS info.
     */
    public static function parseUserAgent($userAgent)
    {
        $browser = 'Unknown';
        $os = 'Unknown';

        // Detect OS
        if (preg_match('/Windows NT 10.0/i', $userAgent)) {
            $os = 'Windows 10';
        } elseif (preg_match('/Windows NT 6.3/i', $userAgent)) {
            $os = 'Windows 8.1';
        } elseif (preg_match('/Windows NT 6.2/i', $userAgent)) {
            $os = 'Windows 8';
        } elseif (preg_match('/Windows NT 6.1/i', $userAgent)) {
            $os = 'Windows 7';
        } elseif (preg_match('/Mac OS X/i', $userAgent)) {
            $os = 'macOS';
        } elseif (preg_match('/Linux/i', $userAgent)) {
            $os = 'Linux';
        } elseif (preg_match('/Android/i', $userAgent)) {
            $os = 'Android';
        } elseif (preg_match('/iPhone/i', $userAgent)) {
            $os = 'iOS';
        }

        // Detect Browser
        if (preg_match('/Edg\/([\d.]+)/i', $userAgent, $match)) {
            $browser = 'Edge ' . $match[1];
        } elseif (preg_match('/Chrome\/([\d.]+)/i', $userAgent, $match)) {
            $browser = 'Chrome ' . $match[1];
        } elseif (preg_match('/Firefox\/([\d.]+)/i', $userAgent, $match)) {
            $browser = 'Firefox ' . $match[1];
        } elseif (preg_match('/Safari\/([\d.]+)/i', $userAgent, $match)) {
            $browser = 'Safari ' . $match[1];
        } elseif (preg_match('/MSIE ([\d.]+)/i', $userAgent, $match)) {
            $browser = 'IE ' . $match[1];
        }

        return ['browser' => $browser, 'os' => $os];
    }

    /**
     * Generate a simple device fingerprint.
     */
    public static function generateFingerprint($userAgent)
    {
        return hash('sha256', $userAgent);
    }
}
