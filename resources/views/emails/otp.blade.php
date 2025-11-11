<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Email Verification') }}</title>
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #20b2aa 0%, #1a1a1a 100%);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .email-body {
            padding: 40px 30px;
            color: #333333;
        }
        .email-body h2 {
            color: #1a1a1a;
            font-size: 22px;
            margin-bottom: 20px;
        }
        .email-body p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .otp-box {
            background-color: #f8f9fa;
            border: 2px dashed #20b2aa;
            border-radius: 8px;
            padding: 25px;
            text-align: center;
            margin: 30px 0;
        }
        .otp-code {
            font-size: 36px;
            font-weight: 700;
            color: #20b2aa;
            letter-spacing: 8px;
            margin: 10px 0;
        }
        .otp-label {
            font-size: 14px;
            color: #666666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .expiry-info {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
            color: #856404;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #666666;
        }
        .email-footer a {
            color: #20b2aa;
            text-decoration: none;
        }
        .warning {
            font-size: 13px;
            color: #dc3545;
            margin-top: 20px;
            padding: 10px;
            background-color: #f8d7da;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>{{ config('app.name') }}</h1>
        </div>
        
        <div class="email-body">
            <h2>{{ __('Hello') }}, {{ $user->name }}!</h2>
            
            @if($action === 'register')
                <p>{{ __('Thank you for registering with us! To complete your registration, please verify your email address using the OTP code below:') }}</p>
            @else
                <p>{{ __('We received a login attempt to your account. Please verify your identity using the OTP code below:') }}</p>
            @endif
            
            <div class="otp-box">
                <div class="otp-label">{{ __('Your OTP Code') }}</div>
                <div class="otp-code">{{ $otp }}</div>
            </div>
            
            <div class="expiry-info">
                <strong>⏰ {{ __('Important:') }}</strong> {{ __('This OTP will expire in 10 minutes.') }}
            </div>
            
            <p>{{ __('Please enter this code on the verification page to continue.') }}</p>
            
            <div class="warning">
                <strong>⚠️ {{ __('Security Notice:') }}</strong><br>
                {{ __('If you did not request this OTP, please ignore this email or contact our support team immediately.') }}
            </div>
        </div>
        
        <div class="email-footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}</p>
            <p>{{ __('Need help?') }} <a href="mailto:{{ config('mail.from.address') }}">{{ __('Contact Support') }}</a></p>
        </div>
    </div>
</body>
</html>