@extends('emails.layout')

@section('content')
<h2 class="email-title">{{ __('Invoice - Order #:order_number', ['order_number' => $order->order_number], $locale) }}</h2>

<p class="email-text">
    {{ __('Hi :name,', ['name' => $order->customer_name], $locale) }}
</p>

<p class="email-text">
    {{ __('Thank you for your purchase. Please find your invoice details below.', [], $locale) }}
</p>

<!-- Invoice Header -->
<div style="background-color: #1a1a1a; color: #ffffff; padding: 30px; border-radius: 5px; margin: 30px 0;">
    <table style="width: 100%;">
        <tr>
            <td style="vertical-align: top;">
                <h1 style="margin: 0; font-size: 32px; color: #20b2aa;">{{ __('INVOICE', [], $locale) }}</h1>
                <p style="margin: 10px 0 0 0; font-size: 14px; color: #ffffff;">
                    {{ __('Invoice Number:', [], $locale) }} #{{ $order->order_number }}<br>
                    {{ __('Date:', [], $locale) }} {{ $order->created_at->format('F d, Y') }}
                </p>
            </td>
            <td style="text-align: right; vertical-align: top;">
                <h2 style="margin: 0 0 10px 0; font-size: 24px; color: #20b2aa;">{{ config('app.name') }}</h2>
                <p style="margin: 0; font-size: 14px; color: #ffffff; line-height: 1.8;">
                    123 Business Street<br>
                    City, State 12345<br>
                    {{ __('Phone:', [], $locale) }} (123) 456-7890<br>
                    {{ __('Email:', [], $locale) }} info@example.com
                </p>
            </td>
        </tr>
    </table>
</div>

<!-- Bill To / Ship To -->
<div style="margin: 30px 0;">
    <table style="width: 100%;">
        <tr>
            <td style="width: 48%; vertical-align: top; padding-right: 2%;">
                <div class="order-details">
                    <h3 style="margin: 0 0 15px 0; font-size: 16px; color: #1a1a1a; text-transform: uppercase;">
                        {{ __('Bill To', [], $locale) }}
                    </h3>
                    <p style="margin: 0; line-height: 1.8; color: #555;">
                        <strong>{{ $order->billing_name }}</strong><br>
                        {{ $order->billing_address }}<br>
                        {{ $order->billing_city }}, {{ $order->billing_state }} {{ $order->billing_postal_code }}<br>
                        {{ $order->billing_country }}<br>
                        @if($order->billing_phone)
                            {{ __('Phone:', [], $locale) }} {{ $order->billing_phone }}<br>
                        @endif
                        {{ __('Email:', [], $locale) }} {{ $order->customer_email }}
                    </p>
                </div>
            </td>
            <td style="width: 48%; vertical-align: top; padding-left: 2%;">
                <div class="order-details">
                    <h3 style="margin: 0 0 15px 0; font-size: 16px; color: #1a1a1a; text-transform: uppercase;">
                        {{ __('Ship To', [], $locale) }}
                    </h3>
                    <p style="margin: 0; line-height: 1.8; color: #555;">
                        <strong>{{ $order->shipping_name }}</strong><br>
                        {{ $order->shipping_address }}<br>
                        {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}<br>
                        {{ $order->shipping_country }}<br>
                        @if($order->shipping_phone)
                            {{ __('Phone:', [], $locale) }} {{ $order->shipping_phone }}
                        @endif
                    </p>
                </div>
            </td>
        </tr>
    </table>
</div>

