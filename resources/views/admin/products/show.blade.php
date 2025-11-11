@extends('admin.layouts.app')

@section('title', __('Product Details'))

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">{{ __('Product Details') }}</h1>
    <p class="page-subtitle">{{ __('View complete product information') }}</p>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">{{ __('Products') }}</a></li>
            <li class="breadcrumb-item active">{{ $product->name() }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Product Images -->
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Product Images') }}</h3>
            </div>
            <div class="card-body">
                @if($product->images->count() > 0)
                <div class="row">
                    <div class="col-md-12 mb-3">
                        @if($product->primaryImage)
                        <img 
                            src="{{ asset('storage/' . $product->primaryImage->image_path) }}" 
                            alt="{{ $product->name() }}"
                            style="width: 100%; max-height: 400px; object-fit: contain; border-radius: 12px; border: 2px solid var(--border-color);"
                        >
                        @endif
                    </div>
                    @if($product->images->count() > 1)
                    <div class="col-md-12">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($product->images as $image)
                            <img 
                                src="{{ asset('storage/' . $image->image_path) }}" 
                                alt="{{ $product->name() }}"
                                style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 2px solid {{ $image->is_primary ? 'var(--primary-color)' : 'var(--border-color)' }}; cursor: pointer;"
                                onclick="changeMainImage('{{ asset('storage/' . $image->image_path) }}')"
                            >
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                @else
                <div class="text-center" style="padding: 60px 20px; background: var(--content-bg); border-radius: 12px;">
                    <i class="bi bi-image" style="font-size: 4rem; color: var(--text-muted);"></i>
                    <p class="text-muted mt-3">{{ __('No images available') }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Product Information -->
        <div class="content-card mt-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('Product Information') }}</h3>
            </div>
            <div class="card-body">
                <!-- Language Tabs -->
                <ul class="nav nav-tabs mb-3" role="tablist">
                    @foreach($product->translations as $translation)
                    <li class="nav-item" role="presentation">
                        <button 
                            class="nav-link {{ $loop->first ? 'active' : '' }}" 
                            id="tab-{{ $translation->locale }}" 
                            data-bs-toggle="tab" 
                            data-bs-target="#content-{{ $translation->locale }}" 
                            type="button" 
                            role="tab">
                            {{ strtoupper($translation->locale) }}
                        </button>
                    </li>
                    @endforeach
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">
                    @foreach($product->translations as $translation)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
                         id="content-{{ $translation->locale }}" 
                         role="tabpanel">
                        
                        <div class="mb-3">
                            <strong style="font-size: 0.9rem; color: var(--text-muted); text-transform: uppercase;">
                                {{ __('Product Name') }}
                            </strong>
                            <div style="font-size: 1.3rem; font-weight: 600; margin-top: 5px;">
                                {{ $translation->name }}
                            </div>
                        </div>

                        @if($translation->description)
                        <div>
                            <strong style="font-size: 0.9rem; color: var(--text-muted); text-transform: uppercase;">
                                {{ __('Description') }}
                            </strong>
                            <div style="margin-top: 5px; line-height: 1.6;">
                                {{ $translation->description }}
                            </div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Pricing & Inventory -->
        <div class="content-card mt-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('Pricing & Inventory') }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <strong style="font-size: 0.9rem; color: var(--text-muted);">{{ __('Price') }}</strong>
                            <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary-color); margin-top: 5px;">
                                ₹{{ number_format($product->price, 2) }}
                            </div>
                        </div>
                    </div>

                    @if($product->compare_price)
                    <div class="col-md-3">
                        <div class="mb-3">
                            <strong style="font-size: 0.9rem; color: var(--text-muted);">{{ __('Compare Price') }}</strong>
                            <div style="font-size: 1.2rem; margin-top: 5px; text-decoration: line-through; color: var(--text-muted);">
                                ₹{{ number_format($product->compare_price, 2) }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <strong style="font-size: 0.9rem; color: var(--text-muted);">{{ __('Discount') }}</strong>
                            <div style="font-size: 1.2rem; font-weight: 600; color: var(--success-color); margin-top: 5px;">
                                {{ $product->discountPercentage() }}%
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="col-md-3">
                        <div class="mb-3">
                            <strong style="font-size: 0.9rem; color: var(--text-muted);">{{ __('Stock') }}</strong>
                            <div style="font-size: 1.2rem; font-weight: 600; margin-top: 5px;">
                                @if($product->stock_quantity > 10)
                                <span class="badge badge-success" style="font-size: 1rem;">{{ $product->stock_quantity }}</span>
                                @elseif($product->stock_quantity > 0)
                                <span class="badge badge-warning" style="font-size: 1rem;">{{ $product->stock_quantity }}</span>
                                @else
                                <span class="badge badge-danger" style="font-size: 1rem;">{{ __('Out of Stock') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <strong style="font-size: 0.9rem; color: var(--text-muted);">{{ __('SKU') }}</strong>
                            <div style="margin-top: 5px;">
                                <code style="font-size: 1rem; padding: 5px 10px; background: var(--content-bg); border-radius: 4px;">
                                    {{ $product->sku }}
                                </code>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <strong style="font-size: 0.9rem; color: var(--text-muted);">{{ __('Category') }}</strong>
                            <div style="margin-top: 5px;">
                                <span class="badge badge-primary" style="font-size: 0.9rem; padding: 6px 12px;">
                                    {{ $product->category->name() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Quick Actions') }}</h3>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if(auth('admin')->user()->hasPermission('products.edit'))
                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> {{ __('Edit Product') }}
                    </a>
                    @endif

                    <a href="{{ route('products.show', $product->slug) }}" target="_blank" class="btn btn-info">
                        <i class="bi bi-eye"></i> {{ __('View in Store') }}
                    </a>

                    @if(auth('admin')->user()->hasPermission('products.delete'))
                    <form action="{{ route('admin.products.destroy', $product->id) }}" 
                          method="POST" 
                          onsubmit="return confirm('{{ __('Are you sure you want to delete this product?') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-trash"></i> {{ __('Delete Product') }}
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Product Status -->
        <div class="content-card mt-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('Product Status') }}</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>{{ __('Status') }}:</strong>
                    <div class="mt-1">
                        @if($product->is_active)
                        <span class="badge badge-success">{{ __('Active') }}</span>
                        @else
                        <span class="badge badge-danger">{{ __('Inactive') }}</span>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <strong>{{ __('Featured') }}:</strong>
                    <div class="mt-1">
                        @if($product->is_featured)
                        <span class="badge badge-warning">{{ __('Yes') }}</span>
                        @else
                        <span class="badge badge-secondary">{{ __('No') }}</span>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <strong>{{ __('Availability') }}:</strong>
                    <div class="mt-1">
                        @if($product->isInStock())
                        <span class="badge badge-success">{{ __('In Stock') }}</span>
                        @else
                        <span class="badge badge-danger">{{ __('Out of Stock') }}</span>
                        @endif
                    </div>
                </div>

                @if($product->hasDiscount())
                <div>
                    <strong>{{ __('On Sale') }}:</strong>
                    <div class="mt-1">
                        <span class="badge badge-info">{{ __('Yes') }} (-{{ $product->discountPercentage() }}%)</span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Product Meta -->
        <div class="content-card mt-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('Product Meta') }}</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>{{ __('Created') }}:</strong>
                    <div class="text-muted">{{ $product->created_at->format('M d, Y h:i A') }}</div>
                </div>
                <div class="mb-3">
                    <strong>{{ __('Last Updated') }}:</strong>
                    <div class="text-muted">{{ $product->updated_at->format('M d, Y h:i A') }}</div>
                </div>
                <div>
                    <strong>{{ __('Product URL') }}:</strong>
                    <div class="mt-1">
                        <code style="font-size: 0.8rem; word-break: break-all;">
                            {{ route('products.show', $product->slug) }}
                        </code>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function changeMainImage(src) {
    const mainImage = document.querySelector('.content-card img[style*="max-height: 400px"]');
    if (mainImage) {
        mainImage.src = src;
    }
}
</script>
@endpush