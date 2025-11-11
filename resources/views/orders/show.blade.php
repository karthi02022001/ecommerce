@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            {{-- Order Header --}}
            <div class="order-header-card mb-4">
                <a href="{{ route('orders.index') }}" class="back-link">
                    <i class="bi bi-arrow-left-circle me-2"></i>{{ __('Back to Orders') }}
                </a>
                
                <div class="d-flex justify-content-between align-items-start mt-3 flex-wrap gap-3">
                    <div>
                        <h1 class="order-title mb-2">
                            <i class="bi bi-receipt-cutoff me-2 text-primary"></i>{{ __('Order') }} <span class="order-number">#{{ $order->order_number }}</span>
                        </h1>
                        <p class="order-date mb-0">
                            <i class="bi bi-calendar-event me-2"></i>{{ __('Placed on') }} {{ $order->created_at->format('F d, Y \a\t g:i A') }}
                        </p>
                    </div>
                    <div>
                        @php
                            $statusConfig = [
                                'pending' => ['class' => 'warning', 'icon' => 'clock-history'],
                                'processing' => ['class' => 'info', 'icon' => 'arrow-repeat'],
                                'shipped' => ['class' => 'primary', 'icon' => 'truck'],
                                'delivered' => ['class' => 'success', 'icon' => 'check-circle-fill'],
                                'cancelled' => ['class' => 'danger', 'icon' => 'x-circle-fill'],
                            ];
                            $config = $statusConfig[$order->status] ?? ['class' => 'secondary', 'icon' => 'question-circle'];
                        @endphp
                        <span class="status-badge-large status-{{ $config['class'] }}">
                            <i class="bi bi-{{ $config['icon'] }} me-2"></i>{{ __(ucfirst($order->status)) }}
                        </span>
                    </div>
                </div>
            </div>
            
            {{-- Order Items --}}
            <div class="themed-card mb-4">
                <div class="themed-card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-box-seam me-2"></i>{{ __('Order Items') }}
                    </h5>
                </div>
                <div class="themed-card-body p-0">
                    <div class="table-responsive">
                        <table class="order-items-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Product') }}</th>
                                    <th class="text-center">{{ __('Price') }}</th>
                                    <th class="text-center">{{ __('Quantity') }}</th>
                                    <th class="text-end">{{ __('Subtotal') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $item)
                                    <tr class="order-item-row">
                                        <td>
                                            <div class="product-info">
                                                <div class="product-image-wrapper">
                                                    @if($item->product && $item->product->featured_image)
                                                        <img src="{{ asset('storage/' . $item->product->featured_image) }}" 
                                                             alt="{{ $item->product_name }}" 
                                                             class="product-image">
                                                    @else
                                                        <div class="product-image-placeholder">
                                                            <i class="bi bi-image"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="product-details">
                                                    <h6 class="product-name mb-1">{{ $item->product_name }}</h6>
                                                    @if($item->product)
                                                        <small class="product-sku">
                                                            <i class="bi bi-upc-scan me-1"></i>{{ __('SKU') }}: {{ $item->product->sku }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center price-cell">
                                            {{ session('currency_symbol', '$') }}{{ number_format($item->price * session('currency_rate', 1), 2) }}
                                        </td>
                                        <td class="text-center">
                                            <span class="quantity-badge">{{ $item->quantity }}</span>
                                        </td>
                                        <td class="text-end subtotal-cell">
                                            <strong>{{ session('currency_symbol', '$') }}{{ number_format($item->price * $item->quantity * session('currency_rate', 1), 2) }}</strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            {{-- Order Timeline --}}
            <div class="themed-card mb-4">
                <div class="themed-card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>{{ __('Order Timeline') }}
                    </h5>
                </div>
                <div class="themed-card-body">
                    <div class="timeline">
                        <div class="timeline-item {{ in_array($order->status, ['pending', 'processing', 'shipped', 'delivered']) ? 'active' : '' }}">
                            <div class="timeline-marker">
                                <i class="bi bi-check-lg"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">{{ __('Order Placed') }}</h6>
                                <p class="timeline-time">{{ $order->created_at->format('M d, Y g:i A') }}</p>
                                <p class="timeline-description">{{ __('Your order has been received') }}</p>
                            </div>
                        </div>
                        
                        <div class="timeline-item {{ in_array($order->status, ['processing', 'shipped', 'delivered']) ? 'active' : '' }}">
                            <div class="timeline-marker">
                                <i class="bi bi-arrow-repeat"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">{{ __('Processing') }}</h6>
                                @if($order->status !== 'pending')
                                    <p class="timeline-time">{{ __('In Progress') }}</p>
                                    <p class="timeline-description">{{ __('Order is being prepared for shipment') }}</p>
                                @else
                                    <p class="timeline-description text-muted">{{ __('Waiting to be processed') }}</p>
                                @endif
                            </div>
                        </div>
                        
                        <div class="timeline-item {{ in_array($order->status, ['shipped', 'delivered']) ? 'active' : '' }}">
                            <div class="timeline-marker">
                                <i class="bi bi-truck"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">{{ __('Shipped') }}</h6>
                                @if(in_array($order->status, ['shipped', 'delivered']))
                                    <p class="timeline-time">{{ __('On the way') }}</p>
                                    <p class="timeline-description">{{ __('Package is en route to your address') }}</p>
                                @else
                                    <p class="timeline-description text-muted">{{ __('Not yet shipped') }}</p>
                                @endif
                            </div>
                        </div>
                        
                        <div class="timeline-item {{ $order->status === 'delivered' ? 'active' : '' }}">
                            <div class="timeline-marker">
                                <i class="bi bi-house-check-fill"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">{{ __('Delivered') }}</h6>
                                @if($order->status === 'delivered')
                                    <p class="timeline-time">{{ $order->updated_at->format('M d, Y g:i A') }}</p>
                                    <p class="timeline-description">{{ __('Order successfully delivered') }}</p>
                                @else
                                    <p class="timeline-description text-muted">{{ __('Not yet delivered') }}</p>
                                @endif
                            </div>
                        </div>
                        
                        @if($order->status === 'cancelled')
                            <div class="timeline-item cancelled active">
                                <div class="timeline-marker cancelled-marker">
                                    <i class="bi bi-x-lg"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title text-danger">{{ __('Cancelled') }}</h6>
                                    <p class="timeline-time">{{ $order->updated_at->format('M d, Y g:i A') }}</p>
                                    <p class="timeline-description">{{ __('Order has been cancelled') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            {{-- Shipping & Billing --}}
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="themed-card h-100">
                        <div class="themed-card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-truck me-2"></i>{{ __('Shipping Address') }}
                            </h6>
                        </div>
                        <div class="themed-card-body">
                            @if($order->shippingAddress)
                                <div class="address-content">
                                    <p class="address-line">
                                        <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                                        <strong>{{ $order->shippingAddress->full_name ?? $order->user->name }}</strong>
                                    </p>
                                    <p class="address-line">{{ $order->shippingAddress->street_address }}</p>
                                    <p class="address-line">
                                        {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }}
                                    </p>
                                    <p class="address-line">{{ $order->shippingAddress->postal_code }}</p>
                                    <p class="address-line mb-0">
                                        <strong>{{ $order->shippingAddress->country }}</strong>
                                    </p>
                                    @if($order->shippingAddress->phone)
                                        <p class="address-line mt-2 mb-0">
                                            <i class="bi bi-telephone-fill text-primary me-2"></i>{{ $order->shippingAddress->phone }}
                                        </p>
                                    @endif
                                </div>
                            @else
                                <p class="text-muted text-center py-3 mb-0">
                                    <i class="bi bi-exclamation-circle me-2"></i>{{ __('No shipping address') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="themed-card h-100">
                        <div class="themed-card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-receipt me-2"></i>{{ __('Billing Address') }}
                            </h6>
                        </div>
                        <div class="themed-card-body">
                            @if($order->billingAddress)
                                <div class="address-content">
                                    <p class="address-line">
                                        <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                                        <strong>{{ $order->billingAddress->full_name ?? $order->user->name }}</strong>
                                    </p>
                                    <p class="address-line">{{ $order->billingAddress->street_address }}</p>
                                    <p class="address-line">
                                        {{ $order->billingAddress->city }}, {{ $order->billingAddress->state }}
                                    </p>
                                    <p class="address-line">{{ $order->billingAddress->postal_code }}</p>
                                    <p class="address-line mb-0">
                                        <strong>{{ $order->billingAddress->country }}</strong>
                                    </p>
                                    @if($order->billingAddress->phone)
                                        <p class="address-line mt-2 mb-0">
                                            <i class="bi bi-telephone-fill text-primary me-2"></i>{{ $order->billingAddress->phone }}
                                        </p>
                                    @endif
                                </div>
                            @else
                                <p class="text-muted text-center py-3 mb-0">
                                    <i class="bi bi-info-circle me-2"></i>{{ __('Same as shipping address') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Notes --}}
            @if($order->notes)
                <div class="themed-card mb-4">
                    <div class="themed-card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-chat-left-text me-2"></i>{{ __('Order Notes') }}
                        </h6>
                    </div>
                    <div class="themed-card-body">
                        <div class="notes-content">
                            <i class="bi bi-quote text-primary" style="font-size: 2rem; opacity: 0.3;"></i>
                            <p class="mb-0 mt-2">{{ $order->notes }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        {{-- Order Summary Sidebar --}}
        <div class="col-lg-4">
            <div class="summary-sidebar">
                <div class="themed-card mb-3">
                    <div class="themed-card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-calculator me-2"></i>{{ __('Order Summary') }}
                        </h5>
                    </div>
                    <div class="themed-card-body">
                        <div class="summary-row">
                            <span class="summary-label">{{ __('Subtotal') }}</span>
                            <span class="summary-value">{{ session('currency_symbol', '$') }}{{ number_format($order->subtotal * session('currency_rate', 1), 2) }}</span>
                        </div>
                        
                        <div class="summary-row">
                            <span class="summary-label">
                                <i class="bi bi-truck me-1"></i>{{ __('Shipping') }}
                            </span>
                            <span class="summary-value">{{ session('currency_symbol', '$') }}{{ number_format($order->shipping_cost * session('currency_rate', 1), 2) }}</span>
                        </div>
                        
                        <div class="summary-row">
                            <span class="summary-label">
                                <i class="bi bi-receipt me-1"></i>{{ __('Tax') }}
                            </span>
                            <span class="summary-value">{{ session('currency_symbol', '$') }}{{ number_format($order->tax_amount * session('currency_rate', 1), 2) }}</span>
                        </div>
                        
                        @if($order->discount_amount > 0)
                            <div class="summary-row discount-row">
                                <span class="summary-label">
                                    <i class="bi bi-tag-fill me-1"></i>{{ __('Discount') }}
                                </span>
                                <span class="summary-value">-{{ session('currency_symbol', '$') }}{{ number_format($order->discount_amount * session('currency_rate', 1), 2) }}</span>
                            </div>
                        @endif
                        
                        <hr class="summary-divider">
                        
                        <div class="summary-total">
                            <span class="total-label">{{ __('Total') }}</span>
                            <span class="total-value">{{ session('currency_symbol', '$') }}{{ number_format($order->total_amount * session('currency_rate', 1), 2) }}</span>
                        </div>
                        
                        <div class="payment-info mt-4">
                            <div class="info-item">
                                <small class="info-label">
                                    <i class="bi bi-credit-card me-1"></i>{{ __('Payment Method') }}
                                </small>
                                <div class="info-value">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</div>
                            </div>
                            
                            <div class="info-item mt-3">
                                <small class="info-label">
                                    <i class="bi bi-check-circle me-1"></i>{{ __('Payment Status') }}
                                </small>
                                <div class="info-value">
                                    @php
                                        $paymentColors = [
                                            'pending' => 'warning',
                                            'paid' => 'success',
                                            'failed' => 'danger',
                                        ];
                                        $payColor = $paymentColors[$order->payment_status] ?? 'secondary';
                                    @endphp
                                    <span class="payment-status-badge status-{{ $payColor }}">
                                        {{ __(ucfirst($order->payment_status)) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="summary-divider">
                        
                        <div class="action-buttons">
                            @if($order->status === 'pending')
                                <form action="{{ route('orders.cancel', $order->id) }}" method="POST" class="mb-2">
                                    @csrf
                               
                                    <button type="submit" 
                                            class="btn btn-outline-danger w-100" 
                                            onclick="return confirm('{{ __('Are you sure you want to cancel this order?') }}')">
                                        <i class="bi bi-x-circle me-2"></i>{{ __('Cancel Order') }}
                                    </button>
                                </form>
                            @endif
                            
                            @if(in_array($order->status, ['delivered', 'shipped']))
                                <a href="{{ route('orders.invoice', $order->id) }}" 
                                   class="btn btn-primary w-100 mb-2" 
                                   target="_blank">
                                    <i class="bi bi-download me-2"></i>{{ __('Download Invoice') }}
                                </a>
                            @endif
                            
                            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-list-ul me-2"></i>{{ __('View All Orders') }}
                            </a>
                        </div>
                    </div>
                </div>
                
                {{-- Need Help? --}}
                <div class="themed-card help-card">
                    <div class="themed-card-body text-center">
                        <div class="help-icon mb-3">
                            <i class="bi bi-headset"></i>
                        </div>
                        <h6 class="help-title">{{ __('Need Help?') }}</h6>
                        <p class="help-description">{{ __('Our support team is here to assist you') }}</p>
                        <a href="mailto:support@store.com" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-envelope me-2"></i>{{ __('Contact Support') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection