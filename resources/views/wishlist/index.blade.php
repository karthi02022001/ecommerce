@extends('layouts.app')

@section('title', __('My Wishlist'))

@section('content')
<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="bg-light py-3 mb-4">
    <div class="container">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('Wishlist') }}</li>
        </ol>
    </div>
</nav>

<div class="container py-5">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="display-6 mb-0">
                    <i class="bi bi-heart-fill text-danger me-2"></i>
                    {{ __('My Wishlist') }}
                </h1>
                @if($wishlists->isNotEmpty())
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#clearWishlistModal">
                        <i class="bi bi-trash me-1"></i> {{ __('Clear All') }}
                    </button>
                @endif
            </div>
            <p class="text-muted mt-2">
                {{ __('You have') }} <strong>{{ $wishlists->total() }}</strong> {{ __('item(s) in your wishlist') }}
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($wishlists->isEmpty())
        <!-- Empty Wishlist -->
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-heart" style="font-size: 6rem; color: #e0e0e0;"></i>
            </div>
            <h3 class="mb-3">{{ __('Your Wishlist is Empty') }}</h3>
            <p class="text-muted mb-4">
                {{ __('Start adding products you love to your wishlist!') }}
            </p>
            <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                <i class="bi bi-shop me-2"></i>
                {{ __('Continue Shopping') }}
            </a>
        </div>
    @else
        <!-- Wishlist Items -->
        <div class="row g-4">
            @foreach($wishlists as $wishlist)
                @php
                    $product = $wishlist->product;
                    $translation = $product->translations->first();
                    $productName = $translation ? $translation->name : $product->name;
                    $productDescription = $translation ? $translation->description : $product->description;
                    $image = $product->images->first();
                    $imagePath = $image ? asset('storage/' . $image->image_path) : asset('images/no-image.png');
                    $inStock = $product->stock_quantity > 0;
                    $discountedPrice = $product->discount_price ?? $product->price;
                    $hasDiscount = $product->discount_price && $product->discount_price < $product->price;
                @endphp

                <div class="col-md-6 col-lg-4" id="wishlist-item-{{ $wishlist->id }}">
                    <div class="card h-100 shadow-sm wishlist-card">
                        <!-- Product Image -->
                        <div class="position-relative">
                            <a href="{{ route('products.show', $product->id) }}">
                                <img src="{{ $imagePath }}" 
                                     class="card-img-top" 
                                     alt="{{ $productName }}"
                                     style="height: 250px; object-fit: cover;">
                            </a>
                            
                            <!-- Badges -->
                            <div class="position-absolute top-0 start-0 p-2">
                                @if(!$inStock)
                                    <span class="badge bg-danger">{{ __('Out of Stock') }}</span>
                                @elseif($hasDiscount)
                                    @php
                                        $discountPercent = round((($product->price - $product->discount_price) / $product->price) * 100);
                                    @endphp
                                    <span class="badge bg-success">-{{ $discountPercent }}%</span>
                                @endif
                            </div>

                            <!-- Remove Button (Hidden Form) -->
                            <form action="{{ route('wishlist.remove', $wishlist->id) }}" 
                                  method="POST" 
                                  class="d-inline remove-form-{{ $wishlist->id }}"
                                  style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                            <button type="button" 
                                    class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 remove-wishlist"
                                    data-wishlist-id="{{ $wishlist->id }}"
                                    data-product-name="{{ $productName }}">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <!-- Category -->
                            @if($product->category)
                                @php
                                    $categoryTranslation = $product->category->translations->first();
                                    $categoryName = $categoryTranslation ? $categoryTranslation->name : $product->category->name;
                                @endphp
                                <a href="{{ route('categories.show', $product->category->id) }}" 
                                   class="text-muted text-decoration-none small mb-2">
                                    <i class="bi bi-tag me-1"></i>{{ $categoryName }}
                                </a>
                            @endif

                            <!-- Product Name -->
                            <h5 class="card-title mb-2">
                                <a href="{{ route('products.show', $product->id) }}" 
                                   class="text-decoration-none text-dark">
                                    {{ Str::limit($productName, 50) }}
                                </a>
                            </h5>

                            <!-- Product Description -->
                            @if($productDescription)
                                <p class="card-text text-muted small mb-3">
                                    {{ Str::limit(strip_tags($productDescription), 80) }}
                                </p>
                            @endif

                            <!-- Price -->
                            <div class="mb-3">
                                @if($hasDiscount)
                                    <span class="h5 text-danger mb-0 me-2">
                                        {{ session('currency_symbol', '$') }}{{ number_format($discountedPrice, 2) }}
                                    </span>
                                    <span class="text-muted text-decoration-line-through">
                                        {{ session('currency_symbol', '$') }}{{ number_format($product->price, 2) }}
                                    </span>
                                @else
                                    <span class="h5 text-primary mb-0">
                                        {{ session('currency_symbol', '$') }}{{ number_format($product->price, 2) }}
                                    </span>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="mt-auto">
                                @if($inStock)
                                    <button type="button" 
                                            class="btn btn-primary w-100 move-to-cart"
                                            data-wishlist-id="{{ $wishlist->id }}"
                                            data-product-name="{{ $productName }}">
                                        <i class="bi bi-cart-plus me-2"></i>
                                        {{ __('Add to Cart') }}
                                    </button>
                                @else
                                    <button type="button" class="btn btn-secondary w-100" disabled>
                                        <i class="bi bi-x-circle me-2"></i>
                                        {{ __('Out of Stock') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($wishlists->hasPages())
            <div class="row mt-5">
                <div class="col-12">
                    <nav aria-label="Wishlist pagination">
                        {{ $wishlists->links() }}
                    </nav>
                </div>
            </div>
        @endif
    @endif
</div>

<!-- Clear Wishlist Modal -->
<div class="modal fade" id="clearWishlistModal" tabindex="-1" aria-labelledby="clearWishlistModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="clearWishlistModalLabel">
                    <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                    {{ __('Clear Wishlist') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>{{ __('Are you sure you want to clear your entire wishlist? This action cannot be undone.') }}</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ __('Cancel') }}
                </button>
                <form action="{{ route('wishlist.clear') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>
                        {{ __('Clear All') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.wishlist-card {
    transition: all 0.3s ease;
    border: 1px solid #dee2e6;
}

.wishlist-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}

.wishlist-card .card-img-top {
    transition: opacity 0.3s ease;
}

.wishlist-card:hover .card-img-top {
    opacity: 0.9;
}

.btn-primary {
    background-color: #20b2aa;
    border-color: #20b2aa;
}

.btn-primary:hover {
    background-color: #008b8b;
    border-color: #008b8b;
}

.text-primary {
    color: #20b2aa !important;
}

.breadcrumb-item.active {
    color: #20b2aa;
}

.breadcrumb-item a {
    color: #6c757d;
    text-decoration: none;
}

.breadcrumb-item a:hover {
    color: #20b2aa;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Remove from wishlist - Using form submission instead of AJAX
    document.querySelectorAll('.remove-wishlist').forEach(button => {
        button.addEventListener('click', function() {
            const wishlistId = this.dataset.wishlistId;
            const productName = this.dataset.productName;
            
            if (confirm('{{ __("Remove") }} "' + productName + '" {{ __("from wishlist?") }}')) {
                // Submit the hidden form
                const form = document.querySelector(`.remove-form-${wishlistId}`);
                if (form) {
                    form.submit();
                }
            }
        });
    });

    // Move to cart - Using AJAX
    document.querySelectorAll('.move-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const wishlistId = this.dataset.wishlistId;
            const productName = this.dataset.productName;
            
            moveToCart(wishlistId, productName, this);
        });
    });

    function moveToCart(wishlistId, productName, button) {
        const originalHtml = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>{{ __("Adding...") }}';

        fetch(`/wishlist/${wishlistId}/move-to-cart`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                
                // Update counts in header
                updateWishlistCount(data.wishlist_count);
                updateCartCount(data.cart_count);
                
                // Remove the card from DOM with animation
                const card = document.getElementById(`wishlist-item-${wishlistId}`);
                if (card) {
                    card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.9)';
                    
                    setTimeout(() => {
                        card.remove();
                        
                        // Reload page if no items left
                        const remainingItems = document.querySelectorAll('[id^="wishlist-item-"]').length;
                        if (remainingItems === 0) {
                            window.location.reload();
                        }
                    }, 300);
                }
            } else {
                button.disabled = false;
                button.innerHTML = originalHtml;
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            button.disabled = false;
            button.innerHTML = originalHtml;
            showAlert('error', '{{ __("An error occurred. Please try again.") }}');
        });
    }

    function updateWishlistCount(count) {
        const wishlistBadges = document.querySelectorAll('.wishlist-count');
        wishlistBadges.forEach(badge => {
            badge.textContent = count;
            if (count === 0) {
                badge.style.display = 'none';
            }
        });
    }

    function updateCartCount(count) {
        const cartBadges = document.querySelectorAll('.cart-count');
        cartBadges.forEach(badge => {
            badge.textContent = count;
        });
    }

    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);';
        alertDiv.innerHTML = `
            <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
});
</script>
@endpush
@endsection