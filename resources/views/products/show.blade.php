@extends('layouts.app')

@section('title', $product->name())

@push('styles')
<style>
    .product-detail-section {
        padding: 60px 0;
        background: white;
    }
    
    .breadcrumb {
        background: var(--light-gray);
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 40px;
    }
    
    .breadcrumb-item a {
        color: var(--medium-gray);
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .breadcrumb-item a:hover {
        color: var(--primary-color);
    }
    
    .breadcrumb-item.active {
        color: var(--accent-color);
    }
    
    /* Product Image Gallery */
    .product-gallery {
        position: relative;
    }
    
    .main-product-image {
        width: 100%;
        height: 550px;
        background: var(--light-gray);
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 20px;
        position: relative;
    }
    
    .main-product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .product-badges {
        position: absolute;
        top: 20px;
        left: 20px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        z-index: 10;
    }
    
    .detail-badge {
        padding: 8px 16px;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.9rem;
        display: inline-block;
        color: white;
    }
    
    .detail-badge.sale {
        background: var(--danger-color);
    }
    
    .detail-badge.stock {
        background: var(--success-color);
    }
    
    .detail-badge.low-stock {
        background: var(--warning-color);
    }
    
    .detail-badge.out-stock {
        background: var(--medium-gray);
    }
    
    /* Thumbnail Gallery */
    .thumbnail-gallery {
        display: flex;
        gap: 15px;
        overflow-x: auto;
        padding: 10px 0;
    }
    
    .thumbnail-item {
        flex: 0 0 100px;
        height: 100px;
        border-radius: 10px;
        overflow: hidden;
        cursor: pointer;
        border: 3px solid transparent;
        transition: all 0.3s ease;
    }
    
    .thumbnail-item:hover,
    .thumbnail-item.active {
        border-color: var(--primary-color);
        transform: scale(1.05);
    }
    
    .thumbnail-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    /* Product Info */
    .product-detail-info {
        padding-left: 40px;
    }
    
    .product-detail-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--accent-color);
        margin-bottom: 15px;
        line-height: 1.2;
    }
    
    .product-sku {
        color: var(--medium-gray);
        font-size: 0.95rem;
        margin-bottom: 20px;
    }
    
    .product-rating {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
    }
    
    .stars {
        color: #ffc107;
        font-size: 1.2rem;
    }
    
    .rating-count {
        color: var(--medium-gray);
        font-size: 0.9rem;
    }
    
    .product-detail-price {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 10px;
    }
    
    .product-compare-price {
        font-size: 1.5rem;
        color: var(--medium-gray);
        text-decoration: line-through;
        font-weight: 400;
        margin-left: 15px;
    }
    
    .savings-badge {
        background: var(--success-color);
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        margin-left: 10px;
    }
    
    .product-short-desc {
        font-size: 1.1rem;
        color: var(--medium-gray);
        line-height: 1.8;
        margin: 25px 0;
        padding: 20px;
        background: var(--light-gray);
        border-radius: 10px;
        border-left: 4px solid var(--primary-color);
    }
    
    /* Stock Status */
    .stock-status {
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
    }
    
    .stock-status.in-stock {
        background: rgba(39, 174, 96, 0.1);
        color: var(--success-color);
    }
    
    .stock-status.low-stock {
        background: rgba(243, 156, 18, 0.1);
        color: var(--warning-color);
    }
    
    .stock-status.out-stock {
        background: rgba(231, 76, 60, 0.1);
        color: var(--danger-color);
    }
    
    /* Quantity Selector */
    .quantity-selector {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .quantity-label {
        font-weight: 600;
        color: var(--accent-color);
        font-size: 1.1rem;
    }
    
    .quantity-input-group {
        display: flex;
        align-items: center;
        border: 2px solid var(--border-color);
        border-radius: 10px;
        overflow: hidden;
    }
    
    .qty-btn {
        background: var(--light-gray);
        border: none;
        width: 45px;
        height: 50px;
        font-size: 1.3rem;
        color: var(--accent-color);
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .qty-btn:hover {
        background: var(--primary-color);
        color: white;
    }
    
    .qty-input {
        border: none;
        width: 70px;
        height: 50px;
        text-align: center;
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--accent-color);
    }
    
    .qty-input:focus {
        outline: none;
    }
    
    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 15px;
        margin-bottom: 30px;
    }
    
    .btn-add-cart {
        flex: 1;
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 18px 40px;
        border-radius: 15px;
        font-size: 1.2rem;
        font-weight: 600;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    
    .btn-add-cart:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(32, 178, 170, 0.3);
    }
    
    .btn-wishlist {
        width: 60px;
        height: 60px;
        background: white;
        border: 2px solid var(--border-color);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: var(--medium-gray);
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .btn-wishlist:hover {
        border-color: var(--danger-color);
        color: var(--danger-color);
        background: rgba(231, 76, 60, 0.1);
    }
    
    /* Product Meta */
    .product-meta {
        border-top: 2px solid var(--border-color);
        padding-top: 30px;
        margin-top: 30px;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
        padding: 15px;
        background: var(--light-gray);
        border-radius: 10px;
    }
    
    .meta-icon {
        width: 50px;
        height: 50px;
        background: white;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: var(--primary-color);
    }
    
    .meta-content h6 {
        margin: 0;
        font-weight: 600;
        color: var(--accent-color);
        font-size: 0.95rem;
    }
    
    .meta-content p {
        margin: 0;
        color: var(--medium-gray);
        font-size: 0.9rem;
    }
    
    /* Tabs Section */
    .product-tabs {
        background: var(--light-gray);
        padding: 60px 0;
        margin-top: 60px;
    }
    
    .custom-tabs {
        border: none;
        display: flex;
        gap: 10px;
        margin-bottom: 40px;
        flex-wrap: wrap;
    }
    
    .custom-tabs .nav-link {
        border: none;
        background: white;
        color: var(--medium-gray);
        padding: 15px 30px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .custom-tabs .nav-link:hover {
        background: var(--primary-color);
        color: white;
    }
    
    .custom-tabs .nav-link.active {
        background: var(--primary-color);
        color: white;
        box-shadow: 0 5px 15px rgba(32, 178, 170, 0.3);
    }
    
    .tab-content-box {
        background: white;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    }
    
    .description-content {
        font-size: 1.05rem;
        line-height: 1.8;
        color: var(--medium-gray);
    }
    
    /* Related Products */
    .related-section {
        padding: 80px 0;
        background: white;
    }
    
    .related-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--accent-color);
        text-align: center;
        margin-bottom: 50px;
    }
    
    /* Mobile Responsive */
    @media (max-width: 991px) {
        .product-detail-info {
            padding-left: 0;
            margin-top: 30px;
        }
        
        .product-detail-title {
            font-size: 2rem;
        }
        
        .product-detail-price {
            font-size: 2rem;
        }
        
        .main-product-image {
            height: 400px;
        }
        
        .action-buttons {
            flex-direction: column;
        }
        
        .btn-wishlist {
            width: 100%;
        }
    }
    
    @media (max-width: 576px) {
        .product-detail-title {
            font-size: 1.5rem;
        }
        
        .product-detail-price {
            font-size: 1.8rem;
        }
        
        .main-product-image {
            height: 300px;
        }
        
        .quantity-selector {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endpush

@section('content')
<div class="product-detail-section">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bi bi-house-door me-2"></i>{{ __('home') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}">{{ __('products') }}</a></li>
                @if($product->category)
                <li class="breadcrumb-item"><a href="{{ route('products.category', $product->category->slug) }}">{{ $product->category->name() }}</a></li>
                @endif
                <li class="breadcrumb-item active">{{ $product->name() }}</li>
            </ol>
        </nav>

        <div class="row">
            <!-- Product Gallery -->
            <div class="col-lg-6">
                <div class="product-gallery">
                    <div class="main-product-image">
                        <div class="product-badges">
                            @if($product->hasDiscount())
                            <span class="detail-badge sale">-{{ $product->discountPercentage() }}% OFF</span>
                            @endif
                            
                            @if($product->isInStock())
                                @if($product->isLowStock())
                                <span class="detail-badge low-stock">{{ __('Low Stock') }}</span>
                                @else
                                <span class="detail-badge stock">{{ __('in_stock') }}</span>
                                @endif
                            @else
                            <span class="detail-badge out-stock">{{ __('out_of_stock') }}</span>
                            @endif
                        </div>
                        
                        @if($product->primaryImage)
                        <img id="mainImage" src="{{ asset('storage/' . $product->primaryImage->image_path) }}" alt="{{ $product->name() }}">
                        @else
                        <img id="mainImage" src="https://via.placeholder.com/600x600?text={{ urlencode($product->name()) }}" alt="{{ $product->name() }}">
                        @endif
                    </div>
                    
                    <!-- Thumbnail Gallery -->
                    @if($product->images->count() > 1)
                    <div class="thumbnail-gallery">
                        @foreach($product->images as $index => $image)
                        <div class="thumbnail-item {{ $index === 0 ? 'active' : '' }}" onclick="changeImage('{{ asset('storage/' . $image->image_path) }}', this)">
                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $image->alt_text }}">
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Product Info -->
            <div class="col-lg-6">
                <div class="product-detail-info">
                    <h1 class="product-detail-title">{{ $product->name() }}</h1>
                    <p class="product-sku"><i class="bi bi-upc-scan me-2"></i>{{ __('sku') }}: {{ $product->sku }}</p>
                    
                    <!-- Rating -->
                    <div class="product-rating">
                        <div class="stars">★★★★★</div>
                        <span class="rating-count">(4.8 / 5.0 - 248 {{ __('reviews') }})</span>
                    </div>
                    
                    <!-- Price -->
                    <div class="product-detail-price">
                        ₹{{ number_format($product->price, 2) }}
                        @if($product->hasDiscount())
                        <span class="product-compare-price">₹{{ number_format($product->compare_price, 2) }}</span>
                        <span class="savings-badge">{{ __('Save') }} ₹{{ number_format($product->compare_price - $product->price, 2) }}</span>
                        @endif
                    </div>
                    
                    <!-- Short Description -->
                    @if($product->shortDescription())
                    <div class="product-short-desc">
                        <i class="bi bi-info-circle me-2"></i>{{ $product->shortDescription() }}
                    </div>
                    @endif
                    
                    <!-- Stock Status -->
                    @if($product->isInStock())
                        @if($product->isLowStock())
                        <div class="stock-status low-stock">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <span>{{ __('Hurry! Only') }} {{ $product->stock_quantity }} {{ __('left in stock!') }}</span>
                        </div>
                        @else
                        <div class="stock-status in-stock">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>{{ __('In Stock') }} - {{ $product->stock_quantity }} {{ __('available') }}</span>
                        </div>
                        @endif
                    @else
                    <div class="stock-status out-stock">
                        <i class="bi bi-x-circle-fill"></i>
                        <span>{{ __('out_of_stock') }}</span>
                    </div>
                    @endif
                    
                    @if($product->isInStock())
                    <form action="{{ route('cart.add', $product) }}" method="POST">
                        @csrf
                        <!-- Quantity Selector -->
                        <div class="quantity-selector">
                            <span class="quantity-label">{{ __('quantity') }}:</span>
                            <div class="quantity-input-group">
                                <button type="button" class="qty-btn" onclick="decreaseQty()">−</button>
                                <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $product->stock_quantity }}" class="qty-input" readonly>
                                <button type="button" class="qty-btn" onclick="increaseQty({{ $product->stock_quantity }})">+</button>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <button type="submit" class="btn-add-cart">
                                <i class="bi bi-cart-plus-fill"></i>
                                <span>{{ __('add_to_cart') }}</span>
                            </button>
                            <button type="button" class="btn-wishlist" title="{{ __('Add to Wishlist') }}">
                                <i class="bi bi-heart"></i>
                            </button>
                        </div>
                    </form>
                    @endif
                    
                    <!-- Product Meta -->
                    <div class="product-meta">
                        <div class="meta-item">
                            <div class="meta-icon">
                                <i class="bi bi-truck"></i>
                            </div>
                            <div class="meta-content">
                                <h6>{{ __('Free Shipping') }}</h6>
                                <p>{{ __('On orders over ₹999') }}</p>
                            </div>
                        </div>
                        
                        <div class="meta-item">
                            <div class="meta-icon">
                                <i class="bi bi-arrow-repeat"></i>
                            </div>
                            <div class="meta-content">
                                <h6>{{ __('Easy Returns') }}</h6>
                                <p>{{ __('30-day return policy') }}</p>
                            </div>
                        </div>
                        
                        <div class="meta-item">
                            <div class="meta-icon">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <div class="meta-content">
                                <h6>{{ __('Secure Payment') }}</h6>
                                <p>{{ __('100% secure transactions') }}</p>
                            </div>
                        </div>
                        
                        @if($product->category)
                        <div class="meta-item">
                            <div class="meta-icon">
                                <i class="bi bi-tag"></i>
                            </div>
                            <div class="meta-content">
                                <h6>{{ __('category') }}</h6>
                                <p>
                                    <a href="{{ route('products.category', $product->category->slug) }}" style="color: var(--primary-color); text-decoration: none;">
                                        {{ $product->category->name() }}
                                    </a>
                                </p>
                            </div>
                        </div>
                        @endif
                        
                        @if($product->weight)
                        <div class="meta-item">
                            <div class="meta-icon">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <div class="meta-content">
                                <h6>{{ __('Weight') }}</h6>
                                <p>{{ $product->weight }} kg</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($product->dimensions)
                        <div class="meta-item">
                            <div class="meta-icon">
                                <i class="bi bi-rulers"></i>
                            </div>
                            <div class="meta-content">
                                <h6>{{ __('Dimensions') }}</h6>
                                <p>{{ $product->dimensions }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Tabs -->
<div class="product-tabs">
    <div class="container">
        <ul class="nav custom-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#description">
                    <i class="bi bi-file-text me-2"></i>{{ __('description') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#specifications">
                    <i class="bi bi-list-check me-2"></i>{{ __('Specifications') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#reviews">
                    <i class="bi bi-star me-2"></i>{{ __('Reviews') }}
                </a>
            </li>
        </ul>
        
        <div class="tab-content">
            <div class="tab-pane fade show active" id="description">
                <div class="tab-content-box">
                    <h4 class="mb-4">{{ __('Product Description') }}</h4>
                    <div class="description-content">
                        {!! nl2br(e($product->description())) !!}
                    </div>
                </div>
            </div>
            
            <div class="tab-pane fade" id="specifications">
                <div class="tab-content-box">
                    <h4 class="mb-4">{{ __('Product Specifications') }}</h4>
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th width="30%">{{ __('SKU') }}</th>
                                <td>{{ $product->sku }}</td>
                            </tr>
                            @if($product->weight)
                            <tr>
                                <th>{{ __('Weight') }}</th>
                                <td>{{ $product->weight }} kg</td>
                            </tr>
                            @endif
                            @if($product->dimensions)
                            <tr>
                                <th>{{ __('Dimensions') }}</th>
                                <td>{{ $product->dimensions }}</td>
                            </tr>
                            @endif
                            @if($product->category)
                            <tr>
                                <th>{{ __('category') }}</th>
                                <td>{{ $product->category->name() }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th>{{ __('Stock Status') }}</th>
                                <td>
                                    @if($product->isInStock())
                                        <span class="badge bg-success">{{ __('in_stock') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('out_of_stock') }}</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="tab-pane fade" id="reviews">
                <div class="tab-content-box">
                    <h4 class="mb-4">{{ __('Customer Reviews') }}</h4>
                    <div class="text-center py-5">
                        <i class="bi bi-chat-square-quote" style="font-size: 4rem; color: var(--medium-gray);"></i>
                        <p class="mt-3 text-muted">{{ __('No reviews yet. Be the first to review this product!') }}</p>
                        <button class="btn btn-primary mt-3">
                            <i class="bi bi-pencil-square me-2"></i>{{ __('Write a Review') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Related Products -->
@if($relatedProducts->count() > 0)
<div class="related-section">
    <div class="container">
        <h2 class="related-title">{{ __('related_products') }}</h2>
        <div class="owl-carousel related-carousel">
            @foreach($relatedProducts as $related)
            <div class="product-card">
                <div class="product-image">
                    @if($related->hasDiscount())
                    <div class="product-badge sale">-{{ $related->discountPercentage() }}%</div>
                    @endif
                    <div class="product-actions">
                        <button class="action-btn" title="{{ __('Add to Wishlist') }}">
                            <i class="bi bi-heart"></i>
                        </button>
                        <a href="{{ route('products.show', $related->slug) }}" class="action-btn" title="{{ __('Quick View') }}">
                            <i class="bi bi-eye"></i>
                        </a>
                    </div>
                    <a href="{{ route('products.show', $related->slug) }}">
                        @if($related->primaryImage)
                        <img src="{{ asset('storage/' . $related->primaryImage->image_path) }}" alt="{{ $related->name() }}">
                        @else
                        <img src="https://via.placeholder.com/400x250?text={{ urlencode($related->name()) }}" alt="{{ $related->name() }}">
                        @endif
                    </a>
                </div>
                <div class="product-info">
                    <a href="{{ route('products.show', $related->slug) }}">
                        <h5 class="product-title">{{ $related->name() }}</h5>
                    </a>
                    <div class="product-price">
                        ₹{{ number_format($related->price, 2) }}
                        @if($related->hasDiscount())
                        <span class="old-price">₹{{ number_format($related->compare_price, 2) }}</span>
                        @endif
                    </div>
                    @if($related->isInStock())
                    <form action="{{ route('cart.add', $related) }}" method="POST">
                        @csrf
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="add-to-cart">
                            <i class="bi bi-cart-plus me-2"></i>{{ __('add_to_cart') }}
                        </button>
                    </form>
                    @else
                    <button class="add-to-cart" disabled style="background: var(--medium-gray);">
                        <i class="bi bi-x-circle me-2"></i>{{ __('out_of_stock') }}
                    </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    // Change main image on thumbnail click
    function changeImage(src, element) {
        document.getElementById('mainImage').src = src;
        
        // Remove active class from all thumbnails
        document.querySelectorAll('.thumbnail-item').forEach(item => {
            item.classList.remove('active');
        });
        
        // Add active class to clicked thumbnail
        element.classList.add('active');
    }
    
    // Quantity controls
    function increaseQty(max) {
        const input = document.getElementById('quantity');
        let value = parseInt(input.value);
        if (value < max) {
            input.value = value + 1;
        }
    }
    
    function decreaseQty() {
        const input = document.getElementById('quantity');
        let value = parseInt(input.value);
        if (value > 1) {
            input.value = value - 1;
        }
    }
    
    // Related Products Carousel
    $(document).ready(function(){
        $('.related-carousel').owlCarousel({
            loop: true,
            margin: 30,
            nav: true,
            dots: true,
            autoplay: true,
            autoplayTimeout: 4000,
            autoplayHoverPause: true,
            responsive: {
                0: {
                    items: 1
                },
                576: {
                    items: 2
                },
                768: {
                    items: 3
                },
                992: {
                    items: 4
                }
            }
        });
    });
</script>
@endpush