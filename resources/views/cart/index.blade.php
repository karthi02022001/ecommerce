@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">{{ __('Shopping Cart') }}</h1>
    
    @if(session('cart') && count(session('cart')) > 0)
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Subtotal') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total = 0;
                                @endphp
                                @foreach(session('cart') as $id => $item)
                                    @php
                                        $subtotal = $item['price'] * $item['quantity'];
                                        $total += $subtotal;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item['image'])
                                                    <img src="{{ asset('storage/' . $item['image']) }}" 
                                                         alt="{{ $item['name'] }}" 
                                                         class="img-thumbnail me-3" 
                                                         style="width: 80px; height: 80px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center me-3" 
                                                         style="width: 80px; height: 80px;">
                                                        <i class="bi bi-image" style="font-size: 2rem; color: #ccc;"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h6 class="mb-0">{{ $item['name'] }}</h6>
                                                    @if(isset($item['sku']))
                                                        <small class="text-muted">{{ __('SKU') }}: {{ $item['sku'] }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>${{ number_format($item['price'], 2) }}</td>
                                        <td>
                                            <form action="{{ route('cart.update', $id) }}" method="POST" class="d-flex align-items-center" style="max-width: 150px;">
                                                @csrf
                                                @method('PUT')
                                                <input type="number" 
                                                       name="quantity" 
                                                       value="{{ $item['quantity'] }}" 
                                                       min="1" 
                                                       max="{{ $item['stock'] ?? 999 }}"
                                                       class="form-control form-control-sm me-2">
                                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-arrow-clockwise"></i>
                                                </button>
                                            </form>
                                        </td>
                                        <td><strong>${{ number_format($subtotal, 2) }}</strong></td>
                                        <td>
                                            <form action="{{ route('cart.remove', $id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('{{ __('Remove this item?') }}')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <div class="d-flex justify-content-between mt-3">
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>{{ __('Continue Shopping') }}
                            </a>
                            <form action="{{ route('cart.clear') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" 
                                        onclick="return confirm('{{ __('Clear entire cart?') }}')">
                                    <i class="bi bi-trash me-2"></i>{{ __('Clear Cart') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('Order Summary') }}</h5>
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('Subtotal') }}</span>
                            <span>${{ number_format($total, 2) }}</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('Shipping') }}</span>
                            <span class="text-muted">{{ __('Calculated at checkout') }}</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('Tax') }}</span>
                            <span class="text-muted">{{ __('Calculated at checkout') }}</span>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <strong>{{ __('Total') }}</strong>
                            <strong class="text-primary">${{ number_format($total, 2) }}</strong>
                        </div>
                        
                        <a href="{{ route('checkout.index') }}" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-lock me-2"></i>{{ __('Proceed to Checkout') }}
                        </a>
                        
                        <div class="text-center">
                            <small class="text-muted">
                                <i class="bi bi-shield-check me-1"></i>{{ __('Secure checkout') }}
                            </small>
                        </div>
                    </div>
                </div>
                
                {{-- Promo Code --}}
                <div class="card mt-3">
                    <div class="card-body">
                        <h6 class="card-title">{{ __('Have a promo code?') }}</h6>
                        <form action="{{ route('cart.apply-coupon') }}" method="POST">
                            @csrf
                            <div class="input-group">
                                <input type="text" 
                                       name="coupon_code" 
                                       class="form-control" 
                                       placeholder="{{ __('Enter code') }}">
                                <button type="submit" class="btn btn-outline-primary">{{ __('Apply') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-cart-x" style="font-size: 5rem; color: #ccc;"></i>
            <h3 class="mt-3">{{ __('Your cart is empty') }}</h3>
            <p class="text-muted">{{ __('Add some products to get started!') }}</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary mt-3">
                <i class="bi bi-shop me-2"></i>{{ __('Start Shopping') }}
            </a>
        </div>
    @endif
</div>
@endsection
