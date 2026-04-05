<?php

namespace App\Http\Controllers;

use App\Models\Meme;
use App\Models\Report;
use App\Models\WeeklyChallenge;
use App\Models\ChallengeEntry;
use App\Models\ChallengePayout;
use App\Models\BrandRequest;
use App\Models\Brand;
use App\Models\IpAddress;
use App\Models\User;
use App\Models\EngagementAudit;
use App\Models\Blog;
use App\EngagementAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'total_memes' => Meme::count(),
            'contest_memes' => Meme::where('is_contest', true)->count(),
            'pending_reports' => Report::where('status', 'pending')->count(),
            'active_challenge' => WeeklyChallenge::current(),
            'suspicious_ips' => IpAddress::select('ip_address')
                ->selectRaw('COUNT(DISTINCT user_id) as user_count')
                ->groupBy('ip_address')
                ->having('user_count', '>=', 2)
                ->get()
                ->count(),
            'flagged_engagements' => \App\Models\EngagementAudit::where('is_flagged', true)->count(),
            'total_blogs' => Blog::count(),
            'published_blogs' => Blog::where('status', 'published')->count(),
            'blog_views' => Blog::sum('views_count'),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function moderationQueue(Request $request)
    {
        $query = Meme::with(['user', 'reports.user']);

        // Check if any specific filter is applied
        $isFiltering = $request->filled('status') || $request->filled('is_contest') || $request->filled('week_id');

        if ($isFiltering) {
            // Apply Filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('is_contest')) {
                $query->where('is_contest', $request->is_contest);
            }
            if ($request->filled('week_id')) {
                $query->where('contest_week_id', $request->week_id);
            }
            $query->latest();
        } else {
            // Default Moderation Queue Logic (Pending/Reported + Brand Memes)
            $query->where(function ($q) {
                $q->whereIn('status', ['pending', 'reported'])
                  ->orWhereHas('reports', function($sq) {
                      $sq->where('status', 'pending');
                  })
                  ->orWhereNotNull('brand_id'); // Also show brand-submitted memes
            })
            ->orderByRaw("CASE WHEN brand_id IS NOT NULL THEN 0 WHEN status = 'reported' THEN 1 WHEN status = 'pending' THEN 2 ELSE 3 END")
            ->latest();
        }

        $memes = $query->paginate(20)->withQueryString();
        $weeks = WeeklyChallenge::orderBy('start_at', 'desc')->pluck('challenge_id');

        return view('admin.moderation', compact('memes', 'weeks'));
    }

    public function reports()
    {
        $reports = Report::with(['meme', 'user'])
            ->latest()
            ->paginate(50);

        return view('admin.reports', compact('reports'));
    }

    public function challengeManager()
    {
        $challenge = WeeklyChallenge::current();
        
        // Live Leaderboard (Top 50)
        $leaderboard = collect([]);
        if ($challenge) {
            $leaderboard = $challenge->getLeaderboard(50);
        }

        return view('admin.weekly-challenge', compact('challenge', 'leaderboard'));
    }

    public function closeWeek(WeeklyChallenge $challenge)
    {
        // Manual trigger for week closing
        DB::transaction(function() use ($challenge) {
            $success = $challenge->closeAndPickWinner();
            
            // Immediately start new week
            WeeklyChallenge::create([
                'challenge_id' => now()->addWeek()->format('Y') . '-W' . now()->addWeek()->format('W'),
                'status' => 'active',
                'start_at' => now()->startOfWeek(),
                'end_at' => now()->endOfWeek(),
                'prize_pool_cents' => 0,
            ]);
        });

        return back()->with('success', 'Week closed, winner picked, and new week started.');
    }

    public function payouts()
    {
        $payouts = ChallengePayout::with(['user', 'meme'])
            ->latest()
            ->paginate(20);
            
        return view('admin.payouts', compact('payouts'));
    }

    public function updatePayout(Request $request, ChallengePayout $payout)
    {
        $payout->update(['notes' => $request->notes]);
        return back()->with('success', 'Payout notes updated.');
    }

    public function markPayoutPaid(ChallengePayout $payout)
    {
        $payout->update(['status' => 'paid']);
        return back()->with('success', 'Marked as Paid.');
    }

    public function updateMemeStatus(Request $request, Meme $meme)
    {
        $request->validate([
            'status' => 'required|in:published,hidden,reported,removed',
        ]);

        $meme->update(['status' => $request->status]);

        return response()->json(['success' => true]);
    }

    public function resolveReport(Request $request, Report $report)
    {
        $request->validate([
            'action' => 'required|in:resolved,dismissed',
        ]);

        $report->update(['status' => $request->action]);

        return response()->json(['success' => true]);
    }

    /**
     * Show the brand requests management page.
     */
    public function brands(Request $request)
    {
        $status = $request->query('status');
        $order = $request->query('order', 'all');

        $query = BrandRequest::with('user');

        if ($status) {
            $query->where('status', $status);
        }

        switch ($order) {
            case 'new':
                $query->orderBy('created_at', 'desc');
                break;
            case 'all':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $brandRequests = $query->paginate(10);

        return view('admin.brands', compact('brandRequests', 'status', 'order'));
    }

    /**
     * Show a specific brand request.
     */
    public function viewBrand(BrandRequest $request)
    {
        return view('admin.view_brand', compact('request'));
    }

    /**
     * Approve a brand request.
     */
    public function approveBrand(BrandRequest $request)
    {
        Brand::create([
            'user_id' => $request->user_id,
            'company_name' => $request->company_name,
            'slug' => Str::slug($request->company_name . '-' . Str::random(5)),
            'website' => $request->website,
            'contact_email' => $request->contact_email,
            'phone' => $request->phone,
            'brand_description' => $request->brand_description,
            'product_brand' => $request->product_brand,
            'product_category' => $request->product_category,
            'tags' => $request->tags,
            'product_content' => $request->product_content,
            'social_links' => $request->social_links ?? [],
            'logo' => $request->brand_logo,
            'product_images' => $request->product_images,
            'other_files' => $request->other_files,
            'campaign_image' => $request->campaign_image,
            'slogan' => $request->slogan,
            'image_description' => $request->image_description,
            'creative_assets' => $request->creative_assets,
            'theme_color' => $request->theme_color ?? '#6f42c1',
            'campaign_title' => $request->campaign_title,
            'campaign_description' => $request->campaign_description,
            'subject_category' => $request->subject_category,
            'campaign_goal' => $request->campaign_goal,
            'audience_location' => $request->audience_location,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'prize_type' => $request->prize_type,
            'prize_amount' => $request->prize_amount,
            'winner_selection' => $request->winner_selection,
            'rules' => $request->rules,
            'status' => 'active',
        ]);

        $request->update(['status' => 'approved']);

        return redirect()->route('admin.brands')->with('success', 'Brand request approved successfully!');
    }

    /**
     * Reject a brand request.
     */
    public function rejectBrand(BrandRequest $request)
    {
        $request->update(['status' => 'rejected']);

        return redirect()->route('admin.brands')->with('success', 'Brand request rejected successfully!');
    }

    /**
     * Delete a brand request.
     */
    public function deleteBrand(BrandRequest $request)
    {
        if ($request->brand_logo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($request->brand_logo);
        }

        if ($request->product_image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($request->product_image);
        }

        if ($request->product_file) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($request->product_file);
        }

        $request->delete();

        return redirect()->route('admin.brands')->with('success', 'Brand request deleted successfully!');
    }

    /**
     * Show the approved brands management page.
     */
    public function approvedBrands(Request $request)
    {
        $campaigns = Brand::with('user')->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.approved-brands', compact('campaigns'));
    }

    /**
     * Delete an approved brand.
     */
    public function deleteApprovedBrand(Brand $brand)
    {
        \App\Models\Meme::where('brand_id', $brand->id)->update(['brand_id' => null]);

        if ($brand->logo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($brand->logo);
        }

        $brand->delete();

        return redirect()->route('admin.approved-brands')->with('success', 'Brand deleted successfully!');
    }

    /**
     * Mark a brand as completed.
     */
    public function completeBrand(Brand $brand)
    {
        $brand->update(['is_completed' => true]);

        return redirect()->route('admin.approved-brands')->with('success', 'Brand campaign marked as completed!');
    }

    /**
     * Mark a brand as active (not completed).
     */
    public function activateBrand(Brand $brand)
    {
        $brand->update(['is_completed' => false]);

        return redirect()->route('admin.approved-brands')->with('success', 'Brand campaign marked as active!');
    }

    /**
     * Show IP tracking page.
     */
    public function ipTracking(Request $request)
    {
        $ipFilter = $request->query('ip');
        $suspiciousOnly = $request->query('suspicious', false);

        if ($suspiciousOnly) {
            $ipRecords = IpAddress::select('ip_address', DB::raw('COUNT(DISTINCT user_id) as user_count'), DB::raw('GROUP_CONCAT(DISTINCT user_id) as user_ids'))
                ->groupBy('ip_address')
                ->having('user_count', '>=', 2)
                ->orderByDesc('user_count')
                ->paginate(20);
        } elseif ($ipFilter) {
            $ipRecords = IpAddress::where('ip_address', $ipFilter)
                ->with('user')
                ->orderByDesc('last_login_at')
                ->paginate(20);
        } else {
            $ipRecords = IpAddress::with('user')
                ->orderByDesc('last_login_at')
                ->paginate(20);
        }

        $suspiciousCount = IpAddress::select('ip_address')
            ->selectRaw('COUNT(DISTINCT user_id) as user_count')
            ->groupBy('ip_address')
            ->having('user_count', '>=', 2)
            ->count();

        return view('admin.ip-tracking', compact('ipRecords', 'ipFilter', 'suspiciousOnly', 'suspiciousCount'));
    }

    /**
     * Delete all users sharing an IP address.
     */
    public function deleteUsersByIp(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip',
        ]);

        $ipAddress = $request->ip_address;
        $userIds = IpAddress::where('ip_address', $ipAddress)->pluck('user_id')->unique();

        if ($userIds->isEmpty()) {
            return back()->with('error', 'No users found for this IP address.');
        }

        // Prevent deleting yourself
        if ($userIds->contains(auth()->id())) {
            return back()->with('error', 'Cannot delete your own account.');
        }

        // Delete users and their related data
        $deletedCount = 0;
        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if ($user) {
                $user->delete();
                $deletedCount++;
            }
        }

        // Also delete IP tracking records
        IpAddress::where('ip_address', $ipAddress)->delete();

        return back()->with('success', "Deleted {$deletedCount} user(s) sharing IP address {$ipAddress}.");
    }

    /**
     * Delete a specific user.
     */
    public function deleteUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $userId = $request->user_id;

        // Prevent deleting yourself
        if ($userId == auth()->id()) {
            return back()->with('error', 'Cannot delete your own account.');
        }

        $user = User::find($userId);
        if ($user) {
            $userName = $user->name;
            $user->delete();

            // Also delete IP tracking records for this user
            IpAddress::where('user_id', $userId)->delete();

            return back()->with('success', "User '{$userName}' deleted successfully.");
        }

        return back()->with('error', 'User not found.');
    }

    /**
     * Show engagement audit page.
     */
    public function engagementAudit(Request $request)
    {
        $filter = $request->query('filter', 'all'); // all, flagged, suspicious
        $memeId = $request->query('meme_id');
        $engagementType = $request->query('type');

        $query = EngagementAudit::with(['user', 'meme']);

        if ($filter === 'flagged') {
            $query->where('is_flagged', true);
        } elseif ($filter === 'suspicious') {
            $query->where('risk_score', '>=', 30);
        }

        if ($memeId) {
            $query->where('meme_id', $memeId);
        }

        if ($engagementType) {
            $query->where('engagement_type', $engagementType);
        }

        $engagements = $query->orderByDesc('created_at')->paginate(50);

        $stats = [
            'total' => EngagementAudit::count(),
            'flagged' => EngagementAudit::where('is_flagged', true)->count(),
            'suspicious' => EngagementAudit::where('risk_score', '>=', 30)
                ->where('is_flagged', false)->count(),
            'today' => EngagementAudit::whereDate('created_at', today())->count(),
        ];

        $suspiciousIps = EngagementAuditService::getSuspiciousIps(5);

        return view('admin.engagement-audit', compact('engagements', 'filter', 'stats', 'suspiciousIps', 'memeId', 'engagementType'));
    }

    /**
     * Verify a flagged engagement.
     */
    public function verifyEngagement(EngagementAudit $engagement)
    {
        $engagement->verify();
        return back()->with('success', 'Engagement verified.');
    }

    /**
     * Remove flagged engagement.
     */
    public function removeEngagement(EngagementAudit $engagement)
    {
        $engagement->delete();
        return back()->with('success', 'Engagement removed.');
    }

    /**
     * Cleanup all flagged engagements.
     */
    public function cleanupFlaggedEngagements()
    {
        $count = EngagementAuditService::cleanupFlaggedEngagements();
        return back()->with('success', "Removed {$count} flagged engagements.");
    }

    /**
     * Show brand-submitted memes.
     */
    public function brandMemes()
    {
        $memes = Meme::whereNotNull('brand_id')
            ->with(['user', 'brand'])
            ->latest()
            ->paginate(20);

        return view('admin.moderation', [
            'memes' => $memes,
            'weeks' => WeeklyChallenge::orderBy('start_at', 'desc')->pluck('challenge_id'),
            'title' => 'Brand Submitted Memes'
        ]);
    }
}
