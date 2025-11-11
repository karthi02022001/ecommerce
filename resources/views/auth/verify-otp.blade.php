@extends('layouts.app')

@section('title', __('Verify Email'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <i class="bi bi-envelope-check" style="font-size: 3rem; color: #20b2aa;"></i>
                        </div>
                        <h2 class="fw-bold">{{ __('Verify Your Email') }}</h2>
                        <p class="text-muted">
                            {{ __('We sent a 6-digit OTP to') }}<br>
                            <strong>{{ $user->email }}</strong>
                        </p>
                        @if($action === 'login_verify')
                            <div class="alert alert-info border-0 mt-3" role="alert">
                                <small><i class="bi bi-info-circle me-1"></i>{{ __('After verification, you will be automatically logged in.') }}</small>
                            </div>
                        @endif
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('verify.otp') }}" id="otpForm">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="otp" class="form-label fw-semibold">{{ __('Enter OTP') }}</label>
                            <input type="text" 
                                   class="form-control form-control-lg text-center @error('otp') is-invalid @enderror" 
                                   id="otp" 
                                   name="otp" 
                                   maxlength="6" 
                                   pattern="[0-9]{6}"
                                   placeholder="000000"
                                   style="letter-spacing: 10px; font-size: 24px; font-weight: 700;"
                                   required 
                                   autofocus>
                            @error('otp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="bi bi-info-circle"></i> {{ __('Enter the 6-digit code sent to your email') }}
                            </small>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg" style="background-color: #20b2aa; border: none;">
                                <i class="bi bi-check-circle me-2"></i>
                                @if($action === 'login_verify')
                                    {{ __('Verify & Login') }}
                                @else
                                    {{ __('Verify Email') }}
                                @endif
                            </button>
                        </div>
                    </form>

                    <div class="text-center">
                        <p class="text-muted mb-2">{{ __('Didn\'t receive the code?') }}</p>
                        <form method="POST" action="{{ route('resend.otp') }}" id="resendForm">
                            @csrf
                            <button type="submit" class="btn btn-link text-decoration-none" id="resendBtn">
                                <i class="bi bi-arrow-clockwise me-1"></i>{{ __('Resend OTP') }}
                            </button>
                        </form>
                        <div id="timer" class="text-muted small mt-2"></div>
                    </div>

                    <hr class="my-4">

                    <div class="text-center">
                        @if($action === 'register')
                            <p class="text-muted small mb-2">{{ __('Wrong email?') }}</p>
                            <a href="{{ route('register') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>{{ __('Back to Register') }}
                            </a>
                        @else
                            <p class="text-muted small mb-2">{{ __('Want to use different account?') }}</p>
                            <a href="{{ route('login') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>{{ __('Back to Login') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="text-center mt-3">
                <small class="text-muted">
                    <i class="bi bi-shield-check"></i> {{ __('Your information is secure and encrypted') }}
                </small>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-focus and format OTP input (numbers only)
document.getElementById('otp').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '');
});

// Resend timer (60 seconds cooldown)
let timeLeft = 60;
const resendBtn = document.getElementById('resendBtn');
const timerDiv = document.getElementById('timer');

function startTimer() {
    resendBtn.disabled = true;
    resendBtn.classList.add('disabled');
    
    const countdown = setInterval(() => {
        timeLeft--;
        timerDiv.textContent = `{{ __('Resend available in') }} ${timeLeft}s`;
        
        if (timeLeft <= 0) {
            clearInterval(countdown);
            resendBtn.disabled = false;
            resendBtn.classList.remove('disabled');
            timerDiv.textContent = '';
            timeLeft = 60;
        }
    }, 1000);
}

// Start timer on page load if OTP was just sent
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
@endsection