@extends('layouts.app')

@section('content')
<div class="container py-5">
    {{-- Page Header --}}
    <div class="row mb-5">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="page-title mb-2">
                        <i class="bi bi-bag-check-fill me-2 text-primary"></i>{{ __('My Orders') }}
                    </h1>
                    <p class="text-muted mb-0">{{ __('Track and manage your orders') }}</p>
                </div>
                <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-shop me-2"></i>{{ __('Continue Shopping') }}
                </a>
            </div>
        </div>
    </div>
    
    @if($orders->count() > 0)
        <div class="row g-4">
            @foreach($orders as $order)
                <div class="col-12">
                    <div class="order-card">
                        {{-- Order Header --}}
                        <div class="order-header">
                            <div class="row align-items-center g-3">
                                <div class="col-lg-3 col-md-6">
                                    <div class="order-info-item">
                                        <small class="text-muted d-block mb-1">
                                            <i class="bi bi-hash me-1"></i>{{ __('Order Number') }}
                                        </small>
                                        <strong class="order-number">#{{ $order->order_number }}</strong>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-6">
                                    <div class="order-info-item">
                                        <small class="text-muted d-block mb-1">
                                            <i class="bi bi-calendar3 me-1"></i>{{ __('Date') }}
                                        </small>
                                        <strong>{{ $order->created_at->format('M d, Y') }}</strong>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-6">
                                    <div class="order-info-item">
                                        <small class="text-muted d-block mb-1">
                                            <i class="bi bi-currency-dollar me-1"></i>{{ __('Total') }}
                                        </small>
                                        <strong class="text-primary fs-5">
                                            {{ session('currency_symbol', '$') }}{{ number_format($order->total_amount * session('currency_rate', 1), 2) }}
                                        </strong>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="order-info-item">
                                        <small class="text-muted d-block mb-1">
                                            <i class="bi bi-info-circle me-1"></i>{{ __('Status') }}
                                        </small>
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
                                        <span class="status-badge status-{{ $config['class'] }}">
                                            <i class="bi bi-{{ $config['icon'] }} me-1"></i>{{ __(ucfirst($order->status)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-12 text-lg-end">
                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm w-100 w-lg-auto">
                                        <i class="bi bi-eye me-1"></i>{{ __('View Details') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Order Body --}}
                        <div class="order-body">
                            <div class="row">
                                <div class="col-lg-9">
                                    <h6 class="mb-3 fw-semibold">
                                        <i class="bi bi-box-seam me-2 text-primary"></i>{{ __('Items') }}:
                                    </h6>
                                    <div class="order-items-grid">
                                        @foreach($order->orderItems->take(4) as $item)
                                            <div class="order-item-preview">
                                                <div class="item-image-wrapper">
                                                    @if($item->product && $item->product->featured_image)
                                                        <img src="{{ asset('storage/' . $item->product->featured_image) }}" 
                                                             alt="{{ $item->product_name }}" 
                                                             class="item-image">
                                                    @else
                                                        <div class="item-image-placeholder">
                                                            <i class="bi bi-image"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="item-details">
                                                    <div class="item-name">{{ $item->product_name }}</div>
                                                    <div class="item-meta">
                                                        <span class="qty-badge">
                                                            <i class="bi bi-x me-1"></i>{{ $item->quantity }}
                                                        </span>
                                                        <span class="item-price">
                                                            {{ session('currency_symbol', '$') }}{{ number_format($item->price * session('currency_rate', 1), 2) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        
                                        @if($order->orderItems->count() > 4)
                                            <div class="order-item-preview">
                                                <div class="more-items-badge">
                                                    <i class="bi bi-plus-circle mb-2"></i>
                                                    <span class="d-block">{{ $order->orderItems->count() - 4 }}</span>
                                                    <small>{{ __('more items') }}</small>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="col-lg-3">
                                    <div class="order-actions">
                                        @if($order->status === 'pending')
                                            <form action="{{ route('orders.cancel', $order->id) }}" 
                                                  method="POST" 
                                                  class="mb-2">
                                                @csrf
                                               
                                                <button type="submit" 
                                                        class="btn btn-outline-danger btn-sm w-100" 
                                                        onclick="return confirm('{{ __('Are you sure you want to cancel this order?') }}')">
                                                    <i class="bi bi-x-circle me-1"></i>{{ __('Cancel Order') }}
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if(in_array($order->status, ['delivered', 'shipped']))
                                            <a href="{{ route('orders.invoice', $order->id) }}" 
                                               class="btn btn-outline-secondary btn-sm w-100" 
                                               target="_blank">
                                                <i class="bi bi-download me-1"></i>{{ __('Download Invoice') }}
                                            </a>
                                        @endif
                                        
                                        @if($order->status === 'delivered')
                                            <a href="{{ route('orders.show', $order->id) }}" 
                                               class="btn btn-outline-primary btn-sm w-100 mt-2">
                                                <i class="bi bi-star me-1"></i>{{ __('Review Products') }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Shipping Info --}}
                            @if($order->shippingAddress)
                                <div class="shipping-info">
                                    <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                                    <span class="text-muted">{{ __('Shipping to') }}:</span>
                                    <strong class="ms-2">
                                        {{ $order->shippingAddress->address_line1 }}, 
                                        {{ $order->shippingAddress->city }}, 
                                        {{ $order->shippingAddress->state }} - {{ $order->shippingAddress->postal_code }}
                                    </strong>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        {{-- Pagination --}}
        @if($orders->hasPages())
            <div class="d-flex justify-content-center mt-5">
                <nav aria-label="Orders pagination">
                    {{ $orders->links() }}
                </nav>
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="bi bi-inbox"></i>
            </div>
            <h2 class="empty-state-title">{{ __('No orders yet') }}</h2>
            <p class="empty-state-description">
                {{ __('Start shopping to create your first order!') }}
            </p>
            <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg mt-4">
                <i class="bi bi-shop me-2"></i>{{ __('Browse Products') }}
            </a>
        </div>
    @endif
</div>

@endsection