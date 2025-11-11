@extends('emails.layout')

@section('content')
<h2 class="email-title">{{ __('Welcome to :store_name!', ['store_name' => config('app.name')], $locale) }}</h2>

<p class="email-text">
    {{ __('Hi :name,', ['name' => $user->name], $locale) }}
</p>

<p class="email-text">
    {{ __('Thank you for joining :store_name! We\'re excited to have you as part of our community.', ['store_name' => config('app.name')], $locale) }}
</p>

<div class="info-box">
    <p style="margin: 0; font-size: 18px; font-weight: 600; color: #1a1a1a;">
        🎉 {{ __('Your account is ready!', [], $locale) }}
    </p>
    <p style="margin: 10px 0 0 0; color: #555;">
        {{ __('You can now start shopping and enjoy exclusive benefits.', [], $locale) }}
    </p>
</div>

<div class="order-details">
    <h3 style="margin: 0 0 15px 0; font-size: 18px; color: #1a1a1a;">{{ __('What You Can Do Now', [], $locale) }}</h3>
    
    <div style="padding: 15px 0; border-bottom: 1px solid #f0f0f0;">
        <h4 style="margin: 0 0 8px 0; font-size: 16px; color: #1a1a1a;">🛍️ {{ __('Browse Products', [], $locale) }}</h4>
        <p style="margin: 0; font-size: 14px; color: #555;">
            {{ __('Explore our wide range of quality products tailored for you.', [], $locale) }}
        </p>
    </div>
    
    <div style="padding: 15px 0; border-bottom: 1px solid #f0f0f0;">
        <h4 style="margin: 0 0 8px 0; font-size: 16px; color: #1a1a1a;">💚 {{ __('Create Wishlists', [], $locale) }}</h4>
        <p style="margin: 0; font-size: 14px; color: #555;">
            {{ __('Save your favorite items and come back to them anytime.', [], $locale) }}
        </p>
    </div>
    
    <div style="padding: 15px 0; border-bottom: 1px solid #f0f0f0;">
        <h4 style="margin: 0 0 8px 0; font-size: 16px; color: #1a1a1a;">📦 {{ __('Track Orders', [], $locale) }}</h4>
        <p style="margin: 0; font-size: 14px; color: #555;">
            {{ __('Keep track of all your orders and delivery status in one place.', [], $locale) }}
        </p>
    </div>
    
    <div style="padding: 15px 0;">
        <h4 style="margin: 0 0 8px 0; font-size: 16px; color: #1a1a1a;">👤 {{ __('Manage Profile', [], $locale) }}</h4>
        <p style="margin: 0; font-size: 14px; color: #555;">
            {{ __('Update your personal information and manage multiple addresses.', [], $locale) }}
        </p>
    </div>
</div>

<div class="info-box">
    <p style="margin: 0; font-weight: 600; color: #1a1a1a;">
        💡 {{ __('Pro Tip:', [], $locale) }}
    </p>
    <p style="margin: 8px 0 0 0; font-size: 14px; color: #555;">
        {{ __('Complete your profile and add your shipping address now to make checkout faster!', [], $locale) }}
    </p>
</div>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('products.index') }}" class="email-button">
        {{ __('Start Shopping', [], $locale) }}
    </a>
</div>

<div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; text-align: center; margin-top: 30px;">
    <p style="margin: 0 0 10px 0; font-size: 16px; font-weight: 600; color: #1a1a1a;">
        {{ __('Need Help?', [], $locale) }}
    </p>
    <p style="margin: 0; font-size: 14px; color: #555;">
        {{ __('Our customer support team is here to help you 24/7.', [], $locale) }}<br>
        <a href="#" style="color: #20b2aa; text-decoration: none;">{{ __('Contact Support', [], $locale) }}</a>
    </p>
</div>

<p class="email-text" style="margin-top: 30px; text-align: center; font-size: 14px; color: #777;">
    {{ __('Thank you for choosing :store_name!', ['store_name' => config('app.name')], $locale) }}
</p>
@endsection