<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MemeController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\FacebookController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\UserActivityController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\BlogController;

// ========================
// PUBLIC ROUTES
// ========================

// Home page — show memes with comments
Route::get('/', [MemeController::class, 'home'])->name('home');

// Memes page — public
Route::get('/memes', [MemeController::class, 'index'])->name('memes.index');

// Winners page — public
Route::get('/winners', [MemeController::class, 'winnersPage'])->name('memes.winners');

// Search page — public
Route::get('/search', [MemeController::class, 'search'])->name('memes.search');

// How It Works page — public
Route::get('/how-it-works', [MemeController::class, 'howItWorks'])->name('how-it-works');
// Terms & Conditions page — public
Route::get('/terms', function () {
    return view('terms');
})->name('terms');



// Get meme score
Route::get('/api/meme/{id}/score', [MemeController::class, 'getMemeScore'])->name('api.meme.score');

// View single Meme — PUBLIC (for shared links, both URL patterns)
Route::get('/meme/{meme}', [MemeController::class, 'show'])->name('memes.show');
Route::get('/memes/{meme}', [MemeController::class, 'show'])->name('memes.show.alt');

// ========================
// LOGIN ROUTES (REDIRECT IF GUEST)
// ========================
Route::get('/login', function () {
    return view('auth.login'); // Laravel Breeze default login view
})->name('login');

// ========================
// EMAIL VERIFICATION ROUTES
// ========================
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'show'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify');
    Route::post('/email/resend', [EmailVerificationController::class, 'resend'])->name('verification.resend');
});

