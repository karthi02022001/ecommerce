@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">{{ __('Checkout') }}</h1>
    
    @if(!auth()->check())
        <div class="alert alert-info">
            {{ __('Please') }} <a href="{{ route('login') }}">{{ __('login') }}</a> {{ __('or') }} 
            <a href="{{ route('register') }}">{{ __('register') }}</a> {{ __('to complete your order.') }}
        </div>
    @endif
    
    @if(session('cart') && count(session('cart')) > 0)
        <form action="{{ route('checkout.process') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-lg-8">
                    {{-- Shipping Address --}}
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-truck me-2"></i>{{ __('Shipping Address') }}</h5>
                        </div>
                        <div class="card-body">
                            @auth
                                @if($addresses->where('address_type', 'shipping')->count() > 0)
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Select saved address') }}</label>
                                        @foreach($addresses->where('address_type', 'shipping') as $address)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" 
                                                       type="radio" 
                                                       name="shipping_address_id" 
                                                       id="shipping_{{ $address->id }}" 
                                                       value="{{ $address->id }}"
                                                       {{ $address->is_default ? 'checked' : '' }}>
                                                <label class="form-check-label" for="shipping_{{ $address->id }}">
                                                    <strong>{{ $address->street_address }}</strong><br>
                                                    {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}<br>
                                                    {{ $address->country }}
                                                    @if($address->is_default)
                                                        <span class="badge bg-primary">{{ __('Default') }}</span>
                                                    @endif
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <hr>
                                    <p class="mb-2"><strong>{{ __('Or enter new address:') }}</strong></p>
                                @endif
                            @endauth
                            
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="shipping_street" class="form-label">{{ __('Street Address') }} *</label>
                                    <input type="text" 
                                           class="form-control @error('shipping_street_address') is-invalid @enderror" 
                                           id="shipping_street" 
                                           name="shipping_street_address" 
                                           value="{{ old('shipping_street_address') }}" 
                                           required>
                                    @error('shipping_street_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="shipping_city" class="form-label">{{ __('City') }} *</label>
                                    <input type="text" 
                                           class="form-control @error('shipping_city') is-invalid @enderror" 
                                           id="shipping_city" 
                                           name="shipping_city" 
                                           value="{{ old('shipping_city') }}" 
                                           required>
                                    @error('shipping_city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="shipping_state" class="form-label">{{ __('State/Province') }} *</label>
                                    <input type="text" 
                                           class="form-control @error('shipping_state') is-invalid @enderror" 
                                           id="shipping_state" 
                                           name="shipping_state" 
                                           value="{{ old('shipping_state') }}" 
                                           required>
                                    @error('shipping_state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="shipping_postal" class="form-label">{{ __('Postal Code') }} *</label>
                                    <input type="text" 
                                           class="form-control @error('shipping_postal_code') is-invalid @enderror" 
                                           id="shipping_postal" 
                                           name="shipping_postal_code" 
                                           value="{{ old('shipping_postal_code') }}" 
                                           required>
                                    @error('shipping_postal_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="shipping_country" class="form-label">{{ __('Country') }} *</label>
                                    <input type="text" 
                                           class="form-control @error('shipping_country') is-invalid @enderror" 
                                           id="shipping_country" 
                                           name="shipping_country" 
                                           value="{{ old('shipping_country') }}" 
                                           required>
                                    @error('shipping_country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Billing Address --}}
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>{{ __('Billing Address') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-3">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="same_as_shipping" 
                                       name="billing_same_as_shipping" 
                                       value="1" 
                                       checked>
                                <label class="form-check-label" for="same_as_shipping">
                                    {{ __('Billing address same as shipping') }}
                                </label>
                            </div>
                            
                            <div id="billing_fields" style="display: none;">
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label for="billing_street" class="form-label">{{ __('Street Address') }}</label>
                                        <input type="text" 
                                               class="form-control @error('billing_street_address') is-invalid @enderror" 
                                               id="billing_street" 
                                               name="billing_street_address" 
                                               value="{{ old('billing_street_address') }}">
                                        @error('billing_street_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="billing_city" class="form-label">{{ __('City') }}</label>
                                        <input type="text" 
                                               class="form-control @error('billing_city') is-invalid @enderror" 
                                               id="billing_city" 
                                               name="billing_city" 
                                               value="{{ old('billing_city') }}">
                                        @error('billing_city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="billing_state" class="form-label">{{ __('State/Province') }}</label>
                                        <input type="text" 
                                               class="form-control @error('billing_state') is-invalid @enderror" 
                                               id="billing_state" 
                                               name="billing_state" 
                                               value="{{ old('billing_state') }}">
                                        @error('billing_state')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="billing_postal" class="form-label">{{ __('Postal Code') }}</label>
                                        <input type="text" 
                                               class="form-control @error('billing_postal_code') is-invalid @enderror" 
                                               id="billing_postal" 
                                               name="billing_postal_code" 
                                               value="{{ old('billing_postal_code') }}">
                                        @error('billing_postal_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="billing_country" class="form-label">{{ __('Country') }}</label>
                                        <input type="text" 
                                               class="form-control @error('billing_country') is-invalid @enderror" 
                                               id="billing_country" 
                                               name="billing_country" 
                                               value="{{ old('billing_country') }}">
                                        @error('billing_country')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Payment Method --}}
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>{{ __('Payment Method') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                                <label class="form-check-label" for="cod">
                                    <strong>{{ __('Cash on Delivery') }}</strong>
                                    <small class="d-block text-muted">{{ __('Pay when you receive') }}</small>
                                </label>
                            </div>
                            
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="card" value="card">
                                <label class="form-check-label" for="card">
                                    <strong>{{ __('Credit/Debit Card') }}</strong>
                                    <small class="d-block text-muted">{{ __('Pay securely online') }}</small>
                                </label>
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="bank" value="bank_transfer">
                                <label class="form-check-label" for="bank">
                                    <strong>{{ __('Bank Transfer') }}</strong>
                                    <small class="d-block text-muted">{{ __('Transfer to our account') }}</small>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Order Notes --}}
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-chat-left-text me-2"></i>{{ __('Order Notes') }}</h5>
                        </div>
                        <div class="card-body">
                            <textarea class="form-control" 
                                      name="notes" 
                                      rows="3" 
                                      placeholder="{{ __('Any special instructions for your order? (Optional)') }}">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
                
                {{-- Order Summary --}}
                <div class="col-lg-4">
                    <div class="card position-sticky" style="top: 20px;">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('Order Summary') }}</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $subtotal = 0;
                            @endphp
                            
                            @foreach(session('cart') as $id => $item)
                                @php
                                    $subtotal += $item['price'] * $item['quantity'];
                                @endphp
                                <div class="d-flex justify-content-between mb-2">
                                    <small>{{ $item['name'] }} × {{ $item['quantity'] }}</small>
                                    <small>${{ number_format($item['price'] * $item['quantity'], 2) }}</small>
                                </div>
                            @endforeach
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ __('Subtotal') }}</span>
                                <span>${{ number_format($subtotal, 2) }}</span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ __('Shipping') }}</span>
                                <span>$10.00</span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ __('Tax') }} (10%)</span>
                                <span>${{ number_format($subtotal * 0.1, 2) }}</span>
                            </div>
                            
                            <hr>
                            
                            @php
                                $tax = $subtotal * 0.1;
                                $shipping = 10.00;
                                $total = $subtotal + $tax + $shipping;
                            @endphp
                            
                            <div class="d-flex justify-content-between mb-3">
                                <strong>{{ __('Total') }}</strong>
                                <strong class="text-primary">${{ number_format($total, 2) }}</strong>
                            </div>
                            
                            @auth
                                <button type="submit" class="btn btn-primary w-100 mb-2">
                                    <i class="bi bi-lock me-2"></i>{{ __('Place Order') }}
                                </button>
                            @else
                                <div class="alert alert-warning mb-2">
                                    {{ __('Please login to place order') }}
                                </div>
                                <a href="{{ route('login') }}" class="btn btn-primary w-100 mb-2">
                                    {{ __('Login to Continue') }}
                                </a>
                            @endauth
                            
                            <div class="text-center">
                                <small class="text-muted">
                                    <i class="bi bi-shield-check me-1"></i>{{ __('Secure checkout') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @else
        <div class="alert alert-warning">
            {{ __('Your cart is empty.') }} <a href="{{ route('products.index') }}">{{ __('Continue shopping') }}</a>
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.getElementById('same_as_shipping').addEventListener('change', function() {
        document.getElementById('billing_fields').style.display = this.checked ? 'none' : 'block';
    });
</script>
@endpush
@endsection
