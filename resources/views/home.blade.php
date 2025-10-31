@extends('layouts.app')

@section('title', __('home'))

@section('content')
<!-- Enhanced Hero Carousel -->
<section class="hero-section">
    <div class="owl-carousel hero-carousel">
        <!-- Slide 1 - Welcome -->
        <div class="hero-slide" style="background-image: url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80')">
            <div class="hero-overlay"></div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-7 col-md-8">
                        <div class="hero-content" data-aos="fade-right" data-aos-delay="200">
                            <div class="hero-badge">{{ __('Welcome') }}</div>
                            <h1 class="hero-title">{{ __('Discover Premium') }}<br>{{ __('Quality Products') }}</h1>
                            <p class="hero-subtitle">{{ __('Shop the best products at unbeatable prices. Experience quality like never before.') }}</p>
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
        <div class="hero-slide" style="background-image: url('https://images.unsplash.com/photo-1472851294608-062f824d29cc?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80')">
            <div class="hero-overlay"></div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-7 col-md-8">
                        <div class="hero-content" data-aos="fade-right" data-aos-delay="200">
                            <div class="hero-badge">{{ __('New Arrival') }}</div>
                            <h1 class="hero-title">{{ __('Latest Products') }}<br>{{ __('Fresh Styles') }}</h1>
                            <p class="hero-subtitle">{{ __('Check out our latest collection of premium products. Fresh styles just for you.') }}</p>
                            <div class="hero-buttons">
                                <a href="{{ route('products.index', ['sort' => 'newest']) }}" class="btn btn-hero-primary">
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
        <div class="hero-slide" style="background-image: url('https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80')">
            <div class="hero-overlay"></div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-7 col-md-8">
                        <div class="hero-content" data-aos="fade-right" data-aos-delay="200">
                            <div class="hero-badge special">{{ __('Special Offer') }}</div>
                            <h1 class="hero-title">{{ __('Amazing Deals') }}<br>{{ __('Up to 50% Off') }}</h1>
                            <p class="hero-subtitle">{{ __('Discover amazing products and great deals. Limited time offers you don\'t want to miss!') }}</p>
                            <div class="hero-buttons">
                                <a href="{{ route('products.index', ['discount' => 1]) }}" class="btn btn-hero-primary">
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
@if(isset($categories) && $categories->count() > 0)
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
            @foreach($categories->take(4) as $index => $category)
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                <a href="{{ route('products.category', $category->slug) }}" class="category-card">
                    <div class="category-image-wrapper">
                        @if($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name() }}" class="category-image">
                        @else
                            <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="{{ $category->name() }}" class="category-image">
                        @endif
                        <div class="category-overlay">
                            <div class="category-content">
                                <h4>{{ $category->name() }}</h4>
                                <p>{{ $category->description() ? Str::limit($category->description(), 50) : __('Explore Collection') }}</p>
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
@if($featuredProducts->count() > 0)
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
            @foreach($featuredProducts as $product)
            <div class="product-card-wrapper">
                <div class="product-card">
                    <div class="product-image">
                        <!-- Product Badges -->
                        <div class="product-badges">
                            @if($product->hasDiscount())
                                <span class="product-badge discount">-{{ $product->discountPercentage() }}%</span>
                            @endif
                            <span class="product-badge featured">{{ __('Featured') }}</span>
                        </div>
                        
                        <!-- Product Actions -->
                        <div class="product-actions">
                            <button class="action-btn" title="{{ __('Add to Wishlist') }}">
                                <i class="bi bi-heart"></i>
                            </button>
                            <a href="{{ route('products.show', $product->slug) }}" class="action-btn" title="{{ __('Quick View') }}">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                        
                        <!-- Product Image -->
                        <a href="{{ route('products.show', $product->slug) }}" class="product-image-link">
                            @if($product->primaryImage)
                                <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" alt="{{ $product->name() }}" class="img-fluid">
                            @else
                                <img src="https://via.placeholder.com/400x250?text={{ urlencode($product->name()) }}" alt="{{ $product->name() }}" class="img-fluid">
                            @endif
                        </a>
                    </div>
                    
                    <div class="product-info">
                        <a href="{{ route('products.show', $product->slug) }}" class="product-title-link">
                            <h5 class="product-title">{{ $product->name() }}</h5>
                        </a>
                        
                        <div class="product-rating">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-half"></i>
                            <span class="rating-count">(4.5)</span>
                        </div>
                        
                        <div class="product-price-wrapper">
                            <span class="product-price">₹{{ number_format($product->price, 2) }}</span>
                            @if($product->hasDiscount())
                                <span class="old-price">₹{{ number_format($product->compare_price, 2) }}</span>
                            @endif
                        </div>
                        
                        @if($product->isInStock())
                            <form action="{{ route('cart.add', $product) }}" method="POST">
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
                    <p class="promo-description">{{ __('Sign up today and enjoy exclusive discounts on your first purchase. Don\'t miss out on this special offer!') }}</p>
                    <a href="{{ route('register') }}" class="btn btn-promo-primary">
                        {{ __('Sign Up Now') }}
                        <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="promo-image">
                    <img src="https://images.unsplash.com/photo-1607082349566-187342175e2f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Promo" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Latest Products Section -->
