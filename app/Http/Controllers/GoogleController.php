<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    // Step 1: Redirect to Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Step 2: Handle callback
    public function handleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        // Check if user exists, add random password to satisfy DB
        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'password' => bcrypt(str()->random(16)), // 🔑 random password
            ]
        );

        // Login user
        Auth::login($user);

        // Redirect to Upload meme page
        return redirect('/upload-meme');
    }
}
