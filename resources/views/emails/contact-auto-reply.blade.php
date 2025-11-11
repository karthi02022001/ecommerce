{{-- resources/views/emails/contact-auto-reply.blade.php --}}
@extends('emails.layout')

@section('content')
<h2 class="email-title">{{ __('Thank You for Contacting Us', [], $locale) }}</h2>

<p class="email-text">
    {{ __('Hi :name,', ['name' => $submission->name], $locale) }}
</p>

<p class="email-text">
    {{ __('Thank you for reaching out to us! We have received your message and will respond as soon as possible.', [], $locale) }}
</p>

<div class="info-box">
    <p style="margin: 0; font-weight: 600; color: #1a1a1a;">
        ✅ {{ __('Message Received', [], $locale) }}
    </p>
    <p style="margin: 10px 0 0 0; font-size: 14px; color: #555;">
        {{ __('Our support team typically responds within 24-48 hours during business days.', [], $locale) }}
    </p>
</div>

<div class="order-details">
    <h3 style="margin: 0 0 15px 0; font-size: 18px; color: #1a1a1a;">{{ __('Your Message Details', [], $locale) }}</h3>
    
    <table style="width: 100%;">
        <tr>
            <td style="padding: 8px 0; color: #555; width: 150px;">{{ __('Name:', [], $locale) }}</td>
            <td style="padding: 8px 0; font-weight: 600;">{{ $submission->name }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; color: #555;">{{ __('Email:', [], $locale) }}</td>
            <td style="padding: 8px 0; font-weight: 600;">{{ $submission->email }}</td>
        </tr>
        @if($submission->phone)
        <tr>
            <td style="padding: 8px 0; color: #555;">{{ __('Phone:', [], $locale) }}</td>
            <td style="padding: 8px 0; font-weight: 600;">{{ $submission->phone }}</td>
        </tr>
        @endif
        <tr>
            <td style="padding: 8px 0; color: #555;">{{ __('Subject:', [], $locale) }}</td>
            <td style="padding: 8px 0; font-weight: 600;">{{ $submission->subject }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; color: #555;">{{ __('Submitted:', [], $locale) }}</td>
            <td style="padding: 8px 0; font-weight: 600;">{{ $submission->created_at->format('F d, Y g:i A') }}</td>
        </tr>
    </table>
</div>

<div class="order-details">
    <h3 style="margin: 0 0 15px 0; font-size: 18px; color: #1a1a1a;">{{ __('Your Message', [], $locale) }}</h3>
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; border-left: 4px solid #20b2aa;">
        <p style="margin: 0; line-height: 1.8; color: #333; white-space: pre-wrap;">{{ $submission->message }}</p>
    </div>
</div>

<div class="order-details">
    <h3 style="margin: 0 0 15px 0; font-size: 18px; color: #1a1a1a;">{{ __('What Happens Next?', [], $locale) }}</h3>
    
    <div style="padding: 15px 0; border-bottom: 1px solid #f0f0f0;">
        <div style="display: flex; align-items: start;">
            <div style="font-size: 24px; margin-right: 15px;">1️⃣</div>
            <div>
                <h4 style="margin: 0 0 5px 0; font-size: 16px; color: #1a1a1a;">{{ __('Review', [], $locale) }}</h4>
                <p style="margin: 0; font-size: 14px; color: #555;">
                    {{ __('Our support team will review your message carefully.', [], $locale) }}
                </p>
            </div>
        </div>
    </div>
    
    <div style="padding: 15px 0; border-bottom: 1px solid #f0f0f0;">
        <div style="display: flex; align-items: start;">
            <div style="font-size: 24px; margin-right: 15px;">2️⃣</div>
            <div>
                <h4 style="margin: 0 0 5px 0; font-size: 16px; color: #1a1a1a;">{{ __('Research', [], $locale) }}</h4>
                <p style="margin: 0; font-size: 14px; color: #555;">
                    {{ __('We\'ll gather all necessary information to provide you with the best answer.', [], $locale) }}
                </p>
            </div>
        </div>
    </div>
    
    <div style="padding: 15px 0;">
        <div style="display: flex; align-items: start;">
            <div style="font-size: 24px; margin-right: 15px;">3️⃣</div>
            <div>
                <h4 style="margin: 0 0 5px 0; font-size: 16px; color: #1a1a1a;">{{ __('Response', [], $locale) }}</h4>
                <p style="margin: 0; font-size: 14px; color: #555;">
                    {{ __('You\'ll receive a detailed response from our team via email.', [], $locale) }}
                </p>
            </div>
        </div>
    </div>
</div>

<div style="background-color: #f8f9fa; padding: 25px; border-radius: 5px; margin: 30px 0;">
    <h3 style="margin: 0 0 15px 0; font-size: 18px; color: #1a1a1a; text-align: center;">
        {{ __('Need Immediate Help?', [], $locale) }}
    </h3>
    <p style="margin: 0 0 20px 0; text-align: center; color: #555;">
        {{ __('Check out these helpful resources while you wait:', [], $locale) }}
    </p>
    <div style="text-align: center;">
        <a href="{{ route('home') }}#faq" style="display: inline-block; margin: 5px 10px; padding: 10px 20px; background-color: #f8f9fa; color: #1a1a1a; text-decoration: none; border-radius: 5px; border: 1px solid #dee2e6;">
            📚 {{ __('FAQ', [], $locale) }}
        </a>
        <a href="{{ route('products.index') }}" style="display: inline-block; margin: 5px 10px; padding: 10px 20px; background-color: #f8f9fa; color: #1a1a1a; text-decoration: none; border-radius: 5px; border: 1px solid #dee2e6;">
            🛍️ {{ __('Shop', [], $locale) }}
        </a>
        <a href="{{ route('home') }}#about" style="display: inline-block; margin: 5px 10px; padding: 10px 20px; background-color: #f8f9fa; color: #1a1a1a; text-decoration: none; border-radius: 5px; border: 1px solid #dee2e6;">
            ℹ️ {{ __('About Us', [], $locale) }}
        </a>
    </div>
</div>

<p class="email-text" style="margin-top: 30px; text-align: center; font-size: 14px; color: #777;">
    {{ __('This is an automated confirmation. Please do not reply to this email.', [], $locale) }}
</p>
@endsection 