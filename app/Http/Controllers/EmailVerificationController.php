<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class EmailVerificationController extends Controller
{
    /**
     * Show email verification notice.
     */
    public function show()
    {
        return view('auth.verify-email');
    }

    /**
     * Handle email verification.
     */
    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect('/')->with('success', 'Email verified successfully! You can now participate in contests.');
    }

    /**
     * Resend verification email.
     */
    public function resend(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Verification link sent! Please check your email.');
    }

    /**
     * Redirect to Google for authentication.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google callback.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Find existing user by Google ID
            $user = User::where('google_id', $googleUser->getId())->first();

            // If not found, try to find by email
            if (!$user) {
                $user = User::where('email', $googleUser->getEmail())->first();
            }

            if ($user) {
                // Update Google ID if not set
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                    ]);
                }

                Auth::login($user);
                return redirect('/')->with('success', 'Welcome back, ' . $user->name . '!');
            } else {
                // Create new user
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => Hash::make(uniqid()), // Random password
                    'email_verified_at' => now(), // Google verified email
                ]);

                Auth::login($user);
                return redirect('/')->with('success', 'Welcome! Your account has been created with Google.');
            }
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Google login failed. Please try again.');
        }
    }

    /**
     * Redirect to Facebook for authentication.
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Handle Facebook callback.
     */
    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();

            // Find existing user by Facebook ID
            $user = User::where('facebook_id', $facebookUser->getId())->first();

            // If not found, try to find by email
            if (!$user) {
                $user = User::where('email', $facebookUser->getEmail())->first();
            }

            if ($user) {
                // Update Facebook ID if not set
                if (!$user->facebook_id) {
                    $user->update([
                        'facebook_id' => $facebookUser->getId(),
                    ]);
                }

                Auth::login($user);
                return redirect('/')->with('success', 'Welcome back, ' . $user->name . '!');
            } else {
                // Create new user
                $user = User::create([
                    'name' => $facebookUser->getName(),
                    'email' => $facebookUser->getEmail(),
                    'facebook_id' => $facebookUser->getId(),
                    'password' => Hash::make(uniqid()), // Random password
                    'email_verified_at' => now(), // Facebook verified email
                ]);

                Auth::login($user);
                return redirect('/')->with('success', 'Welcome! Your account has been created with Facebook.');
            }
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Facebook login failed. Please try again.');
        }
    }
}
