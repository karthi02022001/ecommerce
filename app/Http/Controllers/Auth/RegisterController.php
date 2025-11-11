<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class RegisterController extends Controller
{
    /**
     * Show registration form
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration (Step 1: Create user and send OTP)
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
        ]);

        // Create user (not verified yet)
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $request->phone,
        ]);

        // Send OTP
        $user->sendOtpEmail('register');

        // Store user ID in session for verification
        session(['otp_user_id' => $user->id, 'otp_action' => 'register']);

        return redirect()->route('verify.otp.form')
            ->with('success', __('Registration successful! Please verify your email with the OTP sent to') . ' ' . $user->email);
    }

    /**
     * Show OTP verification form
     */
    public function showOtpForm()
    {
        if (!session('otp_user_id')) {
            return redirect()->route('register')->with('error', __('Session expired. Please register or login again.'));
        }

        $user = User::find(session('otp_user_id'));
        $action = session('otp_action', 'register');

        if (!$user) {
            return redirect()->route('register')->with('error', __('User not found. Please register again.'));
        }

        return view('auth.verify-otp', compact('user', 'action'));
    }

    /**
     * Verify OTP (Step 2: Verify and auto login with cart merge)
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        if (!session('otp_user_id')) {
            return redirect()->route('register')->with('error', __('Session expired. Please try again.'));
        }

        $user = User::find(session('otp_user_id'));
        $action = session('otp_action', 'register');
        $remember = session('remember', false);

        if (!$user) {
            return back()->with('error', __('User not found.'));
        }

        // Verify OTP
        $result = $user->verifyOtp($request->otp);

        if ($result['success']) {
            // Log verification
            DB::table('email_verification_logs')
                ->where('user_id', $user->id)
                ->where('otp', $request->otp)
                ->update(['verified_at' => now()]);

         
            // Clear OTP session data
            session()->forget(['otp_user_id', 'otp_action', 'remember']);

            // Auto login after verification
            Auth::guard('web')->login($user, $remember);
            $request->session()->regenerate();

            // Merge guest cart with user cart after login
            $this->mergeGuestCart();

            if ($action === 'register') {
                return redirect()->route('home')
                    ->with('success', __('Email verified successfully! Welcome to our store, ') . $user->name . '!');
            } else {
                // From login attempt
                return redirect()->intended(route('home'))
                    ->with('success', __('Email verified and logged in successfully! Welcome back, ') . $user->name . '!');
            }
        }

        return back()->with('error', $result['message']);
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        if (!session('otp_user_id')) {
            return redirect()->route('register')->with('error', __('Session expired. Please try again.'));
        }

        $user = User::find(session('otp_user_id'));
        $action = session('otp_action', 'register');

        if (!$user) {
            return back()->with('error', __('User not found.'));
        }

        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            session()->forget(['otp_user_id', 'otp_action']);
            Auth::guard('web')->login($user);

            // Merge guest cart
            $this->mergeGuestCart();

            return redirect()->route('home')->with('success', __('Your email is already verified. Welcome!'));
        }

        // Check if last OTP was sent less than 1 minute ago (rate limiting)
        if ($user->otp_expires_at && $user->otp_expires_at->diffInSeconds(now()) > 540) { // 9 minutes remaining
            return back()->with('error', __('Please wait before requesting a new OTP.'));
        }

        // Send new OTP
        $user->sendOtpEmail($action === 'register' ? 'register' : 'verify');

        return back()->with('success', __('A new OTP has been sent to your email.'));
    }

    /**
     * Merge guest cart with authenticated user cart
     * (Same as LoginController)
     */
    protected function mergeGuestCart()
    {
        $sessionId = Session::get('cart_session_id');

        if (!$sessionId) {
            return;
        }

        $userId = auth()->id();

        // Get all guest cart items
        $guestCartItems = Cart::where('session_id', $sessionId)->get();

        foreach ($guestCartItems as $guestItem) {
            // Check if user already has this product in cart
            $userCartItem = Cart::where('user_id', $userId)
                ->where('product_id', $guestItem->product_id)
                ->first();

            if ($userCartItem) {
                // Merge quantities - update user's cart item
                $newQuantity = $userCartItem->quantity + $guestItem->quantity;

                // Check stock limit
                if ($guestItem->product && $newQuantity <= $guestItem->product->stock_quantity) {
                    $userCartItem->update(['quantity' => $newQuantity]);
                } else {
                    // If exceeds stock, use max available
                    $userCartItem->update(['quantity' => $guestItem->product->stock_quantity]);
                }

                // Delete guest cart item
                $guestItem->delete();
            } else {
                // Transfer guest cart item to user
                $guestItem->update([
                    'user_id' => $userId,
                    'session_id' => null
                ]);
            }
        }

        // Clear the session cart ID
        Session::forget('cart_session_id');
    }
}
