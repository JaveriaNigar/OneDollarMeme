<?php

namespace App\Http\Controllers;

use App\Models\Meme;
use App\Models\BrandRequest;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function dashboard(Request $request)
    {
        // For the dashboard, we just need to pass the status and order for the tabs/filters
        $status = $request->query('status');
        $order = $request->query('order', 'all'); // Default to 'all' if not specified

        return view('admin.dashboard', compact('status', 'order'));
    }

    /**
     * Approve a meme.
     */
    public function approveMeme(Meme $meme)
    {
        $meme->update(['status' => 'active']);

        return redirect()->back()->with('success', 'Meme approved successfully!');
    }

    /**
     * Reject a meme.
     */
    public function rejectMeme(Meme $meme)
    {
        $meme->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Meme rejected successfully!');
    }

    /**
     * Set meme status to pending.
     */
    public function pendingMeme(Meme $meme)
    {
        $meme->update(['status' => 'pending']);

        return redirect()->back()->with('success', 'Meme marked as pending successfully!');
    }

    /**
     * Show the memes management page.
     */
    public function memes(Request $request)
    {
        $status = $request->query('status');
        $order = $request->query('order', 'all'); // Default to 'all' if not specified

        $query = Meme::with('user');

        // Apply status filter if specified
        if ($status) {
            $query->where('status', $status);
        }

        // Apply ordering
        switch ($order) {
            case 'published':
                $query->where('status', 'active')->orderBy('created_at', 'desc');
                break;
            case 'new':
                $query->orderBy('created_at', 'desc');
                break;
            case 'remove':
                $query->where('status', 'rejected')->orderBy('created_at', 'desc');
                break;
            case 'all':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $memes = $query->paginate(10);

        return view('admin.memes', compact('memes', 'status', 'order'));
    }

    /**
     * Show the brand requests management page.
     */
    public function brands(Request $request)
    {
        $status = $request->query('status');
        $order = $request->query('order', 'all'); // Default to 'all' if not specified

        $query = BrandRequest::with('user');

        // Apply status filter if specified
        if ($status) {
            $query->where('status', $status);
        }

        // Apply ordering
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
        // Create a Brand record from the approved request
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
        // Delete associated files if they exist
        if ($request->brand_logo) {
            \Storage::disk('public')->delete($request->brand_logo);
        }

        if ($request->product_image) {
            \Storage::disk('public')->delete($request->product_image);
        }

        if ($request->product_file) {
            \Storage::disk('public')->delete($request->product_file);
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
        // Set brand_id to null for all associated memes (don't delete memes)
        \App\Models\Meme::where('brand_id', $brand->id)->update(['brand_id' => null]);

        // Delete associated files if they exist
        if ($brand->logo) {
            \Storage::disk('public')->delete($brand->logo);
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
}