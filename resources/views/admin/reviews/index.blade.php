@extends('admin.layouts.app')

@section('title', __('Product Reviews'))

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">{{ __('Product Reviews') }}</h1>
            <p class="page-subtitle">{{ __('Manage customer product reviews and ratings') }}</p>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('Reviews') }}</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="stats-icon bg-primary">
                <i class="bi bi-star-fill"></i>
            </div>
            <div class="stats-content">
                <h3 class="stats-value">{{ number_format($stats['total']) }}</h3>
                <p class="stats-label">{{ __('Total Reviews') }}</p>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="stats-icon bg-warning">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="stats-content">
                <h3 class="stats-value">{{ number_format($stats['pending']) }}</h3>
                <p class="stats-label">{{ __('Pending Approval') }}</p>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="stats-icon bg-success">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="stats-content">
                <h3 class="stats-value">{{ number_format($stats['approved']) }}</h3>
                <p class="stats-label">{{ __('Approved') }}</p>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="stats-icon bg-info">
                <i class="bi bi-star-half"></i>
            </div>
            <div class="stats-content">
                <h3 class="stats-value">{{ number_format($stats['avg_rating'], 1) }} <small>/5</small></h3>
                <p class="stats-label">{{ __('Average Rating') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="content-card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.reviews.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label">{{ __('Search') }}</label>
                <input type="text" name="search" id="search" class="form-control" 
                    value="{{ request('search') }}" placeholder="{{ __('Title, comment, customer...') }}">
            </div>

            <div class="col-md-2">
                <label for="is_approved" class="form-label">{{ __('Status') }}</label>
                <select name="is_approved" id="is_approved" class="form-select">
                    <option value="">{{ __('All') }}</option>
                    <option value="0" {{ request('is_approved') === '0' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                    <option value="1" {{ request('is_approved') === '1' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="rating" class="form-label">{{ __('Rating') }}</label>
                <select name="rating" id="rating" class="form-select">
                    <option value="">{{ __('All Ratings') }}</option>
                    @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                            {{ $i }} <i class="bi bi-star-fill"></i>
                        </option>
                    @endfor
                </select>
            </div>

            <div class="col-md-3">
                <label for="product_id" class="form-label">{{ __('Product') }}</label>
                <select name="product_id" id="product_id" class="form-select">
                    <option value="">{{ __('All Products') }}</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-funnel"></i> {{ __('Filter') }}
                </button>
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Actions -->
@if(auth('admin')->user()->hasPermission('reviews.approve') || auth('admin')->user()->hasPermission('reviews.delete'))
<div class="content-card mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <div class="form-check me-3">
                <input class="form-check-input" type="checkbox" id="selectAll">
                <label class="form-check-label" for="selectAll">
                    {{ __('Select All') }}
                </label>
            </div>
            
            @if(auth('admin')->user()->hasPermission('reviews.approve'))
            <button type="button" class="btn btn-sm btn-success me-2" onclick="bulkAction('approve')">
                <i class="bi bi-check-circle"></i> {{ __('Approve Selected') }}
            </button>
            @endif

            @if(auth('admin')->user()->hasPermission('reviews.delete'))
            <button type="button" class="btn btn-sm btn-danger" onclick="bulkAction('delete')">
                <i class="bi bi-trash"></i> {{ __('Delete Selected') }}
            </button>
            @endif
        </div>
    </div>
</div>
@endif

<!-- Reviews Table -->
<div class="content-card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    @if(auth('admin')->user()->hasPermission('reviews.approve') || auth('admin')->user()->hasPermission('reviews.delete'))
                    <th width="30">
                        <input type="checkbox" id="selectAllTable">
                    </th>
                    @endif
                    <th>{{ __('Product') }}</th>
                    <th>{{ __('Customer') }}</th>
                    <th>{{ __('Rating') }}</th>
                    <th>{{ __('Review') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th width="150">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $review)
                <tr>
                    @if(auth('admin')->user()->hasPermission('reviews.approve') || auth('admin')->user()->hasPermission('reviews.delete'))
                    <td>
                        <input type="checkbox" class="review-checkbox" value="{{ $review->id }}">
                    </td>
                    @endif
                    <td>
                        <div class="d-flex align-items-center">
                            @if($review->product && $review->product->primaryImage)
                                <img src="{{ asset('storage/' . $review->product->primaryImage->image_path) }}" 
                                     alt="{{ $review->product->name() }}" 
                                     class="product-thumbnail me-2">
                            @else
                                <div class="product-thumbnail-placeholder me-2">
                                    <i class="bi bi-image"></i>
                                </div>
                            @endif
                            <div>
                                <a href="{{ route('admin.products.show', $review->product_id) }}" class="text-decoration-none">
                                    {{ $review->product->name() }}
                                </a>
                                <br>
                                <small class="text-muted">SKU: {{ $review->product->sku }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <strong>{{ $review->user->name }}</strong><br>
                        <small class="text-muted">{{ $review->user->email }}</small>
                        @if($review->is_verified_purchase)
                            <br><span class="badge bg-info"><i class="bi bi-patch-check"></i> {{ __('Verified') }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="rating-stars">
                            {!! $review->stars_html !!}
                        </div>
                        <small class="text-muted">{{ $review->rating }}/5</small>
                    </td>
                    <td>
                        <strong>{{ $review->title }}</strong><br>
                        <small class="text-muted">{{ Str::limit($review->comment, 50) }}</small>
                        @if($review->hasAdminResponse())
                            <br><span class="badge bg-secondary"><i class="bi bi-reply"></i> {{ __('Responded') }}</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-{{ $review->status_badge }}">
                    {{ $review->is_approved ? 'pending' : 'approved' }}

                        </span>
                    </td>
                    <td>
                        <small>{{ $review->created_at->format('M d, Y') }}</small><br>
                        <small class="text-muted">{{ $review->created_at->format('h:i A') }}</small>
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            @if(auth('admin')->user()->hasPermission('reviews.view'))
                            <a href="{{ route('admin.reviews.show', $review) }}" 
                               class="btn btn-sm btn-info" 
                               title="{{ __('View Details') }}">
                                <i class="bi bi-eye"></i>
                            </a>
                            @endif

                            @if(auth('admin')->user()->hasPermission('reviews.approve'))
                                @if($review->isApproved())
                                <form action="{{ route('admin.reviews.unapprove', $review) }}" method="POST" class="d-inline">
                                    @csrf
                               
                                    <button type="submit" class="btn btn-sm btn-warning" 
                                            title="{{ __('Unapprove') }}">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('admin.reviews.approve', $review) }}" method="POST" class="d-inline">
                                    @csrf
                                  
                                    <button type="submit" class="btn btn-sm btn-success" 
                                            title="{{ __('Approve') }}">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                </form>
                                @endif
                            @endif

                            @if(auth('admin')->user()->hasPermission('reviews.delete'))
                            <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('{{ __('Are you sure you want to delete this review?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" 
                                        title="{{ __('Delete') }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <i class="bi bi-inbox display-4 text-muted"></i>
                        <p class="mt-2 text-muted">{{ __('No reviews found') }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($reviews->hasPages())
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <p class="text-muted mb-0">
                    {{ __('Showing :from to :to of :total reviews', [
                        'from' => $reviews->firstItem() ?? 0,
                        'to' => $reviews->lastItem() ?? 0,
                        'total' => $reviews->total()
                    ]) }}
                </p>
            </div>
            <div>
                {{ $reviews->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

@endsection

@push('styles')
<style>
    .stats-card {
        background: #fff;
        border-radius: 10px;
        padding: 1.5rem;
        display: flex;
        align-items-center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: transform 0.2s;
    }

    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }

    .stats-icon {
        width: 60px;
        height: 60px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #fff;
        margin-right: 1rem;
    }

    .stats-content h3 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
        color: #1a1a1a;
    }

    .stats-content p {
        margin: 0;
        font-size: 14px;
        color: #6c757d;
    }

    .product-thumbnail {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 8px;
    }

    .product-thumbnail-placeholder {
        width: 50px;
        height: 50px;
        background: #f8f9fa;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
    }

    .rating-stars {
        font-size: 16px;
    }

    .rating-stars i {
        margin-right: 2px;
    }
</style>
@endpush

@push('scripts')
<script>
    // Select All Functionality
    document.getElementById('selectAll')?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.review-checkbox');
        checkboxes.forEach(checkbox => checkbox.checked = this.checked);
    });

    document.getElementById('selectAllTable')?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.review-checkbox');
        checkboxes.forEach(checkbox => checkbox.checked = this.checked);
    });

    // Bulk Actions
    function bulkAction(action) {
        const checkboxes = document.querySelectorAll('.review-checkbox:checked');
        const reviewIds = Array.from(checkboxes).map(cb => cb.value);

        if (reviewIds.length === 0) {
            alert('{{ __("Please select at least one review") }}');
            return;
        }

        if (action === 'delete') {
            if (!confirm('{{ __("Are you sure you want to delete the selected reviews?") }}')) {
                return;
            }
        }

        // Create form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = action === 'approve' 
            ? '{{ route("admin.reviews.bulk-approve") }}' 
            : '{{ route("admin.reviews.bulk-delete") }}';

        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);

        // Add review IDs
        reviewIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'review_ids[]';
            input.value = id;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    }
</script>
@endpush