@if($latestProducts->count() > 0)
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
            @foreach($latestProducts as $product)
            <div class="product-card-wrapper">
                <div class="product-card">
                    <div class="product-image">
                        <!-- Product Badges -->
                        <div class="product-badges">
                            <span class="product-badge new">{{ __('New') }}</span>
                            @if($product->hasDiscount())
                                <span class="product-badge discount">-{{ $product->discountPercentage() }}%</span>
                            @endif
                        </div>
                        
                        <!-- Product Actions -->
                        <div class="product-actions">
                            <button class="action-btn" title="{{ __('Add to Wishlist') }}">
                                <i class="bi bi-heart"></i>
                            </button>
                            <a href="{{ route('products.show', $product->slug) }}" class="action-btn" title="{{ __('Quick View') }}">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                        
                        <!-- Product Image -->
                        <a href="{{ route('products.show', $product->slug) }}" class="product-image-link">
                            @if($product->primaryImage)
                                <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" alt="{{ $product->name() }}" class="img-fluid">
                            @else
                                <img src="https://via.placeholder.com/400x250?text={{ urlencode($product->name()) }}" alt="{{ $product->name() }}" class="img-fluid">
                            @endif
                        </a>
                    </div>
                    
                    <div class="product-info">
                        <a href="{{ route('products.show', $product->slug) }}" class="product-title-link">
                            <h5 class="product-title">{{ $product->name() }}</h5>
                        </a>
                        
                        <div class="product-rating">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star"></i>
                            <span class="rating-count">(4.0)</span>
                        </div>
                        
                        <div class="product-price-wrapper">
                            <span class="product-price">₹{{ number_format($product->price, 2) }}</span>
                            @if($product->hasDiscount())
                                <span class="old-price">₹{{ number_format($product->compare_price, 2) }}</span>
                            @endif
                        </div>
                        
                        @if($product->isInStock())
                            <form action="{{ route('cart.add', $product) }}" method="POST">
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
<section class="reviews-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <div class="section-title-wrapper">
                <h2 class="section-title">{{ __('What Our Customers Say') }}</h2>
                <p class="section-subtitle">{{ __('Real reviews from real customers') }}</p>
            </div>
        </div>
        
        <div class="owl-carousel reviews-carousel">
            <div class="review-card">
                <div class="review-header">
                    <div class="reviewer-avatar">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="reviewer-info">
                        <h6 class="reviewer-name">{{ __('Priya Sharma') }}</h6>
                        <div class="review-stars">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                    </div>
                </div>
                <p class="review-text">{{ __('"Absolutely amazing quality! The products I bought have exceeded my expectations. Great customer service and fast delivery!"') }}</p>
                <div class="review-footer">
                    <span class="review-badge">{{ __('Verified Buyer') }}</span>
                    <span class="review-date">{{ __('2 days ago') }}</span>
                </div>
            </div>
            
            <div class="review-card">
                <div class="review-header">
                    <div class="reviewer-avatar">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="reviewer-info">
                        <h6 class="reviewer-name">{{ __('Rahul Verma') }}</h6>
                        <div class="review-stars">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                    </div>
                </div>
                <p class="review-text">{{ __('"Best online store I\'ve ever shopped at! Fast shipping, excellent customer service, and top-quality products."') }}</p>
                <div class="review-footer">
                    <span class="review-badge">{{ __('Regular Customer') }}</span>
                    <span class="review-date">{{ __('5 days ago') }}</span>
                </div>
            </div>
            
            <div class="review-card">
                <div class="review-header">
                    <div class="reviewer-avatar">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="reviewer-info">
                        <h6 class="reviewer-name">{{ __('Anjali Patel') }}</h6>
                        <div class="review-stars">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                    </div>
                </div>
                <p class="review-text">{{ __('"The products are excellent and the prices are very reasonable. I highly recommend this store to everyone!"') }}</p>
                <div class="review-footer">
                    <span class="review-badge">{{ __('Premium Member') }}</span>
                    <span class="review-date">{{ __('1 week ago') }}</span>
                </div>
            </div>
            
            <div class="review-card">
                <div class="review-header">
                    <div class="reviewer-avatar">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="reviewer-info">
                        <h6 class="reviewer-name">{{ __('Vikram Singh') }}</h6>
                        <div class="review-stars">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                    </div>
                </div>
                <p class="review-text">{{ __('"Outstanding selection! They have everything I need. Shopping experience was smooth and delivery was super fast."') }}</p>
                <div class="review-footer">
                    <span class="review-badge">{{ __('Happy Customer') }}</span>
                    <span class="review-date">{{ __('2 weeks ago') }}</span>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    $(document).ready(function(){
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
@endpush