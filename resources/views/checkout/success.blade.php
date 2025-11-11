@extends('layouts.app')

@section('title', __('Order Success'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Success Header -->
            <div class="success-header-card">
                <div class="success-icon-wrapper">
                    <div class="success-icon-bg">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
                
                <h1 class="success-title">{{ __('Order Placed Successfully!') }}</h1>
                
                <p class="success-subtitle">
                    {{ __('Thank you for your order. We\'ve received your payment and will process your order soon.') }}
                </p>
                
                @if(isset($order))
                    <div class="order-number-badge">
                        <span class="order-number-label">{{ __('Order Number') }}</span>
                        <span class="order-number-value">#{{ $order->order_number }}</span>
                    </div>
                @endif
            </div>

            @if(isset($order))
                <!-- Order Summary Cards -->
                <div class="row mt-4">
                    <!-- Order Details -->
                    <div class="col-md-4 mb-4">
                        <div class="info-card">
                            <div class="info-card-icon">
                                <i class="bi bi-receipt"></i>
                            </div>
                            <h5 class="info-card-title">{{ __('Total Amount') }}</h5>
                            <p class="info-card-value">${{ number_format($order->total_amount, 2) }}</p>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="col-md-4 mb-4">
                        <div class="info-card">
                            <div class="info-card-icon">
                                <i class="bi bi-credit-card"></i>
                            </div>
                            <h5 class="info-card-title">{{ __('Payment Method') }}</h5>
                            <p class="info-card-value">
                                @if($order->payment_method === 'stripe')
                                    {{ __('Credit/Debit Card') }}
                                @else
                                    {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Order Status -->
                    <div class="col-md-4 mb-4">
                        <div class="info-card">
                            <div class="info-card-icon">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <h5 class="info-card-title">{{ __('Status') }}</h5>
                            <p class="info-card-value">
                                <span class="status-badge status-{{ $order->status }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons-section">
                    <a href="{{ route('orders.show', $order->id) }}" class="btn-action btn-primary-action">
                        <i class="bi bi-receipt me-2"></i>{{ __('View Order Details') }}
                    </a>
                    <a href="{{ route('home') }}" class="btn-action btn-secondary-action">
                        <i class="bi bi-shop me-2"></i>{{ __('Continue Shopping') }}
                    </a>
                </div>

                <!-- Email Confirmation Notice -->
                <div class="notification-card">
                    <i class="bi bi-envelope-check notification-icon"></i>
                    <div class="notification-content">
                        <h6 class="notification-title">{{ __('Confirmation Email Sent') }}</h6>
                        <p class="notification-text">
                            {{ __('We\'ve sent a confirmation email to') }} <strong>{{ auth()->user()->email }}</strong> {{ __('with your order details and tracking information.') }}
                        </p>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="details-card mt-4">
                    <div class="details-card-header">
                        <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>{{ __('Items Ordered') }}</h5>
                        <span class="items-count">{{ $order->orderItems->count() }} {{ __('items') }}</span>
                    </div>
                    <div class="details-card-body">
                        <div class="order-items-list">
                            @foreach($order->orderItems as $item)
                                <div class="order-item-row">
                                    <div class="item-info">
                                        <h6 class="item-name">{{ $item->product_name }}</h6>
                                        <p class="item-meta">
                                            <span class="item-qty">{{ __('Qty') }}: {{ $item->quantity }}</span>
                                            <span class="item-separator">•</span>
                                            <span class="item-price">${{ number_format($item->price, 2) }} {{ __('each') }}</span>
                                        </p>
                                    </div>
                                    <div class="item-total">
                                        ${{ number_format($item->total, 2) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Order Totals -->
                        <div class="order-totals">
                            <div class="total-row">
                                <span>{{ __('Subtotal') }}</span>
                                <span>${{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            <div class="total-row">
                                <span>{{ __('Shipping') }}</span>
                                <span>
                                    @if($order->shipping_amount == 0)
                                        <span class="text-success">{{ __('Free') }}</span>
                                    @else
                                        ${{ number_format($order->shipping_amount, 2) }}
                                    @endif
                                </span>
                            </div>
                            <div class="total-row">
                                <span>{{ __('Tax') }}</span>
                                <span>${{ number_format($order->tax_amount, 2) }}</span>
                            </div>
                            @if($order->discount_amount > 0)
                                <div class="total-row discount-row">
                                    <span>{{ __('Discount') }}</span>
                                    <span>-${{ number_format($order->discount_amount, 2) }}</span>
                                </div>
                            @endif
                            <div class="total-row-final">
                                <span>{{ __('Total') }}</span>
                                <span>${{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Information -->
                <div class="details-card mt-4">
                    <div class="details-card-header">
                        <h5 class="mb-0"><i class="bi bi-truck me-2"></i>{{ __('Shipping Information') }}</h5>
                    </div>
                    <div class="details-card-body">
                        @if($order->shippingAddress)
                            <div class="address-display">
                                <div class="address-field">
                                    <span class="address-label">{{ __('Recipient') }}</span>
                                    <span class="address-value">{{ $order->shippingAddress->full_name }}</span>
                                </div>
                                <div class="address-field">
                                    <span class="address-label">{{ __('Address') }}</span>
                                    <span class="address-value">
                                        {{ $order->shippingAddress->address_line_1 }}
                                        @if($order->shippingAddress->address_line_2)
                                            <br>{{ $order->shippingAddress->address_line_2 }}
                                        @endif
                                        <br>{{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->postal_code }}
                                        <br>{{ $order->shippingAddress->country }}
                                    </span>
                                </div>
                                @if($order->shippingAddress->phone)
                                    <div class="address-field">
                                        <span class="address-label">{{ __('Phone') }}</span>
                                        <span class="address-value">{{ $order->shippingAddress->phone }}</span>
                                    </div>
                                @endif
                            </div>
                        @else
                            <p class="text-muted text-center py-3">{{ __('Shipping address not available') }}</p>
                        @endif
                    </div>
                </div>

                <!-- What's Next -->
                <div class="timeline-card mt-4">
                    <div class="timeline-header">
                        <i class="bi bi-clock-history me-2"></i>{{ __('What happens next?') }}
                    </div>
                    <div class="timeline-body">
                        <div class="timeline-item">
                            <div class="timeline-marker">1</div>
                            <div class="timeline-content">
                                <h6>{{ __('Order Processing') }}</h6>
                                <p>{{ __('We will process your order within 24 hours') }}</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker">2</div>
                            <div class="timeline-content">
                                <h6>{{ __('Shipping Notification') }}</h6>
                                <p>{{ __('You will receive an email when your order is shipped') }}</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker">3</div>
                            <div class="timeline-content">
                                <h6>{{ __('Track Your Order') }}</h6>
                                <p>{{ __('Track your order status in "My Orders" section') }}</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker">4</div>
                            <div class="timeline-content">
                                <h6>{{ __('Delivery') }}</h6>
                                <p>{{ __('Delivery will be made within 3-5 business days') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- No Order Info -->
                <div class="action-buttons-section">
                    <a href="{{ route('orders.index') }}" class="btn-action btn-primary-action">
                        <i class="bi bi-list-ul me-2"></i>{{ __('View My Orders') }}
                    </a>
                    <a href="{{ route('home') }}" class="btn-action btn-secondary-action">
                        <i class="bi bi-house me-2"></i>{{ __('Back to Home') }}
                    </a>
                </div>
            @endif

            <!-- Support Section -->
            <div class="support-section">
                <i class="bi bi-headset"></i>
                <p>
                    {{ __('Need help?') }} 
                    <a href="mailto:support@store.com">{{ __('Contact Support') }}</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Poppins Font */
* {
    font-family: 'Poppins', sans-serif;
}

/* Theme Colors */
:root {
    --primary-color: #20b2aa;
    --primary-dark: #008b8b;
    --accent-color: #1a1a1a;
    --success-color: #27ae60;
    --warning-color: #f39c12;
    --border-color: #e0e0e0;
    --light-bg: #f8f9fa;
    --text-muted: #6c757d;
}

/* Success Header Card */
.success-header-card {
    background: white;
    border-radius: 20px;
    padding: 60px 40px;
    text-align: center;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    position: relative;
    overflow: hidden;
}

.success-header-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, var(--primary-color), var(--primary-dark));
}

.success-icon-wrapper {
    margin-bottom: 30px;
}

.success-icon-bg {
    width: 120px;
    height: 120px;
    margin: 0 auto;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(32, 178, 170, 0.1), rgba(32, 178, 170, 0.2));
    display: flex;
    align-items: center;
    justify-content: center;
    animation: scaleIn 0.5s ease-out;
}

.success-icon-bg i {
    font-size: 4rem;
    color: var(--primary-color);
    animation: checkPulse 2s infinite;
}

@keyframes scaleIn {
    from {
        transform: scale(0);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

@keyframes checkPulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

.success-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--accent-color);
    margin-bottom: 15px;
}

.success-subtitle {
    font-size: 1.1rem;
    color: var(--text-muted);
    margin-bottom: 30px;
    line-height: 1.6;
}

.order-number-badge {
    display: inline-flex;
    flex-direction: column;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
    padding: 20px 40px;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(32, 178, 170, 0.3);
}

.order-number-label {
    font-size: 0.9rem;
    opacity: 0.9;
    margin-bottom: 5px;
}

.order-number-value {
    font-size: 1.8rem;
    font-weight: 700;
}

/* Info Cards */
.info-card {
    background: white;
    border-radius: 15px;
    padding: 30px 25px;
    text-align: center;
    box-shadow: 0 3px 15px rgba(0,0,0,0.06);
    transition: all 0.3s ease;
    height: 100%;
}

.info-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.info-card-icon {
    width: 60px;
    height: 60px;
    margin: 0 auto 20px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(32, 178, 170, 0.1), rgba(32, 178, 170, 0.2));
    display: flex;
    align-items: center;
    justify-content: center;
}

.info-card-icon i {
    font-size: 1.8rem;
    color: var(--primary-color);
}

.info-card-title {
    font-size: 0.95rem;
    color: var(--text-muted);
    margin-bottom: 10px;
    font-weight: 500;
}

.info-card-value {
    font-size: 1.3rem;
    font-weight: 600;
    color: var(--accent-color);
    margin: 0;
}

.status-badge {
    display: inline-block;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
}

.status-pending {
    background: rgba(243, 156, 18, 0.1);
    color: var(--warning-color);
}

.status-processing {
    background: rgba(32, 178, 170, 0.1);
    color: var(--primary-color);
}

.status-completed {
    background: rgba(39, 174, 96, 0.1);
    color: var(--success-color);
}

/* Action Buttons */
.action-buttons-section {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin: 40px 0;
    flex-wrap: wrap;
}

.btn-action {
    padding: 14px 32px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 1rem;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
}

.btn-primary-action {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
    border: none;
}

.btn-primary-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(32, 178, 170, 0.3);
    color: white;
}

.btn-secondary-action {
    background: white;
    color: var(--accent-color);
    border: 2px solid var(--border-color);
}

.btn-secondary-action:hover {
    border-color: var(--primary-color);
    color: var(--primary-color);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

/* Notification Card */
.notification-card {
    background: linear-gradient(135deg, rgba(32, 178, 170, 0.05), rgba(32, 178, 170, 0.1));
    border: 2px solid rgba(32, 178, 170, 0.2);
    border-radius: 15px;
    padding: 25px;
    display: flex;
    align-items: start;
    gap: 20px;
    margin: 30px 0;
}

.notification-icon {
    font-size: 2.5rem;
    color: var(--primary-color);
    flex-shrink: 0;
}

.notification-content {
    flex-grow: 1;
}

.notification-title {
    font-weight: 600;
    color: var(--accent-color);
    margin-bottom: 8px;
}

.notification-text {
    color: var(--text-muted);
    margin: 0;
    line-height: 1.6;
}

/* Details Card */
.details-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.06);
    overflow: hidden;
}

.details-card-header {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
    padding: 20px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.details-card-header h5 {
    margin: 0;
    font-weight: 600;
}

.items-count {
    background: rgba(255, 255, 255, 0.2);
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.9rem;
}

.details-card-body {
    padding: 0;
}

/* Order Items List */
.order-items-list {
    padding: 25px;
}

.order-item-row {
    display: flex;
    justify-content: space-between;
    align-items: start;
    padding: 20px 0;
    border-bottom: 1px solid var(--border-color);
}

.order-item-row:last-child {
    border-bottom: none;
}

.item-info {
    flex-grow: 1;
}

.item-name {
    font-size: 1.05rem;
    font-weight: 600;
    color: var(--accent-color);
    margin-bottom: 8px;
}

.item-meta {
    color: var(--text-muted);
    font-size: 0.9rem;
    margin: 0;
}

.item-separator {
    margin: 0 10px;
}

.item-total {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--primary-color);
}

/* Order Totals */
.order-totals {
    padding: 25px;
    background: var(--light-bg);
}

.total-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    font-size: 1rem;
    color: var(--text-muted);
}

.discount-row {
    color: var(--success-color);
}

.total-row-final {
    display: flex;
    justify-content: space-between;
    padding: 20px 0 0;
    border-top: 2px solid var(--border-color);
    margin-top: 10px;
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--accent-color);
}

.total-row-final span:last-child {
    color: var(--primary-color);
}

/* Address Display */
.address-display {
    padding: 25px;
}

.address-field {
    display: flex;
    padding: 15px 0;
    border-bottom: 1px solid var(--border-color);
}

.address-field:last-child {
    border-bottom: none;
}

.address-label {
    font-weight: 600;
    color: var(--accent-color);
    width: 120px;
    flex-shrink: 0;
}

.address-value {
    color: var(--text-muted);
    line-height: 1.6;
}

/* Timeline Card */
.timeline-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.06);
    overflow: hidden;
}

.timeline-header {
    background: linear-gradient(135deg, var(--accent-color), #2d2d2d);
    color: white;
    padding: 20px 25px;
    font-weight: 600;
    font-size: 1.1rem;
}

.timeline-body {
    padding: 30px 25px;
}

.timeline-item {
    display: flex;
    gap: 20px;
    margin-bottom: 25px;
    position: relative;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 19px;
    top: 45px;
    bottom: -25px;
    width: 2px;
    background: linear-gradient(180deg, var(--primary-color), transparent);
}

.timeline-marker {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    flex-shrink: 0;
    box-shadow: 0 3px 10px rgba(32, 178, 170, 0.3);
}

.timeline-content h6 {
    font-weight: 600;
    color: var(--accent-color);
    margin-bottom: 5px;
}

.timeline-content p {
    color: var(--text-muted);
    margin: 0;
    line-height: 1.5;
}

/* Support Section */
.support-section {
    text-align: center;
    padding: 30px;
    margin-top: 30px;
}

.support-section i {
    font-size: 2rem;
    color: var(--primary-color);
    display: block;
    margin-bottom: 15px;
}

.support-section p {
    color: var(--text-muted);
    margin: 0;
}

.support-section a {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
}

.support-section a:hover {
    text-decoration: underline;
}

/* Responsive */
@media (max-width: 768px) {
    .success-header-card {
        padding: 40px 20px;
    }

    .success-title {
        font-size: 1.5rem;
    }

    .order-number-badge {
        padding: 15px 25px;
    }

    .order-number-value {
        font-size: 1.4rem;
    }

    .action-buttons-section {
        flex-direction: column;
    }

    .btn-action {
        width: 100%;
        justify-content: center;
    }

    .order-item-row {
        flex-direction: column;
        gap: 10px;
    }

    .item-total {
        text-align: left;
    }

    .address-field {
        flex-direction: column;
        gap: 5px;
    }

    .address-label {
        width: 100%;
    }
}
</style>
@endpush