// ========================
// GOOGLE AUTH ROUTES (NEW - Email Verified)
// ========================
Route::get('/auth/google', [EmailVerificationController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [EmailVerificationController::class, 'handleGoogleCallback'])->name('google.callback');

// ========================
// FACEBOOK AUTH ROUTES (NEW - Email Verified)
// ========================
Route::get('/auth/facebook', [EmailVerificationController::class, 'redirectToFacebook'])->name('facebook.login');
Route::get('/auth/facebook/callback', [EmailVerificationController::class, 'handleFacebookCallback'])->name('facebook.callback');

// ========================
// PROTECTED ROUTES (AUTH REQUIRED)
// ========================
Route::middleware(['auth', 'track.ip'])->group(function () {

    // Email Verification Notice (if not verified)
    Route::get('/email/verify-notice', function () {
        if (auth()->user()->hasVerifiedEmail()) {
            return redirect('/');
        }
        return view('auth.verify-email');
    })->name('verification.notice');

    // ========================
    // MEME AGENT ROUTES
    // ========================
    Route::match(['get', 'post'], '/meme-agent', [App\Http\Controllers\MemeAgentController::class, 'index'])->name('meme-agent');

    // New chat functionality routes
    Route::post('/meme-agent/conversation', [App\Http\Controllers\MemeAgentController::class, 'createConversation']);
    Route::get('/meme-agent/conversations/{user_id}', [App\Http\Controllers\MemeAgentController::class, 'getConversations']);
    Route::get('/meme-agent/conversation/{id}', [App\Http\Controllers\MemeAgentController::class, 'getConversation']);
    Route::post('/meme-agent/message', [App\Http\Controllers\MemeAgentController::class, 'saveMessage']);
    Route::put('/meme-agent/conversation/{id}/title', [App\Http\Controllers\MemeAgentController::class, 'updateConversationTitle']);
    Route::post('/meme-agent/chat', [App\Http\Controllers\MemeAgentController::class, 'chat']);
    Route::post('/meme-agent/generate-meme-from-conversation', [App\Http\Controllers\MemeAgentController::class, 'generateMemeFromConversation']);
    Route::get('/meme-agent/search-conversations', [App\Http\Controllers\MemeAgentController::class, 'searchConversations']);
    Route::delete('/meme-agent/conversation/{id}', [App\Http\Controllers\MemeAgentController::class, 'deleteConversation']);

    Route::get('/meme-agent/styles', function () {
        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->get('http://127.0.0.1:8003/styles', ['timeout' => 10]);
            return response($response->getBody()->getContents())->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to fetch styles'], 500);
        }
    });

    Route::get('/meme-agent/tones', function () {
        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->get('http://127.0.0.1:8003/tones', ['timeout' => 10]);
            return response($response->getBody()->getContents())->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to fetch tones'], 500);
        }
    });

    Route::get('/meme-agent/status/{job_id}', function ($job_id) {
        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->get("http://127.0.0.1:8003/meme-agent/status/{$job_id}", ['timeout' => 10]);
            return response($response->getBody()->getContents())->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to fetch status'], 500);
        }
    });

    Route::get('/meme-agent/history/{user_id}', function ($user_id) {
        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->get("http://127.0.0.1:8003/meme-agent/history/{$user_id}", ['timeout' => 10]);
            return response($response->getBody()->getContents())->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to fetch history'], 500);
        }
    });

    Route::post('/meme-agent/feedback', function (Illuminate\Http\Request $request) {
        $payload = [
            'topic' => $request->input('topic', ''),
            'style' => $request->input('style', ''),
            'tone' => $request->input('tone', ''),
            'meme_text' => $request->input('edited_text', $request->input('meme_text', '')),
            'rating' => (int) $request->input('rating', 3),
            'timestamp' => now()->toIso8601String(),
            'ip' => $request->ip(),
        ];
        Storage::append('meme_agent_feedback.jsonl', json_encode($payload));
        return response()->json(['status' => 'ok']);
    })->name('meme-agent.feedback');


    // ========================
    // BLOG ROUTES (Admin Only for Creation/Management)
    // ========================
    Route::prefix('blogs')->name('blogs.')->middleware(['admin'])->group(function () {
        Route::get('/dashboard', [BlogController::class, 'dashboard'])->name('dashboard');
        Route::get('/my-blogs', [BlogController::class, 'myBlogs'])->name('my-blogs');
        Route::get('/create', [BlogController::class, 'create'])->name('create');
        Route::post('/store', [BlogController::class, 'store'])->name('store');
        Route::get('/{blog}/edit', [BlogController::class, 'edit'])->name('edit');
        Route::put('/{blog}', [BlogController::class, 'update'])->name('update');
        Route::delete('/{blog}', [BlogController::class, 'destroy'])->name('destroy');
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/update-info', [ProfileController::class, 'updateProfile'])->name('profile.update.info');
    Route::post('/profile/update-avatar', [ProfileController::class, 'updateAvatar'])->name('profile.update.avatar');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/{name}', [ProfileController::class, 'showPublic'])->name('profile.show');

    // Account Settings
    Route::get('/account-settings', [ProfileController::class, 'accountSettings'])->name('account.settings');
    Route::patch('/account-settings', [ProfileController::class, 'updatePassword'])->name('account.settings.update');
    Route::patch('/account-settings/name', [ProfileController::class, 'updateName'])->name('account.settings.username.update');

    // Upload Meme (requires verified email, meme users only)
    Route::middleware(['verified', 'restrict.blogger'])->group(function () {
        Route::get('/upload-meme', [MemeController::class, 'create'])->name('upload-meme.create');
        Route::post('/upload-meme', [MemeController::class, 'store'])->name('upload-meme.store');
    });

    // View single Meme — moved to public routes above



    // Pay for Meme
    Route::get('/pay-for-meme/{meme}', [MemeController::class, 'payForMeme'])->name('memes.pay');
    Route::post('/pay-for-meme-dummy/{meme}', [MemeController::class, 'dummyPayment'])->name('memes.pay.dummy');

    // Edit and update meme
    Route::get('/meme/{meme}/edit', [MemeController::class, 'edit'])->name('memes.edit');
    Route::put('/meme/{meme}', [MemeController::class, 'update'])->name('memes.update');

    // Delete meme
    Route::delete('/meme/{meme}', [MemeController::class, 'destroy'])->name('memes.destroy');

    // Comment on Meme (existing route for backward compatibility)
    Route::post('/meme/{meme}/comment', [MemeController::class, 'comment'])->name('meme.comment');

    // New routes for comments with reply and delete functionality (meme users only)
    Route::middleware(['restrict.blogger'])->prefix('api')->group(function () {
        Route::post('/meme/{meme}/comments', [CommentsController::class, 'store'])->name('comments.store');
        Route::get('/meme/{meme}/comments', [CommentsController::class, 'getCommentsTree'])->name('comments.tree');
        Route::delete('/comments/{comment}', [CommentsController::class, 'destroy'])->name('comments.destroy');
        Route::get('/user/{id}/activity', [UserActivityController::class, 'getUserActivity'])->name('user.activity');

        // Add score route
        Route::get('/meme/{meme}/score', [MemeController::class, 'getScore'])->name('memes.score');
    });

    // Sponsored Campaign routes (meme users only)
    Route::middleware(['restrict.blogger'])->group(function () {
        Route::get('/sponsored/{slug}/submit', [\App\Http\Controllers\SponsoredCampaignController::class, 'showSubmitForm'])->name('sponsored.submit.form');
        Route::post('/sponsored/{slug}/submit', [\App\Http\Controllers\SponsoredCampaignController::class, 'storeSubmission'])->name('sponsored.submit');
        Route::get('/sponsored/{slug}/details', [\App\Http\Controllers\SponsoredCampaignController::class, 'getCampaignDetails'])->name('sponsored.details');
        Route::get('/sponsored/{slug}/winners', [\App\Http\Controllers\SponsoredCampaignController::class, 'getWinners'])->name('sponsored.winners');

        // Sponsored Submission routes (authenticated)
        Route::get('/sponsored/submission/{id}/edit', [\App\Http\Controllers\SponsoredSubmissionController::class, 'edit'])->name('sponsored.submission.edit');
        Route::put('/sponsored/submission/{id}', [\App\Http\Controllers\SponsoredSubmissionController::class, 'update'])->name('sponsored.submission.update');
        Route::delete('/sponsored/submission/{id}', [\App\Http\Controllers\SponsoredSubmissionController::class, 'destroy'])->name('sponsored.submission.destroy');
    });

    // Brand routes that need authentication (except show) - meme users only
    Route::middleware(['restrict.blogger'])->group(function () {
        Route::get('/work', function () {
            return view('brands.work');
        })->name('brands.work');
        Route::get('/brands/create', [\App\Http\Controllers\BrandController::class, 'create'])->name('brands.create');
        Route::resource('my-brands', \App\Http\Controllers\BrandController::class)->parameters(['my-brands' => 'brand'])->names('brands')->except(['show', 'create']);

        // AJAX date validation route
        Route::post('/my-brands/check-dates', [\App\Http\Controllers\BrandController::class, 'checkDates'])->name('brands.check-dates');

        // Draft Campaign routes
        Route::post('/drafts/save', [\App\Http\Controllers\BrandController::class, 'saveDraft'])->name('drafts.save');
        Route::get('/drafts', [\App\Http\Controllers\BrandController::class, 'getDrafts'])->name('drafts.index');
        Route::get('/drafts/{draft}', [\App\Http\Controllers\BrandController::class, 'getDraft'])->name('drafts.show');
        Route::delete('/drafts/{draft}', [\App\Http\Controllers\BrandController::class, 'deleteDraft'])->name('drafts.destroy');
    });

    // ========================
    // Emoji Reaction (AJAX) - meme users only
    // Meme-specific route using route model binding
    // ========================
    Route::middleware(['restrict.blogger'])->post('/memes/{meme}/reaction', [MemeController::class, 'react'])->name('memes.reaction.store');

    // Share functionality
    Route::post('/api/meme/{meme}/share', [MemeController::class, 'incrementShare'])->name('memes.share.increment');

    // Report meme
    Route::post('/api/memes/{meme}/report', [MemeController::class, 'report'])->name('memes.report');

    // ========================
    // ADMIN ROUTES
    // ========================
    Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');
        
        // Moderation
        Route::get('/moderation', [AdminController::class, 'moderationQueue'])->name('moderation');
        Route::post('/memes/{meme}/status', [AdminController::class, 'updateMemeStatus'])->name('memes.status');
        
        // Reports
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::post('/reports/{report}/resolve', [AdminController::class, 'resolveReport'])->name('reports.resolve');

        // Weekly Challenge
        Route::get('/challenge', [AdminController::class, 'challengeManager'])->name('challenge');
        Route::post('/challenge/create', [AdminController::class, 'createWeek'])->name('challenge.create'); // Optional helper
        Route::post('/challenge/{challenge}/close', [AdminController::class, 'closeWeek'])->name('challenge.close');

        // Payouts
        Route::get('/payouts', [AdminController::class, 'payouts'])->name('payouts');
        Route::post('/payouts/{payout}/update', [AdminController::class, 'updatePayout'])->name('payout.update');
        Route::post('/payouts/{payout}/paid', [AdminController::class, 'markPayoutPaid'])->name('payout.markPaid');

        // Brand management routes
        Route::get('/brands', [AdminController::class, 'brands'])->name('brands');
        Route::get('/brands/{request}', [AdminController::class, 'viewBrand'])->name('brands.view');
        Route::patch('/brands/{request}/approve', [AdminController::class, 'approveBrand'])->name('brands.approve');
        Route::patch('/brands/{request}/reject', [AdminController::class, 'rejectBrand'])->name('brands.reject');
        Route::delete('/brands/{request}', [AdminController::class, 'deleteBrand'])->name('brands.delete');
        Route::get('/brand-memes', [AdminController::class, 'brandMemes'])->name('brand-memes');

        // Approved brands management routes
        Route::get('/approved-brands', [AdminController::class, 'approvedBrands'])->name('approved-brands');
        Route::delete('/approved-brands/{brand}', [AdminController::class, 'deleteApprovedBrand'])->name('approved-brands.delete');
        Route::patch('/approved-brands/{brand}/complete', [AdminController::class, 'completeBrand'])->name('approved-brands.complete');
        Route::patch('/approved-brands/{brand}/activate', [AdminController::class, 'activateBrand'])->name('approved-brands.activate');

        // IP Tracking routes
        Route::get('/ip-tracking', [AdminController::class, 'ipTracking'])->name('ip-tracking');
        Route::post('/delete-users-by-ip', [AdminController::class, 'deleteUsersByIp'])->name('delete-users-by-ip');
        Route::post('/delete-user', [AdminController::class, 'deleteUser'])->name('delete-user');

        // Engagement Audit routes
        Route::get('/engagement-audit', [AdminController::class, 'engagementAudit'])->name('engagement-audit');
        Route::post('/engagement/{engagement}/verify', [AdminController::class, 'verifyEngagement'])->name('engagement.verify');
        Route::post('/engagement/{engagement}/remove', [AdminController::class, 'removeEngagement'])->name('engagement.remove');
        Route::post('/engagement/cleanup', [AdminController::class, 'cleanupFlaggedEngagements'])->name('engagement.cleanup');

        // Blog Management routes
        Route::get('/blogs', [BlogController::class, 'adminIndex'])->name('blogs');
        Route::post('/blogs/{blog}/status', [BlogController::class, 'adminUpdateStatus'])->name('blogs.status');
    });

    // Get winner meme based on scoring algorithm
    Route::get('/memes/winner', [MemeController::class, 'getWinner'])->name('memes.winner');
});

