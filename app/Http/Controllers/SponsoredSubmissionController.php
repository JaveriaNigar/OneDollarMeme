<?php

namespace App\Http\Controllers;

use App\Models\SponsoredSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SponsoredSubmissionController extends Controller
{
    /**
     * Show the form for editing the specified sponsored submission.
     */
    public function edit($id)
    {
        $submission = SponsoredSubmission::findOrFail($id);

        // Authorize that the user owns this submission
        if ($submission->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to edit this sponsored submission.');
        }

        return view('memes.edit', ['meme' => $submission]); // Using the same view as regular memes
    }

    /**
     * Update the specified sponsored submission in storage.
     */
    public function update(Request $request, $id)
    {
        $submission = SponsoredSubmission::findOrFail($id);

        // Authorize that the user owns this submission
        if ($submission->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to update this sponsored submission.');
        }

        $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string|max:5000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Delete the old image
            if ($submission->image_path) {
                Storage::disk('public')->delete($submission->image_path);
            }

            $imagePath = $request->file('image')->store('sponsored_memes', 'public');
            $submission->image_path = $imagePath;
        }

        $submission->title = $request->title;
        $submission->content = $request->content;
        $submission->save();

        return redirect()->route('home')->with('success', 'Sponsored submission updated successfully!');
    }

    /**
     * Remove the specified sponsored submission from storage.
     */
    public function destroy($id)
    {
        $submission = SponsoredSubmission::findOrFail($id);

        // Authorize that the user owns this submission
        if ($submission->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to delete this sponsored submission.');
        }

        // Delete the image file
        if ($submission->image_path) {
            Storage::disk('public')->delete($submission->image_path);
        }

        $submission->delete();

        return redirect()->route('home')->with('success', 'Sponsored submission deleted successfully!');
    }
}