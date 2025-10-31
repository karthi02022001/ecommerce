<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - @yield('title', __('eCommerce Store'))</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Owl Carousel CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    
    @stack('styles')
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <span><i class="bi bi-telephone me-2"></i>{{ __('Contact Us') }}</span>
                    <span class="ms-4"><i class="bi bi-envelope me-2"></i>{{ __('support@store.com') }}</span>
                </div>
                <div class="col-md-6 text-md-end">
                    @auth
                        <a href="{{ route('orders.index') }}" class="me-3">{{ __('Track Order') }}</a>
                    @endauth
                    <a href="#" class="me-3">{{ __('Help') }}</a>
                    
                    <!-- Language Switcher -->
                    <div class="dropdown d-inline-block ms-3">
                        <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            @if(app()->getLocale() == 'en')
                                <i class="bi bi-globe"></i> English
                            @else
                                <i class="bi bi-globe"></i> Español
                            @endif
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('language.switch', 'en') }}">English</a></li>
                            <li><a class="dropdown-item" href="{{ route('language.switch', 'es') }}">Español</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="main-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-2 col-md-3">
                    <a href="{{ route('home') }}" class="navbar-brand">
                        <i class="bi bi-shop me-2"></i>{{ config('app.name', 'Store') }}
                    </a>
                </div>
                
                <div class="col-lg-6 col-md-5">
                    <form action="{{ route('products.index') }}" method="GET" class="search-container">
                        <input type="text" name="search" class="search-input" placeholder="{{ __('Search for products...') }}" value="{{ request('search') }}">
                        <button type="submit" class="search-btn">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                </div>
                
                <div class="col-lg-4 col-md-4">
                    <div class="header-icons justify-content-end">
                        @auth
                            <a href="{{ route('profile.index') }}" class="header-icon">
                                <i class="bi bi-person-circle"></i>
                                <span>{{ __('Account') }}</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="header-icon">
                                <i class="bi bi-person-circle"></i>
                                <span>{{ __('Login') }}</span>
                            </a>
                        @endauth
                        
                        <a href="#" class="header-icon">
                            <i class="bi bi-heart"></i>
                            <span class="icon-badge">0</span>
                            <span>{{ __('Wishlist') }}</span>
                        </a>
                        
                        <a href="{{ route('cart.index') }}" class="header-icon">
                            <i class="bi bi-bag"></i>
                            <span class="icon-badge">{{ session('cart') ? count(session('cart')) : 0 }}</span>
                            <span>{{ __('Cart') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="main-nav">
        <div class="container nav-container">
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-grid-3x3-gap me-2"></i>{{ __('All Categories') }}
                    </a>
                    <div class="mega-menu">
                        <div class="mega-menu-content">
                            <div class="row">
                                @php
                                    try {
                                        $categories = \App\Models\Category::where('is_active', true)->take(12)->get();
                                        $categoryChunks = $categories->chunk(3);
                                    } catch (\Exception $e) {
                                        $categoryChunks = collect([]);
                                    }
                                @endphp
                                @foreach($categoryChunks as $chunk)
                                <div class="col-md-3">
                                    <ul>
                                        @foreach($chunk as $category)
                                        <li><a href="{{ route('products.category', $category->slug) }}">{{ $category->name() }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endforeach
                                
                                @if($categoryChunks->isEmpty())
                                <div class="col-md-12 text-center py-3">
                                    <p class="text-muted">{{ __('No categories available') }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('products.index') }}">{{ __('Products') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('products.index', ['featured' => 1]) }}">{{ __('Featured') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('products.index', ['on_sale' => 1]) }}">{{ __('Deals') }}</a>
                </li>
                @auth
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('orders.index') }}">{{ __('My Orders') }}</a>
                </li>
                @endauth
            </ul>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="container mt-3">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="container mt-3">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Newsletter -->
    <section class="newsletter-section">
        <div class="container">
            <h3>{{ __('Stay Updated with Our Latest Products') }}</h3>
            <p>{{ __('Subscribe to our newsletter and get exclusive deals, new arrivals, and expert tips.') }}</p>
            <form action="#" method="POST" class="newsletter-form">
                @csrf
                <input type="email" name="email" class="newsletter-input" placeholder="{{ __('Enter your email address') }}" required>
                <button type="submit" class="newsletter-btn">{{ __('Subscribe') }}</button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 footer-section">
                    <div class="footer-brand">
                        <i class="bi bi-shop me-2"></i>{{ config('app.name', 'Store') }}
                    </div>
                    <p class="footer-description">{{ __('Your trusted partner for premium products and exceptional shopping experience. We provide quality items that help you achieve your goals.') }}</p>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="social-link"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="social-link"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="social-link"><i class="bi bi-linkedin"></i></a>
                        <a href="#" class="social-link"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 footer-section">
                    <h5>{{ __('Customer Care') }}</h5>
                    <ul>
                        <li><a href="#">{{ __('Contact Us') }}</a></li>
                        <li><a href="#">{{ __('FAQ') }}</a></li>
                        <li><a href="#">{{ __('Shipping Info') }}</a></li>
                        <li><a href="#">{{ __('Returns & Exchanges') }}</a></li>
                        <li><a href="#">{{ __('Size Guide') }}</a></li>
                        <li><a href="#">{{ __('Track Your Order') }}</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 footer-section">
                    <h5>{{ __('Company') }}</h5>
                    <ul>
                        <li><a href="#">{{ __('About Us') }}</a></li>
                        <li><a href="#">{{ __('Careers') }}</a></li>
                        <li><a href="#">{{ __('Press Center') }}</a></li>
                        <li><a href="#">{{ __('Blog') }}</a></li>
                        <li><a href="#">{{ __('Affiliate Program') }}</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 footer-section">
                    <h5>{{ __('Categories') }}</h5>
                    <ul>
                        @php
                            try {
                                $footerCategories = \App\Models\Category::where('is_active', true)->take(6)->get();
                            } catch (\Exception $e) {
                                $footerCategories = collect([]);
                            }
                        @endphp
                        @forelse($footerCategories as $category)
                        <li><a href="{{ route('products.category', $category->slug) }}">{{ $category->name() }}</a></li>
                        @empty
                        <li><a href="{{ route('products.index') }}">{{ __('All Products') }}</a></li>
                        @endforelse
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 footer-section">
                    <h5>{{ __('Connect') }}</h5>
                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <i class="bi bi-geo-alt me-2"></i>
                            <small>{{ __('123 Store Avenue') }}<br>{{ __('City, State 12345') }}</small>
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-telephone me-2"></i>
                            <small>{{ __('Contact Number') }}</small>
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-envelope me-2"></i>
                            <small>{{ __('support@store.com') }}</small>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name', 'Store') }}. {{ __('All rights reserved.') }}</p>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-md-end align-items-center">
                            <span class="me-3">{{ __('We Accept:') }}</span>
                            <div class="payment-methods">
                                <div class="payment-icon">VISA</div>
                                <div class="payment-icon">MC</div>
                                <div class="payment-icon">AMEX</div>
                                <div class="payment-icon">PP</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 text-center">
                        <a href="#" class="me-3">{{ __('Privacy Policy') }}</a>
                        <a href="#" class="me-3">{{ __('Terms of Service') }}</a>
                        <a href="#" class="me-3">{{ __('Cookie Policy') }}</a>
                        <a href="#">{{ __('Accessibility') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- jQuery (required for Owl Carousel) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <!-- Owl Carousel JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    
    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>