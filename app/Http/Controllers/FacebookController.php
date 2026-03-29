<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FacebookController extends Controller
{
    // Step 1: Redirect to Facebook
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    // Step 2: Handle callback
    public function handleCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->stateless()->user();

            if (!$facebookUser->getEmail()) {
                return redirect('/login')->with('error', 'Facebook did not provide an email address. Please use another login method.');
            }

            // Check if user exists, add random password to satisfy DB
            $user = User::firstOrCreate(
                ['email' => $facebookUser->getEmail()],
                [
                    'name' => $facebookUser->getName() ?? 'Facebook User',
                    'password' => bcrypt(str()->random(16)), // 🔑 random password
                ]
            );

            // Login user
            Auth::login($user);

            // Redirect to Upload meme page
            return redirect('/upload-meme');
        } catch (\Exception $e) {
            \Log::error('Facebook auth error: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Facebook login failed. Please try again.');
        }
    }
}
