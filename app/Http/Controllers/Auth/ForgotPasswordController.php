<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    /**
     * Show forgot password form
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send OTP for password reset
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => __('We could not find an account with this email address.'),
        ]);

        $user = User::where('email', $request->email)->first();

        // Send OTP
        $user->sendOtpEmail('password_reset');

        // Store email in session
        session(['password_reset_email' => $user->email]);

        return redirect()->route('password.reset')
            ->with('success', __('We have sent a password reset code to your email address.'));
    }

    /**
     * Show reset password form (with OTP verification)
     */
    public function showResetForm()
    {
        if (!session('password_reset_email')) {
            return redirect()->route('password.request')
                ->with('error', __('Session expired. Please try again.'));
        }

        $email = session('password_reset_email');
        $user = User::where('email', $email)->first();

        return view('auth.reset-password', compact('user'));
    }

    /**
     * Reset password after OTP verification
     */
    public function reset(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!session('password_reset_email')) {
            return redirect()->route('password.request')
                ->with('error', __('Session expired. Please try again.'));
        }

        $user = User::where('email', session('password_reset_email'))->first();

        if (!$user) {
            return back()->with('error', __('User not found.'));
        }

        // Verify OTP
        $result = $user->verifyOtp($request->otp);

        if ($result['success']) {
            // Update password
            $user->password = Hash::make($request->password);
            $user->save();

            // Log the password reset in verification logs
            DB::table('email_verification_logs')
                ->where('user_id', $user->id)
                ->where('otp', $request->otp)
                ->update(['verified_at' => now()]);

         
            // Clear session
            session()->forget('password_reset_email');

            return redirect()->route('login')
                ->with('success', __('Your password has been reset successfully. Please login with your new password.'));
        }

        return back()->with('error', $result['message']);
    }

    /**
     * Resend OTP for password reset
     */
    public function resendOtp(Request $request)
    {
        if (!session('password_reset_email')) {
            return redirect()->route('password.request')
                ->with('error', __('Session expired. Please try again.'));
        }

        $user = User::where('email', session('password_reset_email'))->first();

        if (!$user) {
            return back()->with('error', __('User not found.'));
        }

        // Check rate limiting
        if ($user->otp_expires_at && $user->otp_expires_at->diffInSeconds(now()) > 540) {
            return back()->with('error', __('Please wait before requesting a new OTP.'));
        }

        // Send new OTP
        $user->sendOtpEmail('password_reset');

        return back()->with('success', __('A new OTP has been sent to your email.'));
    }
}
