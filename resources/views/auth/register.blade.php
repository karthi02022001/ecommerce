@extends('layouts.app')

@section('title', __('register'))

@push('styles')
<style>
    /* Register Page Specific Styles */
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
    
    .password-strength-meter {
        margin-top: 10px;
        height: 4px;
        background: var(--border-color);
        border-radius: 2px;
        overflow: hidden;
        position: relative;
    }
    
    .password-strength-bar {
        height: 100%;
        transition: all 0.3s ease;
        width: 0%;
    }
    
    .password-strength-bar.weak {
        width: 33%;
        background: var(--danger-color);
    }
    
    .password-strength-bar.medium {
        width: 66%;
        background: var(--warning-color);
    }
    
    .password-strength-bar.strong {
        width: 100%;
        background: var(--success-color);
    }
    
    .password-strength-text {
        font-size: 0.8rem;
        margin-top: 5px;
        font-weight: 500;
    }
    
    .password-strength-text.weak {
        color: var(--danger-color);
    }
    
    .password-strength-text.medium {
        color: var(--warning-color);
    }
    
    .password-strength-text.strong {
        color: var(--success-color);
    }
    
    .password-requirements {
        font-size: 0.85rem;
        color: var(--medium-gray);
        margin-top: 8px;
        padding: 10px;
        background: var(--light-gray);
        border-radius: 8px;
    }
    
    .password-requirements ul {
        margin: 0;
        padding-left: 20px;
    }
    
    .password-requirements li {
        margin-bottom: 5px;
    }
    
    .password-requirements li.valid {
        color: var(--success-color);
    }
    
    .password-requirements li.valid::before {
        content: '✓ ';
        font-weight: bold;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
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
    
    .alert-custom {
        border-radius: 12px;
        border: none;
        padding: 15px 20px;
        margin-bottom: 25px;
        background: #fee;
        border-left: 4px solid var(--danger-color);
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
        
        .form-row {
            grid-template-columns: 1fr;
            gap: 0;
        }
    }
</style>
@endpush

@section('content')
<div class="auth-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="auth-card">
                    <!-- Header -->
                    <div class="auth-header">
                        <div class="auth-header-icon">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                        <h2>{{ __('Create Account') }}</h2>
                        <p>{{ __('Join us today and start shopping') }}</p>
                    </div>
                    
                    <!-- Body -->
                    <div class="auth-body">
                        @if($errors->any())
                        <div class="alert-custom">
                            <ul>
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        
                        <form action="{{ route('register') }}" method="POST" id="registerForm">
                            @csrf
                            
                            <!-- Name Field -->
                            <div class="form-floating">
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       placeholder="{{ __('name') }}"
                                       value="{{ old('name') }}" 
                                       required 
                                       autofocus>
                                <label for="name">
                                    <i class="bi bi-person me-2"></i>{{ __('Full Name') }}
                                </label>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Email and Phone Row -->
                            <div class="form-row">
                                <!-- Email Field -->
                                <div class="form-floating">
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           placeholder="{{ __('email') }}"
                                           value="{{ old('email') }}" 
                                           required>
                                    <label for="email">
                                        <i class="bi bi-envelope me-2"></i>{{ __('Email Address') }}
                                    </label>
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Phone Field -->
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" 
                                           name="phone" 
                                           placeholder="{{ __('phone') }}"
                                           value="{{ old('phone') }}">
                                    <label for="phone">
                                        <i class="bi bi-telephone me-2"></i>{{ __('Phone Number') }}
                                    </label>
                                    @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
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
                                
                                <!-- Password Strength Meter -->
                                <div class="password-strength-meter">
                                    <div class="password-strength-bar" id="strengthBar"></div>
                                </div>
                                <div class="password-strength-text" id="strengthText"></div>
                                
                                <!-- Password Requirements -->
                                <div class="password-requirements" id="passwordRequirements">
                                    <ul>
                                        <li id="req-length">{{ __('At least 8 characters') }}</li>
                                        <li id="req-uppercase">{{ __('One uppercase letter') }}</li>
                                        <li id="req-lowercase">{{ __('One lowercase letter') }}</li>
                                        <li id="req-number">{{ __('One number') }}</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <!-- Confirm Password Field -->
                            <div class="form-floating" style="position: relative;">
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       placeholder="{{ __('confirm_password') }}"
                                       required>
                                <label for="password_confirmation">
                                    <i class="bi bi-lock-fill me-2"></i>{{ __('Confirm Password') }}
                                </label>
                                <i class="bi bi-eye password-toggle" id="togglePasswordConfirm"></i>
                            </div>
                            
                            <!-- Submit Button -->
                            <button type="submit" class="btn-auth-primary">
                                <i class="bi bi-person-check me-2"></i>{{ __('Create Account') }}
                            </button>
                        </form>
                        
                        <!-- Social Registration Divider -->
                        <div class="auth-divider">
                            <span>{{ __('or sign up with') }}</span>
                        </div>
                        
                        <!-- Social Registration Buttons -->
                        <a href="{{ route('auth.google') }}" class="social-login-btn">
                            <i class="bi bi-google"></i>
                            {{ __('Sign up with Google') }}
                        </a>
                        
                        
                    </div>
                    
                    <!-- Footer -->
                    <div class="auth-footer">
                        <p>
                            {{ __('Already have an account?') }} 
                            <a href="{{ route('login') }}">{{ __('Sign In') }}</a>
                        </p>
                    </div>
                </div>
                
                <!-- Benefits Section -->
                <div class="auth-benefits">
                    <h5>{{ __('Why Join Us?') }}</h5>
                    <div class="benefit-item">
                        <i class="bi bi-gift"></i>
                        <span>{{ __('Exclusive deals and offers for registered members') }}</span>
                    </div>
                    <div class="benefit-item">
                        <i class="bi bi-clock-history"></i>
                        <span>{{ __('Track your orders and view order history anytime') }}</span>
                    </div>
                    <div class="benefit-item">
                        <i class="bi bi-bookmark"></i>
                        <span>{{ __('Save your favorite items to wishlist') }}</span>
                    </div>
                    <div class="benefit-item">
                        <i class="bi bi-lightning"></i>
                        <span>{{ __('Faster checkout with saved addresses and payment info') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Password Toggle for Password Field
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
    
    // Password Toggle for Confirm Password Field
    document.getElementById('togglePasswordConfirm').addEventListener('click', function() {
        const passwordInput = document.getElementById('password_confirmation');
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
    
    // Password Strength Checker
    const passwordInput = document.getElementById('password');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        let strength = 0;
        
        // Check requirements
        const hasLength = password.length >= 8;
        const hasUppercase = /[A-Z]/.test(password);
        const hasLowercase = /[a-z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        
        // Update requirement indicators
        document.getElementById('req-length').classList.toggle('valid', hasLength);
        document.getElementById('req-uppercase').classList.toggle('valid', hasUppercase);
        document.getElementById('req-lowercase').classList.toggle('valid', hasLowercase);
        document.getElementById('req-number').classList.toggle('valid', hasNumber);
        
        // Calculate strength
        if (hasLength) strength++;
        if (hasUppercase) strength++;
        if (hasLowercase) strength++;
        if (hasNumber) strength++;
        
        // Update strength bar and text
        strengthBar.className = 'password-strength-bar';
        strengthText.className = 'password-strength-text';
        
        if (password.length === 0) {
            strengthBar.classList.remove('weak', 'medium', 'strong');
            strengthText.textContent = '';
        } else if (strength <= 2) {
            strengthBar.classList.add('weak');
            strengthText.classList.add('weak');
            strengthText.textContent = '{{ __("Weak password") }}';
        } else if (strength === 3) {
            strengthBar.classList.add('medium');
            strengthText.classList.add('medium');
            strengthText.textContent = '{{ __("Medium password") }}';
        } else {
            strengthBar.classList.add('strong');
            strengthText.classList.add('strong');
            strengthText.textContent = '{{ __("Strong password") }}';
        }
    });
    
    // Form Validation Enhancement
    const form = document.getElementById('registerForm');
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
    
    // Password Match Validation
    const confirmPasswordInput = document.getElementById('password_confirmation');
    
    confirmPasswordInput.addEventListener('input', function() {
        if (this.value && this.value !== passwordInput.value) {
            this.setCustomValidity('{{ __("Passwords do not match") }}');
        } else {
            this.setCustomValidity('');
        }
    });
    
    passwordInput.addEventListener('input', function() {
        if (confirmPasswordInput.value && confirmPasswordInput.value !== this.value) {
            confirmPasswordInput.setCustomValidity('{{ __("Passwords do not match") }}');
        } else {
            confirmPasswordInput.setCustomValidity('');
        }
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