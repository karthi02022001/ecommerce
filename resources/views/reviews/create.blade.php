@extends('layouts.app')

@section('title', __('Write a Review'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">{{ __('My Orders') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('orders.show', $order->id) }}">{{ __('Order') }} #{{ $order->order_number }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Write Review') }}</li>
                </ol>
            </nav>

            <!-- Page Header -->
            <div class="mb-4">
                <h2 class="mb-2">{{ __('Write a Review') }}</h2>
                <p class="text-muted">{{ __('Share your experience with this product') }}</p>
            </div>

            <!-- Product Info Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            @if($product->primaryImage)
                                <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" 
                                     alt="{{ $product->name() }}" 
                                     class="rounded" 
                                     style="width: 80px; height: 80px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                     style="width: 80px; height: 80px;">
                                    <i class="bi bi-image fs-3 text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col">
                            <h5 class="mb-1">{{ $product->name() }}</h5>
                            <p class="text-muted mb-0 small">{{ __('SKU') }}: {{ $product->sku }}</p>
                            <p class="text-muted mb-0 small">{{ __('Order') }} #{{ $order->order_number }}</p>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-success">{{ __('Verified Purchase') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Review Form -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('reviews.store', [$order->id, $product->id]) }}" method="POST">
                        @csrf

                        <!-- Rating -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">{{ __('Rating') }} <span class="text-danger">*</span></label>
                            <div class="star-rating-input">
                                <div class="d-flex align-items-center gap-2">
                                    @for ($i = 5; $i >= 1; $i--)
                                        <input type="radio" 
                                               id="star{{ $i }}" 
                                               name="rating" 
                                               value="{{ $i }}" 
                                               class="d-none"
                                               {{ old('rating') == $i ? 'checked' : '' }}
                                               required>
                                        <label for="star{{ $i }}" class="star-label mb-0" style="cursor: pointer; font-size: 2rem;">
                                            <i class="bi bi-star text-muted"></i>
                                        </label>
                                    @endfor
                                    <span class="ms-2 rating-text text-muted"></span>
                                </div>
                            </div>
                            @error('rating')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Review Title -->
                        <div class="mb-4">
                            <label for="title" class="form-label fw-semibold">{{ __('Review Title') }} <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title') }}" 
                                   placeholder="{{ __('Summarize your review') }}"
                                   maxlength="255"
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Review Comment -->
                        <div class="mb-4">
                            <label for="comment" class="form-label fw-semibold">{{ __('Your Review') }} <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('comment') is-invalid @enderror" 
                                      id="comment" 
                                      name="comment" 
                                      rows="6" 
                                      placeholder="{{ __('Tell others about your experience with this product') }}"
                                      minlength="10"
                                      maxlength="1000"
                                      required>{{ old('comment') }}</textarea>
                            <div class="form-text">{{ __('Minimum 10 characters, maximum 1000 characters') }}</div>
                            @error('comment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Guidelines -->
                        <div class="alert alert-info mb-4">
                            <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>{{ __('Review Guidelines') }}</h6>
                            <ul class="mb-0 small">
                                <li>{{ __('Be honest and constructive in your review') }}</li>
                                <li>{{ __('Focus on your experience with the product') }}</li>
                                <li>{{ __('Reviews will be moderated before being published') }}</li>
                                <li>{{ __('Avoid offensive language or personal information') }}</li>
                            </ul>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-circle me-2"></i>{{ __('Submit Review') }}
                            </button>
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-secondary px-4">
                                <i class="bi bi-x-circle me-2"></i>{{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .star-rating-input .star-label {
        transition: all 0.2s ease;
    }

    .star-rating-input input[type="radio"]:checked ~ label i,
    .star-rating-input label:hover i,
    .star-rating-input label:hover ~ label i {
        color: #ffc107 !important;
    }

    .star-rating-input .star-label i.bi-star-fill {
        color: #ffc107 !important;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star-label');
    const ratingText = document.querySelector('.rating-text');
    const ratingLabels = {
        1: '{{ __("Poor") }}',
        2: '{{ __("Fair") }}',
        3: '{{ __("Good") }}',
        4: '{{ __("Very Good") }}',
        5: '{{ __("Excellent") }}'
    };

    // Initialize stars on page load if rating is already selected
    const checkedInput = document.querySelector('input[name="rating"]:checked');
    if (checkedInput) {
        updateStars(checkedInput.value);
    }

    stars.forEach((star, index) => {
        const ratingValue = 5 - index;
        
        // On click
        star.addEventListener('click', function() {
            document.getElementById('star' + ratingValue).checked = true;
            updateStars(ratingValue);
        });

        // On hover
        star.addEventListener('mouseenter', function() {
            updateStars(ratingValue, true);
        });
    });

    // Reset on mouse leave
    document.querySelector('.star-rating-input').addEventListener('mouseleave', function() {
        const checkedInput = document.querySelector('input[name="rating"]:checked');
        if (checkedInput) {
            updateStars(checkedInput.value);
        } else {
            resetStars();
        }
    });

    function updateStars(rating, isHover = false) {
        stars.forEach((star, index) => {
            const starValue = 5 - index;
            const icon = star.querySelector('i');
            
            if (starValue <= rating) {
                icon.classList.remove('bi-star', 'text-muted');
                icon.classList.add('bi-star-fill', 'text-warning');
            } else {
                icon.classList.remove('bi-star-fill', 'text-warning');
                icon.classList.add('bi-star', 'text-muted');
            }
        });

        if (!isHover) {
            ratingText.textContent = ratingLabels[rating] || '';
        }
    }

    function resetStars() {
        stars.forEach(star => {
            const icon = star.querySelector('i');
            icon.classList.remove('bi-star-fill', 'text-warning');
            icon.classList.add('bi-star', 'text-muted');
        });
        ratingText.textContent = '';
    }
});
</script>
@endpush
@endsection