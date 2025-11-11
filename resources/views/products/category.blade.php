@extends('layouts.app')

@section('title', $category->name())

@push('styles')
    <style>
        /* Category Page Specific Styles */
        .category-wrapper {
            padding: 40px 0;
            background: var(--light-gray);
        }

        .category-hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 60px 0;
            border-radius: 20px;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }

        .category-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: pulse 15s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1) rotate(0deg);
                opacity: 0.5;
            }

            50% {
                transform: scale(1.2) rotate(5deg);
                opacity: 0.8;
            }
        }

        .category-hero-content {
            position: relative;
            z-index: 2;
        }

        .category-icon {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            backdrop-filter: blur(10px);
        }

        .category-icon i {
            font-size: 3rem;
            color: white;
        }

        .category-hero h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .category-hero-description {
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto 20px;
        }

        .category-stats {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 25px;
            flex-wrap: wrap;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .stat-item i {
            font-size: 1.5rem;
        }

        .stat-item span {
            font-size: 1.1rem;
            font-weight: 600;
        }

        /* Breadcrumb */
        .breadcrumb-modern {
            background: white;
            padding: 15px 25px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .breadcrumb-modern a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .breadcrumb-modern a:hover {
            color: var(--primary-dark);
        }

        .breadcrumb-modern .active {
            color: var(--accent-color);
            font-weight: 600;
        }

        /* Sidebar */
        .categories-sidebar {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 20px;
        }

        .sidebar-header {
            padding-bottom: 15px;
            margin-bottom: 20px;
            border-bottom: 2px solid var(--border-color);
        }

        .sidebar-header h5 {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--accent-color);
            margin: 0;
        }

        .category-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .category-list-item {
            margin-bottom: 12px;
        }

        .category-list-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 15px;
            border-radius: 10px;
            color: var(--accent-color);
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .category-list-link:hover {
            background: var(--light-gray);
            color: var(--primary-color);
            transform: translateX(5px);
        }

        .category-list-link.active {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            font-weight: 600;
        }

        .category-list-link i {
            font-size: 1.1rem;
        }

        .category-count {
            background: rgba(0, 0, 0, 0.1);
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .category-list-link.active .category-count {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Products Section */
        .products-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--border-color);
            flex-wrap: wrap;
            gap: 15px;
        }

        .section-title-group h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--accent-color);
            margin: 0 0 5px 0;
        }

        .products-count {
            color: var(--medium-gray);
            font-size: 1rem;
        }

        .view-toggle {
            display: flex;
            gap: 10px;
            background: var(--light-gray);
            padding: 5px;
            border-radius: 10px;
        }

        .view-btn {
            padding: 8px 15px;
            border: none;
            background: transparent;
            color: var(--medium-gray);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .view-btn.active {
            background: white;
            color: var(--primary-color);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .view-btn:hover {
            color: var(--primary-color);
        }

        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
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
            height: 260px;
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
            transform: scale(1.15);
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
            text-decoration: none;
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

        .product-title-modern {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--accent-color);
            margin-bottom: 8px;
            text-decoration: none;
            display: block;
            line-height: 1.4;
            min-height: 2.8em;
        }

        .product-title-modern:hover {
            color: var(--primary-color);
        }

        .product-description {
            font-size: 0.9rem;
            color: var(--medium-gray);
            margin-bottom: 12px;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
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

        /* Responsive */
        @media (max-width: 991px) {
            .categories-sidebar {
                position: static;
                margin-bottom: 30px;
            }

            .category-hero {
                padding: 40px 20px;
            }

            .category-hero h1 {
                font-size: 2rem;
            }

            .category-stats {
                gap: 20px;
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

            .section-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
@endpush

@section('content')
    <div class="category-wrapper">
        <div class="container">
            <!-- Breadcrumb -->
            <nav class="breadcrumb-modern">
                <a href="{{ route('home') }}">
                    <i class="bi bi-house-door me-2"></i>{{ __('Home') }}
                </a>
                <span class="mx-2">/</span>
                <a href="{{ route('products.index') }}">{{ __('Products') }}</a>
                <span class="mx-2">/</span>
                <span class="active">{{ $category->name() }}</span>
            </nav>

            <!-- Category Hero -->
            <div class="category-hero">
                <div class="category-hero-content text-center">
                    <div class="category-icon">
                        <i class="bi bi-tag-fill"></i>
                    </div>
                    <h1>{{ $category->name() }}</h1>
                    @if ($category->description())
                        <p class="category-hero-description">{{ $category->description() }}</p>
                    @endif
                    <div class="category-stats">
                        <div class="stat-item">
                            <i class="bi bi-box-seam"></i>
                            <span>{{ $products->total() }} {{ __('Products') }}</span>
                        </div>
                        <div class="stat-item">
                            <i class="bi bi-lightning-fill"></i>
                            <span>{{ __('Fast Delivery') }}</span>
                        </div>
                        <div class="stat-item">
                            <i class="bi bi-shield-check"></i>
                            <span>{{ __('Quality Guaranteed') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Sidebar -->
                <div class="col-lg-3">
                    <div class="categories-sidebar">
                        <div class="sidebar-header">
                            <h5><i class="bi bi-grid-3x3-gap me-2"></i>{{ __('All Categories') }}</h5>
                        </div>

                        <ul class="category-list">
                            <li class="category-list-item">
                                <a href="{{ route('products.index') }}" class="category-list-link">
                                    <span><i class="bi bi-collection me-2"></i>{{ __('All Products') }}</span>
                                </a>
                            </li>
                            @foreach ($categories as $cat)
                                <li class="category-list-item">
                                    <a href="{{ route('products.category', $cat->slug) }}"
                                        class="category-list-link {{ $cat->id == $category->id ? 'active' : '' }}">
                                        <span><i class="bi bi-tag me-2"></i>{{ $cat->name() }}</span>
                                        @if ($cat->products_count ?? 0)
                                            <span class="category-count">{{ $cat->products_count }}</span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Products -->
                <div class="col-lg-9">
                    <div class="products-section">
                        <!-- Section Header -->
                        <div class="section-header">
                            <div class="section-title-group">
                                <h2>{{ $category->name() }}</h2>
                                <p class="products-count">
                                    {{ __('Showing') }} <strong>{{ $products->count() }}</strong> {{ __('of') }}
                                    <strong>{{ $products->total() }}</strong> {{ __('products') }}
                                </p>
                            </div>

                            <div class="view-toggle">
                                <button class="view-btn active" onclick="setGridView(3)">
                                    <i class="bi bi-grid-3x3-gap"></i>
                                </button>
                                <button class="view-btn" onclick="setGridView(4)">
                                    <i class="bi bi-grid"></i>
                                </button>
                                <button class="view-btn" onclick="setListView()">
                                    <i class="bi bi-list"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Products Grid -->
                        <div class="products-grid" id="productsGrid">
                            @forelse($products as $product)
                                <div class="product-card-modern">
                                    <div class="product-image-container">
                                        @if ($product->primaryImage)
                                            <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}"
                                                alt="{{ $product->name() }}">
                                        @else
                                            <img src="https://via.placeholder.com/400x260?text={{ urlencode($product->name()) }}"
                                                alt="{{ $product->name() }}">
                                        @endif

                                        @if ($product->hasDiscount())
                                            <div class="product-badge-modern">
                                                -{{ $product->discountPercentage() }}% OFF
                                            </div>
                                        @endif

                                        <div class="product-actions-overlay">
                                            <button class="action-btn-circle" title="{{ __('Quick View') }}">
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
                                            <a href="{{ route('products.show', $product->slug) }}"
                                                class="action-btn-circle" title="{{ __('View Details') }}">
                                                <i class="bi bi-info-circle"></i>
                                            </a>
                                        </div>

                                        @if ($product->isInStock())
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
                                        <a href="{{ route('products.show', $product->slug) }}"
                                            class="product-title-modern">
                                            {{ $product->name() }}
                                        </a>

                                        @if ($product->shortDescription())
                                            <p class="product-description">
                                                {{ Str::limit($product->shortDescription(), 80) }}</p>
                                        @endif

                                        <div class="product-price-section">
                                            <span class="product-price-current">
                                                ${{ number_format($product->price, 2) }}
                                            </span>
                                            @if ($product->hasDiscount())
                                                <span class="product-price-original">
                                                    ${{ number_format($product->compare_price, 2) }}
                                                </span>
                                            @endif
                                        </div>

                                        @if ($product->isInStock())
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
                                        <h4>{{ __('No Products in This Category') }}</h4>
                                        <p>{{ __('This category is currently empty. Check back soon or browse other categories.') }}
                                        </p>
                                        <a href="{{ route('products.index') }}" class="btn btn-hero-primary">
                                            <i class="bi bi-arrow-left me-2"></i>{{ __('Browse All Products') }}
                                        </a>
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        <!-- Pagination -->
                        @if ($products->hasPages())
                            <div class="pagination-wrapper">
                                {{ $products->links() }}
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
        // Grid View Toggle
        function setGridView(columns) {
            const grid = document.getElementById('productsGrid');
            if (columns === 3) {
                grid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(260px, 1fr))';
            } else {
                grid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(200px, 1fr))';
            }

            // Update active button
            document.querySelectorAll('.view-btn').forEach(btn => btn.classList.remove('active'));
            event.target.closest('.view-btn').classList.add('active');
        }

        // List View (placeholder)
        function setListView() {
            alert('List view feature coming soon!');
        }
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
