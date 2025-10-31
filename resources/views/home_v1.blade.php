@extends('layouts.app')

@section('title', __('home'))

@section('content')
<!-- Hero Carousel -->
<section class="hero-section">
    <div class="owl-carousel hero-carousel">
        <!-- Slide 1 - Welcome -->
        <div class="hero-slide" style="background-image: url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80')">
            <div class="hero-overlay"></div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="hero-content">
                            <h1 class="hero-title">{{ __('Welcome to') }} {{ config('app.name') }}</h1>
                            <p class="hero-subtitle">{{ __('Shop the best products at unbeatable prices') }}</p>
                            <div class="hero-buttons">
                                <a href="{{ route('products.index') }}" class="btn btn-hero-primary">{{ __('shop_now') }}</a>
                                <a href="#featured" class="btn btn-hero-outline">{{ __('View Collections') }}</a>
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
                    <div class="col-lg-6">
                        <div class="hero-content">
                            <h1 class="hero-title">{{ __('latest_products') }}</h1>
                            <p class="hero-subtitle">{{ __('Check out our latest collection of premium products. Fresh styles just for you.') }}</p>
                            <div class="hero-buttons">
                                <a href="{{ route('products.index', ['sort' => 'newest']) }}" class="btn btn-hero-primary">{{ __('View New Items') }}</a>
                                <a href="{{ route('products.index') }}" class="btn btn-hero-outline">{{ __('Browse All') }}</a>
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
                    <div class="col-lg-6">
                        <div class="hero-content">
                            <h1 class="hero-title">{{ __('Special Offers') }}</h1>
                            <p class="hero-subtitle">{{ __('Discover amazing products and great deals') }}</p>
                            <div class="hero-buttons">
                                <a href="{{ route('products.index', ['discount' => 1]) }}" class="btn btn-hero-primary">{{ __('View Deals') }}</a>
                                <a href="{{ route('products.index') }}" class="btn btn-hero-outline">{{ __('Shop All') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
@if(isset($categories) && $categories->count() > 0)
<section class="category-section" id="categories">
    <div class="container">
        <h2 class="section-title">{{ __('categories') }}</h2>
        <div class="row">
            @foreach($categories->take(4) as $category)
            <div class="col-lg-3 col-md-6">
                <a href="{{ route('products.category', $category->slug) }}">
                    <div class="category-card">
                        @if($category->image)
                            <div class="category-bg" style="background-image: url('{{ asset('storage/' . $category->image) }}');"></div>
                        @else
                            <div class="category-bg" style="background-image: url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80');"></div>
                        @endif
                        <div class="category-overlay">
                            <div class="category-content">
                                <h4>{{ $category->name() }}</h4>
                                <p>{{ $category->description() ? Str::limit($category->description(), 50) : __('Browse Collection') }}</p>
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
<section class="product-section" id="featured">
    <div class="container">
        <h2 class="section-title">{{ __('featured_products') }}</h2>
        <div class="owl-carousel featured-carousel">
            @foreach($featuredProducts as $product)
            <div class="product-card">
                <div class="product-image">
                    @if($product->hasDiscount())
                        <div class="product-badge sale">-{{ $product->discountPercentage() }}%</div>
                    @endif
                    <div class="product-actions">
                        <button class="action-btn" title="{{ __('Add to Wishlist') }}">
                            <i class="bi bi-heart"></i>
                        </button>
                        <a href="{{ route('products.show', $product->slug) }}" class="action-btn" title="{{ __('Quick View') }}">
                            <i class="bi bi-eye"></i>
                        </a>
                    </div>
                    <a href="{{ route('products.show', $product->slug) }}">
                        @if($product->primaryImage)
                            <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" alt="{{ $product->name() }}">
                        @else
                            <img src="https://via.placeholder.com/400x250?text={{ urlencode($product->name()) }}" alt="{{ $product->name() }}">
                        @endif
                    </a>
                </div>
                <div class="product-info">
                    <a href="{{ route('products.show', $product->slug) }}">
                        <h5 class="product-title">{{ $product->name() }}</h5>
                    </a>
                    <div class="product-price">
                        ₹{{ number_format($product->price, 2) }}
                        @if($product->hasDiscount())
                            <span class="old-price">₹{{ number_format($product->compare_price, 2) }}</span>
                        @endif
                    </div>
                    @if($product->isInStock())
                        <form action="{{ route('cart.add', $product) }}" method="POST">
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
</section>
@endif

<!-- Latest Products Section -->
@if($latestProducts->count() > 0)
<section class="product-section">
    <div class="container">
        <h2 class="section-title">{{ __('latest_products') }}</h2>
        <div class="owl-carousel new-arrivals-carousel">
            @foreach($latestProducts as $product)
            <div class="product-card">
                <div class="product-image">
                    <div class="product-badge new">{{ __('New') }}</div>
                    @if($product->hasDiscount())
                        <div class="product-badge sale" style="top: 50px;">-{{ $product->discountPercentage() }}%</div>
                    @endif
                    <div class="product-actions">
                        <button class="action-btn" title="{{ __('Add to Wishlist') }}">
                            <i class="bi bi-heart"></i>
                        </button>
                        <a href="{{ route('products.show', $product->slug) }}" class="action-btn" title="{{ __('Quick View') }}">
                            <i class="bi bi-eye"></i>
                        </a>
                    </div>
                    <a href="{{ route('products.show', $product->slug) }}">
                        @if($product->primaryImage)
                            <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" alt="{{ $product->name() }}">
                        @else
                            <img src="https://via.placeholder.com/400x250?text={{ urlencode($product->name()) }}" alt="{{ $product->name() }}">
                        @endif
                    </a>
                </div>
                <div class="product-info">
                    <a href="{{ route('products.show', $product->slug) }}">
                        <h5 class="product-title">{{ $product->name() }}</h5>
                    </a>
                    <div class="product-price">
                        ₹{{ number_format($product->price, 2) }}
                        @if($product->hasDiscount())
                            <span class="old-price">₹{{ number_format($product->compare_price, 2) }}</span>
                        @endif
                    </div>
                    @if($product->isInStock())
                        <form action="{{ route('cart.add', $product) }}" method="POST">
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
</section>
@endif

<!-- Customer Reviews Section -->
<section class="reviews-section">
    <div class="container">
        <h2 class="section-title">{{ __('What Our Customers Say') }}</h2>
        <div class="owl-carousel reviews-carousel">
            <div class="review-card">
                <div class="review-stars">★★★★★</div>
                <p class="review-text">{{ __('"Absolutely amazing quality! The products I bought have exceeded my expectations. Great customer service and fast delivery!"') }}</p>
                <div class="reviewer-info">
                    <div class="reviewer-avatar">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="reviewer-details">
                        <h6>{{ __('Priya Sharma') }}</h6>
                        <small>{{ __('Verified Buyer') }}</small>
                    </div>
                </div>
            </div>
            
            <div class="review-card">
                <div class="review-stars">★★★★★</div>
                <p class="review-text">{{ __('"Best online store I\'ve ever shopped at! Fast shipping, excellent customer service, and top-quality products."') }}</p>
                <div class="reviewer-info">
                    <div class="reviewer-avatar">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="reviewer-details">
                        <h6>{{ __('Rahul Verma') }}</h6>
                        <small>{{ __('Regular Customer') }}</small>
                    </div>
                </div>
            </div>
            
            <div class="review-card">
                <div class="review-stars">★★★★★</div>
                <p class="review-text">{{ __('"The products are excellent and the prices are very reasonable. I highly recommend this store to everyone!"') }}</p>
                <div class="reviewer-info">
                    <div class="reviewer-avatar">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="reviewer-details">
                        <h6>{{ __('Anjali Patel') }}</h6>
                        <small>{{ __('Premium Member') }}</small>
                    </div>
                </div>
            </div>
            
            <div class="review-card">
                <div class="review-stars">★★★★★</div>
                <p class="review-text">{{ __('"Outstanding selection! They have everything I need. Shopping experience was smooth and delivery was super fast."') }}</p>
                <div class="reviewer-info">
                    <div class="reviewer-avatar">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="reviewer-details">
                        <h6>{{ __('Vikram Singh') }}</h6>
                        <small>{{ __('Happy Customer') }}</small>
                    </div>
                </div>
            </div>
            
            <div class="review-card">
                <div class="review-stars">★★★★★</div>
                <p class="review-text">{{ __('"I\'ve been shopping here for years. The quality and service are consistently excellent. Highly trusted store!"') }}</p>
                <div class="reviewer-info">
                    <div class="reviewer-avatar">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="reviewer-details">
                        <h6>{{ __('Meera Reddy') }}</h6>
                        <small>{{ __('VIP Customer') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<div class="container text-center my-5 py-5">
    <h3 class="mb-3">{{ __('Start Shopping Today') }}</h3>
    <p class="lead mb-4">{{ __('Discover amazing products and great deals') }}</p>
    <a href="{{ route('products.index') }}" class="btn btn-hero-primary">{{ __('Browse Products') }}</a>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function(){
        // Hero Carousel
        $('.hero-carousel').owlCarousel({
            loop: true,
            margin: 0,
            nav: false,
            dots: true,
            items: 1,
            autoplay: true,
            autoplayTimeout: 5000,
            autoplayHoverPause: true,
            animateOut: 'fadeOut',
            animateIn: 'fadeIn'
        });
        
        // Featured Products Carousel
        $('.featured-carousel').owlCarousel({
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
        
        // New Arrivals Carousel
        $('.new-arrivals-carousel').owlCarousel({
            loop: true,
            margin: 30,
            nav: true,
            dots: true,
            autoplay: true,
            autoplayTimeout: 3000,
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
        
        // Reviews Carousel
        $('.reviews-carousel').owlCarousel({
            loop: true,
            margin: 30,
            nav: true,
            dots: true,
            autoplay: true,
            autoplayTimeout: 6000,
            autoplayHoverPause: true,
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