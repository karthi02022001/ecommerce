@extends('emails.layout')

@section('content')
<h2 class="email-title">📧 New Contact Form Submission</h2>

<p class="email-text">
    You have received a new message from your website's contact form.
</p>

<div class="info-box">
    <p style="margin: 0; font-weight: 600; color: #1a1a1a; font-size: 18px;">
        {{ $contactData['subject'] }}
    </p>
    <p style="margin: 10px 0 0 0; font-size: 14px; color: #555;">
        Received on {{ now()->format('F d, Y \a\t g:i A') }}
    </p>
</div>

<!-- Contact Information -->
<div class="order-details">
    <h3 style="margin: 0 0 15px 0; font-size: 18px; color: #1a1a1a;">Contact Information</h3>
    
    <table style="width: 100%;">
        <tr>
            <td style="width: 150px; padding: 12px 0; color: #555;">Name:</td>
            <td style="padding: 12px 0; font-weight: 600;">{{ $contactData['name'] }}</td>
        </tr>
        <tr>
            <td style="padding: 12px 0; color: #555;">Email:</td>
            <td style="padding: 12px 0; font-weight: 600;">
                <a href="mailto:{{ $contactData['email'] }}" style="color: #20b2aa; text-decoration: none;">
                    {{ $contactData['email'] }}
                </a>
            </td>
        </tr>
        @if(isset($contactData['phone']) && $contactData['phone'])
        <tr>
            <td style="padding: 12px 0; color: #555;">Phone:</td>
            <td style="padding: 12px 0; font-weight: 600;">
                <a href="tel:{{ $contactData['phone'] }}" style="color: #20b2aa; text-decoration: none;">
                    {{ $contactData['phone'] }}
                </a>
            </td>
        </tr>
        @endif
        <tr>
            <td style="padding: 12px 0; color: #555;">Subject:</td>
            <td style="padding: 12px 0; font-weight: 600;">{{ $contactData['subject'] }}</td>
        </tr>
    </table>
</div>

<!-- Message Content -->
<div class="order-details">
    <h3 style="margin: 0 0 15px 0; font-size: 18px; color: #1a1a1a;">Message</h3>
    
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; border-left: 4px solid #20b2aa;">
        <p style="margin: 0; line-height: 1.8; color: #333; white-space: pre-wrap;">{{ $contactData['message'] }}</p>
    </div>
</div>

<!-- Quick Actions -->
<div style="background-color: #f8f9fa; padding: 25px; border-radius: 5px; text-align: center; margin-top: 30px;">
    <h3 style="margin: 0 0 20px 0; font-size: 18px; color: #1a1a1a;">Quick Actions</h3>
    
    <div style="margin: 15px 0;">
        <a href="mailto:{{ $contactData['email'] }}?subject=Re: {{ $contactData['subject'] }}" class="email-button" style="margin: 0 10px 10px 10px;">
            Reply via Email
        </a>
    </div>
    
    @if(isset($contactData['phone']) && $contactData['phone'])
    <div style="margin: 15px 0;">
        <a href="tel:{{ $contactData['phone'] }}" style="display: inline-block; padding: 14px 30px; background-color: #1a1a1a; color: #ffffff !important; text-decoration: none; border-radius: 5px; font-weight: 600; margin: 0 10px 10px 10px;">
            Call {{ $contactData['name'] }}
        </a>
    </div>
    @endif
</div>

<!-- Additional Information -->
@if(isset($contactData['order_number']) || isset($contactData['customer_id']))
<div class="info-box">
    <p style="margin: 0 0 8px 0; font-weight: 600; color: #1a1a1a;">Additional Information</p>
    <p style="margin: 0; font-size: 14px; color: #555;">
        @if(isset($contactData['order_number']))
            <strong>Related Order:</strong> #{{ $contactData['order_number'] }}<br>
        @endif
        @if(isset($contactData['customer_id']))
            <strong>Customer ID:</strong> {{ $contactData['customer_id'] }}<br>
        @endif
    </p>
</div>
@endif

<!-- Response Guidelines -->
<div class="order-details">
    <h3 style="margin: 0 0 15px 0; font-size: 18px; color: #1a1a1a;">Response Guidelines</h3>
    
    <div style="padding: 15px 0; border-bottom: 1px solid #f0f0f0;">
        <div style="display: flex; align-items: start;">
            <div style="font-size: 24px; margin-right: 15px;">⚡</div>
            <div>
                <h4 style="margin: 0 0 5px 0; font-size: 16px; color: #1a1a1a;">Respond Promptly</h4>
                <p style="margin: 0; font-size: 14px; color: #555;">
                    Try to respond within 24 hours to maintain excellent customer service.
                </p>
            </div>
        </div>
    </div>
    
    <div style="padding: 15px 0; border-bottom: 1px solid #f0f0f0;">
        <div style="display: flex; align-items: start;">
            <div style="font-size: 24px; margin-right: 15px;">💬</div>
            <div>
                <h4 style="margin: 0 0 5px 0; font-size: 16px; color: #1a1a1a;">Be Professional</h4>
                <p style="margin: 0; font-size: 14px; color: #555;">
                    Maintain a professional and friendly tone in all communications.
                </p>
            </div>
        </div>
    </div>
    
    <div style="padding: 15px 0;">
        <div style="display: flex; align-items: start;">
            <div style="font-size: 24px; margin-right: 15px;">✅</div>
            <div>
                <h4 style="margin: 0 0 5px 0; font-size: 16px; color: #1a1a1a;">Follow Up</h4>
                <p style="margin: 0; font-size: 14px; color: #555;">
                    If the issue requires further action, ensure proper follow-up until resolution.
                </p>
            </div>
        </div>
    </div>
</div>

<p class="email-text" style="margin-top: 30px; text-align: center; font-size: 14px; color: #777;">
    <strong>Note:</strong> This email was sent from your website's contact form. Please respond directly to the customer's email address.
</p>
@endsection