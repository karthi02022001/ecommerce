{{-- 
    Star Rating Component
    Usage: @include('components.star-rating', ['rating' => $product->averageRating(), 'count' => $product->reviewCount()])
    
    Parameters:
    - rating: Average rating (0-5)
    - count: Total review count (optional)
    - size: Icon size class (optional, default: '')
    - showCount: Show count in parentheses (optional, default: true)
--}}

@php
    $rating = $rating ?? 0;
    $count = $count ?? 0;
    $size = $size ?? '';
    $showCount = $showCount ?? true;
    
    // Calculate stars
    $fullStars = floor($rating);
    $hasHalfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
@endphp

<div class="product-rating d-inline-flex align-items-center">
    {{-- Full Stars --}}
    @for($i = 0; $i < $fullStars; $i++)
        <i class="bi bi-star-fill text-warning {{ $size }}"></i>
    @endfor
    
    {{-- Half Star --}}
    @if($hasHalfStar)
        <i class="bi bi-star-half text-warning {{ $size }}"></i>
    @endif
    
    {{-- Empty Stars --}}
    @for($i = 0; $i < $emptyStars; $i++)
        <i class="bi bi-star text-muted {{ $size }}"></i>
    @endfor
    
    {{-- Rating Number and Count --}}
    @if($showCount)
        <span class="rating-count ms-1 text-muted small">
            @if($rating > 0)
                ({{ number_format($rating, 1) }})
            @else
                ({{ __('No reviews') }})
            @endif
            
            @if($count > 0)
                <span class="review-count">· {{ $count }} {{ __('reviews') }}</span>
            @endif
        </span>
    @endif
</div>