// Public API routes (accessible to non-authenticated users)
Route::get('/api/leaderboard', [MemeController::class, 'getLeaderboardData'])->name('api.leaderboard');
Route::get('/api/home-feed', [MemeController::class, 'getHomeFeedData'])->name('api.home-feed');
Route::get('/api/trending-feed', [MemeController::class, 'getTrendingFeedData'])->name('api.trending-feed');

// Get meme score (Public)
Route::get('/api/meme/{id}/score', [MemeController::class, 'getMemeScore'])->name('api.meme.score');

// Public Brands Listing
Route::get('/brands', [\App\Http\Controllers\BrandController::class, 'publicList'])->name('brands.public');

// Public Brand Page
Route::get('/brands/{brand}', [\App\Http\Controllers\BrandController::class, 'show'])->name('brands.show');

// API for brand winners live updates
Route::get('/api/brand-winners', [\App\Http\Controllers\BrandController::class, 'getBrandWinners'])->name('api.brand-winners');

// ========================
// PUBLIC BLOG ROUTES
// ========================
Route::get('/blogs', [BlogController::class, 'index'])->name('blogs.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blogs.show');

// Public comment route (requires auth)
Route::middleware(['auth'])->group(function () {
    Route::post('/blog/{blog}/comment', [BlogController::class, 'storeComment'])->name('blogs.comment.store');
    Route::put('/blog-comment/{comment}', [BlogController::class, 'updateComment'])->name('blogs.comment.update');
    Route::delete('/blog-comment/{comment}', [BlogController::class, 'deleteComment'])->name('blogs.comment.delete');
});

// ========================
// LARAVEL BREEZE AUTH
// ========================
require __DIR__.'/auth.php';
