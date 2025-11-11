<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request with OTP verification for unverified users
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        // Check if user exists
        if (!$user) {
            return back()->withErrors([
                'email' => __('No account found with this email address. Please register first.'),
            ])->with('show_register_link', true)->withInput($request->only('email'));
        }

        // Check password
        if (!Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'email' => __('The provided credentials do not match our records.'),
            ])->withInput($request->only('email'));
        }

        // Check if email is verified
        if (!$user->hasVerifiedEmail()) {
            // Automatically send OTP
            $user->sendOtpEmail('verify');

            // Store user ID and remember preference in session
            session([
                'otp_user_id' => $user->id,
                'otp_action' => 'login_verify',
                'remember' => $request->filled('remember')
            ]);

            // Redirect directly to verification page
            return redirect()->route('verify.otp.form')
                ->with('success', __('Your email is not verified yet. We have sent a verification code to') . ' ' . $user->email);
        }

        // Email is verified - proceed with normal login
        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Merge guest cart with user cart
            $this->mergeGuestCart();

            return redirect()->intended(route('home'))
                ->with('success', __('Welcome back!'));
        }

        return back()
            ->withErrors(['email' => __('Unable to login. Please try again.')])
            ->withInput($request->only('email'));
    }

 

    /**
     * Merge guest cart with authenticated user cart
     * (UNCHANGED - Your original function)
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
