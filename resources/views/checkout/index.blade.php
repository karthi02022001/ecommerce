@extends('layouts.app')

@section('title', __('Checkout'))

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Progress Steps -->
        <div class="col-12 mb-4">
            <div class="checkout-progress">
                <div class="progress-step completed">
                    <div class="step-number">1</div>
                    <div class="step-label">{{ __('Cart') }}</div>
                </div>
                <div class="progress-line completed"></div>
                <div class="progress-step active">
                    <div class="step-number">2</div>
                    <div class="step-label">{{ __('Checkout') }}</div>
                </div>
                <div class="progress-line"></div>
                <div class="progress-step">
                    <div class="step-number">3</div>
                    <div class="step-label">{{ __('Complete') }}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
                @csrf

                <!-- Shipping Address Section -->
                <div class="checkout-card mb-4">
                    <div class="checkout-card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-truck me-2"></i>{{ __('Shipping Address') }}
                        </h5>
                    </div>
                    <div class="checkout-card-body">
                        <!-- Saved Shipping Addresses -->
                        @if($addresses->where('address_type', 'shipping')->count() > 0)
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark">
                                    <i class="bi bi-bookmark-check me-2"></i>{{ __('Select saved address') }}
                                </label>
                                <div class="row g-3">
                                    @foreach($addresses->where('address_type', 'shipping') as $address)
                                        <div class="col-md-6">
                                            <div class="saved-address-card {{ $address->is_default ? 'selected' : '' }}">
                                                <input class="form-check-input" 
                                                       type="radio" 
                                                       name="saved_shipping_address_id" 
                                                       id="shipping_{{ $address->id }}" 
                                                       value="{{ $address->id }}"
                                                       onclick="fillShippingAddress({{ $address->id }})"
                                                       {{ $address->is_default ? 'checked' : '' }}>
                                                <label class="form-check-label w-100" for="shipping_{{ $address->id }}">
                                                    @if($address->is_default)
                                                        <span class="address-badge">{{ __('Default') }}</span>
                                                    @endif
                                                    <div class="address-content">
                                                        <div class="address-name">{{ $address->full_name }}</div>
                                                        <div class="address-details">
                                                            {{ $address->address_line1 }}<br>
                                                            @if($address->address_line2)
                                                                {{ $address->address_line2 }}<br>
                                                            @endif
                                                            {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}<br>
                                                            {{ $address->country }}<br>
                                                            <i class="bi bi-telephone me-1"></i>{{ $address->phone }}
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <hr class="my-4">
                                <p class="text-muted mb-3">
                                    <i class="bi bi-info-circle me-1"></i>
                                    {{ __('Or enter address manually below') }}
                                </p>
                            </div>
                        @endif

                        <!-- Manual Shipping Address Form -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="shipping_first_name" class="form-label">{{ __('First Name') }} <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('shipping_first_name') is-invalid @enderror" 
                                       id="shipping_first_name" 
                                       name="shipping_first_name" 
                                       value="{{ old('shipping_first_name') }}" 
                                       required>
                                @error('shipping_first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="shipping_last_name" class="form-label">{{ __('Last Name') }} <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('shipping_last_name') is-invalid @enderror" 
                                       id="shipping_last_name" 
                                       name="shipping_last_name" 
                                       value="{{ old('shipping_last_name') }}" 
                                       required>
                                @error('shipping_last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="shipping_address_line_1" class="form-label">{{ __('Address Line 1') }} <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('shipping_address_line_1') is-invalid @enderror" 
                                       id="shipping_address_line_1" 
                                       name="shipping_address_line_1" 
                                       value="{{ old('shipping_address_line_1') }}" 
                                       required>
                                @error('shipping_address_line_1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="shipping_address_line_2" class="form-label">{{ __('Address Line 2') }} <span class="text-muted">({{ __('Optional') }})</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       id="shipping_address_line_2" 
                                       name="shipping_address_line_2" 
                                       value="{{ old('shipping_address_line_2') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="shipping_city" class="form-label">{{ __('City') }} <span class="text-danger">*</span></label>
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
                            <div class="col-md-6">
                                <label for="shipping_state" class="form-label">{{ __('State/Province') }} <span class="text-danger">*</span></label>
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
                            <div class="col-md-6">
                                <label for="shipping_postal_code" class="form-label">{{ __('Postal Code') }} <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('shipping_postal_code') is-invalid @enderror" 
                                       id="shipping_postal_code" 
                                       name="shipping_postal_code" 
                                       value="{{ old('shipping_postal_code') }}" 
                                       required>
                                @error('shipping_postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="shipping_country" class="form-label">{{ __('Country') }} <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('shipping_country') is-invalid @enderror" 
                                       id="shipping_country" 
                                       name="shipping_country" 
                                       value="{{ old('shipping_country', 'India') }}" 
                                       required>
                                @error('shipping_country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="shipping_phone" class="form-label">{{ __('Phone') }} <span class="text-danger">*</span></label>
                                <input type="tel" 
                                       class="form-control @error('shipping_phone') is-invalid @enderror" 
                                       id="shipping_phone" 
                                       name="shipping_phone" 
                                       value="{{ old('shipping_phone') }}" 
                                       required>
                                @error('shipping_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Billing Address Section -->
                <div class="checkout-card mb-4">
                    <div class="checkout-card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-credit-card me-2"></i>{{ __('Billing Address') }}
                        </h5>
                    </div>
                    <div class="checkout-card-body">
                        <!-- Same as Shipping Checkbox -->
                        <div class="form-check mb-4 same-as-shipping-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="same_as_shipping" 
                                   name="same_as_shipping"
                                   onchange="toggleBillingAddress()">
                            <label class="form-check-label" for="same_as_shipping">
                                {{ __('Same as shipping address') }}
                            </label>
                        </div>

                        <div id="billingAddressSection">
                            <!-- Saved Billing Addresses -->
                            @if($addresses->where('address_type', 'billing')->count() > 0)
                                <div class="mb-4">
                                    <label class="form-label fw-semibold text-dark">
                                        <i class="bi bi-bookmark-check me-2"></i>{{ __('Select saved billing address') }}
                                    </label>
                                    <div class="row g-3">
                                        @foreach($addresses->where('address_type', 'billing') as $address)
                                            <div class="col-md-6">
                                                <div class="saved-address-card {{ $address->is_default ? 'selected' : '' }}">
                                                    <input class="form-check-input" 
                                                           type="radio" 
                                                           name="saved_billing_address_id" 
                                                           id="billing_{{ $address->id }}" 
                                                           value="{{ $address->id }}"
                                                           onclick="fillBillingAddress({{ $address->id }})"
                                                           {{ $address->is_default ? 'checked' : '' }}>
                                                    <label class="form-check-label w-100" for="billing_{{ $address->id }}">
                                                        @if($address->is_default)
                                                            <span class="address-badge">{{ __('Default') }}</span>
                                                        @endif
                                                        <div class="address-content">
                                                            <div class="address-name">{{ $address->full_name }}</div>
                                                            <div class="address-details">
                                                                {{ $address->address_line1 }}<br>
                                                                @if($address->address_line2)
                                                                    {{ $address->address_line2 }}<br>
                                                                @endif
                                                                {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}<br>
                                                                {{ $address->country }}<br>
                                                                <i class="bi bi-telephone me-1"></i>{{ $address->phone }}
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <hr class="my-4">
                                    <p class="text-muted mb-3">
                                        <i class="bi bi-info-circle me-1"></i>
                                        {{ __('Or enter billing address manually below') }}
                                    </p>
                                </div>
                            @endif

                            <!-- Manual Billing Address Form -->
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="billing_first_name" class="form-label">{{ __('First Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('billing_first_name') is-invalid @enderror" 
                                           id="billing_first_name" 
                                           name="billing_first_name" 
                                           value="{{ old('billing_first_name') }}">
                                    @error('billing_first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="billing_last_name" class="form-label">{{ __('Last Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('billing_last_name') is-invalid @enderror" 
                                           id="billing_last_name" 
                                           name="billing_last_name" 
                                           value="{{ old('billing_last_name') }}">
                                    @error('billing_last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label for="billing_address_line_1" class="form-label">{{ __('Address Line 1') }} <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('billing_address_line_1') is-invalid @enderror" 
                                           id="billing_address_line_1" 
                                           name="billing_address_line_1" 
                                           value="{{ old('billing_address_line_1') }}">
                                    @error('billing_address_line_1')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label for="billing_address_line_2" class="form-label">{{ __('Address Line 2') }} <span class="text-muted">({{ __('Optional') }})</span></label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="billing_address_line_2" 
                                           name="billing_address_line_2" 
                                           value="{{ old('billing_address_line_2') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="billing_city" class="form-label">{{ __('City') }} <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('billing_city') is-invalid @enderror" 
                                           id="billing_city" 
                                           name="billing_city" 
                                           value="{{ old('billing_city') }}">
                                    @error('billing_city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="billing_state" class="form-label">{{ __('State/Province') }} <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('billing_state') is-invalid @enderror" 
                                           id="billing_state" 
                                           name="billing_state" 
                                           value="{{ old('billing_state') }}">
                                    @error('billing_state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="billing_postal_code" class="form-label">{{ __('Postal Code') }} <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('billing_postal_code') is-invalid @enderror" 
                                           id="billing_postal_code" 
                                           name="billing_postal_code" 
                                           value="{{ old('billing_postal_code') }}">
                                    @error('billing_postal_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="billing_country" class="form-label">{{ __('Country') }} <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('billing_country') is-invalid @enderror" 
                                           id="billing_country" 
                                           name="billing_country" 
                                           value="{{ old('billing_country', 'India') }}">
                                    @error('billing_country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="billing_phone" class="form-label">{{ __('Phone') }} <span class="text-danger">*</span></label>
                                    <input type="tel" 
                                           class="form-control @error('billing_phone') is-invalid @enderror" 
                                           id="billing_phone" 
                                           name="billing_phone" 
                                           value="{{ old('billing_phone') }}">
                                    @error('billing_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Method Section -->
                <div class="checkout-card mb-4">
                    <div class="checkout-card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-wallet2 me-2"></i>{{ __('Payment Method') }}
                        </h5>
                    </div>
                    <div class="checkout-card-body">
                        <div class="payment-method-option mb-3">
                            <input class="form-check-input" 
                                   type="radio" 
                                   name="payment_method" 
                                   id="cash_on_delivery" 
                                   value="cash_on_delivery" 
                                   checked>
                            <label class="form-check-label" for="cash_on_delivery">
                                <i class="bi bi-cash-coin me-2"></i>{{ __('Cash on Delivery') }}
                            </label>
                        </div>
                        <div class="payment-method-option">
                            <input class="form-check-input" 
                                   type="radio" 
                                   name="payment_method" 
                                   id="stripe" 
                                   value="stripe">
                            <label class="form-check-label" for="stripe">
                                <i class="bi bi-credit-card me-2"></i>{{ __('Credit/Debit Card') }}
                            </label>
                        </div>
                        
                        <!-- Stripe Card Element (Hidden by default) -->
                        <div id="stripe-card-section" style="display: none;" class="mt-4">
                            <div class="stripe-card-container">
                                <label class="form-label fw-semibold">{{ __('Card Details') }}</label>
                                <div id="card-element" class="stripe-card-element">
                                    <!-- Stripe Card Element will be inserted here -->
                                </div>
                                <div id="card-errors" class="text-danger mt-2" role="alert"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Notes Section -->
                <div class="checkout-card mb-4">
                    <div class="checkout-card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-chat-left-text me-2"></i>{{ __('Order Notes') }} <span class="text-muted fw-normal">({{ __('Optional') }})</span>
                        </h5>
                    </div>
                    <div class="checkout-card-body">
                        <textarea class="form-control" 
                                  name="notes" 
                                  rows="3" 
                                  placeholder="{{ __('Special instructions for your order...') }}">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </form>
        </div>

        <!-- Order Summary Sidebar -->
        <div class="col-lg-4">
            <div class="checkout-summary sticky-top">
                <div class="checkout-card-header">
                    <h5 class="mb-0">{{ __('Order Summary') }}</h5>
                </div>
                <div class="checkout-card-body">
                    <!-- Cart Items -->
                    <div class="order-items-list mb-3">
                        @foreach($cartItems as $item)
                            <div class="order-item-card">
                                @if($item->product->primaryImage)
                                    <img src="{{ asset('storage/' . $item->product->primaryImage->image_path) }}" 
                                         alt="{{ $item->product->name() }}" 
                                         class="order-item-image">
                                @else
                                    <div class="order-item-image-placeholder">
                                        <i class="bi bi-image"></i>
                                    </div>
                                @endif
                                <div class="order-item-details">
                                    <h6 class="order-item-name">{{ $item->product->name() }}</h6>
                                    <small class="order-item-qty">{{ __('Qty') }}: {{ $item->quantity }}</small>
                                </div>
                                <div class="order-item-price">
                                    {{ $settings->currency_symbol }}{{ number_format($item->subtotal(), 2) }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pricing Summary -->
                    <div class="pricing-summary">
                        <div class="pricing-row">
                            <span>{{ __('Subtotal') }}</span>
                            <strong>{{ $settings->currency_symbol }}{{ number_format($subtotal, 2) }}</strong>
                        </div>
                        <div class="pricing-row">
                            <span>{{ __('Shipping') }}</span>
                            <strong>
                                @if($shippingCost == 0)
                                    <span class="text-success">{{ __('Free') }}</span>
                                @else
                                    {{ $settings->currency_symbol }}{{ number_format($shippingCost, 2) }}
                                @endif
                            </strong>
                        </div>
                        <div class="pricing-row">
                            <span>{{ __('Tax') }} ({{ $settings->tax_rate }}%)</span>
                            <strong>{{ $settings->currency_symbol }}{{ number_format($taxAmount, 2) }}</strong>
                        </div>
                        <div class="pricing-divider"></div>
                        <div class="pricing-row pricing-total">
                            <strong>{{ __('Total') }}</strong>
                            <strong class="total-amount">{{ $settings->currency_symbol }}{{ number_format($total, 2) }}</strong>
                        </div>
                    </div>

                    <!-- Place Order Button -->
                    <button type="submit" 
                            form="checkoutForm" 
                            class="btn-place-order">
                        <i class="bi bi-check-circle me-2"></i>{{ __('Place Order') }}
                    </button>

                    <div class="text-center mt-3">
                        <a href="{{ route('cart.index') }}" class="back-to-cart-link">
                            <i class="bi bi-arrow-left me-1"></i>{{ __('Return to Cart') }}
                        </a>
                    </div>
                </div>
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
    --border-color: #e0e0e0;
    --light-bg: #f8f9fa;
}

/* Progress Steps */
.checkout-progress {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 30px 0;
    background: white;
    border-radius: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.progress-step {
    text-align: center;
    position: relative;
}

.step-number {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: var(--light-bg);
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1.2rem;
    margin: 0 auto 10px;
    border: 3px solid var(--light-bg);
    transition: all 0.3s ease;
}

.progress-step.completed .step-number,
.progress-step.active .step-number {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.progress-step.active .step-number {
    box-shadow: 0 0 0 4px rgba(32, 178, 170, 0.2);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { box-shadow: 0 0 0 4px rgba(32, 178, 170, 0.2); }
    50% { box-shadow: 0 0 0 8px rgba(32, 178, 170, 0.1); }
}

.step-label {
    font-size: 0.9rem;
    color: #6c757d;
    font-weight: 500;
}

.progress-step.completed .step-label,
.progress-step.active .step-label {
    color: var(--accent-color);
    font-weight: 600;
}

.progress-line {
    width: 100px;
    height: 3px;
    background: var(--light-bg);
    margin: 0 20px;
    position: relative;
    top: -20px;
}

.progress-line.completed {
    background: var(--primary-color);
}

/* Checkout Cards */
.checkout-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: all 0.3s ease;
}

.checkout-card:hover {
    box-shadow: 0 5px 25px rgba(0,0,0,0.12);
}

.checkout-card-header {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
    padding: 20px 25px;
}

.checkout-card-header h5 {
    margin: 0;
    font-weight: 600;
    font-size: 1.1rem;
}

.checkout-card-body {
    padding: 30px 25px;
}

/* Saved Address Cards */
.saved-address-card {
    border: 2px solid var(--border-color);
    border-radius: 12px;
    padding: 20px;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    background: white;
}

.saved-address-card:hover {
    border-color: var(--primary-color);
    background: rgba(32, 178, 170, 0.02);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(32, 178, 170, 0.1);
}

.saved-address-card.selected {
    border-color: var(--primary-color);
    background: rgba(32, 178, 170, 0.05);
}

.saved-address-card input[type="radio"] {
    position: absolute;
    top: 20px;
    right: 20px;
    width: 20px;
    height: 20px;
    accent-color: var(--primary-color);
}

.saved-address-card label {
    cursor: pointer;
    margin-bottom: 0;
    padding-right: 40px;
    position: relative;
}

.address-content {
    width: 100%;
}

.address-name {
    font-weight: 600;
    font-size: 1.05rem;
    color: var(--accent-color);
    margin-bottom: 8px;
    padding-top: 25px; /* Space for badge */
}

.address-details {
    font-size: 0.9rem;
    color: #6c757d;
    line-height: 1.7;
}

.address-badge {
    position: absolute;
    top: 0;
    left: 0;
    background: var(--primary-color);
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    z-index: 1;
}

/* Form Controls */
.form-control,
.form-select {
    border: 2px solid var(--border-color);
    border-radius: 8px;
    padding: 12px 15px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.form-control:focus,
.form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 4px rgba(32, 178, 170, 0.1);
}

.form-label {
    font-weight: 500;
    color: var(--accent-color);
    margin-bottom: 8px;
    font-size: 0.95rem;
}

/* Same as Shipping Checkbox */
.same-as-shipping-check {
    background: var(--light-bg);
    padding: 15px 20px;
    border-radius: 10px;
    border: 2px dashed var(--border-color);
}

.same-as-shipping-check .form-check-input {
    width: 20px;
    height: 20px;
    accent-color: var(--primary-color);
}

.same-as-shipping-check .form-check-label {
    font-weight: 500;
    color: var(--accent-color);
    margin-left: 10px;
}

/* Payment Method Options */
.payment-method-option {
    background: white;
    border: 2px solid var(--border-color);
    border-radius: 10px;
    padding: 15px 20px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.payment-method-option:hover {
    border-color: var(--primary-color);
    background: rgba(32, 178, 170, 0.02);
}

.payment-method-option input[type="radio"]:checked + label {
    color: var(--primary-color);
    font-weight: 600;
}

.payment-method-option .form-check-input {
    width: 20px;
    height: 20px;
    accent-color: var(--primary-color);
}

.payment-method-option .form-check-label {
    font-weight: 500;
    margin-left: 10px;
    cursor: pointer;
}

/* Stripe Card Element */
.stripe-card-container {
    background: var(--light-bg);
    padding: 20px;
    border-radius: 10px;
    border: 2px solid var(--border-color);
}

.stripe-card-element {
    background: white;
    padding: 15px;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    transition: all 0.3s ease;
}

.stripe-card-element:focus-within {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 4px rgba(32, 178, 170, 0.1);
}

#card-errors {
    font-size: 0.9rem;
    font-weight: 500;
}

/* Order Summary */
.checkout-summary {
    position: sticky;
    top: 20px;
}

.checkout-summary .checkout-card {
    background: var(--accent-color);
}

.checkout-summary .checkout-card-header {
    background: linear-gradient(135deg, #2d2d2d, var(--accent-color));
}

.checkout-summary .checkout-card-body {
    background: white;
}

/* Order Items List */
.order-items-list {
    max-height: 300px;
    overflow-y: auto;
}

.order-item-card {
    display: flex;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid var(--border-color);
}

.order-item-card:last-child {
    border-bottom: none;
}

.order-item-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
    margin-right: 15px;
}

.order-item-image-placeholder {
    width: 60px;
    height: 60px;
    background: var(--light-bg);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    margin-right: 15px;
}

.order-item-details {
    flex-grow: 1;
}

.order-item-name {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--accent-color);
    margin-bottom: 4px;
}

.order-item-qty {
    color: #6c757d;
    font-size: 0.85rem;
}

.order-item-price {
    font-weight: 600;
    color: var(--primary-color);
    font-size: 1.05rem;
}

/* Pricing Summary */
.pricing-summary {
    margin-top: 20px;
}

.pricing-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    font-size: 0.95rem;
}

.pricing-row span {
    color: #6c757d;
}

.pricing-row strong {
    color: var(--accent-color);
}

.pricing-divider {
    height: 2px;
    background: var(--border-color);
    margin: 15px 0;
}

.pricing-total {
    font-size: 1.2rem;
    margin-top: 10px;
}

.total-amount {
    color: var(--primary-color) !important;
    font-size: 1.4rem;
}

/* Place Order Button */
.btn-place-order {
    width: 100%;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
    border: none;
    padding: 16px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 1.05rem;
    margin-top: 20px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.btn-place-order:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(32, 178, 170, 0.3);
}

.btn-place-order:active {
    transform: translateY(0);
}

/* Back to Cart Link */
.back-to-cart-link {
    color: #6c757d;
    text-decoration: none;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.back-to-cart-link:hover {
    color: var(--primary-color);
}

/* Responsive */
@media (max-width: 991px) {
    .checkout-summary {
        position: static;
        margin-top: 30px;
    }
    
    .progress-line {
        width: 60px;
        margin: 0 10px;
    }
}

@media (max-width: 576px) {
    .checkout-card-body {
        padding: 20px 15px;
    }
    
    .saved-address-card {
        padding: 15px;
    }
    
    .progress-step {
        font-size: 0.85rem;
    }
    
    .step-number {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
    
    .progress-line {
        width: 40px;
    }
}
</style>
@endpush

@push('scripts')
<!-- Stripe.js SDK -->
<script src="https://js.stripe.com/v3/"></script>

<script>
// Store addresses data for JavaScript access
const savedAddresses = @json($addresses);

console.log('📦 Loaded addresses:', savedAddresses);

// Initialize Stripe

const stripe = Stripe('{{ config("services.stripe.key") }}');
const elements = stripe.elements();

// Custom styling for Stripe Card Element
const style = {
    base: {
        color: '#1a1a1a',
        fontFamily: 'Poppins, sans-serif',
        fontSmoothing: 'antialiased',
        fontSize: '16px',
        '::placeholder': {
            color: '#6c757d',
        },
    },
    invalid: {
        color: '#e74c3c',
        iconColor: '#e74c3c',
    },
};

// Create Stripe Card Element
const cardElement = elements.create('card', { style: style });

// Mount Card Element (but don't show it yet)
cardElement.mount('#card-element');

// Handle Stripe validation errors
cardElement.on('change', function(event) {
    const displayError = document.getElementById('card-errors');
    if (event.error) {
        displayError.textContent = event.error.message;
    } else {
        displayError.textContent = '';
    }
});

// Toggle Stripe card section when payment method changes
document.addEventListener('DOMContentLoaded', function() {
    const cashOnDelivery = document.getElementById('cash_on_delivery');
    const stripePayment = document.getElementById('stripe');
    const stripeCardSection = document.getElementById('stripe-card-section');
    
    // Show/hide Stripe card element based on selection
    function toggleStripeSection() {
        if (stripePayment.checked) {
            stripeCardSection.style.display = 'block';
        } else {
            stripeCardSection.style.display = 'none';
        }
    }
    
    cashOnDelivery.addEventListener('change', toggleStripeSection);
    stripePayment.addEventListener('change', toggleStripeSection);
});

// Handle form submission with Stripe
document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
    const stripePayment = document.getElementById('stripe');
    
    // If Stripe is selected, handle payment
    if (stripePayment.checked) {
        e.preventDefault();
        
        console.log('💳 Processing Stripe payment...');
        
        // Disable submit button
        const submitButton = document.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>{{ __("Processing Payment...") }}';
        
        try {
            // Create payment method
            const { error, paymentMethod } = await stripe.createPaymentMethod({
                type: 'card',
                card: cardElement,
            });
            
            if (error) {
                // Show error
                const displayError = document.getElementById('card-errors');
                displayError.textContent = error.message;
                
                // Re-enable submit button
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="bi bi-check-circle me-2"></i>{{ __("Place Order") }}';
                
                console.error('❌ Stripe error:', error);
            } else {
                // Add payment method ID to form
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'stripe_payment_method_id';
                input.value = paymentMethod.id;
                this.appendChild(input);
                
                console.log('✅ Payment method created:', paymentMethod.id);
                
                // Submit the form
                this.submit();
            }
        } catch (err) {
            console.error('❌ Payment processing error:', err);
            alert('{{ __("Payment processing failed. Please try again.") }}');
            
            // Re-enable submit button
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="bi bi-check-circle me-2"></i>{{ __("Place Order") }}';
        }
        
        return false;
    }
    
    // For Cash on Delivery, continue normally
    console.log('📤 Submitting checkout form...');
    
    const sameAsShipping = document.getElementById('same_as_shipping');
    if (sameAsShipping && sameAsShipping.checked) {
        toggleBillingAddress();
    }
});

// Fill shipping address fields when radio button is clicked
function fillShippingAddress(addressId) {
    const address = savedAddresses.find(a => a.id === addressId);
    if (!address) {
        console.error('❌ Address not found:', addressId);
        return;
    }
    
    console.log('✅ Filling shipping address:', address);
    
    // Split full name into first and last
    const nameParts = address.full_name.split(' ');
    const firstName = nameParts[0];
    const lastName = nameParts.slice(1).join(' ');
    
    // Fill form fields
    document.getElementById('shipping_first_name').value = firstName;
    document.getElementById('shipping_last_name').value = lastName || '';
    document.getElementById('shipping_address_line_1').value = address.address_line1;
    document.getElementById('shipping_address_line_2').value = address.address_line2 || '';
    document.getElementById('shipping_city').value = address.city;
    document.getElementById('shipping_state').value = address.state;
    document.getElementById('shipping_postal_code').value = address.postal_code;
    document.getElementById('shipping_country').value = address.country;
    document.getElementById('shipping_phone').value = address.phone;
    
    console.log('✅ Shipping address filled from saved address #' + addressId);
}

// Fill billing address fields when radio button is clicked
function fillBillingAddress(addressId) {
    const address = savedAddresses.find(a => a.id === addressId);
    if (!address) {
        console.error('❌ Address not found:', addressId);
        return;
    }
    
    console.log('✅ Filling billing address:', address);
    
    const nameParts = address.full_name.split(' ');
    const firstName = nameParts[0];
    const lastName = nameParts.slice(1).join(' ');
    
    document.getElementById('billing_first_name').value = firstName;
    document.getElementById('billing_last_name').value = lastName || '';
    document.getElementById('billing_address_line_1').value = address.address_line1;
    document.getElementById('billing_address_line_2').value = address.address_line2 || '';
    document.getElementById('billing_city').value = address.city;
    document.getElementById('billing_state').value = address.state;
    document.getElementById('billing_postal_code').value = address.postal_code;
    document.getElementById('billing_country').value = address.country;
    document.getElementById('billing_phone').value = address.phone;
    
    console.log('✅ Billing address filled from saved address #' + addressId);
}

// Toggle billing address section visibility
function toggleBillingAddress() {
    const checkbox = document.getElementById('same_as_shipping');
    const billingSection = document.getElementById('billingAddressSection');
    const billingInputs = billingSection.querySelectorAll('input, select, textarea');
    
    if (checkbox.checked) {
        // Copy shipping to billing
        document.getElementById('billing_first_name').value = document.getElementById('shipping_first_name').value;
        document.getElementById('billing_last_name').value = document.getElementById('shipping_last_name').value;
        document.getElementById('billing_address_line_1').value = document.getElementById('shipping_address_line_1').value;
        document.getElementById('billing_address_line_2').value = document.getElementById('shipping_address_line_2').value;
        document.getElementById('billing_city').value = document.getElementById('shipping_city').value;
        document.getElementById('billing_state').value = document.getElementById('shipping_state').value;
        document.getElementById('billing_postal_code').value = document.getElementById('shipping_postal_code').value;
        document.getElementById('billing_country').value = document.getElementById('shipping_country').value;
        document.getElementById('billing_phone').value = document.getElementById('shipping_phone').value;
        
        // Hide billing section
        billingSection.style.display = 'none';
        
        // Disable required validation for hidden fields
        billingInputs.forEach(input => {
            input.removeAttribute('required');
        });
        
        console.log('✅ Billing same as shipping enabled');
    } else {
        // Show billing section
        billingSection.style.display = 'block';
        
        // Re-enable required validation
        billingSection.querySelectorAll('[data-required="true"]').forEach(input => {
            input.setAttribute('required', 'required');
        });
        
        console.log('✅ Billing section shown');
    }
}

// Auto-fill default addresses on page load
window.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Page loaded, auto-filling default addresses...');
    
    // Fill default shipping address
    const defaultShipping = savedAddresses.find(a => a.address_type === 'shipping' && a.is_default);
    if (defaultShipping) {
        console.log('📍 Auto-filling default shipping address:', defaultShipping.id);
        fillShippingAddress(defaultShipping.id);
    }
    
    // Fill default billing address (only if "same as shipping" is not checked)
    const sameAsShipping = document.getElementById('same_as_shipping');
    if (sameAsShipping && !sameAsShipping.checked) {
        const defaultBilling = savedAddresses.find(a => a.address_type === 'billing' && a.is_default);
        if (defaultBilling) {
            console.log('📍 Auto-filling default billing address:', defaultBilling.id);
            fillBillingAddress(defaultBilling.id);
        }
    }
    
    console.log('✅ Auto-fill complete');
});

// Form validation before submit
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    console.log('📤 Submitting checkout form...');
    
    const sameAsShipping = document.getElementById('same_as_shipping');
    if (sameAsShipping && sameAsShipping.checked) {
        // Ensure billing fields have values from shipping
        toggleBillingAddress();
    }
});
</script>
@endpush