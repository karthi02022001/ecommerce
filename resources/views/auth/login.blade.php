@extends('layouts.app')

@section('title', __('login'))

@push('styles')
<style>
    /* Login Page Specific Styles */
    .auth-wrapper {
        min-height: 80vh;
        display: flex;
        align-items: center;
        background: linear-gradient(135deg, var(--light-gray) 0%, #ffffff 100%);
        padding: 60px 0;
    }
    
    .auth-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        border: none;
    }
    
    .auth-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 40px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    
    .auth-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: pulse 15s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 0.5; }
        50% { transform: scale(1.1); opacity: 0.8; }
    }
    
    .auth-header-icon {
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        backdrop-filter: blur(10px);
        position: relative;
        z-index: 2;
    }
    
    .auth-header-icon i {
        font-size: 2.5rem;
        color: white;
    }
    
    .auth-header h2 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 10px;
        position: relative;
        z-index: 2;
    }
    
    .auth-header p {
        opacity: 0.9;
        margin: 0;
        position: relative;
        z-index: 2;
    }
    
    .auth-body {
        padding: 50px;
    }
    
    .form-floating {
        margin-bottom: 20px;
    }
    
    .form-floating > .form-control {
        border: 2px solid var(--border-color);
        border-radius: 12px;
        padding: 1rem 1.25rem;
        height: calc(3.5rem + 2px);
        transition: all 0.3s ease;
        font-size: 1rem;
    }
    
    .form-floating > .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(var(--primary-rgb), 0.15);
    }
    
    .form-floating > label {
        padding: 1rem 1.25rem;
        color: var(--medium-gray);
        font-weight: 500;
    }
    
    .form-control.is-invalid {
        border-color: var(--danger-color);
    }
    
    .form-control.is-invalid:focus {
        border-color: var(--danger-color);
        box-shadow: 0 0 0 0.25rem rgba(231, 76, 60, 0.15);
    }
    
    .invalid-feedback {
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: block;
    }
    
    .password-toggle {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: var(--medium-gray);
        z-index: 10;
        transition: color 0.3s ease;
    }
    
    .password-toggle:hover {
        color: var(--primary-color);
    }
    
    .form-check {
        padding: 0;
        margin-bottom: 25px;
    }
    
    .form-check-input {
        width: 20px;
        height: 20px;
        border: 2px solid var(--border-color);
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .form-check-input:focus {
        box-shadow: 0 0 0 0.25rem rgba(var(--primary-rgb), 0.15);
    }
    
    .form-check-label {
        margin-left: 10px;
        cursor: pointer;
        font-weight: 500;
        color: var(--accent-color);
    }
    
    .btn-auth-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        border: none;
        color: white;
        padding: 15px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        width: 100%;
        position: relative;
        overflow: hidden;
    }
    
    .btn-auth-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s ease;
    }
    
    .btn-auth-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(var(--primary-rgb), 0.4);
    }
    
    .btn-auth-primary:hover::before {
        left: 100%;
    }
    
    .btn-auth-primary:active {
        transform: translateY(0);
    }
    
    .auth-divider {
        display: flex;
        align-items: center;
        text-align: center;
        margin: 30px 0;
        color: var(--medium-gray);
    }
    
    .auth-divider::before,
    .auth-divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid var(--border-color);
    }
    
    .auth-divider span {
        padding: 0 15px;
        font-weight: 500;
        font-size: 0.9rem;
    }
    
    .social-login-btn {
        border: 2px solid var(--border-color);
        border-radius: 12px;
        padding: 12px;
        transition: all 0.3s ease;
        background: white;
        color: var(--accent-color);
        font-weight: 500;
        width: 100%;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        text-decoration: none;
    }
    
    .social-login-btn:hover {
        border-color: var(--primary-color);
        background: var(--light-gray);
        color: var(--primary-color);
        transform: translateY(-2px);
    }
    
    .social-login-btn i {
        font-size: 1.3rem;
    }
    
    .auth-footer {
        text-align: center;
        padding: 30px 50px;
        background: var(--light-gray);
        border-top: 1px solid var(--border-color);
    }
    
    .auth-footer p {
        margin: 0;
        color: var(--medium-gray);
    }
    
    .auth-footer a {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s ease;
    }
    
    .auth-footer a:hover {
        color: var(--primary-dark);
    }
    
    .forgot-password {
        text-align: right;
        margin-top: -10px;
        margin-bottom: 20px;
    }
    
    .forgot-password a {
        color: var(--primary-color);
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
        transition: color 0.3s ease;
    }
    
    .forgot-password a:hover {
        color: var(--primary-dark);
        text-decoration: underline;
    }
    
    .alert-custom {
        border-radius: 12px;
        border: none;
        padding: 15px 20px;
        margin-bottom: 25px;
        background: #fee;
        border-left: 4px solid var(--danger-color);
    }
    
    .alert-custom.alert-success-custom {
        background: #d4edda;
        border-left: 4px solid #28a745;
    }
    
    .alert-custom.alert-success-custom ul li {
        color: #155724;
    }
    
    .alert-custom.alert-info-custom {
        background: #d1ecf1;
        border-left: 4px solid #17a2b8;
    }
    
    .alert-custom.alert-info-custom .alert-content {
        color: #0c5460;
    }
    
    .alert-custom ul {
        margin: 0;
        padding-left: 20px;
    }
    
    .alert-custom li {
        color: var(--danger-color);
        margin-bottom: 5px;
    }
    
    .alert-custom li:last-child {
        margin-bottom: 0;
    }
    
    .alert-custom .alert-content {
        display: flex;
        align-items: start;
        gap: 10px;
    }
    
    .alert-custom .alert-content i {
        font-size: 1.3rem;
        margin-top: 2px;
    }
    
    .alert-custom .alert-text {
        flex: 1;
    }
    
    .alert-custom .alert-text strong {
        display: block;
        margin-bottom: 8px;
        font-size: 1rem;
    }
    
    .alert-custom .alert-text p {
        margin: 0 0 12px 0;
        font-size: 0.9rem;
        line-height: 1.5;
    }
    
    .alert-custom .btn-sm {
        padding: 8px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }
    
    .btn-register-suggest {
        background: var(--primary-color);
        color: white;
        border: none;
    }
    
    .btn-register-suggest:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(var(--primary-rgb), 0.3);
        color: white;
    }
    
    .auth-benefits {
        background: var(--light-gray);
        border-radius: 15px;
        padding: 30px;
        margin-top: 30px;
    }
    
    .auth-benefits h5 {
        color: var(--accent-color);
        font-weight: 600;
        margin-bottom: 20px;
        font-size: 1.1rem;
    }
    
    .benefit-item {
        display: flex;
        align-items: start;
        margin-bottom: 15px;
    }
    
    .benefit-item i {
        color: var(--primary-color);
        font-size: 1.2rem;
        margin-right: 12px;
        margin-top: 3px;
    }
    
    .benefit-item span {
        color: var(--medium-gray);
        font-size: 0.95rem;
        line-height: 1.5;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .auth-body {
            padding: 30px;
        }
        
        .auth-header {
            padding: 30px 20px;
        }
        
        .auth-footer {
            padding: 20px 30px;
        }
        
        .auth-header h2 {
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="auth-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="auth-card">
                    <!-- Header -->
                    <div class="auth-header">
                        <div class="auth-header-icon">
                            <i class="bi bi-person-circle"></i>
                        </div>
                        <h2>{{ __('Welcome Back!') }}</h2>
                        <p>{{ __('Sign in to access your account') }}</p>
                    </div>
                    
                    <!-- Body -->
                    <div class="auth-body">
                        {{-- Success Message --}}
                        @if(session('success'))
                        <div class="alert-custom alert-success-custom">
                            <div class="alert-content">
                                <i class="bi bi-check-circle-fill" style="color: #28a745;"></i>
                                <div class="alert-text">
                                    {{ session('success') }}
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        {{-- Error Messages --}}
                        @if($errors->any())
                        <div class="alert-custom">
                            <ul>
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        
                        {{-- Show Register Suggestion if email not found --}}
                        @if(session('show_register_link'))
                        <div class="alert-custom alert-info-custom">
                            <div class="alert-content">
                                <i class="bi bi-person-plus-fill" style="color: #17a2b8;"></i>
                                <div class="alert-text">
                                    <strong>{{ __('New to our store?') }}</strong>
                                    <p>{{ __('Create an account to start shopping and enjoy exclusive benefits!') }}</p>
                                    <a href="{{ route('register') }}" class="btn-sm btn-register-suggest">
                                        <i class="bi bi-person-plus-fill me-1"></i>{{ __('Create Account') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endif
                        
                        <form action="{{ route('login') }}" method="POST" id="loginForm">
                            @csrf
                            
                            <!-- Email Field -->
                            <div class="form-floating">
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       placeholder="{{ __('email') }}"
                                       value="{{ old('email') }}" 
                                       required 
                                       autofocus>
                                <label for="email">
                                    <i class="bi bi-envelope me-2"></i>{{ __('Email Address') }}
                                </label>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Password Field -->
                            <div class="form-floating" style="position: relative;">
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       placeholder="{{ __('password') }}"
                                       required>
                                <label for="password">
                                    <i class="bi bi-lock me-2"></i>{{ __('Password') }}
                                </label>
                                <i class="bi bi-eye password-toggle" id="togglePassword"></i>
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Forgot Password Link -->
                            <div class="forgot-password">
                                <a href="{{ route('password.request') }}">{{ __('Forgot Password?') }}</a>
                            </div>
                            
                            {{-- <!-- Remember Me -->
                            <div class="form-check">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="remember" 
                                       name="remember">
                                <label class="form-check-label" for="remember">
                                    {{ __('Keep me signed in') }}
                                </label>
                            </div> --}}
                            
                            <!-- Submit Button -->
                            <button type="submit" class="btn-auth-primary">
                                <i class="bi bi-box-arrow-in-right me-2"></i>{{ __('Sign In') }}
                            </button>
                        </form>
                        
                        <!-- Social Login Divider -->
                        @if(config('services.google.client_id'))
                        <div class="auth-divider">
                            <span>{{ __('or continue with') }}</span>
                        </div>
                        
                        <!-- Social Login Buttons -->
                        <a href="{{ route('auth.google') }}" class="social-login-btn">
                            <i class="bi bi-google"></i>
                            {{ __('Continue with Google') }}
                        </a>
                        @endif
                        
                    </div>
                    
                    <!-- Footer -->
                    <div class="auth-footer">
                        <p>
                            {{ __("Don't have an account?") }} 
                            <a href="{{ route('register') }}">{{ __('Create Account') }}</a>
                        </p>
                    </div>
                </div>
                
                <!-- Benefits Section -->
                <div class="auth-benefits">
                    <h5>{{ __('Why Shop With Us?') }}</h5>
                    <div class="benefit-item">
                        <i class="bi bi-shield-check"></i>
                        <span>{{ __('Secure checkout and encrypted payment processing') }}</span>
                    </div>
                    <div class="benefit-item">
                        <i class="bi bi-truck"></i>
                        <span>{{ __('Fast and reliable shipping to your doorstep') }}</span>
                    </div>
                    <div class="benefit-item">
                        <i class="bi bi-arrow-repeat"></i>
                        <span>{{ __('Easy returns and hassle-free exchanges') }}</span>
                    </div>
                    <div class="benefit-item">
                        <i class="bi bi-headset"></i>
                        <span>{{ __('24/7 customer support for all your needs') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Password Toggle
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this;
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });
    
    // Form Validation Enhancement
    const form = document.getElementById('loginForm');
    const inputs = form.querySelectorAll('input[required]');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (!this.value) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
        
        input.addEventListener('input', function() {
            if (this.value) {
                this.classList.remove('is-invalid');
            }
        });
    });
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-custom');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
</script>
@endpush