@extends('layouts.app')

@section('title', __('products'))

@push('styles')
<style>
    /* Products Page Specific Styles */
    .products-wrapper {
        padding: 40px 0;
        background: var(--light-gray);
    }
    
    .page-header {
        background: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    }
    
    .page-header h1 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--accent-color);
        margin: 0;
    }
    
    .breadcrumb-custom {
        background: transparent;
        padding: 0;
        margin: 10px 0 0 0;
        font-size: 0.9rem;
    }
    
    .breadcrumb-custom a {
        color: var(--primary-color);
        text-decoration: none;
    }
    
    .breadcrumb-custom a:hover {
        color: var(--primary-dark);
    }
    
    /* Sidebar Filters */
    .filters-sidebar {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        position: sticky;
        top: 20px;
    }
    
    .filter-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--border-color);
    }
    
    .filter-header h5 {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--accent-color);
        margin: 0;
    }
    
    .filter-clear {
        color: var(--primary-color);
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
        transition: color 0.3s ease;
    }
    
    .filter-clear:hover {
        color: var(--primary-dark);
    }
    
    .filter-group {
        margin-bottom: 25px;
    }
    
    .filter-group label {
        font-weight: 600;
        color: var(--accent-color);
        margin-bottom: 10px;
        display: block;
        font-size: 0.95rem;
    }
    
    .filter-group .form-control,
    .filter-group .form-select {
        border: 2px solid var(--border-color);
        border-radius: 10px;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }
    
    .filter-group .form-control:focus,
    .filter-group .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(var(--primary-rgb), 0.15);
    }
    
    .price-range-inputs {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    
    .btn-apply-filters {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        border: none;
        color: white;
        padding: 12px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        width: 100%;
    }
    
    .btn-apply-filters:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(var(--primary-rgb), 0.3);
    }
    
    /* Products Section */
    .products-section {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    }
    
    .products-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid var(--border-color);
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .products-count {
        font-size: 1.1rem;
        color: var(--medium-gray);
    }
    
    .products-count strong {
        color: var(--accent-color);
        font-weight: 600;
    }
    
    .sort-dropdown {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .sort-dropdown label {
        font-weight: 500;
        color: var(--accent-color);
        margin: 0;
        white-space: nowrap;
    }
    
    .sort-dropdown select {
        border: 2px solid var(--border-color);
        border-radius: 10px;
        padding: 8px 15px;
        min-width: 200px;
        transition: all 0.3s ease;
    }
    
    .sort-dropdown select:focus {
        border-color: var(--primary-color);
        outline: none;
        box-shadow: 0 0 0 0.2rem rgba(var(--primary-rgb), 0.15);
    }
    
    /* Product Cards */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 25px;
        margin-bottom: 30px;
    }
    
    .product-card-modern {
        background: white;
        border: 2px solid var(--border-color);
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .product-card-modern:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        border-color: var(--primary-color);
    }
    
    .product-image-container {
        position: relative;
        height: 280px;
        overflow: hidden;
        background: var(--light-gray);
    }
    
    .product-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .product-card-modern:hover .product-image-container img {
        transform: scale(1.1);
    }
    
    .product-badge-modern {
        position: absolute;
        top: 15px;
        left: 15px;
        background: var(--danger-color);
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        z-index: 10;
    }
    
    .product-badge-modern.featured {
        background: var(--success-color);
    }
    
    .product-actions-overlay {
        position: absolute;
        top: 15px;
        right: 15px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        opacity: 0;
        transform: translateX(20px);
        transition: all 0.3s ease;
        z-index: 10;
    }
    
    .product-card-modern:hover .product-actions-overlay {
        opacity: 1;
        transform: translateX(0);
    }
    
    .action-btn-circle {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: white;
        border: none;
        color: var(--medium-gray);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        cursor: pointer;
    }
    
    .action-btn-circle:hover {
        background: var(--primary-color);
        color: white;
        transform: scale(1.1);
    }
    
    .stock-badge {
        position: absolute;
        bottom: 15px;
        left: 15px;
        background: rgba(255, 255, 255, 0.95);
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .stock-badge.in-stock {
        color: var(--success-color);
    }
    
    .stock-badge.out-of-stock {
        color: var(--danger-color);
    }
    
    .product-info-modern {
        padding: 20px;
    }
    
    .product-category {
        font-size: 0.85rem;
        color: var(--primary-color);
        font-weight: 500;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .product-title-modern {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--accent-color);
        margin-bottom: 12px;
        text-decoration: none;
        display: block;
        line-height: 1.4;
        min-height: 2.8em;
    }
    
    .product-title-modern:hover {
        color: var(--primary-color);
    }
    
    .product-price-section {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }
    
    .product-price-current {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-color);
    }
    
    .product-price-original {
        font-size: 1.1rem;
        color: var(--medium-gray);
        text-decoration: line-through;
    }
    
    .btn-add-cart {
        background: var(--primary-color);
        border: none;
        color: white;
        padding: 12px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    
    .btn-add-cart:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
    }
    
    .btn-add-cart:disabled {
        background: var(--medium-gray);
        cursor: not-allowed;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 80px 20px;
    }
    
    .empty-state-icon {
        font-size: 6rem;
        color: var(--border-color);
        margin-bottom: 20px;
    }
    
    .empty-state h4 {
        font-size: 1.5rem;
        color: var(--accent-color);
        font-weight: 600;
        margin-bottom: 15px;
    }
    
    .empty-state p {
        color: var(--medium-gray);
        margin-bottom: 25px;
    }
    
    /* Pagination */
    .pagination-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 40px;
    }
    
    .pagination {
        gap: 8px;
    }
    
    .pagination .page-link {
        border: 2px solid var(--border-color);
        border-radius: 10px;
        color: var(--accent-color);
        padding: 10px 16px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .pagination .page-link:hover {
        border-color: var(--primary-color);
        background: var(--primary-color);
        color: white;
    }
    
    .pagination .page-item.active .page-link {
        background: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    /* Active Filters Tags */
    .active-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
    }
    
    .filter-tag {
        background: var(--primary-color);
        color: white;
        padding: 6px 15px;
        border-radius: 20px;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .filter-tag-close {
        cursor: pointer;
        font-weight: bold;
        transition: transform 0.2s ease;
    }
    
    .filter-tag-close:hover {
        transform: scale(1.2);
    }
    
    /* Responsive */
    @media (max-width: 991px) {
        .filters-sidebar {
            position: static;
            margin-bottom: 30px;
        }
        
        .products-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .sort-dropdown {
            width: 100%;
        }
        
        .sort-dropdown select {
            flex: 1;
        }
    }
    
    @media (max-width: 768px) {
        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 15px;
        }
        
        .product-image-container {
            height: 200px;
        }
        
        .product-info-modern {
            padding: 15px;
        }
        
        .product-title-modern {
            font-size: 0.95rem;
            min-height: auto;
        }
        
        .product-price-current {
            font-size: 1.2rem;
        }
        
        .price-range-inputs {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="products-wrapper">
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="bi bi-grid-3x3-gap me-2"></i>{{ __('All Products') }}</h1>
            <nav class="breadcrumb-custom">
                <a href="{{ route('home') }}">{{ __('Home') }}</a>
                <span class="mx-2">/</span>
                <span>{{ __('Products') }}</span>
            </nav>
        </div>
        
        <div class="row">
            <!-- Sidebar Filters -->
            <div class="col-lg-3">
                <div class="filters-sidebar">
                    <div class="filter-header">
                        <h5><i class="bi bi-funnel me-2"></i>{{ __('Filters') }}</h5>
                        <a href="{{ route('products.index') }}" class="filter-clear">
                            <i class="bi bi-x-circle me-1"></i>{{ __('Clear All') }}
                        </a>
                    </div>
                    
                    <form action="{{ route('products.index') }}" method="GET" id="filterForm">
                        <!-- Search -->
                        <div class="filter-group">
                            <label>
                                <i class="bi bi-search me-2"></i>{{ __('Search Products') }}
                            </label>
                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   value="{{ request('search') }}" 
                                   placeholder="{{ __('Type to search...') }}">
                        </div>
                        
                        <!-- Category -->
                        <div class="filter-group">
                            <label>
                                <i class="bi bi-tag me-2"></i>{{ __('Category') }}
                            </label>
                            <select name="category" class="form-select">
                                <option value="">{{ __('All Categories') }}</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name() }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Price Range -->
                        <div class="filter-group">
                            <label>
                                <i class="bi bi-currency-dollar me-2"></i>{{ __('Price Range') }}
                            </label>
                            <div class="price-range-inputs">
                                <input type="number" 
                                       name="min_price" 
                                       class="form-control" 
                                       placeholder="{{ __('Min') }}" 
                                       value="{{ request('min_price') }}" 
                                       step="0.01">
                                <input type="number" 
                                       name="max_price" 
                                       class="form-control" 
                                       placeholder="{{ __('Max') }}" 
                                       value="{{ request('max_price') }}" 
                                       step="0.01">
                            </div>
                        </div>
                        
                        <button type="submit" class="btn-apply-filters">
                            <i class="bi bi-check-circle me-2"></i>{{ __('Apply Filters') }}
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Products Grid -->
            <div class="col-lg-9">
                <div class="products-section">
                    <!-- Products Header -->
                    <div class="products-header">
                        <div class="products-count">
                            {{ __('Showing') }} <strong>{{ $products->count() }}</strong> {{ __('of') }} <strong>{{ $products->total() }}</strong> {{ __('products') }}
                        </div>
                        
                        <form action="{{ route('products.index') }}" method="GET" class="sort-dropdown">
                            @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                            @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif
                            @if(request('min_price'))
                            <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                            @endif
                            @if(request('max_price'))
                            <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                            @endif
                            
                            <label>{{ __('Sort by:') }}</label>
                            <select name="sort" class="form-select" onchange="this.form.submit()">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>{{ __('Latest First') }}</option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>{{ __('Price: Low to High') }}</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>{{ __('Price: High to Low') }}</option>
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>{{ __('Name: A to Z') }}</option>
                            </select>
                        </form>
                    </div>
                    
                    <!-- Active Filters Tags -->
                    @if(request()->hasAny(['search', 'category', 'min_price', 'max_price']))
                    <div class="active-filters">
                        @if(request('search'))
                        <div class="filter-tag">
                            <span>{{ __('Search') }}: {{ request('search') }}</span>
                            <span class="filter-tag-close" onclick="removeFilter('search')">×</span>
                        </div>
                        @endif
                        
                        @if(request('category'))
                        <div class="filter-tag">
                            <span>{{ __('Category') }}: {{ $categories->find(request('category'))->name() ?? '' }}</span>
                            <span class="filter-tag-close" onclick="removeFilter('category')">×</span>
                        </div>
                        @endif
                        
                        @if(request('min_price'))
                        <div class="filter-tag">
                            <span>{{ __('Min Price') }}: ${{ request('min_price') }}</span>
                            <span class="filter-tag-close" onclick="removeFilter('min_price')">×</span>
                        </div>
                        @endif
                        
                        @if(request('max_price'))
                        <div class="filter-tag">
                            <span>{{ __('Max Price') }}: ${{ request('max_price') }}</span>
                            <span class="filter-tag-close" onclick="removeFilter('max_price')">×</span>
                        </div>
                        @endif
                    </div>
                    @endif
                    
                    <!-- Products Grid -->
                    <div class="products-grid">
                        @forelse($products as $product)
                        <div class="product-card-modern">
                            <div class="product-image-container">
                                @if($product->primaryImage)
                                <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" alt="{{ $product->name() }}">
                                @else
                                <img src="https://via.placeholder.com/400x280?text={{ urlencode($product->name()) }}" alt="{{ $product->name() }}">
                                @endif
                                
                                @if($product->hasDiscount())
                                <div class="product-badge-modern">
                                    -{{ $product->discountPercentage() }}% OFF
                                </div>
                                @endif
                                
                                @if($product->is_featured)
                                <div class="product-badge-modern featured" style="top: 55px;">
                                    <i class="bi bi-star-fill me-1"></i>{{ __('Featured') }}
                                </div>
                                @endif
                                
                                <div class="product-actions-overlay">
                                    <button class="action-btn-circle" title="{{ __('Quick View') }}" onclick="quickView({{ $product->id }})">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    

                                      @auth('web')
                                            @php
                                                $inWishlist = $product->isInWishlist(auth('web')->id());
                                            @endphp

                                            <button type="button"
                                                class="action-btn-circle wishlist-toggle {{ $inWishlist ? 'active' : '' }}"
                                                data-product-id="{{ $product->id }}"
                                                data-in-wishlist="{{ $inWishlist ? 'true' : 'false' }}"
                                                data-product-name="test"
                                                title="{{ $inWishlist ? __('Remove from Wishlist') : __('Add to Wishlist') }}">
                                                <i class="bi {{ $inWishlist ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                                            </button>
                                        @else
                                            <a href="{{ route('login') }}" class="action-btn-circle"
                                                title="{{ __('Login to add to wishlist') }}">
                                                <i class="bi bi-heart"></i>
                                            </a>
                                        @endauth
                                    <a href="{{ route('products.show', $product->slug) }}" class="action-btn-circle" title="{{ __('View Details') }}">
                                        <i class="bi bi-info-circle"></i>
                                    </a>
                                </div>
                                
                                @if($product->isInStock())
                                <div class="stock-badge in-stock">
                                    <i class="bi bi-check-circle-fill"></i>
                                    {{ __('In Stock') }}
                                </div>
                                @else
                                <div class="stock-badge out-of-stock">
                                    <i class="bi bi-x-circle-fill"></i>
                                    {{ __('Out of Stock') }}
                                </div>
                                @endif
                            </div>
                            
                            <div class="product-info-modern">
                                <div class="product-category">
                                    {{ $product->category->name() }}
                                </div>
                                
                                <a href="{{ route('products.show', $product->slug) }}" class="product-title-modern">
                                    {{ $product->name() }}
                                </a>
                                
                                <div class="product-price-section">
                                    <span class="product-price-current">
                                        ${{ number_format($product->price, 2) }}
                                    </span>
                                    @if($product->hasDiscount())
                                    <span class="product-price-original">
                                        ${{ number_format($product->compare_price, 2) }}
                                    </span>
                                    @endif
                                </div>
                                
                                @if($product->isInStock())
                                <form action="{{ route('cart.add', $product) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn-add-cart">
                                        <i class="bi bi-cart-plus"></i>
                                        {{ __('Add to Cart') }}
                                    </button>
                                </form>
                                @else
                                <button class="btn-add-cart" disabled>
                                    <i class="bi bi-x-circle"></i>
                                    {{ __('Out of Stock') }}
                                </button>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="bi bi-inbox"></i>
                                </div>
                                <h4>{{ __('No Products Found') }}</h4>
                                <p>{{ __('We couldn\'t find any products matching your criteria. Try adjusting your filters.') }}</p>
                                <a href="{{ route('products.index') }}" class="btn btn-hero-primary">
                                    <i class="bi bi-arrow-counterclockwise me-2"></i>{{ __('View All Products') }}
                                </a>
                            </div>
                        </div>
                        @endforelse
                    </div>
                    
                    <!-- Pagination -->
                    @if($products->hasPages())
                    <div class="pagination-wrapper">
                        {{ $products->appends(request()->query())->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Remove individual filter
    function removeFilter(filterName) {
        const form = document.getElementById('filterForm');
        const input = form.querySelector(`[name="${filterName}"]`);
        if (input) {
            input.value = '';
            form.submit();
        }
    }
    
    // Quick View (placeholder - implement modal)
    function quickView(productId) {
        console.log('Quick view for product:', productId);
        // TODO: Implement quick view modal
        alert('Quick view feature coming soon!');
    }
    
    // Add to Wishlist (placeholder)
    function addToWishlist(productId) {
        console.log('Add to wishlist:', productId);
        // TODO: Implement wishlist functionality
        alert('Wishlist feature coming soon!');
    }
    
    // Auto-submit filter form on select change
    document.querySelectorAll('.filters-sidebar select').forEach(select => {
        select.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.wishlist-toggle').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const productId = this.dataset.productId;
                const inWishlist = this.dataset.inWishlist === 'true';
                const productName = this.dataset.productName;

                if (inWishlist) {
                    removeFromWishlist(productId, this);
                } else {
                    addToWishlist(productId, this);
                }
            });
        });

        function addToWishlist(productId, button) {
            const originalHtml = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            fetch('/wishlist/add', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId
                })
            })
            .then(response => {
                // Check if response is ok
                if (!response.ok) {
                    // Try to parse error response
                    return response.json().then(errData => {
                        throw new Error(errData.message || 'Server error');
                    }).catch(() => {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update button state
                    button.classList.add('active');
                    button.dataset.inWishlist = 'true';
                    button.innerHTML = '<i class="bi bi-heart-fill"></i>';
                    button.title = '{{ __('Remove from Wishlist') }}';

                    // Update wishlist count
                    updateWishlistCount(data.wishlist_count);

                    showToast('success', data.message);
                } else {
                    button.innerHTML = originalHtml;
                    showToast('error', data.message || '{{ __('Failed to add to wishlist') }}');
                }
                button.disabled = false;
            })
            .catch(error => {
                console.error('Wishlist Add Error:', error);
                button.innerHTML = originalHtml;
                button.disabled = false;
                showToast('error', error.message || '{{ __('An error occurred. Please try again.') }}');
            });
        }

        function removeFromWishlist(productId, button) {
            const originalHtml = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            fetch(`/wishlist/product/${productId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                // Check if response is ok
                if (!response.ok) {
                    // Try to parse error response
                    return response.json().then(errData => {
                        throw new Error(errData.message || 'Server error');
                    }).catch(() => {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update button state
                    button.classList.remove('active');
                    button.dataset.inWishlist = 'false';
                    button.innerHTML = '<i class="bi bi-heart"></i>';
                    button.title = '{{ __('Add to Wishlist') }}';

                    // Update wishlist count
                    updateWishlistCount(data.wishlist_count);

                    showToast('success', data.message);
                } else {
                    button.innerHTML = originalHtml;
                    showToast('error', data.message || '{{ __('Failed to remove from wishlist') }}');
                }
                button.disabled = false;
            })
            .catch(error => {
                console.error('Wishlist Remove Error:', error);
                button.innerHTML = originalHtml;
                button.disabled = false;
                showToast('error', error.message || '{{ __('An error occurred. Please try again.') }}');
            });
        }

        function updateWishlistCount(count) {
            // Update all wishlist count badges
            const wishlistBadges = document.querySelectorAll('.wishlist-count, .icon-badge');
            wishlistBadges.forEach(badge => {
                if (badge.closest('.icon-wrapper')?.querySelector('.bi-heart') || 
                    badge.classList.contains('wishlist-count')) {
                    badge.textContent = count;
                    badge.style.display = count > 0 ? 'flex' : 'none';
                }
            });
        }

        function showToast(type, message) {
            // Remove existing toasts
            const existingToasts = document.querySelectorAll('.wishlist-toast');
            existingToasts.forEach(toast => toast.remove());

            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed wishlist-toast`;
            toast.style.cssText = 'top: 80px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);';
            toast.innerHTML = `
                <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(toast);

            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 3000);
        }
    });
</script>
@endpush