<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminMiddleware;

Route::get('/', [\App\Http\Controllers\MemeController::class, 'index'])->name('home');
Route::get('/memes', [\App\Http\Controllers\MemeController::class, 'index'])->name('memes.index');
Route::get('/memes/filter', [\App\Http\Controllers\MemeController::class, 'getFilteredMemes'])->name('memes.filter');
Route::post('/memes/{meme}/view', [\App\Http\Controllers\MemeController::class, 'incrementView'])->name('memes.view');
Route::post('/memes/{meme}/share', [\App\Http\Controllers\MemeController::class, 'share'])->name('memes.share');
Route::get('/winners', [\App\Http\Controllers\MemeController::class, 'winners'])->name('winners');
Route::get('/api/brand-winners', [\App\Http\Controllers\MemeController::class, 'getBrandWinners'])->name('api.brand-winners');
Route::get('/battles', function() {
    return redirect()->route('home');
})->name('battles.index');

// Sponsored Campaign routes
Route::get('/sponsored/{slug}/submit', [\App\Http\Controllers\SponsoredCampaignController::class, 'showSubmitForm'])->name('sponsored.submit.form');
Route::post('/sponsored/{slug}/submit', [\App\Http\Controllers\SponsoredCampaignController::class, 'storeSubmission'])->name('sponsored.submit');
Route::get('/sponsored/{slug}/details', [\App\Http\Controllers\SponsoredCampaignController::class, 'getCampaignDetails'])->name('sponsored.details');
Route::get('/sponsored/{slug}/winners', [\App\Http\Controllers\SponsoredCampaignController::class, 'getWinners'])->name('sponsored.winners');

// Sponsored Submission routes (authenticated)
Route::middleware('auth')->group(function () {
    Route::get('/sponsored/submission/{id}/edit', [\App\Http\Controllers\SponsoredSubmissionController::class, 'edit'])->name('sponsored.submission.edit');
    Route::put('/sponsored/submission/{id}', [\App\Http\Controllers\SponsoredSubmissionController::class, 'update'])->name('sponsored.submission.update');
    Route::delete('/sponsored/submission/{id}', [\App\Http\Controllers\SponsoredSubmissionController::class, 'destroy'])->name('sponsored.submission.destroy');
});

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'showProfile'])->name('profile.show');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::get('/profile/password', [\App\Http\Controllers\ProfileController::class, 'showPasswordForm'])->name('profile.password.show');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Follow routes
    Route::post('/follow/{user}', [\App\Http\Controllers\FollowController::class, 'toggleFollow'])->name('follow.toggle');
    
    // Meme routes that need authentication
    Route::get('/memes/{meme}/edit', [\App\Http\Controllers\MemeController::class, 'edit'])->name('memes.edit');
    Route::put('/memes/{meme}', [\App\Http\Controllers\MemeController::class, 'update'])->name('memes.update');
    Route::delete('/memes/{meme}', [\App\Http\Controllers\MemeController::class, 'destroy'])->name('memes.destroy');
    
    // JavaScript/Fetch API routes for memes (no page refresh)
    Route::post('/api/memes/{meme}/update', [\App\Http\Controllers\MemeController::class, 'updateViaFetch'])->name('memes.update.fetch');
    Route::post('/api/memes/{meme}/delete', [\App\Http\Controllers\MemeController::class, 'destroyViaFetch'])->name('memes.delete.fetch');
    
    // Comment routes
    Route::post('/comments/{comment}/update', [\App\Http\Controllers\MemeController::class, 'updateComment'])->name('comments.update');
    Route::delete('/comments/{comment}', [\App\Http\Controllers\MemeController::class, 'deleteComment'])->name('comments.delete');

    // Brand routes that need authentication
    Route::resource('brands', \App\Http\Controllers\BrandController::class)->except(['show']);

    // AJAX date validation route
    Route::post('/brands/check-dates', [\App\Http\Controllers\BrandController::class, 'checkDates'])->name('brands.check-dates');
    
    // Draft Campaign routes
    Route::post('/drafts/save', [\App\Http\Controllers\BrandController::class, 'saveDraft'])->name('drafts.save');
    Route::get('/drafts', [\App\Http\Controllers\BrandController::class, 'getDrafts'])->name('drafts.index');
    Route::get('/drafts/{draft}', [\App\Http\Controllers\BrandController::class, 'getDraft'])->name('drafts.show');
    Route::delete('/drafts/{draft}', [\App\Http\Controllers\BrandController::class, 'deleteDraft'])->name('drafts.destroy');
});


// Admin routes - only for specific user
Route::middleware([AdminMiddleware::class])->group(function () {
    Route::get('/admin', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/memes', [\App\Http\Controllers\AdminController::class, 'memes'])->name('admin.memes');
    Route::post('/admin/memes/{meme}/approve', [\App\Http\Controllers\AdminController::class, 'approveMeme'])->name('admin.memes.approve');
    Route::post('/admin/memes/{meme}/reject', [\App\Http\Controllers\AdminController::class, 'rejectMeme'])->name('admin.memes.reject');
    Route::post('/admin/memes/{meme}/pending', [\App\Http\Controllers\AdminController::class, 'pendingMeme'])->name('admin.memes.pending');
    
    // Brand management routes
    Route::get('/admin/brands', [\App\Http\Controllers\AdminController::class, 'brands'])->name('admin.brands');
    Route::get('/admin/brands/{request}', [\App\Http\Controllers\AdminController::class, 'viewBrand'])->name('admin.brands.view');
    Route::patch('/admin/brands/{request}/approve', [\App\Http\Controllers\AdminController::class, 'approveBrand'])->name('admin.brands.approve');
    Route::patch('/admin/brands/{request}/reject', [\App\Http\Controllers\AdminController::class, 'rejectBrand'])->name('admin.brands.reject');
    Route::delete('/admin/brands/{request}', [\App\Http\Controllers\AdminController::class, 'deleteBrand'])->name('admin.brands.delete');

    // Approved brands management routes
    Route::get('/admin/approved-brands', [\App\Http\Controllers\AdminController::class, 'approvedBrands'])->name('admin.approved-brands');
    Route::delete('/admin/approved-brands/{brand}', [\App\Http\Controllers\AdminController::class, 'deleteApprovedBrand'])->name('admin.approved-brands.delete');
    Route::patch('/admin/approved-brands/{brand}/complete', [\App\Http\Controllers\AdminController::class, 'completeBrand'])->name('admin.approved-brands.complete');
    Route::patch('/admin/approved-brands/{brand}/activate', [\App\Http\Controllers\AdminController::class, 'activateBrand'])->name('admin.approved-brands.activate');
});

require __DIR__.'/auth.php';