@extends('admin.layouts.app')

@section('title', __('Translation Settings'))

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">{{ __('Translation Settings') }}</h1>
    <p class="page-subtitle">{{ __('Manage language translations for your store') }}</p>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Translation Settings') }}</li>
        </ol>
    </nav>
</div>

<!-- Language Tabs -->
<div class="content-card">
    <div class="card-header">
        <h3 class="card-title">{{ __('Manage Translations') }}</h3>
        <div class="card-actions">
            <span class="badge badge-primary">{{ count($locales) }} {{ __('Languages') }}</span>
        </div>
    </div>
    <div class="card-body">
        <!-- Language Selector -->
        <ul class="nav nav-tabs mb-4" id="languageTabs" role="tablist">
            @foreach($locales as $index => $locale)
            <li class="nav-item" role="presentation">
                <button 
                    class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                    id="tab-{{ $locale }}" 
                    data-bs-toggle="tab" 
                    data-bs-target="#content-{{ $locale }}" 
                    type="button" 
                    role="tab"
                >
                    @if($locale === 'en')
                        <i class="bi bi-flag me-2"></i>{{ __('English') }}
                    @elseif($locale === 'es')
                        <i class="bi bi-flag me-2"></i>{{ __('Spanish') }}
                    @else
                        <i class="bi bi-translate me-2"></i>{{ strtoupper($locale) }}
                    @endif
                </button>
            </li>
            @endforeach
        </ul>
        
        <!-- Tab Content -->
        <div class="tab-content" id="languageTabContent">
            @foreach($locales as $index => $locale)
            <div 
                class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                id="content-{{ $locale }}" 
                role="tabpanel"
            >
                <form action="{{ route('admin.settings.update-translations') }}" method="POST" class="translation-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="locale" value="{{ $locale }}">
                    
                    <!-- Search Box -->
                    <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input 
                                type="text" 
                                class="form-control translation-search" 
                                placeholder="{{ __('Search translations...') }}"
                                data-target="translation-{{ $locale }}"
                            >
                        </div>
                    </div>
                    
                    <!-- Common Translations Section -->
                    @php
                        $commonKeys = [
                            // Navigation
                            'home' => 'Home',
                            'products' => 'Products',
                            'categories' => 'Categories',
                            'about' => 'About',
                            'contact' => 'Contact',
                            'cart' => 'Cart',
                            'checkout' => 'Checkout',
                            'my_account' => 'My Account',
                            'orders' => 'Orders',
                            'logout' => 'Logout',
                            'login' => 'Login',
                            'register' => 'Register',
                            
                            // Product Related
                            'shop_now' => 'Shop Now',
                            'add_to_cart' => 'Add to Cart',
                            'buy_now' => 'Buy Now',
                            'out_of_stock' => 'Out of Stock',
                            'in_stock' => 'In Stock',
                            'featured_products' => 'Featured Products',
                            'latest_products' => 'Latest Products',
                            'related_products' => 'Related Products',
                            'product_details' => 'Product Details',
                            'price' => 'Price',
                            'quantity' => 'Quantity',
                            'total' => 'Total',
                            'subtotal' => 'Subtotal',
                            
                            // Common Actions
                            'view' => 'View',
                            'edit' => 'Edit',
                            'delete' => 'Delete',
                            'save' => 'Save',
                            'cancel' => 'Cancel',
                            'submit' => 'Submit',
                            'search' => 'Search',
                            'filter' => 'Filter',
                            'sort' => 'Sort',
                            'next' => 'Next',
                            'previous' => 'Previous',
                            'back' => 'Back',
                            
                            // Forms
                            'name' => 'Name',
                            'email' => 'Email',
                            'password' => 'Password',
                            'confirm_password' => 'Confirm Password',
                            'phone' => 'Phone',
                            'address' => 'Address',
                            'city' => 'City',
                            'state' => 'State',
                            'postal_code' => 'Postal Code',
                            'country' => 'Country',
                            
                            // Messages
                            'success' => 'Success',
                            'error' => 'Error',
                            'warning' => 'Warning',
                            'info' => 'Info',
                            'thank_you' => 'Thank You',
                            'welcome' => 'Welcome',
                            'copyright' => 'Copyright',
                            'all_rights_reserved' => 'All Rights Reserved',
                        ];
                        
                        $currentTranslations = isset($translations[$locale]) && is_array($translations[$locale]) ? $translations[$locale] : [];
                    @endphp
                    
                    <div class="translation-container" id="translation-{{ $locale }}">
                        @foreach($commonKeys as $key => $defaultValue)
                        <div class="form-group translation-item">
                            <label for="{{ $locale }}_{{ $key }}" class="form-label">
                                <strong>{{ $key }}</strong>
                                <span class="text-muted ms-2">({{ __('Default') }}: {{ $defaultValue }})</span>
                            </label>
                            <input 
                                type="text" 
                                name="translations[{{ $key }}]" 
                                id="{{ $locale }}_{{ $key }}" 
                                class="form-control" 
                                value="{{ old('translations.' . $key, isset($currentTranslations[$key]) ? $currentTranslations[$key] : '') }}"
                                placeholder="{{ $defaultValue }}"
                            >
                        </div>
                        @endforeach
                        
                        <!-- Custom Translations -->
                        @php
                            $customTranslations = array_diff_key($currentTranslations, $commonKeys);
                        @endphp
                        
                        @if(count($customTranslations) > 0)
                        <hr class="my-4">
                        <h5 class="mb-3">{{ __('Custom Translations') }}</h5>
                        
                        @foreach($customTranslations as $key => $value)
                        <div class="form-group translation-item">
                            <label for="{{ $locale }}_custom_{{ $loop->index }}" class="form-label">
                                <strong>{{ $key }}</strong>
                            </label>
                            <div class="input-group">
                                <input 
                                    type="text" 
                                    name="translations[{{ $key }}]" 
                                    id="{{ $locale }}_custom_{{ $loop->index }}" 
                                    class="form-control" 
                                    value="{{ old('translations.' . $key, is_string($value) ? $value : '') }}"
                                >
                                <button 
                                    type="button" 
                                    class="btn btn-danger btn-sm remove-translation"
                                    onclick="removeTranslation(this)"
                                >
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                        @endif
                    </div>
                    
                    <!-- No Results Message -->
                    <div class="no-results text-center py-5" style="display: none;">
                        <i class="bi bi-search" style="font-size: 3rem; color: var(--text-muted);"></i>
                        <p class="mt-3 text-muted">{{ __('No translations found matching your search') }}</p>
                    </div>
                    
                    <!-- Add Custom Translation -->
                    <div class="mt-4 p-3" style="background: var(--content-bg); border-radius: 8px;">
                        <h6 class="mb-3">{{ __('Add Custom Translation') }}</h6>
                        <div class="row">
                            <div class="col-md-5">
                                <input 
                                    type="text" 
                                    id="new_key_{{ $locale }}" 
                                    class="form-control" 
                                    placeholder="{{ __('Translation key (e.g., welcome_message)') }}"
                                >
                            </div>
                            <div class="col-md-5">
                                <input 
                                    type="text" 
                                    id="new_value_{{ $locale }}" 
                                    class="form-control" 
                                    placeholder="{{ __('Translation value') }}"
                                >
                            </div>
                            <div class="col-md-2">
                                <button 
                                    type="button" 
                                    class="btn btn-success w-100" 
                                    onclick="addCustomTranslation('{{ $locale }}')"
                                >
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>{{ __('Save Translations') }}
                        </button>
                    </div>
                </form>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Info Card -->
