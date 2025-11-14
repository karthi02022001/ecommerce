@extends('emails.layout')

@section('content')
<h2 class="email-title">{{ __('Order Confirmation', [], $locale) }}</h2>

<p class="email-text">
    {{ __('Hi :name,', ['name' => $order->customer_name], $locale) }}
</p>

<p class="email-text">
    {{ __('Thank you for your order! We have received your order and it is now being processed.', [], $locale) }}
</p>

<div class="info-box">
    <p style="margin: 0; font-weight: 600; color: #1a1a1a;">
        {{ __('Order Number:', [], $locale) }} <span style="color: #20b2aa;">#{{ $order->order_number }}</span>
    </p>
    <p style="margin: 5px 0 0 0; font-size: 14px; color: #777;">
        {{ __('Order Date:', [], $locale) }} {{ $order->created_at->format('F d, Y') }}
    </p>
</div>

<!-- Order Details -->
<div class="order-details">
    <h3 style="margin: 0 0 15px 0; font-size: 18px; color: #1a1a1a;">{{ __('Order Details', [], $locale) }}</h3>
    
    <table class="product-table">
        <thead>
            <tr>
                <th>{{ __('Product', [], $locale) }}</th>
                <th style="text-align: center;">{{ __('Quantity', [], $locale) }}</th>
                <th style="text-align: right;">{{ __('Price', [], $locale) }}</th>
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
                <td style="text-align: right; font-weight: 600;">
                    {{ $order->currency_symbol }}{{ number_format($item->unit_price * $item->quantity, 2) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div style="margin-top: 20px; padding-top: 15px; border-top: 2px solid #e0e0e0;">
        <div style="display: flex; justify-content: space-between; padding: 5px 0;">
            <span style="color: #555;">{{ __('Subtotal:', [], $locale) }}</span>
            <span style="font-weight: 600;">{{ $order->currency_symbol }}{{ number_format($order->subtotal, 2) }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 5px 0;">
            <span style="color: #555;">{{ __('Tax:', [], $locale) }}</span>
            <span style="font-weight: 600;">{{ $order->currency_symbol }}{{ number_format($order->tax, 2) }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 5px 0;">
            <span style="color: #555;">{{ __('Shipping:', [], $locale) }}</span>
            <span style="font-weight: 600;">{{ $order->currency_symbol }}{{ number_format($order->shipping_cost, 2) }}</span>
        </div>
        @if($order->discount > 0)
        <div style="display: flex; justify-content: space-between; padding: 5px 0;">
            <span style="color: #555;">{{ __('Discount:', [], $locale) }}</span>
            <span style="font-weight: 600; color: #20b2aa;">-{{ $order->currency_symbol }}{{ number_format($order->discount, 2) }}</span>
        </div>
        @endif
        <div style="display: flex; justify-content: space-between; padding: 15px 0 0 0; margin-top: 10px; border-top: 2px solid #1a1a1a;">
            <span style="font-size: 18px; font-weight: 700; color: #1a1a1a;">{{ __('Total:', [], $locale) }}</span>
            <span style="font-size: 18px; font-weight: 700; color: #20b2aa;">{{ $order->currency_symbol }}{{ number_format($order->total, 2) }}</span>
        </div>
    </div>
</div>

<!-- Shipping Address -->
<div class="order-details">
    <h3 style="margin: 0 0 15px 0; font-size: 18px; color: #1a1a1a;">{{ __('Shipping Address', [], $locale) }}</h3>
    <p style="margin: 0; line-height: 1.8; color: #555;">
        <strong>{{ $order->shipping_name }}</strong><br>
        {{ $order->address_line_1 }}<br>
        {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}<br>
        {{ $order->shipping_country }}<br>
        @if($order->shipping_phone)
            {{ __('Phone:', [], $locale) }} {{ $order->shipping_phone }}
        @endif
    </p>
</div>

<!-- Payment Method -->
<div class="info-box">
    <p style="margin: 0;">
        <strong>{{ __('Payment Method:', [], $locale) }}</strong> {{ ucfirst($order->payment_method) }}<br>
        <strong>{{ __('Payment Status:', [], $locale) }}</strong> 
        <span style="color: {{ $order->payment_status === 'paid' ? '#28a745' : '#ffc107' }};">
            {{ ucfirst($order->payment_status) }}
        </span>
    </p>
</div>

<p class="email-text" style="margin-top: 30px;">
    {{ __('We will send you another email when your order has been shipped.', [], $locale) }}
</p>

<div style="text-align: center;">
    <a href="{{ route('orders.show', $order->id) }}" class="email-button">
        {{ __('View Order Details', [], $locale) }}
    </a>
</div>

<p class="email-text" style="margin-top: 30px; font-size: 14px; color: #777;">
    {{ __('If you have any questions, please don\'t hesitate to contact us.', [], $locale) }}
</p>
@endsection