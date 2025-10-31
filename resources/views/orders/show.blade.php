@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            {{-- Order Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <a href="{{ route('orders.index') }}" class="text-decoration-none">
                        <i class="bi bi-arrow-left me-2"></i>{{ __('Back to Orders') }}
                    </a>
                    <h1 class="mt-2">{{ __('Order') }} #{{ $order->order_number }}</h1>
                    <p class="text-muted mb-0">
                        {{ __('Placed on') }} {{ $order->created_at->format('F d, Y \a\t g:i A') }}
                    </p>
                </div>
                <div>
                    @php
                        $statusColors = [
                            'pending' => 'warning',
                            'processing' => 'info',
                            'shipped' => 'primary',
                            'delivered' => 'success',
                            'cancelled' => 'danger',
                        ];
                        $color = $statusColors[$order->status] ?? 'secondary';
                    @endphp
                    <span class="badge bg-{{ $color }} fs-6">{{ ucfirst($order->status) }}</span>
                </div>
            </div>
            
            {{-- Order Items --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Order Items') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Subtotal') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->product && $item->product->featured_image)
                                                    <img src="{{ asset('storage/' . $item->product->featured_image) }}" 
                                                         alt="{{ $item->product_name }}" 
                                                         class="img-thumbnail me-3" 
                                                         style="width: 80px; height: 80px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center me-3" 
                                                         style="width: 80px; height: 80px;">
                                                        <i class="bi bi-image" style="font-size: 2rem; color: #ccc;"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h6 class="mb-0">{{ $item->product_name }}</h6>
                                                    @if($item->product)
                                                        <small class="text-muted">{{ __('SKU') }}: {{ $item->product->sku }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>${{ number_format($item->price, 2) }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td><strong>${{ number_format($item->price * $item->quantity, 2) }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            {{-- Order Timeline --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>{{ __('Order Timeline') }}</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item {{ in_array($order->status, ['pending', 'processing', 'shipped', 'delivered']) ? 'active' : '' }}">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">{{ __('Order Placed') }}</h6>
                                <small class="text-muted">{{ $order->created_at->format('M d, Y g:i A') }}</small>
                            </div>
                        </div>
                        
                        <div class="timeline-item {{ in_array($order->status, ['processing', 'shipped', 'delivered']) ? 'active' : '' }}">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">{{ __('Processing') }}</h6>
                                @if($order->status !== 'pending')
                                    <small class="text-muted">{{ __('Order is being prepared') }}</small>
                                @endif
                            </div>
                        </div>
                        
                        <div class="timeline-item {{ in_array($order->status, ['shipped', 'delivered']) ? 'active' : '' }}">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">{{ __('Shipped') }}</h6>
                                @if(in_array($order->status, ['shipped', 'delivered']))
                                    <small class="text-muted">{{ __('On the way to you') }}</small>
                                @endif
                            </div>
                        </div>
                        
                        <div class="timeline-item {{ $order->status === 'delivered' ? 'active' : '' }}">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">{{ __('Delivered') }}</h6>
                                @if($order->status === 'delivered')
                                    <small class="text-muted">{{ __('Order completed') }}</small>
                                @endif
                            </div>
                        </div>
                        
                        @if($order->status === 'cancelled')
                            <div class="timeline-item cancelled">
                                <div class="timeline-marker bg-danger"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1 text-danger">{{ __('Cancelled') }}</h6>
                                    <small class="text-muted">{{ $order->updated_at->format('M d, Y g:i A') }}</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            {{-- Shipping & Billing --}}
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-truck me-2"></i>{{ __('Shipping Address') }}</h6>
                        </div>
                        <div class="card-body">
                            @if($order->shippingAddress)
                                <p class="mb-0">
                                    {{ $order->shippingAddress->street_address }}<br>
                                    {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }}<br>
                                    {{ $order->shippingAddress->postal_code }}<br>
                                    {{ $order->shippingAddress->country }}
                                </p>
                            @else
                                <p class="text-muted">{{ __('No shipping address') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-receipt me-2"></i>{{ __('Billing Address') }}</h6>
                        </div>
                        <div class="card-body">
                            @if($order->billingAddress)
                                <p class="mb-0">
                                    {{ $order->billingAddress->street_address }}<br>
                                    {{ $order->billingAddress->city }}, {{ $order->billingAddress->state }}<br>
                                    {{ $order->billingAddress->postal_code }}<br>
                                    {{ $order->billingAddress->country }}
                                </p>
                            @else
                                <p class="text-muted">{{ __('Same as shipping address') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Notes --}}
            @if($order->notes)
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bi bi-chat-left-text me-2"></i>{{ __('Order Notes') }}</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $order->notes }}</p>
                    </div>
                </div>
            @endif
        </div>
        
        {{-- Order Summary Sidebar --}}
        <div class="col-lg-4">
            <div class="card position-sticky" style="top: 20px;">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Order Summary') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ __('Subtotal') }}</span>
                        <span>${{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ __('Shipping') }}</span>
                        <span>${{ number_format($order->shipping_cost, 2) }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ __('Tax') }}</span>
                        <span>${{ number_format($order->tax_amount, 2) }}</span>
                    </div>
                    
                    @if($order->discount_amount > 0)
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>{{ __('Discount') }}</span>
                            <span>-${{ number_format($order->discount_amount, 2) }}</span>
                        </div>
                    @endif
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <strong>{{ __('Total') }}</strong>
                        <strong class="text-primary">${{ number_format($order->total_amount, 2) }}</strong>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">{{ __('Payment Method') }}:</small><br>
                        <strong>{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</strong>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">{{ __('Payment Status') }}:</small><br>
                        @php
                            $paymentColors = [
                                'pending' => 'warning',
                                'paid' => 'success',
                                'failed' => 'danger',
                            ];
                            $payColor = $paymentColors[$order->payment_status] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $payColor }}">{{ ucfirst($order->payment_status) }}</span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-grid gap-2">
                        @if($order->status === 'pending')
                            <form action="{{ route('orders.cancel', $order->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" 
                                        class="btn btn-outline-danger w-100" 
                                        onclick="return confirm('{{ __('Are you sure you want to cancel this order?') }}')">
                                    <i class="bi bi-x-circle me-2"></i>{{ __('Cancel Order') }}
                                </button>
                            </form>
                        @endif
                        
                        @if(in_array($order->status, ['delivered', 'shipped']))
                            <a href="{{ route('orders.invoice', $order->id) }}" 
                               class="btn btn-outline-primary" 
                               target="_blank">
                                <i class="bi bi-download me-2"></i>{{ __('Download Invoice') }}
                            </a>
                        @endif
                        
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-list-ul me-2"></i>{{ __('View All Orders') }}
                        </a>
                    </div>
                </div>
            </div>
            
            {{-- Need Help? --}}
            <div class="card mt-3">
                <div class="card-body text-center">
                    <h6><i class="bi bi-question-circle me-2"></i>{{ __('Need Help?') }}</h6>
                    <p class="small mb-2">{{ __('Contact our customer support') }}</p>
                    <a href="mailto:support@store.com" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-envelope me-2"></i>{{ __('Contact Support') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    padding-bottom: 30px;
    opacity: 0.5;
}

.timeline-item.active {
    opacity: 1;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -23px;
    top: 10px;
    bottom: -10px;
    width: 2px;
    background: #dee2e6;
}

.timeline-item:last-child::before {
    display: none;
}

.timeline-item.active::before {
    background: #0d6efd;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: #dee2e6;
    border: 2px solid white;
}

.timeline-item.active .timeline-marker {
    background: #0d6efd;
}

.timeline-item.cancelled .timeline-marker {
    background: #dc3545;
}
</style>
@endsection
