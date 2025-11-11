@extends('layouts.app')

@section('title', __('Shopping Cart'))

@section('content')
<div class="container py-5">
    <div class="page-header mb-4">
        <h1 class="page-title">
            <i class="bi bi-cart3 me-2"></i>{{ __('Shopping Cart') }}
        </h1>
        <p class="text-muted">{{ __('Review your items before checkout') }}</p>
    </div>
    
    @if($cartItems->count() > 0)
        <div class="row g-4">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">{{ __('Cart Items') }} ({{ $cartCount }})</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0">{{ __('Product') }}</th>
                                        <th class="border-0 text-center">{{ __('Price') }}</th>
                                        <th class="border-0 text-center">{{ __('Quantity') }}</th>
                                        <th class="border-0 text-center">{{ __('Subtotal') }}</th>
                                        <th class="border-0 text-center">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cartItems as $cartItem)
                                        <tr>
                                            <!-- Product Info -->
                                            <td class="py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="product-img me-3">
                                                        @if($cartItem->product->primaryImage)
                                                            <img src="{{ asset('storage/' . $cartItem->product->primaryImage->image_path) }}" 
                                                                 alt="{{ $cartItem->product->name() }}" 
                                                                 class="img-thumbnail rounded"
                                                                 style="width: 80px; height: 80px; object-fit: cover;">
                                                        @else
                                                            <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                                                 style="width: 80px; height: 80px;">
                                                                <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">
                                                            <a href="{{ route('products.show', $cartItem->product->slug) }}" 
                                                               class="text-decoration-none text-dark">
                                                                {{ $cartItem->product->name() }}
                                                            </a>
                                                        </h6>
                                                        <small class="text-muted">
                                                            <i class="bi bi-upc me-1"></i>{{ __('SKU') }}: {{ $cartItem->product->sku }}
                                                        </small>
                                                        <br>
                                                        <small class="badge bg-light text-dark">
                                                            <i class="bi bi-box-seam me-1"></i>{{ __('Stock') }}: {{ $cartItem->product->stock_quantity }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            <!-- Price -->
                                            <td class="text-center">
                                                <strong class="text-primary">₹{{ number_format($cartItem->price, 2) }}</strong>
                                            </td>
                                            
                                            <!-- Quantity -->
                                            <td class="text-center">
                                                <form action="{{ route('cart.update', $cartItem->id) }}" 
                                                      method="POST" 
                                                      class="d-inline-block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="input-group input-group-sm" style="width: 130px; margin: 0 auto;">
                                                        <button class="btn btn-outline-secondary" 
                                                                type="button"
                                                                onclick="let input = this.parentNode.querySelector('input[type=number]'); if(input.value > 1) { input.stepDown(); }">
                                                            <i class="bi bi-dash"></i>
                                                        </button>
                                                        <input type="number" 
                                                               name="quantity" 
                                                               value="{{ $cartItem->quantity }}" 
                                                               min="1" 
                                                               max="{{ $cartItem->product->stock_quantity }}"
                                                               class="form-control text-center"
                                                               style="width: 60px;">
                                                        <button class="btn btn-outline-secondary" 
                                                                type="button"
                                                                onclick="let input = this.parentNode.querySelector('input[type=number]'); if(input.value < input.max) { input.stepUp(); }">
                                                            <i class="bi bi-plus"></i>
                                                        </button>
                                                        <button class="btn btn-outline-primary" type="submit" title="{{ __('Update') }}">
                                                            <i class="bi bi-arrow-repeat"></i>
                                                        </button>
                                                    </div>
                                                </form>
                                            </td>
                                            
                                            <!-- Subtotal -->
                                            <td class="text-center">
                                                <strong>₹{{ number_format($cartItem->subtotal(), 2) }}</strong>
                                            </td>
                                            
                                            <!-- Actions -->
                                            <td class="text-center">
                                                <form action="{{ route('cart.remove', $cartItem->id) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger"
                                                            onclick="return confirm('{{ __('Remove this item from cart?') }}')"
                                                            title="{{ __('Remove') }}">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white py-3">
                        <form action="{{ route('cart.clear') }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="btn btn-outline-danger btn-sm"
                                    onclick="return confirm('{{ __('Clear all items from cart?') }}')">
                                <i class="bi bi-trash me-1"></i>{{ __('Clear Cart') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 100px;">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">{{ __('Cart Summary') }}</h5>
                    </div>
                    <div class="card-body">
                        <!-- Subtotal -->
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">{{ __('Subtotal') }}</span>
                            <strong>₹{{ number_format($cartTotal, 2) }}</strong>
                        </div>
                        
                        <!-- Estimated Shipping -->
                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                            <span class="text-muted">
                                {{ __('Shipping') }}
                                <i class="bi bi-info-circle" 
                                   data-bs-toggle="tooltip" 
                                   data-bs-placement="top" 
                                   title="{{ __('Calculated at checkout') }}"></i>
                            </span>
                            <span class="text-muted">{{ __('Calculated at checkout') }}</span>
                        </div>
                        
                        <!-- Total -->
                        <div class="d-flex justify-content-between mb-4">
                            <h5 class="mb-0">{{ __('Total') }}</h5>
                            <h5 class="mb-0 text-primary">₹{{ number_format($cartTotal, 2) }}</h5>
                        </div>
                        
                        <!-- Checkout Button -->
                        <div class="d-grid gap-2">
                            @auth
                                <a href="{{ route('checkout.index') }}" 
                                   class="btn btn-lg text-white fw-bold py-3"
                                   style="background: linear-gradient(135deg, #20b2aa 0%, #008b8b 100%);">
                                    <i class="bi bi-lock-fill me-2"></i>{{ __('Proceed to Checkout') }}
                                </a>
                            @else
                                <div class="alert alert-warning border-0 mb-3" style="background-color: #fff3cd;">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    {{ __('Please login to checkout') }}
                                </div>
                                <a href="{{ route('login') }}" 
                                   class="btn btn-lg text-white fw-bold py-3"
                                   style="background: linear-gradient(135deg, #20b2aa 0%, #008b8b 100%);">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>{{ __('Login to Continue') }}
                                </a>
                            @endauth
                            
                            <a href="{{ route('products.index') }}" 
                               class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-arrow-left me-2"></i>{{ __('Continue Shopping') }}
                            </a>
                        </div>
                        
                        <!-- Security Badge -->
                        <div class="text-center mt-4">
                            <div class="d-flex align-items-center justify-content-center text-muted small">
                                <i class="bi bi-shield-check me-2" style="color: #20b2aa;"></i>
                                <span>{{ __('Secure SSL encrypted checkout') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Info -->
                    <div class="card-footer bg-light">
                        <div class="small text-muted">
                            <div class="mb-2">
                                <i class="bi bi-truck me-2" style="color: #20b2aa;"></i>
                                <strong>{{ __('Free Shipping') }}</strong> {{ __('on orders over ₹500') }}
                            </div>
                            <div class="mb-2">
                                <i class="bi bi-arrow-clockwise me-2" style="color: #20b2aa;"></i>
                                <strong>{{ __('Easy Returns') }}</strong> {{ __('within 30 days') }}
                            </div>
                            <div>
                                <i class="bi bi-headset me-2" style="color: #20b2aa;"></i>
                                <strong>{{ __('24/7 Support') }}</strong> {{ __('Customer service') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Coupon Code (Optional) -->
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0">
                            <i class="bi bi-tag me-2"></i>{{ __('Have a Coupon?') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('cart.applyCoupon') }}" method="POST">
                            @csrf
                            <div class="input-group">
                                <input type="text" 
                                       name="coupon_code" 
                                       class="form-control" 
                                       placeholder="{{ __('Enter coupon code') }}"
                                       required>
                                <button class="btn btn-outline-secondary" type="submit">
                                    {{ __('Apply') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty Cart -->
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-cart-x display-1 mb-4" style="color: #20b2aa;"></i>
                        <h3 class="mb-3">{{ __('Your cart is empty') }}</h3>
                        <p class="text-muted mb-4">{{ __('Looks like you haven\'t added any products to your cart yet.') }}</p>
                        <a href="{{ route('products.index') }}" 
                           class="btn btn-lg text-white fw-bold px-5"
                           style="background: linear-gradient(135deg, #20b2aa 0%, #008b8b 100%);">
                            <i class="bi bi-bag me-2"></i>{{ __('Start Shopping') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush
@endsection