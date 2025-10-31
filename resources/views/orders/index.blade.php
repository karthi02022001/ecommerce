@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ __('My Orders') }}</h1>
        <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-shop me-2"></i>{{ __('Continue Shopping') }}
        </a>
    </div>
    
    @if($orders->count() > 0)
        <div class="row">
            @foreach($orders as $order)
                <div class="col-12 mb-3">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <small class="text-muted">{{ __('Order Number') }}</small><br>
                                    <strong>#{{ $order->order_number }}</strong>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">{{ __('Date') }}</small><br>
                                    <strong>{{ $order->created_at->format('M d, Y') }}</strong>
                                </div>
                                <div class="col-md-2">
                                    <small class="text-muted">{{ __('Total') }}</small><br>
                                    <strong>${{ number_format($order->total_amount, 2) }}</strong>
                                </div>
                                <div class="col-md-2">
                                    <small class="text-muted">{{ __('Status') }}</small><br>
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
                                    <span class="badge bg-{{ $color }}">{{ ucfirst($order->status) }}</span>
                                </div>
                                <div class="col-md-2 text-end">
                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                        {{ __('View Details') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-9">
                                    <h6 class="mb-2">{{ __('Items') }}:</h6>
                                    <div class="d-flex flex-wrap gap-3">
                                        @foreach($order->orderItems->take(4) as $item)
                                            <div class="d-flex align-items-center">
                                                @if($item->product && $item->product->featured_image)
                                                    <img src="{{ asset('storage/' . $item->product->featured_image) }}" 
                                                         alt="{{ $item->product_name }}" 
                                                         class="img-thumbnail me-2" 
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center me-2" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="bi bi-image"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <small class="d-block">{{ $item->product_name }}</small>
                                                    <small class="text-muted">{{ __('Qty') }}: {{ $item->quantity }}</small>
                                                </div>
                                            </div>
                                        @endforeach
                                        
                                        @if($order->orderItems->count() > 4)
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-secondary">
                                                    +{{ $order->orderItems->count() - 4 }} {{ __('more') }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="col-md-3 text-end">
                                    @if($order->status === 'pending')
                                        <form action="{{ route('orders.cancel', $order->id) }}" 
                                              method="POST" 
                                              class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirm('{{ __('Are you sure you want to cancel this order?') }}')">
                                                <i class="bi bi-x-circle me-1"></i>{{ __('Cancel Order') }}
                                            </button>
                                        </form>
                                    @endif
                                    
                                    @if(in_array($order->status, ['delivered', 'shipped']))
                                        <a href="{{ route('orders.invoice', $order->id) }}" 
                                           class="btn btn-sm btn-outline-secondary" 
                                           target="_blank">
                                            <i class="bi bi-download me-1"></i>{{ __('Invoice') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                            
                            @if($order->shippingAddress)
                                <div class="mt-3 pt-3 border-top">
                                    <small class="text-muted">
                                        <i class="bi bi-truck me-1"></i>{{ __('Shipping to') }}: 
                                        {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }}
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-inbox" style="font-size: 5rem; color: #ccc;"></i>
            <h3 class="mt-3">{{ __('No orders yet') }}</h3>
            <p class="text-muted">{{ __('Start shopping to create your first order!') }}</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary mt-3">
                <i class="bi bi-shop me-2"></i>{{ __('Browse Products') }}
            </a>
        </div>
    @endif
</div>
@endsection