<div class="content-card mt-4">
    <div class="card-header">
        <h3 class="card-title">{{ __('Translation Guidelines') }}</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5><i class="bi bi-info-circle text-primary me-2"></i>{{ __('How it works') }}</h5>
                <ul style="color: var(--text-muted); line-height: 1.8;">
                    <li>{{ __('Translations are stored in JSON files') }}</li>
                    <li>{{ __('Each language has its own translation file') }}</li>
                    <li>{{ __('Changes take effect immediately after saving') }}</li>
                    <li>{{ __('Empty values will use the default English text') }}</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h5><i class="bi bi-lightbulb text-warning me-2"></i>{{ __('Best practices') }}</h5>
                <ul style="color: var(--text-muted); line-height: 1.8;">
                    <li>{{ __('Keep translations consistent across the site') }}</li>
                    <li>{{ __('Use clear and concise language') }}</li>
                    <li>{{ __('Test translations on the frontend') }}</li>
                    <li>{{ __('Backup translations before major changes') }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Translation search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInputs = document.querySelectorAll('.translation-search');
    
    searchInputs.forEach(input => {
        input.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const targetId = this.getAttribute('data-target');
            const container = document.getElementById(targetId);
            const items = container.querySelectorAll('.translation-item');
            const noResults = container.nextElementSibling;
            let visibleCount = 0;
            
            items.forEach(item => {
                const label = item.querySelector('label').textContent.toLowerCase();
                const input = item.querySelector('input').value.toLowerCase();
                
                if (label.includes(searchTerm) || input.includes(searchTerm)) {
                    item.style.display = 'block';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });
            
            if (visibleCount === 0) {
                container.style.display = 'none';
                noResults.style.display = 'block';
            } else {
                container.style.display = 'block';
                noResults.style.display = 'none';
            }
        });
    });
});

