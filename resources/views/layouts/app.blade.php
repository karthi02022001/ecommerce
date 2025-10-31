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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Owl Carousel CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    
    @stack('styles')
</head>
<body>
    <!-- Top Bar with Enhanced Design -->
    <div class="top-bar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 col-12">
                    <div class="top-bar-left">
                        <a href="tel:+1234567890" class="top-bar-link">
                            <i class="bi bi-telephone-fill"></i>
                            <span class="d-none d-md-inline">+91 123 456 7890</span>
                        </a>
                        <a href="mailto:support@store.com" class="top-bar-link ms-3">
                            <i class="bi bi-envelope-fill"></i>
                            <span class="d-none d-md-inline">support@store.com</span>
                        </a>
                    </div>
                </div>
                <div class="col-md-6 col-12 text-md-end text-center mt-2 mt-md-0">
                    <div class="top-bar-right">
                        @auth
                            <a href="{{ route('orders.index') }}" class="top-bar-link">
                                <i class="bi bi-box-seam"></i> {{ __('Track Order') }}
                            </a>
                        @endauth
                        <a href="#" class="top-bar-link ms-3">
                            <i class="bi bi-question-circle"></i> {{ __('Help') }}
                        </a>
                        
                        <!-- Enhanced Language Switcher -->
                        <div class="dropdown d-inline-block ms-3">
                            <a class="dropdown-toggle top-bar-link" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-globe2"></i>
                                @if(app()->getLocale() == 'en')
                                    EN
                                @else
                                    ES
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}" href="{{ route('language.switch', 'en') }}">
                                        <i class="bi bi-check-circle me-2"></i> English
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ app()->getLocale() == 'es' ? 'active' : '' }}" href="{{ route('language.switch', 'es') }}">
                                        <i class="bi bi-check-circle me-2"></i> Español
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Main Header -->
    <header class="main-header sticky-header">
        <div class="container">
            <div class="row align-items-center">
                <!-- Logo Section -->
                <div class="col-lg-2 col-md-3 col-6">
                    <a href="{{ route('home') }}" class="navbar-brand">
                        <div class="logo-container">
                            <i class="bi bi-shop-window"></i>
                            <span class="brand-name">{{ config('app.name', 'Store') }}</span>
                        </div>
                    </a>
                </div>
                
                <!-- Enhanced Search Section -->
                <div class="col-lg-5 col-md-4 d-none d-md-block">
                    <form action="{{ route('products.index') }}" method="GET" class="search-container">
                        <div class="search-wrapper">
                            <i class="bi bi-search search-icon"></i>
                            <input type="text" 
                                   name="search" 
                                   class="search-input" 
                                   placeholder="{{ __('Search for products, categories...') }}" 
                                   value="{{ request('search') }}"
                                   autocomplete="off">
                            <button type="submit" class="search-btn">
                                <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Enhanced Header Icons -->
                <div class="col-lg-5 col-md-5 col-6">
                    <div class="header-icons">
                        @auth
                            <div class="dropdown">
                                <a href="#" class="header-icon" data-bs-toggle="dropdown">
                                    <div class="icon-wrapper">
                                        <i class="bi bi-person-circle"></i>
                                        <span class="icon-label">{{ __('Account') }}</span>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('profile.index') }}"><i class="bi bi-person me-2"></i> {{ __('My Profile') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('orders.index') }}"><i class="bi bi-box-seam me-2"></i> {{ __('My Orders') }}</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-box-arrow-right me-2"></i> {{ __('Logout') }}
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="header-icon">
                                <div class="icon-wrapper">
                                    <i class="bi bi-person-circle"></i>
                                    <span class="icon-label d-none d-lg-inline">{{ __('Login') }}</span>
                                </div>
                            </a>
                        @endauth
                        
                        <a href="#" class="header-icon position-relative">
                            <div class="icon-wrapper">
                                <i class="bi bi-heart"></i>
                                <span class="icon-badge">0</span>
                                <span class="icon-label d-none d-lg-inline">{{ __('Wishlist') }}</span>
                            </div>
                        </a>
                        
                        <a href="{{ route('cart.index') }}" class="header-icon position-relative">
                            <div class="icon-wrapper">
                                <i class="bi bi-bag"></i>
                                @php
                                    $cartCount = session('cart') ? count(session('cart')) : 0;
                                @endphp
                                @if($cartCount > 0)
                                    <span class="icon-badge pulse">{{ $cartCount }}</span>
                                @endif
                                <span class="icon-label d-none d-lg-inline">{{ __('Cart') }}</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Mobile Search -->
            <div class="row d-md-none mt-3">
                <div class="col-12">
                    <form action="{{ route('products.index') }}" method="GET" class="search-container">
                        <div class="search-wrapper">
                            <i class="bi bi-search search-icon"></i>
                            <input type="text" 
                                   name="search" 
                                   class="search-input" 
                                   placeholder="{{ __('Search products...') }}" 
                                   value="{{ request('search') }}">
                            <button type="submit" class="search-btn">
                                <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Enhanced Navigation with Mega Menu -->
    <nav class="main-nav">
        <div class="container">
            <div class="nav-wrapper">
                <ul class="nav-list">
                    <!-- Categories Mega Menu -->
                    <li class="nav-item has-mega-menu">
                        <a href="#" class="nav-link">
                            <i class="bi bi-grid-3x3-gap"></i>
                            <span>{{ __('All Categories') }}</span>
                            <i class="bi bi-chevron-down ms-1"></i>
                        </a>
                        <div class="mega-menu">
                            <div class="mega-menu-content">
                                @php
                                    try {
                                        $categories = \App\Models\Category::where('is_active', true)->take(12)->get();
                                        $categoryChunks = $categories->chunk(3);
                                    } catch (\Exception $e) {
                                        $categoryChunks = collect([]);
                                    }
                                @endphp
                                @if($categoryChunks->isNotEmpty())
                                    <div class="row">
                                        @foreach($categoryChunks as $chunk)
                                        <div class="col-md-3">
                                            <ul class="mega-menu-list">
                                                @foreach($chunk as $category)
                                                <li>
                                                    <a href="{{ route('products.category', $category->slug) }}">
                                                        <i class="bi bi-arrow-right-short"></i>
                                                        {{ $category->name() }}
                                                    </a>
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <p class="text-muted mb-0">{{ __('No categories available') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">
                            <i class="bi bi-house-door"></i>
                            <span>{{ __('Home') }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('products.index') }}">
                            <i class="bi bi-grid"></i>
                            <span>{{ __('Products') }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('products.index', ['featured' => 1]) }}">
                            <i class="bi bi-star"></i>
                            <span>{{ __('Featured') }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('products.index', ['on_sale' => 1]) }}">
                            <i class="bi bi-tag"></i>
                            <span>{{ __('Deals') }}</span>
                        </a>
                    </li>
                    @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('orders.index') }}">
                            <i class="bi bi-box-seam"></i>
                            <span>{{ __('My Orders') }}</span>
                        </a>
                    </li>
                    @endauth
                </ul>
                
                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                    <i class="bi bi-list"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu Offcanvas -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">{{ __('Menu') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="mobile-nav-list">
                <li><a href="{{ route('home') }}"><i class="bi bi-house-door me-2"></i> {{ __('Home') }}</a></li>
                <li><a href="{{ route('products.index') }}"><i class="bi bi-grid me-2"></i> {{ __('Products') }}</a></li>
                <li><a href="{{ route('products.index', ['featured' => 1]) }}"><i class="bi bi-star me-2"></i> {{ __('Featured') }}</a></li>
                <li><a href="{{ route('products.index', ['on_sale' => 1]) }}"><i class="bi bi-tag me-2"></i> {{ __('Deals') }}</a></li>
                @auth
                    <li><a href="{{ route('orders.index') }}"><i class="bi bi-box-seam me-2"></i> {{ __('My Orders') }}</a></li>
                    <li><a href="{{ route('profile.index') }}"><i class="bi bi-person me-2"></i> {{ __('My Profile') }}</a></li>
                @else
                    <li><a href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right me-2"></i> {{ __('Login') }}</a></li>
                    <li><a href="{{ route('register') }}"><i class="bi bi-person-plus me-2"></i> {{ __('Register') }}</a></li>
                @endauth
            </ul>
            
            <hr class="my-3">
            
            <div class="mobile-categories">
                <h6 class="mb-3">{{ __('Categories') }}</h6>
                <ul class="list-unstyled">
                    @foreach($categories ?? [] as $category)
                    <li class="mb-2">
                        <a href="{{ route('products.category', $category->slug) }}" class="text-decoration-none">
                            <i class="bi bi-arrow-right-short"></i> {{ $category->name() }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Enhanced Flash Messages -->
    @if(session('success'))
    <div class="alert-container">
        <div class="container">
            <div class="alert alert-success alert-dismissible fade show custom-alert" role="alert">
                <div class="alert-content">
                    <i class="bi bi-check-circle-fill alert-icon"></i>
                    <div class="alert-text">
                        <strong>{{ __('Success!') }}</strong>
                        <p class="mb-0">{{ session('success') }}</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="alert-container">
        <div class="container">
            <div class="alert alert-danger alert-dismissible fade show custom-alert" role="alert">
                <div class="alert-content">
                    <i class="bi bi-exclamation-triangle-fill alert-icon"></i>
                    <div class="alert-text">
                        <strong>{{ __('Error!') }}</strong>
                        <p class="mb-0">{{ session('error') }}</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Enhanced Newsletter Section -->
    <section class="newsletter-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right">
                    <div class="newsletter-content">
                        <h3>{{ __('Stay Updated with Our Latest Products') }}</h3>
                        <p>{{ __('Subscribe to our newsletter and get exclusive deals, new arrivals, and expert tips delivered right to your inbox.') }}</p>
                        <div class="newsletter-features">
                            <div class="feature-item">
                                <i class="bi bi-check-circle"></i>
                                <span>{{ __('Exclusive Deals') }}</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle"></i>
                                <span>{{ __('New Arrivals') }}</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle"></i>
                                <span>{{ __('Expert Tips') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <form action="#" method="POST" class="newsletter-form">
                        @csrf
                        <div class="newsletter-input-group">
                            <i class="bi bi-envelope newsletter-input-icon"></i>
                            <input type="email" 
                                   name="email" 
                                   class="newsletter-input" 
                                   placeholder="{{ __('Enter your email address') }}" 
                                   required>
                            <button type="submit" class="newsletter-btn">
                                {{ __('Subscribe') }}
                                <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>
                        <small class="form-text">{{ __('We respect your privacy. Unsubscribe anytime.') }}</small>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Enhanced Footer -->
    <footer class="footer">
        <div class="footer-main">
            <div class="container">
                <div class="row">
                    <!-- Brand Section -->
                    <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                        <div class="footer-brand-section">
                            <div class="footer-brand">
                                <i class="bi bi-shop-window"></i>
                                <span>{{ config('app.name', 'Store') }}</span>
                            </div>
                            <p class="footer-description">
                                {{ __('Your trusted partner for premium products and exceptional shopping experience. We provide quality items that help you achieve your goals.') }}
                            </p>
                            <div class="social-links">
                                <a href="#" class="social-link" title="Facebook">
                                    <i class="bi bi-facebook"></i>
                                </a>
                                <a href="#" class="social-link" title="Twitter">
                                    <i class="bi bi-twitter"></i>
                                </a>
                                <a href="#" class="social-link" title="Instagram">
                                    <i class="bi bi-instagram"></i>
                                </a>
                                <a href="#" class="social-link" title="LinkedIn">
                                    <i class="bi bi-linkedin"></i>
                                </a>
                                <a href="#" class="social-link" title="YouTube">
                                    <i class="bi bi-youtube"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Links -->
                    <div class="col-lg-2 col-md-6 col-6 mb-4 mb-lg-0">
                        <h5 class="footer-title">{{ __('Quick Links') }}</h5>
                        <ul class="footer-links">
                            <li><a href="#">{{ __('About Us') }}</a></li>
                            <li><a href="#">{{ __('Contact Us') }}</a></li>
                            <li><a href="#">{{ __('Blog') }}</a></li>
                            <li><a href="#">{{ __('Careers') }}</a></li>
                            <li><a href="#">{{ __('Press') }}</a></li>
                        </ul>
                    </div>
                    
                    <!-- Customer Care -->
                    <div class="col-lg-2 col-md-6 col-6 mb-4 mb-lg-0">
                        <h5 class="footer-title">{{ __('Customer Care') }}</h5>
                        <ul class="footer-links">
                            <li><a href="#">{{ __('Help Center') }}</a></li>
                            <li><a href="#">{{ __('Track Order') }}</a></li>
                            <li><a href="#">{{ __('Returns') }}</a></li>
                            <li><a href="#">{{ __('Shipping Info') }}</a></li>
                            <li><a href="#">{{ __('Size Guide') }}</a></li>
                        </ul>
                    </div>
                    
                    <!-- Categories -->
                    <div class="col-lg-2 col-md-6 col-6 mb-4 mb-lg-0">
                        <h5 class="footer-title">{{ __('Categories') }}</h5>
                        <ul class="footer-links">
                            @php
                                try {
                                    $footerCategories = \App\Models\Category::where('is_active', true)->take(5)->get();
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
                    
                    <!-- Contact Info -->
                    <div class="col-lg-2 col-md-6 col-6">
                        <h5 class="footer-title">{{ __('Contact') }}</h5>
                        <ul class="footer-contact">
                            <li>
                                <i class="bi bi-geo-alt"></i>
                                <span>123 Store Avenue<br>City, State 12345</span>
                            </li>
                            <li>
                                <i class="bi bi-telephone"></i>
                                <span>+91 123 456 7890</span>
                            </li>
                            <li>
                                <i class="bi bi-envelope"></i>
                                <span>support@store.com</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <p class="copyright mb-0">
                            &copy; {{ date('Y') }} {{ config('app.name', 'Store') }}. {{ __('All rights reserved.') }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <div class="footer-bottom-right">
                            <div class="payment-methods">
                                <span>{{ __('We Accept:') }}</span>
                                <div class="payment-icons">
                                    <i class="bi bi-credit-card" title="Credit Card"></i>
                                    <i class="bi bi-wallet2" title="Wallet"></i>
                                    <i class="bi bi-paypal" title="PayPal"></i>
                                    <i class="bi bi-bank" title="Bank Transfer"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="footer-legal-links">
                            <a href="#">{{ __('Privacy Policy') }}</a>
                            <a href="#">{{ __('Terms of Service') }}</a>
                            <a href="#">{{ __('Cookie Policy') }}</a>
                            <a href="#">{{ __('Accessibility') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button class="scroll-to-top" id="scrollToTop">
        <i class="bi bi-arrow-up"></i>
    </button>

    <!-- jQuery (required for Owl Carousel) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <!-- Owl Carousel JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    
    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- AOS Animation JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
        
        // Scroll to Top
        const scrollToTopBtn = document.getElementById('scrollToTop');
        
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
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.custom-alert').fadeOut('slow');
        }, 5000);
        
        // Sticky Header
        let lastScroll = 0;
        const header = document.querySelector('.sticky-header');
        
        window.addEventListener('scroll', function() {
            const currentScroll = window.pageYOffset;
            
            if (currentScroll <= 0) {
                header.classList.remove('scroll-up');
                return;
            }
            
            if (currentScroll > lastScroll && !header.classList.contains('scroll-down')) {
                header.classList.remove('scroll-up');
                header.classList.add('scroll-down');
            } else if (currentScroll < lastScroll && header.classList.contains('scroll-down')) {
                header.classList.remove('scroll-down');
                header.classList.add('scroll-up');
            }
            lastScroll = currentScroll;
        });
    </script>
    
    @stack('scripts')
</body>
</html>