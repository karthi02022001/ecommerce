@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-success">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                    </div>
                    
                    <h1 class="mb-3">{{ __('Order Placed Successfully!') }}</h1>
                    
                    <p class="lead mb-4">
                        {{ __('Thank you for your order.') }}
                    </p>
                    
                    @if(isset($order))
                        <div class="alert alert-info d-inline-block">
                            <strong>{{ __('Order Number') }}:</strong> #{{ $order->order_number }}
                        </div>
                        
                        <div class="mt-4">
                            <p class="mb-2">{{ __('Order Details:') }}</p>
                            <ul class="list-unstyled">
                                <li><strong>{{ __('Total Amount') }}:</strong> ${{ number_format($order->total_amount, 2) }}</li>
                                <li><strong>{{ __('Payment Method') }}:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</li>
                                <li><strong>{{ __('Status') }}:</strong> 
                                    <span class="badge bg-warning">{{ ucfirst($order->status) }}</span>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="alert alert-light mt-4">
                            <i class="bi bi-info-circle me-2"></i>
                            {{ __('A confirmation email has been sent to your email address with order details.') }}
                        </div>
                        
                        <div class="mt-4">
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-primary me-2">
                                <i class="bi bi-receipt me-2"></i>{{ __('View Order Details') }}
                            </a>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                                <i class="bi bi-shop me-2"></i>{{ __('Continue Shopping') }}
                            </a>
                        </div>
                    @else
                        <div class="mt-4">
                            <a href="{{ route('orders.index') }}" class="btn btn-primary me-2">
                                <i class="bi bi-list-ul me-2"></i>{{ __('View My Orders') }}
                            </a>
                            <a href="{{ route('home') }}" class="btn btn-outline-primary">
                                <i class="bi bi-house me-2"></i>{{ __('Back to Home') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            @if(isset($order))
                {{-- Order Items Summary --}}
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Items Ordered') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ __('Product') }}</th>
                                        <th>{{ __('Quantity') }}</th>
                                        <th>{{ __('Price') }}</th>
                                        <th>{{ __('Subtotal') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->orderItems as $item)
                                        <tr>
                                            <td>{{ $item->product_name }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>${{ number_format($item->price, 2) }}</td>
                                            <td>${{ number_format($item->quantity * $item->price, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>{{ __('Subtotal') }}:</strong></td>
                                        <td><strong>${{ number_format($order->subtotal, 2) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end">{{ __('Shipping') }}:</td>
                                        <td>${{ number_format($order->shipping_cost, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end">{{ __('Tax') }}:</td>
                                        <td>${{ number_format($order->tax_amount, 2) }}</td>
                                    </tr>
                                    @if($order->discount_amount > 0)
                                        <tr>
                                            <td colspan="3" class="text-end text-success">{{ __('Discount') }}:</td>
                                            <td class="text-success">-${{ number_format($order->discount_amount, 2) }}</td>
                                        </tr>
                                    @endif
                                    <tr class="table-primary">
                                        <td colspan="3" class="text-end"><strong>{{ __('Total') }}:</strong></td>
                                        <td><strong>${{ number_format($order->total_amount, 2) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                
                {{-- Shipping Address --}}
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Shipping Information') }}</h5>
                    </div>
                    <div class="card-body">
                        @if($order->shippingAddress)
                            <p class="mb-1"><strong>{{ __('Shipping Address') }}:</strong></p>
                            <p class="mb-0">
                                {{ $order->shippingAddress->street_address }}<br>
                                {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->postal_code }}<br>
                                {{ $order->shippingAddress->country }}
                            </p>
                        @endif
                    </div>
                </div>
            @endif
            
            {{-- What's Next --}}
            <div class="card mt-4 border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>{{ __('What happens next?') }}</h5>
                </div>
                <div class="card-body">
                    <ol class="mb-0">
                        <li class="mb-2">{{ __('We will process your order within 24 hours') }}</li>
                        <li class="mb-2">{{ __('You will receive an email when your order is shipped') }}</li>
                        <li class="mb-2">{{ __('Track your order status in "My Orders" section') }}</li>
                        <li class="mb-0">{{ __('Delivery will be made within 3-5 business days') }}</li>
                    </ol>
                </div>
            </div>
            
            {{-- Support --}}
            <div class="text-center mt-4">
                <p class="text-muted">
                    {{ __('Need help?') }} 
                    <a href="mailto:support@store.com">{{ __('Contact Support') }}</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