<!-- Invoice Items -->
<table class="product-table" style="margin: 30px 0;">
    <thead>
        <tr>
            <th style="text-align: left; width: 50%;">{{ __('Description', [], $locale) }}</th>
            <th style="text-align: center; width: 15%;">{{ __('Qty', [], $locale) }}</th>
            <th style="text-align: right; width: 17.5%;">{{ __('Unit Price', [], $locale) }}</th>
            <th style="text-align: right; width: 17.5%;">{{ __('Total', [], $locale) }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->items as $item)
        <tr>
            <td>
                <strong>{{ $item->product_name }}</strong>
                @if($item->product_sku)
                    <br><small style="color: #777;">{{ __('SKU:', [], $locale) }} {{ $item->product_sku }}</small>
                @endif
            </td>
            <td style="text-align: center;">{{ $item->quantity }}</td>
            <td style="text-align: right;">{{ $order->currency_symbol }}{{ number_format($item->unit_price, 2) }}</td>
            <td style="text-align: right; font-weight: 600;">
                {{ $order->currency_symbol }}{{ number_format($item->unit_price * $item->quantity, 2) }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Invoice Totals -->
<div style="max-width: 350px; margin-left: auto;">
    <table style="width: 100%; border: 1px solid #e0e0e0; border-radius: 5px; overflow: hidden;">
        <tr>
            <td style="padding: 12px; border-bottom: 1px solid #f0f0f0; color: #555;">{{ __('Subtotal', [], $locale) }}</td>
            <td style="padding: 12px; border-bottom: 1px solid #f0f0f0; text-align: right; font-weight: 600;">
                {{ $order->currency_symbol }}{{ number_format($order->subtotal, 2) }}
            </td>
        </tr>
        <tr>
            <td style="padding: 12px; border-bottom: 1px solid #f0f0f0; color: #555;">{{ __('Tax', [], $locale) }}</td>
            <td style="padding: 12px; border-bottom: 1px solid #f0f0f0; text-align: right; font-weight: 600;">
                {{ $order->currency_symbol }}{{ number_format($order->tax, 2) }}
            </td>
        </tr>
        <tr>
            <td style="padding: 12px; border-bottom: 1px solid #f0f0f0; color: #555;">{{ __('Shipping', [], $locale) }}</td>
            <td style="padding: 12px; border-bottom: 1px solid #f0f0f0; text-align: right; font-weight: 600;">
                {{ $order->currency_symbol }}{{ number_format($order->shipping_cost, 2) }}
            </td>
        </tr>
        @if($order->discount > 0)
        <tr>
            <td style="padding: 12px; border-bottom: 1px solid #f0f0f0; color: #555;">{{ __('Discount', [], $locale) }}</td>
            <td style="padding: 12px; border-bottom: 1px solid #f0f0f0; text-align: right; font-weight: 600; color: #20b2aa;">
                -{{ $order->currency_symbol }}{{ number_format($order->discount, 2) }}
            </td>
        </tr>
        @endif
        <tr>
            <td style="padding: 15px; background-color: #1a1a1a; color: #ffffff; font-size: 18px; font-weight: 700;">
                {{ __('Total', [], $locale) }}
            </td>
            <td style="padding: 15px; background-color: #1a1a1a; color: #20b2aa; text-align: right; font-size: 18px; font-weight: 700;">
                {{ $order->currency_symbol }}{{ number_format($order->total, 2) }}
            </td>
        </tr>
    </table>
</div>

<!-- Payment Information -->
<div class="info-box" style="margin-top: 30px;">
    <p style="margin: 0 0 8px 0; font-weight: 600; color: #1a1a1a;">{{ __('Payment Information', [], $locale) }}</p>
    <p style="margin: 0; font-size: 14px; color: #555;">
        <strong>{{ __('Method:', [], $locale) }}</strong> {{ ucfirst($order->payment_method) }}<br>
        <strong>{{ __('Status:', [], $locale) }}</strong> 
        <span style="color: {{ $order->payment_status === 'paid' ? '#28a745' : '#ffc107' }}; font-weight: 600;">
            {{ ucfirst($order->payment_status) }}
        </span><br>
        @if($order->payment_status === 'paid')
            <strong>{{ __('Paid on:', [], $locale) }}</strong> {{ $order->updated_at->format('F d, Y') }}
        @endif
    </p>
</div>

<div style="text-align: center; margin-top: 40px;">
    <a href="{{ route('orders.show', $order->id) }}" class="email-button">
        {{ __('View Order Details', [], $locale) }}
    </a>
</div>

<p class="email-text" style="margin-top: 30px; text-align: center; font-size: 12px; color: #777;">
    {{ __('This is an automatically generated invoice. For any questions, please contact our support team.', [], $locale) }}
</p>
@endsection