@extends('admin.layouts.app')

@section('title', __('General Settings'))

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">{{ __('General Settings') }}</h1>
    <p class="page-subtitle">{{ __('Configure store information and basic settings') }}</p>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('General Settings') }}</li>
        </ol>
    </nav>
</div>

<!-- Settings Form -->
<div class="row">
    <div class="col-lg-8">
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Store Information') }}</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.update-general') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- Store Name -->
                    <div class="form-group">
                        <label for="site_name" class="form-label">{{ __('Store Name') }} <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            name="site_name" 
                            id="site_name" 
                            class="form-control @error('site_name') is-invalid @enderror" 
                            value="{{ old('site_name', $settings->site_name ?? config('app.name')) }}"
                            required
                        >
                        @error('site_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text">{{ __('Your store name displayed across the site') }}</small>
                    </div>
                    
                    <!-- Admin Email -->
                    <div class="form-group">
                        <label for="admin_email" class="form-label">{{ __('Admin Email') }} <span class="text-danger">*</span></label>
                        <input 
                            type="email" 
                            name="admin_email" 
                            id="admin_email" 
                            class="form-control @error('admin_email') is-invalid @enderror" 
                            value="{{ old('admin_email', $settings->admin_email ?? '') }}"
                            required
                        >
                        @error('admin_email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text">{{ __('Email for receiving order notifications and customer inquiries') }}</small>
                    </div>
                    
                    <!-- Meta Title -->
                    <div class="form-group">
                        <label for="meta_title" class="form-label">{{ __('Meta Title') }}</label>
                        <input 
                            type="text" 
                            name="meta_title" 
                            id="meta_title" 
                            class="form-control @error('meta_title') is-invalid @enderror" 
                            value="{{ old('meta_title', $settings->meta_title ?? '') }}"
                            maxlength="255"
                        >
                        @error('meta_title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text">{{ __('SEO title for search engines (recommended: 50-60 characters)') }}</small>
                    </div>
                    
                    <!-- Meta Description -->
                    <div class="form-group">
                        <label for="meta_description" class="form-label">{{ __('Meta Description') }}</label>
                        <textarea 
                            name="meta_description" 
                            id="meta_description" 
                            class="form-control @error('meta_description') is-invalid @enderror" 
                            rows="3"
                            maxlength="500"
                        >{{ old('meta_description', $settings->meta_description ?? '') }}</textarea>
                        @error('meta_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text">{{ __('SEO description for search engines (recommended: 150-160 characters)') }}</small>
                    </div>
                    
                    <!-- Meta Keywords -->
                    <div class="form-group">
                        <label for="meta_keywords" class="form-label">{{ __('Meta Keywords') }}</label>
                        <input 
                            type="text" 
                            name="meta_keywords" 
                            id="meta_keywords" 
                            class="form-control @error('meta_keywords') is-invalid @enderror" 
                            value="{{ old('meta_keywords', $settings->meta_keywords ?? '') }}"
                        >
                        @error('meta_keywords')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text">{{ __('Comma-separated keywords for SEO') }}</small>
                    </div>
                    
                    <hr class="my-4">
                    
                    <!-- Currency Settings -->
                    <h5 class="mb-3">{{ __('Currency Settings') }}</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="currency_code" class="form-label">{{ __('Currency Code') }} <span class="text-danger">*</span></label>
                                <input 
                                    type="text" 
                                    name="currency_code" 
                                    id="currency_code" 
                                    class="form-control @error('currency_code') is-invalid @enderror" 
                                    value="{{ old('currency_code', $settings->currency_code ?? 'INR') }}"
                                    maxlength="3"
                                    required
                                >
                                @error('currency_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text">{{ __('ISO currency code (e.g., USD, EUR, INR)') }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="currency_symbol" class="form-label">{{ __('Currency Symbol') }} <span class="text-danger">*</span></label>
                                <input 
                                    type="text" 
                                    name="currency_symbol" 
                                    id="currency_symbol" 
                                    class="form-control @error('currency_symbol') is-invalid @enderror" 
                                    value="{{ old('currency_symbol', $settings->currency_symbol ?? '₹') }}"
                                    maxlength="5"
                                    required
                                >
                                @error('currency_symbol')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text">{{ __('Symbol to display (e.g., $, €, ₹)') }}</small>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <!-- Pricing & Taxes -->
                    <h5 class="mb-3">{{ __('Pricing & Taxes') }}</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="shipping_rate" class="form-label">{{ __('Default Shipping Rate') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ $settings->currency_symbol ?? '₹' }}</span>
                                    <input 
                                        type="number" 
                                        name="shipping_rate" 
                                        id="shipping_rate" 
                                        class="form-control @error('shipping_rate') is-invalid @enderror" 
                                        value="{{ old('shipping_rate', $settings->shipping_rate ?? 50) }}"
                                        min="0"
                                        step="0.01"
                                    >
                                </div>
                                @error('shipping_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="free_shipping_threshold" class="form-label">{{ __('Free Shipping Threshold') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ $settings->currency_symbol ?? '₹' }}</span>
                                    <input 
                                        type="number" 
                                        name="free_shipping_threshold" 
                                        id="free_shipping_threshold" 
                                        class="form-control @error('free_shipping_threshold') is-invalid @enderror" 
                                        value="{{ old('free_shipping_threshold', $settings->free_shipping_threshold ?? 500) }}"
                                        min="0"
                                        step="0.01"
                                    >
                                </div>
                                @error('free_shipping_threshold')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text">{{ __('Order amount for free shipping (0 to disable)') }}</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="tax_rate" class="form-label">{{ __('Tax Rate (%)') }}</label>
                        <input 
                            type="number" 
                            name="tax_rate" 
                            id="tax_rate" 
                            class="form-control @error('tax_rate') is-invalid @enderror" 
                            value="{{ old('tax_rate', $settings->tax_rate ?? 18) }}"
                            min="0"
                            max="100"
                            step="0.01"
                        >
                        @error('tax_rate')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text">{{ __('Default tax rate (e.g., GST, VAT)') }}</small>
                    </div>
                    
                    <hr class="my-4">
                    
                    <!-- Inventory Settings -->
                    <h5 class="mb-3">{{ __('Inventory Settings') }}</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="low_stock_threshold" class="form-label">{{ __('Low Stock Threshold') }}</label>
                                <input 
                                    type="number" 
                                    name="low_stock_threshold" 
                                    id="low_stock_threshold" 
                                    class="form-control @error('low_stock_threshold') is-invalid @enderror" 
                                    value="{{ old('low_stock_threshold', $settings->low_stock_threshold ?? 10) }}"
                                    min="1"
                                >
                                @error('low_stock_threshold')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text">{{ __('Alert when product stock falls below this number') }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="items_per_page" class="form-label">{{ __('Items Per Page') }}</label>
                                <input 
                                    type="number" 
                                    name="items_per_page" 
                                    id="items_per_page" 
                                    class="form-control @error('items_per_page') is-invalid @enderror" 
                                    value="{{ old('items_per_page', $settings->items_per_page ?? 12) }}"
                                    min="1"
                                    max="100"
                                >
                                @error('items_per_page')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text">{{ __('Number of products to display per page') }}</small>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <!-- Checkout Settings -->
                    <h5 class="mb-3">{{ __('Checkout Settings') }}</h5>
                    
                    <div class="form-check mb-3">
                        <input 
                            type="checkbox" 
                            name="allow_guest_checkout" 
                            id="allow_guest_checkout" 
                            class="form-check-input"
                            value="1"
                            {{ old('allow_guest_checkout', $settings->allow_guest_checkout ?? false) ? 'checked' : '' }}
                        >
                        <label for="allow_guest_checkout" class="form-check-label">
                            {{ __('Allow Guest Checkout') }}
                        </label>
                        <small class="d-block text-muted">{{ __('Enable customers to checkout without creating an account') }}</small>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>{{ __('Save Changes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Quick Info Sidebar -->
    <div class="col-lg-4">
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Quick Info') }}</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <i class="bi bi-info-circle text-primary me-2"></i>
                    <strong>{{ __('Settings Guidelines') }}</strong>
                </div>
                <ul style="font-size: 0.9rem; color: var(--text-muted); line-height: 1.8;">
                    <li>{{ __('Store name appears in the header and emails') }}</li>
                    <li>{{ __('Meta tags improve search engine visibility') }}</li>
                    <li>{{ __('Currency settings affect all product pricing') }}</li>
                    <li>{{ __('Tax rate is applied at checkout') }}</li>
                    <li>{{ __('Low stock alerts help manage inventory') }}</li>
                    <li>{{ __('Changes take effect immediately') }}</li>
                </ul>
            </div>
        </div>
        
        <div class="content-card mt-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('System Information') }}</h3>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>{{ __('Laravel Version') }}:</span>
                    <strong>{{ app()->version() }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>{{ __('PHP Version') }}:</span>
                    <strong>{{ phpversion() }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>{{ __('Default Locale') }}:</span>
                    <strong>{{ app()->getLocale() }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>{{ __('Timezone') }}:</span>
                    <strong>{{ config('app.timezone') }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-format currency inputs
    document.addEventListener('DOMContentLoaded', function() {
        const currencyInputs = document.querySelectorAll('input[type="number"][step="0.01"]');
        
        currencyInputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value !== '') {
                    this.value = parseFloat(this.value).toFixed(2);
                }
            });
        });
    });
</script>
@endpush