// Add custom translation
function addCustomTranslation(locale) {
    const keyInput = document.getElementById('new_key_' + locale);
    const valueInput = document.getElementById('new_value_' + locale);
    const key = keyInput.value.trim();
    const value = valueInput.value.trim();
    
    if (!key || !value) {
        alert('{{ __("Please enter both key and value") }}');
        return;
    }
    
    // Validate key format (alphanumeric and underscores only)
    if (!/^[a-z0-9_]+$/.test(key)) {
        alert('{{ __("Key must contain only lowercase letters, numbers, and underscores") }}');
        return;
    }
    
    const container = document.getElementById('translation-' + locale);
    const customSection = container.querySelector('h5');
    
    // Check if custom section exists, if not create it
    if (!customSection || !customSection.textContent.includes('Custom Translations')) {
        const hr = document.createElement('hr');
        hr.className = 'my-4';
        container.appendChild(hr);
        
        const heading = document.createElement('h5');
        heading.className = 'mb-3';
        heading.textContent = '{{ __("Custom Translations") }}';
        container.appendChild(heading);
    }
    
    // Create new form group
    const formGroup = document.createElement('div');
    formGroup.className = 'form-group translation-item';
    formGroup.innerHTML = `
        <label class="form-label">
            <strong>${key}</strong>
        </label>
        <div class="input-group">
            <input 
                type="text" 
                name="translations[${key}]" 
                class="form-control" 
                value="${value}"
            >
            <button 
                type="button" 
                class="btn btn-danger btn-sm remove-translation"
                onclick="removeTranslation(this)"
            >
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
    
    container.appendChild(formGroup);
    
    // Clear inputs
    keyInput.value = '';
    valueInput.value = '';
    
    // Show success message
    alert('{{ __("Custom translation added. Don\'t forget to save!") }}');
}

// Remove translation
function removeTranslation(button) {
    if (confirm('{{ __("Are you sure you want to remove this translation?") }}')) {
        button.closest('.form-group').remove();
    }
}

// Form submission confirmation
document.querySelectorAll('.translation-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const locale = this.querySelector('input[name="locale"]').value;
        const localeName = locale === 'en' ? '{{ __("English") }}' : '{{ __("Spanish") }}';
        
        if (!confirm(`{{ __("Save translations for") }} ${localeName}?`)) {
            e.preventDefault();
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.nav-tabs .nav-link {
    color: var(--text-muted);
    border: none;
    border-bottom: 2px solid transparent;
    padding: 12px 20px;
}

.nav-tabs .nav-link:hover {
    border-color: var(--primary-light);
    color: var(--primary-color);
}

.nav-tabs .nav-link.active {
    color: var(--primary-color);
    border-bottom-color: var(--primary-color);
    background: none;
}

.translation-item {
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--border-color);
}

.translation-item:last-child {
    border-bottom: none;
}

.remove-translation {
    border-radius: 0 8px 8px 0;
}
</style>
@endpush
