<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\BrandRequest;
use App\Models\User;
use App\Models\DraftCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class BrandController extends Controller
{
    /**
     * Show the brand dashboard (Authenticated).
     */
    public function index()
    {
        // For dashboard, maybe show user's own brands? 
        // For now, it shows all.
        $brands = Brand::all();
        return view('brands.index', compact('brands'));
    }

    /**
     * Show the public brands listing.
     */
    public function publicList(Request $request)
    {
        $activeChallenge = \App\Models\WeeklyChallenge::current();
        $currentChallengeId = $activeChallenge ? $activeChallenge->challenge_id : null;

        // Get top 3 brand-specific memes for the leaderboard
        $leaderboard = \App\Models\Meme::whereNotNull('brand_id')
            ->where('status', '!=', 'rejected')
            ->orderBy('score', 'desc')
            ->limit(3)
            ->get();

        $highlightMemeId = $request->input('highlight') ?: session('highlight_meme_id');

        // Filter memes from the last 30 days for brand page feed
        $thirtyDaysAgo = now()->subDays(30);

        $query = \App\Models\Meme::with(['reactions', 'comments.user', 'user', 'shareEvents', 'brand'])
            ->where('status', '!=', 'rejected')
            ->whereNotNull('brand_id')
            ->where('created_at', '>=', $thirtyDaysAgo);

        // Sponsored Memes FIRST
        $query->orderByRaw('CASE WHEN brand_id IS NOT NULL THEN 0 ELSE 1 END ASC');
        $query->orderBy('is_contest', 'desc');

        if ($highlightMemeId) {
            $query->orderByRaw("CASE WHEN id = ? THEN 0 ELSE 1 END ASC", [$highlightMemeId]);
        }

        $query->orderBy('contest_week_id', 'desc')
              ->orderBy('score', 'desc')
              ->orderBy('created_at', 'desc');

        $memes = $query->get();

        // Add user selected emoji
        $userId = Auth::id();
        foreach ($memes as $meme) {
            $meme->userEmoji = $meme->reactions->where('user_id', $userId)->first()?->emoji;
        }

        // Pick the first active brand to show as featured header on /brands route
        $featuredBrand = Brand::where('status', 'active')->first();

        // Get sidebar data (using the same logic as MemeController)
        $sidebarData = $this->getSidebarDataForPublic($featuredBrand);
        
        // Get 5 active brands sorted by start_date (chronological order)
        $brands = Brand::where('status', 'active')->orderBy('start_date', 'asc')->take(5)->get();

        return view('brands.brandhome', array_merge(
            compact('memes', 'currentChallengeId', 'leaderboard', 'brands', 'featuredBrand'), 
            $sidebarData
        ));
    }

    /**
     * Internal helper for sidebar data (Replicated from MemeController)
     */
    private function getSidebarDataForPublic($featuredBrand = null)
    {
        $activeChallenge = \App\Models\WeeklyChallenge::current();
        
        // Use brand end_date if it's a featured brand route, else weekly challenge
        $sidebarEndTime = null;
        if ($featuredBrand && $featuredBrand->end_date) {
            $sidebarEndTime = $featuredBrand->end_date->toIso8601String();
        } elseif ($activeChallenge && $activeChallenge->end_at) {
            $sidebarEndTime = $activeChallenge->end_at->toIso8601String();
        }
            
        // Use brand prize if featured, else weekly pool
        $sidebarPrizePool = '0';
        if ($featuredBrand && $featuredBrand->prize_amount) {
            $sidebarPrizePool = number_format($featuredBrand->prize_amount, 0);
        } elseif ($activeChallenge && $activeChallenge->prize_pool_cents) {
            $sidebarPrizePool = number_format($activeChallenge->prize_pool_cents / 100, 0);
        }

        $sidebarTop3 = User::whereHas('memes')
            ->withSum('memes as total_score', 'score')
            ->orderByDesc('total_score')
            ->limit(3)
            ->get()
            ->map(function($user) {
                return (object) [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_avatar' => $user->profile_photo_url,
                    'score' => $user->total_score ?? 0,
                ];
            });

        $sidebarLiveActivity = \App\Models\Meme::whereNotNull('brand_id')
            ->where('status', '!=', 'rejected')
            ->with(['user', 'reactions', 'comments'])
            ->get()
            ->map(function($meme) {
                // Formula: Reactions*2 + Comments*3 + Shares*5
                $calculatedScore = ($meme->reactions->count() * 2) + ($meme->comments->count() * 3) + (($meme->shares_count ?? 0) * 5);
                return [
                    'user' => $meme->user->name ?? 'User',
                    'title' => $meme->title ?? 'Untitled',
                    'score' => $calculatedScore,
                    'meme_id' => $meme->id,
                    'meme' => $meme
                ];
            })
            ->sortByDesc('score')
            ->take(10)
            ->values();
          $sidebarLastWeekWinners = [];

        // 5. Brand Campaign Winners (Added for integration)
        $brandWinners = \App\Models\Brand::where('status', 'active')
            ->with(['memes' => function($q) {
                $q->where('status', 'active');
            }, 'memes.user', 'memes.reactions', 'memes.comments'])
            ->get()
            ->filter(function($brand) {
                return $brand->memes->count() > 0;
            })
            ->map(function($brand) {
                foreach ($brand->memes as $meme) {
                    $meme->calculated_score = ($meme->reactions->count() * 2) + ($meme->comments->count() * 3) + (($meme->shares_count ?? 0) * 5);
                }
                return [
                    'brand' => $brand,
                    'winners' => $brand->memes->sortByDesc('calculated_score')->take(3)
                ];
            })
            ->take(3)
            ->all();

        return [
            'sidebarEndTime' => $sidebarEndTime,
            'sidebarPrizePool' => $sidebarPrizePool,
            'sidebarTop3' => $sidebarTop3,
            'sidebarLastWeekWinners' => $sidebarLastWeekWinners,
            'brandWinners' => $brandWinners,
            'sidebarLiveActivity' => $sidebarLiveActivity
        ];
    }

    /**
     * Display the specified brand's page with its memes.
     */
    public function show(Brand $brand)
    {
        $memes = \App\Models\Meme::where('brand_id', $brand->id)
            ->whereNotIn('status', ['rejected', 'removed', 'hidden'])
            ->with(['user', 'reactions', 'comments'])
            ->orderBy('score', 'desc')
            ->paginate(12);

        // Calculate scores for display
        $memes->getCollection()->each(function($meme) {
            $meme->calculated_score = ($meme->reactions->count() * 2) + ($meme->comments->count() * 3) + (($meme->shares_count ?? 0) * 5);
        });

        // Get sidebar data
        $sidebarData = $this->getSidebarDataForPublic($brand);
            
        return view('brands.show', array_merge(compact('brand', 'memes'), $sidebarData));
    }

    /**
     * Show the form for creating a new brand.
     */
    public function create(Request $request)
    {
        $draft = null;

        // Load draft if ID is provided
        if ($request->has('draft')) {
            $draft = DraftCampaign::where('id', $request->draft)
                ->where('user_id', auth()->id())
                ->first();
            
            if (!$draft) {
                return redirect()->route('drafts.index')
                    ->with('error', 'Draft not found.');
            }
        }

        return view('brands.create', compact('draft'));
    }

    /**
     * Check if dates overlap with existing campaigns (AJAX).
     */
    public function checkDates(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date', $startDate);

        $overlappingCampaign = BrandRequest::where(function($query) use ($startDate, $endDate) {
            $query->where(function($q) use ($startDate, $endDate) {
                // Campaign starts within the requested period
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  // Campaign completely encompasses the requested period
                  ->orWhere(function($q2) use ($startDate, $endDate) {
                      $q2->where('start_date', '<=', $startDate)
                         ->where('end_date', '>=', $endDate);
                  });
            })
            ->whereIn('status', ['pending', 'approved', 'active']);
        })
        ->first();

        return response()->json([
            'overlapping' => $overlappingCampaign !== null,
            'campaign' => $overlappingCampaign ? [
                'company_name' => $overlappingCampaign->company_name,
                'start_date' => $overlappingCampaign->start_date,
                'end_date' => $overlappingCampaign->end_date,
            ] : null
        ]);
    }

    /**
     * Store a newly created brand in storage.
     */
    public function store(Request $request)
    {
        // Require authentication
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please log in to create a brand campaign.');
        }

        try {
            $request->validate([
                'company_name' => 'required|string|max:255',
                'website' => 'nullable|string|max:255',
                'contact_email' => 'required|email|max:255',
                'tags' => 'nullable|string',
                'product_category' => 'nullable|string|max:255',
                'campaign_title' => 'required|string|max:255',
                'product_content' => 'required|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'dos_guidelines' => 'nullable|string',
                'donts_guidelines' => 'nullable|string',
                'campaign_goal' => 'required|string|max:255',
                'prize_type' => 'required|string|max:255',
                'audience_location' => 'required|string|max:255',
                'prize_amount' => 'required|numeric|min:100',
                'brand_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
                'product_images' => 'required|array|min:1|max:5',
                'product_images.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
                'image_description' => 'nullable|string|max:1000',
                'slogan' => 'nullable|string|max:255',
                'theme_color' => 'nullable|string|max:7',
                'terms_accepted' => 'accepted',
            ]);

            // Track draft ID if submitting from a draft
            $draftId = $request->input('draft_id');

            // Upload brand logo (commented out - may be needed in future)
            /*
            $brandLogo = null;
            if ($request->hasFile('brand_logo')) {
                $brandLogo = $request->file('brand_logo')->store('brand_logos', 'public');
            }
            */

            // Upload brand logo
            $brandLogo = null;
            if ($request->hasFile('brand_logo')) {
                $brandLogo = $request->file('brand_logo')->store('brand_logos', 'public');
            }

            // Upload product images (multiple)
            $productImages = [];
            if ($request->hasFile('product_images')) {
                foreach ($request->file('product_images') as $image) {
                    $productImages[] = $image->store('product_images', 'public');
                }
            }

            // Upload other files (multiple) (commented out - may be needed in future)
            /*
            $otherFiles = [];
            if ($request->hasFile('other_files')) {
                foreach ($request->file('other_files') as $file) {
                    $otherFiles[] = $file->store('other_files', 'public');
                }
            }
            */

            // Upload campaign image (commented out - may be needed in future)
            /*
            $campaignImage = null;
            if ($request->hasFile('campaign_image')) {
                $campaignImage = $request->file('campaign_image')->store('campaign_images', 'public');
            }
            */

            // Upload creative assets (multiple) (commented out - may be needed in future)
            /*
            $creativeAssets = [];
            if ($request->hasFile('creative_assets')) {
                foreach ($request->file('creative_assets') as $asset) {
                    $creativeAssets[] = $asset->store('creative_assets', 'public');
                }
            }
            */

            // Parse tags
            $tagsArray = [];
            if ($request->filled('tags')) {
                $tagsArray = array_map('trim', explode(',', $request->tags));
            }

            // Handle "Other" options - use custom text if "Other" is selected
            $campaignGoal = $request->campaign_goal;
            if ($campaignGoal === 'Other' && $request->filled('campaign_goal_other')) {
                $campaignGoal = trim($request->campaign_goal_other);
            }

            $prizeType = $request->prize_type;
            if ($prizeType === 'Other' && $request->filled('prize_type_other')) {
                $prizeType = trim($request->prize_type_other);
            }

            $audienceLocation = $request->audience_location;
            if ($audienceLocation === 'Other' && $request->filled('audience_location_other')) {
                $audienceLocation = trim($request->audience_location_other);
            }

            $productCategory = $request->product_category;
            if ($productCategory === 'Other' && $request->filled('product_category_other')) {
                $productCategory = trim($request->product_category_other);
            }

            // Check for overlapping campaign dates with other active/pending campaigns
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $overlappingCampaign = \App\Models\BrandRequest::where(function($query) use ($startDate, $endDate) {
                $query->where(function($q) use ($startDate, $endDate) {
                    // Campaign starts within the requested period
                    $q->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      // Campaign completely encompasses the requested period
                      ->orWhere(function($q2) use ($startDate, $endDate) {
                          $q2->where('start_date', '<=', $startDate)
                             ->where('end_date', '>=', $endDate);
                      });
                })
                ->whereIn('status', ['pending', 'approved', 'active']);
            })
            ->first();

            if ($overlappingCampaign) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'The selected dates are already chosen by another campaign. Please choose different dates.');
            }

            BrandRequest::create([
                'user_id' => Auth::id(),
                'company_name' => $request->company_name,
                'website' => $request->website,
                'contact_email' => $request->contact_email,
                // 'phone' => $request->phone, // Commented out - field removed from form
                // 'product_brand' => $request->product_brand, // Commented out - field removed from form
                'product_category' => $productCategory,
                'tags' => $tagsArray,
                // 'brand_description' => $request->brand_description, // Commented out - field removed from form
                'product_content' => $request->product_content,
                'campaign_title' => $request->campaign_title,
                // 'subject_category' => $request->subject_category, // Commented out - field removed from form
                'brand_logo' => $brandLogo,
                'product_images' => $productImages,
                // 'other_files' => $otherFiles, // Commented out - field removed from form
                // 'campaign_image' => $campaignImage, // Commented out - field removed from form
                'slogan' => $request->slogan,
                'image_description' => $request->image_description,
                // 'creative_assets' => $creativeAssets, // Commented out - field removed from form
                'campaign_goal' => $campaignGoal,
                'prize_type' => $prizeType,
                'audience_location' => $audienceLocation,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'prize_amount' => $request->prize_amount,
                // 'winner_selection' => $request->winner_selection, // Commented out - field removed from form
                'dos_guidelines' => $request->dos_guidelines,
                'donts_guidelines' => $request->donts_guidelines,
                'status' => 'pending',
                'theme_color' => $request->theme_color ?: '#6f42c1',
            ]);

            // Delete the draft if this submission was from a draft
            if ($draftId) {
                $draft = DraftCampaign::where('id', $draftId)
                    ->where('user_id', Auth::id())
                    ->first();
                
                if ($draft) {
                    // Delete associated files
                    if ($draft->campaign_image) {
                        Storage::disk('public')->delete($draft->campaign_image);
                    }
                    
                    if ($draft->product_images) {
                        foreach (json_decode($draft->product_images, true) as $path) {
                            Storage::disk('public')->delete($path);
                        }
                    }
                    
                    if ($draft->brand_assets) {
                        foreach (json_decode($draft->brand_assets, true) as $path) {
                            Storage::disk('public')->delete($path);
                        }
                    }
                    
                    $draft->delete();
                }
            }

            return redirect()->route('home')->with('success', 'Brand campaign request submitted successfully! We will review it shortly.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw validation exceptions to be handled properly
            throw $e;
        } catch (\Exception $e) {
            // Log the error with full details
            \Log::error('Brand campaign submission failed: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return with specific error message for debugging
            $errorMessage = 'Failed to submit campaign. Error: ' . $e->getMessage();
            
            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);
        }
    }

    /**
     * Show the form for editing the specified brand.
     */
    public function edit(Brand $brand)
    {
        return view('brands.edit', compact('brand'));
    }

    /**
     * Update the specified brand in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'website' => 'nullable|url',
            'brand_description' => 'nullable|string',
            'social_links' => 'nullable|array',
            'social_links.*' => 'url',
            'brand_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
            'theme_color' => 'nullable|string|max:7',
            'campaign_title' => 'nullable|string|max:255',
            'campaign_description' => 'nullable|string',
            'dos_guidelines' => 'nullable|string',
            'donts_guidelines' => 'nullable|string',
            'product_images' => 'nullable|array|max:5',
            'product_images.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
            'image_description' => 'nullable|string|max:1000',
            'slogan' => 'nullable|string|max:255',
            'prize_amount' => 'nullable|numeric|min:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if this is a no-payment update (simple update without changing prize)
        if ($request->filled('no_payment') && $request->no_payment == '1') {
            // Keep existing prize amount, don't require payment
            $request->merge(['prize_amount' => $brand->prize_amount]);
        }

        $logoPath = $brand->logo;
        if ($request->hasFile('brand_logo')) {
            if ($brand->logo) {
                Storage::disk('public')->delete($brand->logo);
            }
            $logoPath = $request->file('brand_logo')->store('brand_logos', 'public');
        }

        // Handle product images - merge with existing images
        $productImages = $brand->product_images ?? [];
        if (!is_array($productImages)) {
            $productImages = [];
        }
        
        if ($request->hasFile('product_images')) {
            foreach ($request->file('product_images') as $image) {
                $productImages[] = $image->store('product_images', 'public');
            }
        }

        // Handle theme color - if text input is provided, use that
        $themeColor = $brand->theme_color;
        if ($request->filled('theme_color_text')) {
            $themeColor = $request->theme_color_text;
        } elseif (!$themeColor) {
            $themeColor = '#000000';
        }

        // Validate color format
        if (!preg_match('/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/', $themeColor)) {
            $themeColor = '#000000'; // Default to black if invalid
        }

        $brand->update([
            'company_name' => $request->company_name,
            'website' => $request->website,
            'brand_description' => $request->brand_description,
            'social_links' => $request->social_links ?? [],
            'logo' => $logoPath,
            'theme_color' => $themeColor,
            'campaign_title' => $request->campaign_title,
            'campaign_description' => $request->campaign_description,
            'dos_guidelines' => $request->dos_guidelines,
            'donts_guidelines' => $request->donts_guidelines,
            'product_images' => $productImages,
            'image_description' => $request->image_description,
            'slogan' => $request->slogan,
            'prize_amount' => $request->prize_amount ?? $brand->prize_amount,
        ]);

        return redirect()->route('brands.index')->with('success', 'Brand updated successfully!');
    }

    /**
     * Remove the specified brand from storage.
     */
    public function destroy(Brand $brand)
    {
        if ($brand->logo) {
            Storage::disk('public')->delete($brand->logo);
        }

        $brand->delete();
        return redirect()->route('brands.index')->with('success', 'Brand deleted successfully!');
    }

    /**
     * Save a draft campaign.
     */
    public function saveDraft(Request $request)
    {
        // Minimal validation for drafts - only validate if fields are present
        $request->validate([
            'company_name' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'campaign_title' => 'nullable|string|max:255',
            'product_content' => 'nullable|string',
            'campaign_goal' => 'nullable|string|max:255',
            'prize_type' => 'nullable|string|max:255',
            'audience_location' => 'nullable|string|max:255',
            'prize_amount' => 'nullable|numeric|min:0',
            'brand_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
        ]);

        $data = $request->except(['_token', '_method', 'product_images', 'brand_assets', 'campaign_image', 'brand_logo']);
        $data['user_id'] = auth()->id();

        // Handle brand logo upload
        if ($request->hasFile('brand_logo')) {
            $data['brand_logo'] = $request->file('brand_logo')->store('brand_logos', 'public');
        }

        // Handle file uploads for draft (store paths as JSON)
        if ($request->hasFile('product_images')) {
            $paths = [];
            foreach ($request->file('product_images') as $image) {
                $paths[] = $image->store('drafts/product_images', 'public');
            }
            $data['product_images'] = json_encode($paths);
        }

        if ($request->hasFile('brand_assets')) {
            $paths = [];
            foreach ($request->file('brand_assets') as $asset) {
                $paths[] = $asset->store('drafts/brand_assets', 'public');
            }
            $data['brand_assets'] = json_encode($paths);
        }

        if ($request->hasFile('campaign_image')) {
            $data['campaign_image'] = $request->file('campaign_image')->store('drafts/campaign_images', 'public');
        }

        // Update existing draft or create new one
        $draft = DraftCampaign::updateOrCreate(
            ['user_id' => auth()->id()],
            $data
        );

        return response()->json([
            'success' => true,
            'message' => 'Draft saved successfully!',
            'draft_id' => $draft->id,
        ]);
    }

    /**
     * Get all drafts for the authenticated user.
     */
    public function getDrafts()
    {
        $drafts = DraftCampaign::where('user_id', auth()->id())
            ->orderBy('updated_at', 'desc')
            ->get();

        // Calculate completion percentage for each draft
        foreach ($drafts as $draft) {
            $filledFields = 0;
            $totalFields = count($draft->getFillable());
            
            foreach ($draft->getFillable() as $field) {
                if (!empty($draft->$field)) {
                    $filledFields++;
                }
            }
            
            $draft->completion_percentage = round(($filledFields / $totalFields) * 100);
        }

        return view('drafts.index', compact('drafts'));
    }

    /**
     * Get a specific draft.
     */
    public function getDraft(DraftCampaign $draft)
    {
        if ($draft->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'draft' => $draft,
        ]);
    }

    /**
     * Delete a draft.
     */
    public function deleteDraft(DraftCampaign $draft)
    {
        if ($draft->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Delete associated files
        if ($draft->campaign_image) {
            Storage::disk('public')->delete($draft->campaign_image);
        }

        if ($draft->product_images) {
            foreach (json_decode($draft->product_images, true) as $path) {
                Storage::disk('public')->delete($path);
            }
        }

        if ($draft->brand_assets) {
            foreach (json_decode($draft->brand_assets, true) as $path) {
                Storage::disk('public')->delete($path);
            }
        }

        $draft->delete();

        return redirect()->route('drafts.index')
            ->with('success', 'Draft deleted successfully!');
    }
}