<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'google_id',
        'avatar',
        'provider',
        'preferred_currency',
        'otp',
        'otp_expires_at',
        'otp_attempts',
        'email_verified_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_expires_at' => 'datetime',
        'password' => 'hashed',
    ];

    // ==================== EXISTING RELATIONSHIPS ====================

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function addresses()
    {
        return $this->hasMany(Address::class, 'customer_id');
    }

    public function cartItems()
    {
        return $this->hasMany(Cart::class, 'user_id');
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function wishlistProducts()
    {
        return $this->belongsToMany(Product::class, 'wishlists')
            ->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class, 'user_id');
    }

    // ==================== EXISTING ACCESSORS ====================

    public function getAvatarUrlAttribute(): string
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=20b2aa&color=ffffff';
    }

    public function getTotalOrdersAttribute(): int
    {
        return $this->orders()->count();
    }

    public function getCompletedOrdersAttribute(): int
    {
        return $this->orders()->where('status', 'delivered')->count();
    }

    public function getPendingOrdersAttribute(): int
    {
        return $this->orders()->where('status', 'pending')->count();
    }

    public function getWishlistCountAttribute(): int
    {
        return $this->wishlists()->count();
    }

    // ==================== EXISTING HELPER METHODS ====================

    public function hasOrders(): bool
    {
        return $this->orders()->exists();
    }

    public function totalSpent(): float
    {
        return $this->orders()
            ->whereIn('status', ['delivered', 'processing', 'shipped'])
            ->sum('total_amount');
    }

    public function hasInWishlist($productId): bool
    {
        return $this->wishlists()->where('product_id', $productId)->exists();
    }

    // ==================== NEW OTP METHODS ====================

    /**
     * Generate OTP for email verification
     */
    public function generateOtp()
    {
        $this->otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->otp_expires_at = Carbon::now()->addMinutes(10); // 10 minutes expiry
        $this->otp_attempts = 0;
        $this->save();

        return $this->otp;
    }

    /**
     * Verify OTP
     */
    public function verifyOtp($otp)
    {
        // Check if OTP expired
        if ($this->otp_expires_at && Carbon::now()->greaterThan($this->otp_expires_at)) {
            return ['success' => false, 'message' => __('OTP has expired')];
        }

        // Check attempts
        if ($this->otp_attempts >= 5) {
            return ['success' => false, 'message' => __('Too many attempts. Please request a new OTP')];
        }

        // Verify OTP
        if ($this->otp === $otp) {
            $this->email_verified_at = Carbon::now();
            $this->otp = null;
            $this->otp_expires_at = null;
            $this->otp_attempts = 0;
            $this->save();

            return ['success' => true, 'message' => __('Email verified successfully')];
        }

        // Increment attempts
        $this->increment('otp_attempts');

        return ['success' => false, 'message' => __('Invalid OTP')];
    }

    /**
     * Send OTP Email
     */
    public function sendOtpEmail($action = 'register')
    {
        $otp = $this->generateOtp();

        // Log the OTP attempt
        \DB::table('email_verification_logs')->insert([
            'user_id' => $this->id,
            'email' => $this->email,
            'otp' => $otp,
            'action' => $action,
            'ip_address' => request()->ip(),
            'created_at' => Carbon::now()
        ]);

        // Send email
        Mail::send('emails.otp', [
            'user' => $this,
            'otp' => $otp,
            'action' => $action
        ], function ($message) {
            $message->to($this->email, $this->name)
                ->subject(__('Your OTP Code - Email Verification'));
        });
    }

    /**
     * Check if email is verified
     */
    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at);
    }
}
