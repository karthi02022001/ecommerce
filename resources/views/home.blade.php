@extends('layouts.app')

@section('title', __('home'))

@section('content')
    <!-- Enhanced Hero Carousel -->
    <section class="hero-section">
        <div class="owl-carousel hero-carousel">
            <!-- Slide 1 - Welcome -->
            <div class="hero-slide"
                style="background-image: url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80')">
                <div class="hero-overlay"></div>
                <div class="container">
                    <div class="row">
                        <div class="col-lg-7 col-md-8">
                            <div class="hero-content" data-aos="fade-right" data-aos-delay="200">
                                <div class="hero-badge">{{ __('Welcome') }}</div>
                                <h1 class="hero-title">{{ __('Discover Premium') }}<br>{{ __('Quality Products') }}</h1>
                                <p class="hero-subtitle">
                                    {{ __('Shop the best products at unbeatable prices. Experience quality like never before.') }}
                                </p>
                                <div class="hero-buttons">
                                    <a href="{{ route('products.index') }}" class="btn btn-hero-primary">
                                        {{ __('Shop Now') }}
                                        <i class="bi bi-arrow-right ms-2"></i>
                                    </a>
                                    <a href="#featured" class="btn btn-hero-outline">
                                        {{ __('View Collections') }}
                                    </a>
                                </div>
                                <div class="hero-stats mt-4">
                                    <div class="stat-item">
                                        <h3>1000+</h3>
                                        <p>{{ __('Products') }}</p>
                                    </div>
                                    <div class="stat-item">
                                        <h3>50k+</h3>
                                        <p>{{ __('Happy Customers') }}</p>
                                    </div>
                                    <div class="stat-item">
                                        <h3>24/7</h3>
                                        <p>{{ __('Support') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 2 - Latest Products -->
            <div class="hero-slide"
                style="background-image: url('https://images.unsplash.com/photo-1472851294608-062f824d29cc?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80')">
                <div class="hero-overlay"></div>
                <div class="container">
                    <div class="row">
                        <div class="col-lg-7 col-md-8">
                            <div class="hero-content" data-aos="fade-right" data-aos-delay="200">
                                <div class="hero-badge">{{ __('New Arrival') }}</div>
                                <h1 class="hero-title">{{ __('Latest Products') }}<br>{{ __('Fresh Styles') }}</h1>
                                <p class="hero-subtitle">
                                    {{ __('Check out our latest collection of premium products. Fresh styles just for you.') }}
                                </p>
                                <div class="hero-buttons">
                                    <a href="{{ route('products.index', ['sort' => 'newest']) }}"
                                        class="btn btn-hero-primary">
                                        {{ __('View New Items') }}
                                        <i class="bi bi-arrow-right ms-2"></i>
                                    </a>
                                    <a href="{{ route('products.index') }}" class="btn btn-hero-outline">
                                        {{ __('Browse All') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 3 - Special Offers -->
            <div class="hero-slide"
                style="background-image: url('https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80')">
                <div class="hero-overlay"></div>
                <div class="container">
                    <div class="row">
                        <div class="col-lg-7 col-md-8">
                            <div class="hero-content" data-aos="fade-right" data-aos-delay="200">
                                <div class="hero-badge special">{{ __('Special Offer') }}</div>
                                <h1 class="hero-title">{{ __('Amazing Deals') }}<br>{{ __('Up to 50% Off') }}</h1>
                                <p class="hero-subtitle">
                                    {{ __('Discover amazing products and great deals. Limited time offers you don\'t want to miss!') }}
                                </p>
                                <div class="hero-buttons">
                                    <a href="{{ route('products.index', ['discount' => 1]) }}"
                                        class="btn btn-hero-primary">
                                        {{ __('View Deals') }}
                                        <i class="bi bi-arrow-right ms-2"></i>
                                    </a>
                                    <a href="{{ route('products.index') }}" class="btn btn-hero-outline">
                                        {{ __('Shop All') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Enhanced Category Section -->
    @if (isset($categories) && $categories->count() > 0)
        <section class="category-section" id="categories">
            <div class="container">
                <div class="section-header" data-aos="fade-up">
                    <div class="section-title-wrapper">
                        <h2 class="section-title">{{ __('Shop by Category') }}</h2>
                        <p class="section-subtitle">{{ __('Browse through our wide range of categories') }}</p>
                    </div>
                    <a href="{{ route('products.index') }}" class="view-all-link">
                        {{ __('View All') }} <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                <div class="row g-4">
                    @foreach ($categories->take(4) as $index => $category)
                        <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                            <a href="{{ route('products.category', $category->slug) }}" class="category-card">
                                <div class="category-image-wrapper">
                                    @if ($category->image)
                                        <img src="{{ asset('storage/' . $category->image) }}"
                                            alt="{{ $category->name() }}" class="category-image">
                                    @else
                                        <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80"
                                            alt="{{ $category->name() }}" class="category-image">
                                    @endif
                                    <div class="category-overlay">
                                        <div class="category-content">
                                            <h4>{{ $category->name() }}</h4>
                                            <p>{{ $category->description() ? Str::limit($category->description(), 50) : __('Explore Collection') }}
                                            </p>
                                            <span class="category-link-btn">
                                                {{ __('Shop Now') }} <i class="bi bi-arrow-right"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Featured Products Section -->
    @if ($featuredProducts->count() > 0)
        <section class="product-section featured-section" id="featured">
            <div class="container">
                <div class="section-header" data-aos="fade-up">
                    <div class="section-title-wrapper">
                        <h2 class="section-title">{{ __('Featured Products') }}</h2>
                        <p class="section-subtitle">{{ __('Handpicked products just for you') }}</p>
                    </div>
                    <a href="{{ route('products.index', ['featured' => 1]) }}" class="view-all-link">
                        {{ __('View All') }} <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                <div class="owl-carousel featured-carousel">
                    @foreach ($featuredProducts as $product)
                        <div class="product-card-wrapper">
                            <div class="product-card">
                                <div class="product-image">
                                    <!-- Product Badges -->
                                    <div class="product-badges">
                                        @if ($product->hasDiscount())
                                            <span
                                                class="product-badge discount">-{{ $product->discountPercentage() }}%</span>
                                        @endif
                                        <span class="product-badge featured">{{ __('Featured') }}</span>
                                    </div>

                                    <!-- Product Actions -->
                                    <div class="product-actions">
                                        @auth('web')
                                            @php
                                                $inWishlist = $product->isInWishlist(auth('web')->id());
                                            @endphp

                                            <button type="button"
                                                class="action-btn wishlist-toggle {{ $inWishlist ? 'active' : '' }}"
                                                data-product-id="{{ $product->id }}"
                                                data-in-wishlist="{{ $inWishlist ? 'true' : 'false' }}"
                                                data-product-name="test"
                                                title="{{ $inWishlist ? __('Remove from Wishlist') : __('Add to Wishlist') }}">
                                                <i class="bi {{ $inWishlist ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                                            </button>
                                        @else
                                            <a href="{{ route('login') }}" class="action-btn"
                                                title="{{ __('Login to add to wishlist') }}">
                                                <i class="bi bi-heart"></i>
                                            </a>
                                        @endauth
                                        <a href="{{ route('products.show', $product->slug) }}" class="action-btn"
                                            title="{{ __('Quick View') }}">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>

                                    <!-- Product Image -->
                                    <a href="{{ route('products.show', $product->slug) }}" class="product-image-link">
                                        @if ($product->primaryImage)
                                            <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}"
                                                alt="{{ $product->name() }}" class="img-fluid">
                                        @else
                                            <img src="https://via.placeholder.com/400x250?text={{ urlencode($product->name()) }}"
                                                alt="{{ $product->name() }}" class="img-fluid">
                                        @endif
                                    </a>
                                </div>

                                <div class="product-info">
                                    <a href="{{ route('products.show', $product->slug) }}" class="product-title-link">
                                        <h5 class="product-title">{{ $product->name() }}</h5>
                                    </a>

                                    @include('components.star-rating', [
                                        'rating' => $product->averageRating(),
                                        'count' => $product->reviewCount(),
                                    ])
                                    <div class="product-price-wrapper">
                                        <span class="product-price">₹{{ number_format($product->price, 2) }}</span>
                                        @if ($product->hasDiscount())
                                            <span
                                                class="old-price">₹{{ number_format($product->compare_price, 2) }}</span>
                                        @endif
                                    </div>

                                    @if ($product->isInStock())
                                        <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="add-to-cart-btn">
                                                <i class="bi bi-cart-plus me-2"></i>{{ __('Add to Cart') }}
                                            </button>
                                        </form>
                                    @else
                                        <button class="add-to-cart-btn out-of-stock" disabled>
                                            <i class="bi bi-x-circle me-2"></i>{{ __('Out of Stock') }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Promotional Banner -->
    <section class="promo-banner">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right">
                    <div class="promo-content">
                        <span class="promo-badge">{{ __('Limited Time Offer') }}</span>
                        <h2 class="promo-title">{{ __('Get 30% Off') }}<br>{{ __('Your First Order') }}</h2>
                        <p class="promo-description">
                            {{ __('Sign up today and enjoy exclusive discounts on your first purchase. Don\'t miss out on this special offer!') }}
                        </p>
                        <a href="{{ route('register') }}" class="btn btn-promo-primary">
                            {{ __('Sign Up Now') }}
                            <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="promo-image">
                        <img src="https://images.unsplash.com/photo-1607082349566-187342175e2f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                            alt="Promo" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Latest Products Section -->
    @if ($latestProducts->count() > 0)
        <section class="product-section latest-section">
            <div class="container">
                <div class="section-header" data-aos="fade-up">
                    <div class="section-title-wrapper">
                        <h2 class="section-title">{{ __('New Arrivals') }}</h2>
                        <p class="section-subtitle">{{ __('Fresh products added this week') }}</p>
                    </div>
                    <a href="{{ route('products.index', ['sort' => 'newest']) }}" class="view-all-link">
                        {{ __('View All') }} <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                <div class="owl-carousel new-arrivals-carousel">
                    @foreach ($latestProducts as $product)
                        <div class="product-card-wrapper">
                            <div class="product-card">
                                <div class="product-image">
                                    <!-- Product Badges -->
                                    <div class="product-badges">
                                        <span class="product-badge new">{{ __('New') }}</span>
                                        @if ($product->hasDiscount())
                                            <span
                                                class="product-badge discount">-{{ $product->discountPercentage() }}%</span>
                                        @endif
                                    </div>

                                    <!-- Product Actions -->
                                    <div class="product-actions">
                                        @auth('web')
                                            @php
                                                $inWishlist = $product->isInWishlist(auth('web')->id());
                                            @endphp

                                            <button type="button"
                                                class="action-btn wishlist-toggle {{ $inWishlist ? 'active' : '' }}"
                                                data-product-id="{{ $product->id }}"
                                                data-in-wishlist="{{ $inWishlist ? 'true' : 'false' }}"
                                                data-product-name="test"
                                                title="{{ $inWishlist ? __('Remove from Wishlist') : __('Add to Wishlist') }}">
                                                <i class="bi {{ $inWishlist ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                                            </button>
                                        @else
                                            <a href="{{ route('login') }}" class="action-btn"
                                                title="{{ __('Login to add to wishlist') }}">
                                                <i class="bi bi-heart"></i>
                                            </a>
                                        @endauth
                                        <a href="{{ route('products.show', $product->slug) }}" class="action-btn"
                                            title="{{ __('Quick View') }}">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>

                                    <!-- Product Image -->
                                    <a href="{{ route('products.show', $product->slug) }}" class="product-image-link">
                                        @if ($product->primaryImage)
                                            <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}"
                                                alt="{{ $product->name() }}" class="img-fluid">
                                        @else
                                            <img src="https://via.placeholder.com/400x250?text={{ urlencode($product->name()) }}"
                                                alt="{{ $product->name() }}" class="img-fluid">
                                        @endif
                                    </a>
                                </div>

                                <div class="product-info">
                                    <a href="{{ route('products.show', $product->slug) }}" class="product-title-link">
                                        <h5 class="product-title">{{ $product->name() }}</h5>
                                    </a>

                                    @include('components.star-rating', [
                                        'rating' => $product->averageRating(),
                                        'count' => $product->reviewCount(),
                                    ])

                                    <div class="product-price-wrapper">
                                        <span class="product-price">₹{{ number_format($product->price, 2) }}</span>
                                        @if ($product->hasDiscount())
                                            <span
                                                class="old-price">₹{{ number_format($product->compare_price, 2) }}</span>
                                        @endif
                                    </div>

                                    @if ($product->isInStock())
                                        <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="add-to-cart-btn">
                                                <i class="bi bi-cart-plus me-2"></i>{{ __('Add to Cart') }}
                                            </button>
                                        </form>
                                    @else
                                        <button class="add-to-cart-btn out-of-stock" disabled>
                                            <i class="bi bi-x-circle me-2"></i>{{ __('Out of Stock') }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="0">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-truck"></i>
                        </div>
                        <h5 class="feature-title">{{ __('Free Shipping') }}</h5>
                        <p class="feature-description">{{ __('Free shipping on orders over ₹500') }}</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h5 class="feature-title">{{ __('Secure Payment') }}</h5>
                        <p class="feature-description">{{ __('100% secure payment gateway') }}</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-arrow-repeat"></i>
                        </div>
                        <h5 class="feature-title">{{ __('Easy Returns') }}</h5>
                        <p class="feature-description">{{ __('30-day hassle-free returns') }}</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-headset"></i>
                        </div>
                        <h5 class="feature-title">{{ __('24/7 Support') }}</h5>
                        <p class="feature-description">{{ __('Always here to help you') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Customer Reviews Section -->
    <!-- Customer Reviews Section -->
    <section class="reviews-section">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <div class="section-title-wrapper">
                    <h2 class="section-title">{{ __('What Our Customers Say') }}</h2>
                    <p class="section-subtitle">{{ __('Real reviews from real customers') }}</p>
                </div>
            </div>

            @if ($reviews->count() > 0)
                <div class="owl-carousel reviews-carousel">
                    @foreach ($reviews as $review)
                        <div class="review-card">
                            <div class="review-header">
                                <div class="reviewer-avatar">
                                    @if ($review->user->profile_photo)
                                        <img src="{{ asset('storage/' . $review->user->profile_photo) }}"
                                            alt="{{ $review->user->name }}">
                                    @else
                                        <i class="bi bi-person-fill"></i>
                                    @endif
                                </div>
                                <div class="reviewer-info">
                                    <h6 class="reviewer-name">{{ $review->user->name }}</h6>
                                    <div class="review-stars">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $review->rating)
                                                <i class="bi bi-star-fill"></i>
                                            @else
                                                <i class="bi bi-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                            </div>

                            @if ($review->title)
                                <h6 class="review-title">{{ $review->title }}</h6>
                            @endif

                            <p class="review-text">
                                "{{ Str::limit($review->comment, 200) }}"
                            </p>

                            @if ($review->product)
                                <div class="review-product">
                                    <small class="text-muted">
                                        <i class="bi bi-box-seam"></i>
                                        {{ $review->product->name() }}
                                    </small>
                                </div>
                            @endif

                            <div class="review-footer">

                                <span class="review-date">{{ $review->time_ago }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info text-center" data-aos="fade-up">
                    <i class="bi bi-info-circle me-2"></i>
                    {{ __('No customer reviews yet. Be the first to share your experience!') }}
                </div>
            @endif
        </div>
    </section>

    <style>
        .reviews-section {
            padding: 80px 0;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 10px;
        }

        .section-subtitle {
            font-size: 1.1rem;
            color: #6c757d;
            margin: 0;
        }

        .review-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            min-height: 320px;
            display: flex;
            flex-direction: column;
        }

        .review-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(32, 178, 170, 0.15);
        }

        .review-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .reviewer-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #20b2aa, #17a2a0);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            overflow: hidden;
        }

        .reviewer-avatar i {
            font-size: 1.8rem;
            color: white;
        }

        .reviewer-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .reviewer-info {
            flex: 1;
        }

        .reviewer-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1a1a1a;
            margin: 0 0 8px 0;
        }

        .review-stars {
            display: flex;
            gap: 4px;
        }

        .review-stars i {
            color: #ffc107;
            font-size: 0.95rem;
        }

        .review-stars .bi-star {
            color: #e0e0e0;
        }

        .review-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 12px;
        }

        .review-text {
            font-size: 1rem;
            color: #495057;
            line-height: 1.7;
            margin-bottom: 15px;
            flex: 1;
            font-style: italic;
        }

        .review-product {
            margin-bottom: 15px;
            padding-bottom: 15px;
        }

        .review-product small {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .review-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #e9ecef;
        }

        .review-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            background: #e9ecef;
            color: #495057;
        }

        .review-badge.verified {
            background: linear-gradient(135deg, #20b2aa, #17a2a0);
            color: white;
        }

        .review-date {
            font-size: 0.85rem;
            color: #6c757d;
        }

        /* Owl Carousel customization for reviews */
        .reviews-carousel .owl-nav {
            margin-top: 30px;
        }

        .reviews-carousel .owl-nav button {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: white !important;
            color: #20b2aa !important;
            border: 2px solid #20b2aa !important;
            font-size: 1.5rem;
            transition: all 0.3s ease;
            margin: 0 8px;
        }

        .reviews-carousel .owl-nav button:hover {
            background: #20b2aa !important;
            color: white !important;
            transform: scale(1.1);
        }

        .reviews-carousel .owl-dots {
            margin-top: 25px;
            text-align: center;
        }

        .reviews-carousel .owl-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #dee2e6;
            display: inline-block;
            margin: 0 5px;
            transition: all 0.3s ease;
        }

        .reviews-carousel .owl-dot.active {
            background: #20b2aa;
            width: 30px;
            border-radius: 6px;
        }

        @media (max-width: 768px) {
            .section-title {
                font-size: 2rem;
            }

            .review-card {
                margin: 10px;
                padding: 25px;
                min-height: 280px;
            }

            .reviewer-avatar {
                width: 50px;
                height: 50px;
            }

            .reviewer-avatar i {
                font-size: 1.5rem;
            }

            .reviewer-name {
                font-size: 1rem;
            }

            .review-text {
                font-size: 0.95rem;
            }
        }
    </style>

    <script>
        $(document).ready(function() {
            $('.reviews-carousel').owlCarousel({
                loop: true,
                margin: 20,
                nav: true,
                dots: true,
                autoplay: true,
                autoplayTimeout: 5000,
                autoplayHoverPause: true,
                navText: ['<i class="bi bi-chevron-left"></i>', '<i class="bi bi-chevron-right"></i>'],
                responsive: {
                    0: {
                        items: 1
                    },
                    768: {
                        items: 2
                    },
                    1024: {
                        items: 3
                    }
                }
            });
        });
    </script>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Hero Carousel
            $('.hero-carousel').owlCarousel({
                loop: true,
                margin: 0,
                nav: true,
                dots: true,
                items: 1,
                autoplay: true,
                autoplayTimeout: 6000,
                autoplayHoverPause: true,
                animateOut: 'fadeOut',
                animateIn: 'fadeIn',
                navText: ['<i class="bi bi-chevron-left"></i>', '<i class="bi bi-chevron-right"></i>']
            });

            // Featured Products Carousel
            $('.featured-carousel').owlCarousel({
                loop: true,
                margin: 20,
                nav: true,
                dots: false,
                autoplay: true,
                autoplayTimeout: 4000,
                autoplayHoverPause: true,
                navText: ['<i class="bi bi-chevron-left"></i>', '<i class="bi bi-chevron-right"></i>'],
                responsive: {
                    0: {
                        items: 1,
                        margin: 15
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

            // New Arrivals Carousel
            $('.new-arrivals-carousel').owlCarousel({
                loop: true,
                margin: 20,
                nav: true,
                dots: false,
                autoplay: true,
                autoplayTimeout: 3500,
                autoplayHoverPause: true,
                navText: ['<i class="bi bi-chevron-left"></i>', '<i class="bi bi-chevron-right"></i>'],
                responsive: {
                    0: {
                        items: 1,
                        margin: 15
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

            // Reviews Carousel
            $('.reviews-carousel').owlCarousel({
                loop: true,
                margin: 20,
                nav: true,
                dots: true,
                autoplay: true,
                autoplayTimeout: 6000,
                autoplayHoverPause: true,
                navText: ['<i class="bi bi-chevron-left"></i>', '<i class="bi bi-chevron-right"></i>'],
                responsive: {
                    0: {
                        items: 1
                    },
                    768: {
                        items: 2
                    },
                    992: {
                        items: 3
                    }
                }
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
                        showToast('error', error.message ||
                            '{{ __('An error occurred. Please try again.') }}');
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
                        showToast('error', error.message ||
                            '{{ __('An error occurred. Please try again.') }}');
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
                toast.className =
                    `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed wishlist-toast`;
                toast.style.cssText =
                    'top: 80px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);';
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

    <script>
        // ================================================

        document.addEventListener('DOMContentLoaded', function() {
            // Header scroll behavior
            let lastScroll = 0;
            const header = document.querySelector('.main-header');
            const mainNav = document.querySelector('.main-nav');
            const scrollThreshold = 100;

            if (header) {
                window.addEventListener('scroll', function() {
                    const currentScroll = window.pageYOffset;

                    // Add shadow when scrolled
                    if (currentScroll > 10) {
                        header.classList.add('is-sticky');
                        header.style.boxShadow = '0 4px 16px rgba(0, 0, 0, 0.12)';
                    } else {
                        header.classList.remove('is-sticky');
                        header.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.08)';
                    }

                    // Update last scroll position
                    lastScroll = currentScroll;
                });
            }

            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    if (href !== '#' && href.length > 1) {
                        e.preventDefault();
                        const target = document.querySelector(href);
                        if (target) {
                            const headerHeight = header ? header.offsetHeight : 0;
                            const targetPosition = target.offsetTop - headerHeight - 20;

                            window.scrollTo({
                                top: targetPosition,
                                behavior: 'smooth'
                            });
                        }
                    }
                });
            });

            // Product image error handling
            document.querySelectorAll('.product-image img').forEach(img => {
                img.addEventListener('error', function() {
                    this.src = 'https://via.placeholder.com/400x250?text=No+Image';
                    this.alt = 'Product image not available';
                });
            });

            // Lazy loading for product images (optional performance improvement)
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            if (img.dataset.src) {
                                img.src = img.dataset.src;
                                img.removeAttribute('data-src');
                                observer.unobserve(img);
                            }
                        }
                    });
                });

                document.querySelectorAll('img[data-src]').forEach(img => {
                    imageObserver.observe(img);
                });
            }
        });

        // ================================================
        // SCROLL TO TOP BUTTON
        // ================================================

        const scrollToTopBtn = document.querySelector('.scroll-to-top');

        if (scrollToTopBtn) {
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    scrollToTopBtn.classList.add('show');
                } else {
                    scrollToTopBtn.classList.remove('show');
                }
            });

            scrollToTopBtn.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }
    @endpush
