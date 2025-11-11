@extends('layouts.app')

@section('title', __('My Reviews'))

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('My Reviews') }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">{{ __('My Reviews') }}</h2>
            <p class="text-muted mb-0">{{ __('Manage all your product reviews') }}</p>
        </div>
        <a href="{{ route('orders.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-box-seam me-2"></i>{{ __('My Orders') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($reviews->count() > 0)
        <!-- Reviews List -->
        <div class="row g-4">
            @foreach($reviews as $review)
                <div class="col-12">
                    <div class="card border-0 shadow-sm hover-shadow transition">
                        <div class="card-body p-4">
                            <div class="row">
                                <!-- Product Image -->
                                <div class="col-auto">
                                    @if($review->product->primaryImage)
                                        <img src="{{ asset('storage/' . $review->product->primaryImage->image_path) }}" 
                                             alt="{{ $review->product->name() }}" 
                                             class="rounded" 
                                             style="width: 100px; height: 100px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                             style="width: 100px; height: 100px;">
                                            <i class="bi bi-image fs-3 text-muted"></i>
                                        </div>
                                    @endif
                                </div>

                                <!-- Review Content -->
                                <div class="col">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h5 class="mb-1">
                                                <a href="{{ route('products.show', $review->product->slug) }}" 
                                                   class="text-decoration-none text-dark">
                                                    {{ $review->product->name() }}
                                                </a>
                                            </h5>
                                            <div class="mb-2">
                                                {!! $review->stars_html !!}
                                                <span class="text-muted ms-2 small">{{ $review->formatted_date }}</span>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2">
                                            @if($review->is_approved)
                                                <span class="badge bg-success">{{ __('Approved') }}</span>
                                            @else
                                                <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                                            @endif
                                            @if($review->is_verified_purchase)
                                                <span class="badge bg-info">{{ __('Verified Purchase') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <h6 class="mb-2">{{ $review->title }}</h6>
                                    <p class="text-muted mb-3">{{ Str::limit($review->comment, 200) }}</p>

                                    @if($review->admin_response)
                                        <div class="alert alert-light border mb-3">
                                            <div class="d-flex">
                                                <i class="bi bi-reply-fill text-primary me-2"></i>
                                                <div class="flex-grow-1">
                                                    <strong class="d-block mb-1">{{ __('Store Response') }}</strong>
                                                    <p class="mb-0 small">{{ $review->admin_response }}</p>
                                                    <small class="text-muted">
                                                        {{ $review->formatted_response_date }} - {{ $review->adminResponder->name }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="{{ route('reviews.show', $review->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye me-1"></i>{{ __('View Details') }}
                                        </a>
                                        
                                        @if(!$review->is_approved)
                                            <a href="{{ route('reviews.edit', $review->id) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-pencil me-1"></i>{{ __('Edit') }}
                                            </a>
                                            
                                            <form action="{{ route('reviews.destroy', $review->id) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('{{ __('Are you sure you want to delete this review?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash me-1"></i>{{ __('Delete') }}
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('orders.show', $review->order_id) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-box me-1"></i>{{ __('View Order') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $reviews->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-star display-1 text-muted mb-3"></i>
                <h4 class="mb-3">{{ __('No Reviews Yet') }}</h4>
                <p class="text-muted mb-4">{{ __('You haven\'t written any reviews yet. Share your experience with products you\'ve purchased!') }}</p>
                <a href="{{ route('orders.index') }}" class="btn btn-primary px-4">
                    <i class="bi bi-box-seam me-2"></i>{{ __('View My Orders') }}
                </a>
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
    .hover-shadow {
        transition: box-shadow 0.3s ease;
    }
    .hover-shadow:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
</style>
@endpush
@endsection