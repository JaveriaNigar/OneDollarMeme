<?php
namespace App\Http\Controllers;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user()->load(['memes.brand', 'memes.reactions', 'memes.comments', 'challengePayouts', 'blogs']);

        // Calculate scores for each meme (ensure shares_count is loaded)
        foreach ($user->memes as $meme) {
            // Force loading of relationships if not already loaded
            if (!$meme->relationLoaded('reactions')) {
                $meme->load('reactions');
            }
            if (!$meme->relationLoaded('comments')) {
                $meme->load('comments');
            }
            $meme->calculated_score = ($meme->reactions->count() * 2) +
                                      ($meme->comments->count() * 3) +
                                      (($meme->shares_count ?? 0) * 5);
        }

        return view('profile.edit', [
            'user' => $user,
            'isOwner' => true,
        ]);
    }

    /**
     * Display public profile by ID.
     */
    public function showPublic($name): View
    {
        // + ko space se replace karo
        $name = str_replace('+', ' ', $name);

        $user = \App\Models\User::with(['memes.brand', 'memes.reactions', 'memes.comments', 'challengePayouts', 'blogs'])
            ->where('name', $name)
            ->firstOrFail();

        $isOwner = auth()->check() && auth()->id() === $user->id;

        // Calculate scores for each meme (ensure shares_count is loaded)
        foreach ($user->memes as $meme) {
            // Force loading of relationships if not already loaded
            if (!$meme->relationLoaded('reactions')) {
                $meme->load('reactions');
            }
            if (!$meme->relationLoaded('comments')) {
                $meme->load('comments');
            }
            $meme->calculated_score = ($meme->reactions->count() * 2) +
                                      ($meme->comments->count() * 3) +
                                      (($meme->shares_count ?? 0) * 5);
        }

        return view('profile.edit', compact('user', 'isOwner'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }
        $request->user()->save();
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update user's bio and country via Ajax.
     */
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'bio' => ['nullable', 'string', 'max:120'],
            'country' => ['nullable', 'string', 'max:100'],
        ]);
        $user = $request->user();
        $user->fill($validated);
        $user->save();
        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
        ]);
    }

    /**
     * Update user's avatar photo.
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);
        $user = $request->user();
        if ($user->profile_photo_path) {
            \Storage::disk('public')->delete($user->profile_photo_path);
        }
        $path = $request->file('avatar')->store('profile-photos', 'public');
        $user->profile_photo_path = $path;
        $user->save();
        return response()->json([
            'success' => true,
            'message' => 'Avatar updated successfully',
            'avatar_url' => asset('storage/' . $path),
        ]);
    }

    /**
     * Display the account settings form.
     */
    public function accountSettings(Request $request): View
    {
        return view('profile.account-settings', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);
        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);
        return Redirect::route('account.settings')->with('status', 'password-updated');
    }

    /**
     * Update the user's name (username).
     */
    public function updateName(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);
        $request->user()->update([
            'name' => $request->name,
        ]);
        return Redirect::route('account.settings')->with('status', 'name-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);
        $user = $request->user();
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return Redirect::to('/');
    }
}