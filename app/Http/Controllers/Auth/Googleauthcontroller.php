<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use Illuminate\Support\Facades\Log;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
    
        try {
            return Socialite::driver('google')->redirect();
        } catch (Exception $e) {
            
            return redirect()->route('login')
                ->with('error', __('Google authentication is not properly configured. Please contact support.'));
        }
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            // Get user info from Google
            $googleUser = Socialite::driver('google')->user();

            // Check if user exists by Google ID
            $user = User::where('google_id', $googleUser->getId())->first();

            if ($user) {
                // User exists, log them in
                Auth::guard('web')->login($user, true);

                // Update avatar if changed
                if ($user->avatar !== $googleUser->getAvatar()) {
                    $user->update(['avatar' => $googleUser->getAvatar()]);
                }

                return redirect()->intended(route('home'))
                    ->with('success', __('Welcome back, ') . $user->name . '!');
            }

            // Check if user exists by email (linking account)
            $existingUser = User::where('email', $googleUser->getEmail())->first();

            if ($existingUser) {
                // Link Google account to existing user
                $existingUser->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'provider' => 'google',
                ]);

                Auth::guard('web')->login($existingUser, true);

                return redirect()->intended(route('home'))
                    ->with('success', __('Your Google account has been linked successfully!'));
            }

            // Create new user
            $newUser = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'provider' => 'google',
                'email_verified_at' => now(),
                'password' => Hash::make(Str::random(32)), // Random password
            ]);

            // Log in the new user
            Auth::guard('web')->login($newUser, true);

            return redirect()->route('home')
                ->with('success', __('Welcome to our store, ') . $newUser->name . '!');
        } catch (Exception $e) {
            Log::error('Google OAuth Error: ' . $e->getMessage());

            return redirect()->route('login')
                ->with('error', __('Failed to authenticate with Google. Please try again or use email login.'));
        }
    }
}
