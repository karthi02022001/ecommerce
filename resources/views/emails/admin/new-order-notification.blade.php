@extends('emails.layout')

@section('content')
<h2 class="email-title">🛍️ New Order Received</h2>

<p class="email-text">
    A new order has been placed on your store.
</p>

<div class="info-box">
    <p style="margin: 0; font-weight: 600; color: #1a1a1a; font-size: 18px;">
        Order #{{ $order->order_number }}
    </p>
    <p style="margin: 10px 0 0 0; font-size: 14px; color: #555;">
        Placed on {{ $order->created_at->format('F d, Y \a\t g:i A') }}
    </p>
</div>

<!-- Customer Information -->
<div class="order-details">
    <h3 style="margin: 0 0 15px 0; font-size: 18px; color: #1a1a1a;">Customer Information</h3>
    <table style="width: 100%;">
        <tr>
            <td style="padding: 8px 0; color: #555;">Customer Name:</td>
            <td style="padding: 8px 0; font-weight: 600;">{{ $order->customer_name }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; color: #555;">Email:</td>
            <td style="padding: 8px 0; font-weight: 600;">
                <a href="mailto:{{ $order->customer_email }}" style="color: #20b2aa;">{{ $order->customer_email }}</a>
            </td>
        </tr>
        @if($order->customer_phone)
        <tr>
            <td style="padding: 8px 0; color: #555;">Phone:</td>
            <td style="padding: 8px 0; font-weight: 600;">{{ $order->customer_phone }}</td>
        </tr>
        @endif
        <tr>
            <td style="padding: 8px 0; color: #555;">Order Total:</td>
            <td style="padding: 8px 0; font-weight: 700; font-size: 18px; color: #20b2aa;">
                {{ $order->currency_symbol }}{{ number_format($order->total, 2) }}
            </td>
        </tr>
    </table>
</div>

<!-- Order Items -->
<div class="order-details">
    <h3 style="margin: 0 0 15px 0; font-size: 18px; color: #1a1a1a;">Order Items</h3>
    <table class="product-table">
        <thead>
            <tr>
                <th>Product</th>
                <th style="text-align: center;">Quantity</th>
                <th style="text-align: right;">Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>
                    <strong>{{ $item->product_name }}</strong>
                    @if($item->product_sku)
                        <br><small style="color: #777;">SKU: {{ $item->product_sku }}</small>
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
</div>

<!-- Order Summary -->
<div style="max-width: 350px; margin-left: auto; margin-top: 20px;">
    <table style="width: 100%;">
        <tr>
            <td style="padding: 8px 0; color: #555;">Subtotal:</td>
            <td style="padding: 8px 0; text-align: right; font-weight: 600;">
                {{ $order->currency_symbol }}{{ number_format($order->subtotal, 2) }}
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; color: #555;">Tax:</td>
            <td style="padding: 8px 0; text-align: right; font-weight: 600;">
                {{ $order->currency_symbol }}{{ number_format($order->tax, 2) }}
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; color: #555;">Shipping:</td>
            <td style="padding: 8px 0; text-align: right; font-weight: 600;">
                {{ $order->currency_symbol }}{{ number_format($order->shipping_cost, 2) }}
            </td>
        </tr>
        @if($order->discount > 0)
        <tr>
            <td style="padding: 8px 0; color: #555;">Discount:</td>
            <td style="padding: 8px 0; text-align: right; font-weight: 600; color: #20b2aa;">
                -{{ $order->currency_symbol }}{{ number_format($order->discount, 2) }}
            </td>
        </tr>
        @endif
        <tr style="border-top: 2px solid #1a1a1a;">
            <td style="padding: 15px 0 0 0; font-size: 18px; font-weight: 700;">Total:</td>
            <td style="padding: 15px 0 0 0; text-align: right; font-size: 18px; font-weight: 700; color: #20b2aa;">
                {{ $order->currency_symbol }}{{ number_format($order->total, 2) }}
            </td>
        </tr>
    </table>
</div>

<!-- Shipping Address -->
<div class="order-details">
    <h3 style="margin: 0 0 15px 0; font-size: 18px; color: #1a1a1a;">Shipping Address</h3>
    <p style="margin: 0; line-height: 1.8; color: #555;">
        <strong>{{ $order->shipping_name }}</strong><br>
        {{ $order->address_line_1 }}<br>
        {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}<br>
        {{ $order->shipping_country }}<br>
        @if($order->shipping_phone)
            Phone: {{ $order->shipping_phone }}
        @endif
    </p>
</div>

<!-- Payment Information -->
<div class="info-box">
    <p style="margin: 0 0 8px 0; font-weight: 600; color: #1a1a1a;">Payment Information</p>
    <p style="margin: 0; font-size: 14px; color: #555;">
        <strong>Method:</strong> {{ ucfirst($order->payment_method) }}<br>
        <strong>Status:</strong> 
        <span style="color: {{ $order->payment_status === 'paid' ? '#28a745' : '#ffc107' }}; font-weight: 600;">
            {{ ucfirst($order->payment_status) }}
        </span>
    </p>
</div>

<div style="text-align: center; margin-top: 30px;">
    <a href="{{ route('admin.orders.show', $order->id) }}" class="email-button">
        View Order in Admin Panel
    </a>
</div>

<p class="email-text" style="margin-top: 30px; text-align: center; font-size: 14px; color: #777;">
    <strong>Action Required:</strong> Please process this order and update the customer on the order status.
</p>
@endsection