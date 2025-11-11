<!DOCTYPE html>
<html lang="{{ $locale ?? 'en' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }}</title>
    <style>
        /* Reset styles */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: #333333;
            background-color: #f4f4f4;
        }
        
        table {
            border-collapse: collapse;
            width: 100%;
        }
        
        img {
            max-width: 100%;
            height: auto;
            border: 0;
            display: block;
        }
        
        a {
            color: #20b2aa;
            text-decoration: none;
        }
        
        a:hover {
            color: #1a8f88;
            text-decoration: underline;
        }
        
        /* Container */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        
        /* Header */
        .email-header {
            background-color: #1a1a1a;
            padding: 30px 20px;
            text-align: center;
        }
        
        .email-logo {
            font-size: 28px;
            font-weight: 700;
            color: #20b2aa;
            margin: 0;
        }
        
        /* Content */
        .email-content {
            padding: 40px 30px;
        }
        
        .email-title {
            font-size: 24px;
            font-weight: 600;
            color: #1a1a1a;
            margin: 0 0 20px 0;
        }
        
        .email-text {
            font-size: 16px;
            color: #555555;
            margin: 0 0 15px 0;
        }
        
        /* Button */
        .email-button {
            display: inline-block;
            padding: 14px 30px;
            background-color: #20b2aa;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            margin: 20px 0;
            transition: background-color 0.3s;
        }
        
        .email-button:hover {
            background-color: #1a8f88;
            text-decoration: none;
        }
        
        /* Info Box */
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #20b2aa;
            padding: 15px 20px;
            margin: 20px 0;
        }
        
        /* Order Details */
        .order-details {
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .order-details-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .order-details-row:last-child {
            border-bottom: none;
        }
        
        .order-details-label {
            font-weight: 600;
            color: #333333;
        }
        
        .order-details-value {
            color: #555555;
        }
        
        /* Product Table */
        .product-table {
            width: 100%;
            margin: 20px 0;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .product-table th {
            background-color: #1a1a1a;
            color: #ffffff;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        
        .product-table td {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .product-table tr:last-child td {
            border-bottom: none;
        }
        
        /* Footer */
        .email-footer {
            background-color: #f8f9fa;
            padding: 30px 20px;
            text-align: center;
            font-size: 14px;
            color: #777777;
        }
        
        .email-footer a {
            color: #20b2aa;
            text-decoration: none;
        }
        
        .social-links {
            margin: 15px 0;
        }
        
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #555555;
        }
        
        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-content {
                padding: 30px 20px;
            }
            
            .email-title {
                font-size: 20px;
            }
            
            .email-text {
                font-size: 14px;
            }
            
            .product-table th,
            .product-table td {
                padding: 8px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f4f4f4; padding: 20px 0;">
        <tr>
            <td align="center">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" class="email-container">
                    <!-- Header -->
                    <tr>
                        <td class="email-header">
                            <h1 class="email-logo">{{ config('app.name') }}</h1>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td class="email-content">
                            @yield('content')
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td class="email-footer">
                            <p style="margin: 0 0 10px 0;">
                                &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.', [], $locale ?? 'en') }}
                            </p>
                            <p style="margin: 0 0 10px 0;">
                                {{ __('You are receiving this email because you are a customer of our store.', [], $locale ?? 'en') }}
                            </p>
                            <div class="social-links">
                                <a href="#">Facebook</a> |
                                <a href="#">Twitter</a> |
                                <a href="#">Instagram</a>
                            </div>
                            <p style="margin: 10px 0 0 0; font-size: 12px;">
                                <a href="{{ route('home') }}">{{ __('Visit our store', [], $locale ?? 'en') }}</a> |
                                <a href="#">{{ __('Contact us', [], $locale ?? 'en') }}</a> |
                                <a href="#">{{ __('Privacy Policy', [], $locale ?? 'en') }}</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>