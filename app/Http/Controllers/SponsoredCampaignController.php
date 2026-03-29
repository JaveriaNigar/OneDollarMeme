<?php

namespace App\Http\Controllers;

use App\Models\SponsoredCampaign;
use App\Models\SponsoredSubmission;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SponsoredCampaignController extends Controller
{
    public function showBrandRequestForm()
    {
        $brands = Brand::all();
        return view('brands.request', compact('brands'));
    }

    public function showSubmitForm($slug)
    {
        $campaign = SponsoredCampaign::where('slug', $slug)->with('brand')->first();

        if (!$campaign) {
            $campaign = Brand::where('slug', $slug)->firstOrFail();
        }

        $brandId = $campaign instanceof Brand ? $campaign->id : $campaign->brand_id;
        $memesWithScores = \App\Models\Meme::with(['user', 'reactions', 'comments'])
            ->where('brand_id', $brandId)
            ->whereNotIn('status', ['rejected', 'removed', 'hidden'])
            ->get()
            ->map(function ($meme) {
                $meme->calculated_score = ($meme->reactions->count() * 2) + ($meme->comments->count() * 3) + (($meme->shares_count ?? 0) * 5);
                return $meme;
            })
            ->sortByDesc('calculated_score')
            ->take(3)
            ->values();

        $brand = $campaign instanceof Brand ? $campaign : $campaign->brand;
        if ($brand->start_date && $brand->start_date->isFuture()) {
            return redirect()->route('brands.show', $brand->id)
                ->with('error', '⚠️ This brand campaign has not started yet. Please wait until ' . $brand->start_date->format('M d, Y') . ' to submit your meme!');
        }

        if ($brand->end_date && $brand->end_date->isPast()) {
            return redirect()->route('brands.show', $brand->id)
                ->with('error', '🚫 This campaign has already ended. Thank you for your interest!');
        }

        return view('memes.sponsored-submit', compact('campaign', 'memesWithScores'));
    }

    public function storeSubmission(Request $request, $slug)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please log in to submit a meme for this campaign.');
        }

        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
        ]);

        if (!$request->title && !$request->hasFile('image')) {
            return back()->withErrors(['error' => 'Please provide either a caption or an image.'])->withInput();
        }

        // ✅ $campaign define kiya
        $campaign = SponsoredCampaign::where('slug', $slug)->with('brand')->first();

        if (!$campaign) {
            $brand = Brand::where('slug', $slug)->firstOrFail();
        } else {
            $brand = $campaign->brand;
        }

        if ($brand->start_date && $brand->start_date->isFuture()) {
            return redirect()->route('brands.show', $brand->id)
                ->with('error', '⚠️ Campaign not started yet. Submission blocked.');
        }

        if ($brand->end_date && $brand->end_date->isPast()) {
            return redirect()->route('brands.show', $brand->id)
                ->with('error', '🚫 Campaign has ended. Submission blocked.');
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $storagePath = $campaign ? 'sponsored_memes' : 'memes';
            $imagePath = $request->file('image')->store($storagePath, 'public');
        }

        if ($campaign) {
            SponsoredSubmission::create([
                'user_id'    => Auth::id(),
                'campaign_id' => $campaign->id,
                'title'      => $request->title ?? 'Untitled Meme',
                'content'    => $request->content ?? null,
                'image_path' => $imagePath,
                'status'     => 'approved',
            ]);
        } else {
            \App\Models\Meme::create([
                'user_id'    => Auth::id(),
                'brand_id'   => $brand->id,
                'title'      => $request->title ?? 'Untitled Meme',
                'image_path' => $imagePath,
                'status'     => 'active',
            ]);
        }

        return redirect()->route('brands.show', $brand->id ?? $campaign->brand_id)
            ->with('success', '🎉 Your meme has been submitted! You can see it on the brand page.');
    }

    public function getCampaignDetails($slug)
    {
        $campaign = SponsoredCampaign::where('slug', $slug)->with('brand')->first();

        if (!$campaign) {
            $campaign = Brand::where('slug', $slug)->firstOrFail();
        }

        return response()->json([
            'success' => true,
            'campaign' => $campaign
        ]);
    }

    public function getWinners($slug)
    {
        $campaign = SponsoredCampaign::where('slug', $slug)->first();

        if (!$campaign) {
            $campaign = Brand::where('slug', $slug)->firstOrFail();
        }

        $brandId = $campaign instanceof Brand ? $campaign->id : $campaign->brand_id;
        $memesWithScores = \App\Models\Meme::with(['user', 'reactions', 'comments'])
            ->where('brand_id', $brandId)
            ->where('status', 'active')
            ->get()
            ->map(function ($meme) {
                $meme->calculated_score = ($meme->reactions->count() * 2) + ($meme->comments->count() * 3) + (($meme->shares_count ?? 0) * 5);
                return $meme;
            })
            ->sortByDesc('calculated_score')
            ->take(3)
            ->values();

        return response()->json([
            'success' => true,
            'winners' => $memesWithScores->map(function ($meme) {
                return [
                    'id'               => $meme->id,
                    'user_name'        => $meme->user->name ?? 'Anonymous',
                    'calculated_score' => $meme->calculated_score,
                    'image_path'       => $meme->image_path,
                ];
            }),
        ]);
    }
}