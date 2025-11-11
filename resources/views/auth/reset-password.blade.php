@extends('layouts.app')

@section('title', __('Reset Password'))

@push('styles')
<style>
    /* Auth Page Styles */
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
        font-size: 0.95rem;
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
    
    .btn-auth-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(var(--primary-rgb), 0.4);
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
    
    .alert-custom ul {
        margin: 0;
        padding-left: 20px;
    }
    
    .alert-custom li {
        color: var(--danger-color);
        margin-bottom: 5px;
    }
    
    .resend-section {
        text-align: center;
        margin: 20px 0;
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
    }
    
    .resend-section p {
        color: var(--medium-gray);
        margin-bottom: 10px;
        font-size: 0.9rem;
    }
    
    .resend-section button {
        background: none;
        border: 2px solid var(--primary-color);
        color: var(--primary-color);
        padding: 10px 30px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .resend-section button:hover:not(:disabled) {
        background: var(--primary-color);
        color: white;
        transform: translateY(-2px);
    }
    
    .resend-section button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .timer-text {
        color: var(--medium-gray);
        font-size: 0.85rem;
        margin-top: 8px;
    }
    
    .otp-input {
        letter-spacing: 10px;
        font-size: 24px;
        font-weight: 700;
        text-align: center;
    }
    
    @media (max-width: 768px) {
        .auth-body {
            padding: 30px;
        }
        
        .auth-header {
            padding: 30px 20px;
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
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                        <h2>{{ __('Reset Password') }}</h2>
                        <p>{{ __('Enter the OTP sent to') }}<br><strong>{{ $user->email }}</strong></p>
                    </div>
                    
                    <!-- Body -->
                    <div class="auth-body">
                        {{-- Success Message --}}
                        @if(session('success'))
                        <div class="alert-custom alert-success-custom">
                            <div style="display: flex; align-items: start; gap: 10px;">
                                <i class="bi bi-check-circle-fill" style="color: #28a745; font-size: 1.3rem;"></i>
                                <div style="flex: 1; color: #155724;">
                                    {{ session('success') }}
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        {{-- Error Messages --}}
                        @if(session('error'))
                        <div class="alert-custom">
                            <div style="display: flex; align-items: start; gap: 10px;">
                                <i class="bi bi-exclamation-triangle-fill" style="color: var(--danger-color); font-size: 1.3rem;"></i>
                                <div style="flex: 1;">
                                    {{ session('error') }}
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if($errors->any())
                        <div class="alert-custom">
                            <ul>
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        
                        <form action="{{ route('password.update') }}" method="POST" id="resetForm">
                            @csrf
                            
                            <!-- OTP Field -->
                            <div class="form-floating">
                                <input type="text" 
                                       class="form-control otp-input @error('otp') is-invalid @enderror" 
                                       id="otp" 
                                       name="otp" 
                                       maxlength="6"
                                       pattern="[0-9]{6}"
                                       placeholder="000000"
                                       required 
                                       autofocus>
                                <label for="otp">
                                    <i class="bi bi-shield-check me-2"></i>{{ __('Enter OTP') }}
                                </label>
                                @error('otp')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="bi bi-info-circle"></i> {{ __('Enter the 6-digit code sent to your email') }}
                                </small>
                            </div>
                            
                            <!-- New Password Field -->
                            <div class="form-floating" style="position: relative;">
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       placeholder="{{ __('password') }}"
                                       required>
                                <label for="password">
                                    <i class="bi bi-lock me-2"></i>{{ __('New Password') }}
                                </label>
                                <i class="bi bi-eye password-toggle" id="togglePassword"></i>
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                <i class="bi bi-check-circle me-2"></i>{{ __('Reset Password') }}
                            </button>
                        </form>
                        
                        <!-- Resend OTP Section -->
                        <div class="resend-section">
                            <p>{{ __('Didn\'t receive the code?') }}</p>
                            <form method="POST" action="{{ route('password.resend.otp') }}" id="resendForm">
                                @csrf
                                <button type="submit" id="resendBtn">
                                    <i class="bi bi-arrow-clockwise me-1"></i>{{ __('Resend OTP') }}
                                </button>
                            </form>
                            <div class="timer-text" id="timer"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // OTP Input - Numbers only
    document.getElementById('otp').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    
    // Password Toggle for New Password
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
    
    // Password Toggle for Confirm Password
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
    
    // Password Match Validation
    const passwordInput = document.getElementById('password');
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
    
    // Resend timer (60 seconds cooldown)
    let timeLeft = 60;
    const resendBtn = document.getElementById('resendBtn');
    const timerDiv = document.getElementById('timer');
    
    function startTimer() {
        resendBtn.disabled = true;
        
        const countdown = setInterval(() => {
            timeLeft--;
            timerDiv.textContent = `{{ __('Resend available in') }} ${timeLeft}s`;
            
            if (timeLeft <= 0) {
                clearInterval(countdown);
                resendBtn.disabled = false;
                timerDiv.textContent = '';
                timeLeft = 60;
            }
        }, 1000);
    }
    
    // Start timer on page load if success message exists
    window.addEventListener('load', () => {
        @if(session('success'))
            startTimer();
        @endif
    });
    
    // Start timer when resend is clicked
    document.getElementById('resendForm').addEventListener('submit', function(e) {
        setTimeout(() => {
            startTimer();
        }, 100);
    });
</script>
@endpush