@extends('layouts.app')

@section('title', __('Forgot Password'))

@push('styles')
<style>
    /* Auth Page Styles (Same as login/register) */
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
    
    .invalid-feedback {
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: block;
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
    
    .info-box {
        background: var(--light-gray);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
        border-left: 4px solid var(--primary-color);
    }
    
    .info-box i {
        color: var(--primary-color);
        font-size: 1.2rem;
        margin-right: 10px;
    }
    
    .info-box p {
        margin: 0;
        color: var(--medium-gray);
        font-size: 0.95rem;
        line-height: 1.6;
    }
    
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
                            <i class="bi bi-key-fill"></i>
                        </div>
                        <h2>{{ __('Forgot Password?') }}</h2>
                        <p>{{ __('No worries, we\'ll send you reset instructions') }}</p>
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
                        @if($errors->any())
                        <div class="alert-custom">
                            <ul>
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        
                        <!-- Info Box -->
                        <div class="info-box">
                            <p>
                                <i class="bi bi-info-circle-fill"></i>
                                {{ __('Enter your email address and we\'ll send you a verification code to reset your password.') }}
                            </p>
                        </div>
                        
                        <form action="{{ route('password.email') }}" method="POST">
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
                            
                            <!-- Submit Button -->
                            <button type="submit" class="btn-auth-primary">
                                <i class="bi bi-send me-2"></i>{{ __('Send Reset Code') }}
                            </button>
                        </form>
                    </div>
                    
                    <!-- Footer -->
                    <div class="auth-footer">
                        <p>
                            <i class="bi bi-arrow-left me-2"></i>
                            <a href="{{ route('login') }}">{{ __('Back to Login') }}</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection