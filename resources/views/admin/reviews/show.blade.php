@extends('admin.layouts.app')

@section('title', __('Review Details'))

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">{{ __('Review Details') }}</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.reviews.index') }}">{{ __('Reviews') }}</a></li>
                        <li class="breadcrumb-item active">#{{ $review->id }}</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> {{ __('Back to Reviews') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Main Review Details -->
        <div class="col-lg-8">
            <!-- Review Information -->
            <div class="content-card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-star me-2"></i>{{ __('Review Information') }}</h5>
                    <div>
                        @if (auth('admin')->user()->hasPermission('reviews.edit'))
                            @if ($review->isApproved())
                                <form action="{{ route('admin.reviews.unapprove', $review) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-warning">
                                        <i class="bi bi-x-circle"></i> {{ __('Unapprove') }}
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.reviews.approve', $review) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="bi bi-check-circle"></i> {{ __('Approve') }}
                                    </button>
                                </form>
                            @endif
                        @endif

                        @if (auth('admin')->user()->hasPermission('reviews.delete'))
                            <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('{{ __('Are you sure you want to delete this review?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i> {{ __('Delete') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Rating -->
                    <div class="review-rating mb-4">
                        <div class="d-flex align-items-center">
                            <div class="rating-display">
                                <span class="rating-number">{{ $review->rating }}</span>
                                <span class="rating-max">/5</span>
                            </div>
                            <div class="ms-3">
                                <div class="rating-stars">
                                    {!! $review->stars_html !!}
                                </div>
                                <small class="text-muted">{{ __('Rating') }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Review Title & Comment -->
                    <div class="review-content mb-4">
                        <h4 class="review-title">{{ $review->title }}</h4>
                        <p class="review-comment">{{ $review->comment }}</p>
                    </div>

                    <!-- Review Metadata -->
                    <div class="review-meta">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">{{ __('Review ID') }}</label>
                                <p class="mb-0"><strong>#{{ $review->id }}</strong></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">{{ __('Order ID') }}</label>
                                <p class="mb-0">
                                    <a href="{{ route('admin.orders.show', $review->order_id) }}"
                                        class="text-decoration-none">
                                        <strong>#{{ $review->order_id }}</strong>
                                    </a>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">{{ __('Verified Purchase') }}</label>
                                <p class="mb-0">
                                    @if ($review->is_verified_purchase)
                                        <span class="badge bg-success">
                                            <i class="bi bi-patch-check"></i> {{ __('Yes') }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('No') }}</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">{{ __('Status') }}</label>
                                <p class="mb-0">
                                    <span class="badge bg-{{ $review->status_badge }}">
                                        {{ $review->status_text }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">{{ __('Helpful Count') }}</label>
                                <p class="mb-0"><strong>{{ $review->helpful_count }}</strong></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">{{ __('Created Date') }}</label>
                                <p class="mb-0">{{ $review->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Response Section -->
            <div class="content-card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-reply me-2"></i>{{ __('Admin Response') }}</h5>
                </div>
                <div class="card-body">
                    @if ($review->hasAdminResponse())
                        <!-- Existing Response -->
                        <div class="alert alert-info mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <p class="mb-2"><strong>{{ __('Response:') }}</strong></p>
                                    <p class="mb-2">{{ $review->admin_response }}</p>
                                    <small class="text-muted">
                                        {{ __('Responded by') }}
                                        <strong>{{ $review->adminResponder->name ?? __('Admin') }}</strong>
                                        {{ __('on') }} {{ $review->admin_response_at->format('M d, Y h:i A') }}
                                    </small>
                                </div>
                                @if (auth('admin')->user()->hasPermission('reviews.delete'))
                                    <form action="#" method="POST" class="ms-2"
                                        onsubmit="return confirm('{{ __('Are you sure you want to delete this response?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        @if (auth('admin')->user()->hasPermission('reviews.respond'))
                            <button type="button" class="btn btn-primary" data-bs-toggle="collapse"
                                data-bs-target="#updateResponseForm">
                                <i class="bi bi-pencil"></i> {{ __('Update Response') }}
                            </button>

                            <div class="collapse mt-3" id="updateResponseForm">
                                <form action="{{ route('admin.reviews.respond', $review) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="admin_response"
                                            class="form-label">{{ __('Updated Response') }}</label>
                                        <textarea name="admin_response" id="admin_response" rows="4"
                                            class="form-control @error('admin_response') is-invalid @enderror" required>{{ old('admin_response', $review->admin_response) }}</textarea>
                                        @error('admin_response')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">{{ __('Maximum 1000 characters') }}</small>
                                    </div>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle"></i> {{ __('Update Response') }}
                                    </button>
                                </form>
                            </div>
                        @endif
                    @else
                        <!-- No Response Yet -->
                        @if (auth('admin')->user()->hasPermission('reviews.respond'))
                            <form action="{{ route('admin.reviews.respond', $review) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="admin_response" class="form-label">{{ __('Your Response') }}</label>
                                    <textarea name="admin_response" id="admin_response" rows="4"
                                        class="form-control @error('admin_response') is-invalid @enderror"
                                        placeholder="{{ __('Write your response to this review...') }}" required>{{ old('admin_response') }}</textarea>
                                    @error('admin_response')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">{{ __('Maximum 1000 characters') }}</small>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send"></i> {{ __('Submit Response') }}
                                </button>
                            </form>
                        @else
                            <p class="text-muted">{{ __('No admin response yet') }}</p>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Product Information -->
            <div class="content-card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>{{ __('Product') }}</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @if ($review->product->primaryImage)
                            <img src="{{ asset('storage/' . $review->product->primaryImage->image_path) }}"
                                alt="{{ $review->product->name() }}" class="img-fluid rounded"
                                style="max-height: 200px; object-fit: cover;">
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                style="height: 200px;">
                                <i class="bi bi-image display-4 text-muted"></i>
                            </div>
                        @endif
                    </div>
                    <h5 class="mb-2">{{ $review->product->name() }}</h5>
                    <p class="text-muted mb-2">{{ __('SKU:') }} {{ $review->product->sku }}</p>
                    <p class="mb-3">
                        <strong>${{ number_format($review->product->price, 2) }}</strong>
                        @if ($review->product->hasDiscount())
                            <del class="text-muted ms-2">${{ number_format($review->product->compare_price, 2) }}</del>
                        @endif
                    </p>
                    <a href="{{ route('admin.products.show', $review->product_id) }}"
                        class="btn btn-sm btn-primary w-100">
                        <i class="bi bi-eye"></i> {{ __('View Product') }}
                    </a>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="content-card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-person me-2"></i>{{ __('Customer') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="customer-avatar">
                            <i class="bi bi-person-circle"></i>
                        </div>
                    </div>
                    <h5 class="mb-2">{{ $review->user->name }}</h5>
                    <p class="text-muted mb-2">
                        <i class="bi bi-envelope me-2"></i>{{ $review->user->email }}
                    </p>
                    @if ($review->user->phone)
                        <p class="text-muted mb-3">
                            <i class="bi bi-telephone me-2"></i>{{ $review->user->phone }}
                        </p>
                    @endif
                    <a href="{{ route('admin.customers.show', $review->user_id) }}"
                        class="btn btn-sm btn-secondary w-100">
                        <i class="bi bi-eye"></i> {{ __('View Customer') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        .rating-display {
            font-size: 48px;
            font-weight: 700;
            color: #20b2aa;
        }

        .rating-display .rating-max {
            font-size: 24px;
            color: #6c757d;
        }

        .rating-stars {
            font-size: 24px;
        }

        .rating-stars i {
            margin-right: 4px;
        }

        .review-title {
            font-size: 24px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 1rem;
        }

        .review-comment {
            font-size: 16px;
            line-height: 1.6;
            color: #495057;
        }

        .customer-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            font-size: 48px;
            color: #6c757d;
        }

        .content-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .card-header {
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 1.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }
    </style>
@endpush
