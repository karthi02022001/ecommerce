@extends('admin.layouts.app')

@section('title', __('Products'))

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">{{ __('Products') }}</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Products') }}</li>
        </ol>
    </nav>
</div>

<!-- Action Bar -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex gap-2">
        @can('products.create', auth('admin')->user())
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>{{ __('Add New Product') }}
        </a>
        @endcan
        @can('products.import', auth('admin')->user())
        <button class="btn btn-outline">
            <i class="bi bi-upload me-2"></i>{{ __('Import') }}
        </button>
        @endcan
        @can('products.export', auth('admin')->user())
        <button class="btn btn-outline">
            <i class="bi bi-download me-2"></i>{{ __('Export') }}
        </button>
        @endcan
    </div>
</div>

<!-- Filters -->
<div class="content-card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.products.index') }}" method="GET" class="row g-3">
            <!-- Search -->
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="{{ __('Search products...') }}" value="{{ request('search') }}">
            </div>
            
            <!-- Category Filter -->
            <div class="col-md-2">
                <select name="category" class="form-select">
                    <option value="">{{ __('All Categories') }}</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name() }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Status Filter -->
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">{{ __('All Status') }}</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                </select>
            </div>
            
            <!-- Stock Filter -->
            <div class="col-md-2">
                <select name="stock" class="form-select">
                    <option value="">{{ __('All Stock') }}</option>
                    <option value="in_stock" {{ request('stock') == 'in_stock' ? 'selected' : '' }}>{{ __('In Stock') }}</option>
                    <option value="low_stock" {{ request('stock') == 'low_stock' ? 'selected' : '' }}>{{ __('Low Stock') }}</option>
                    <option value="out_of_stock" {{ request('stock') == 'out_of_stock' ? 'selected' : '' }}>{{ __('Out of Stock') }}</option>
                </select>
            </div>
            
            <!-- Submit -->
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-2"></i>{{ __('Filter') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Products Table -->
<div class="content-card">
    <div class="card-header">
        <h3 class="card-title">{{ __('All Products') }} ({{ $products->total() }})</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">{{ __('Image') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('SKU') }}</th>
                        <th>{{ __('Category') }}</th>
                        <th>{{ __('Price') }}</th>
                        <th>{{ __('Stock') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th style="width: 150px;">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>
                            @if($product->primaryImage)
                            <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" 
                                 alt="{{ $product->name() }}" 
                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                            @else
                            <div style="width: 50px; height: 50px; background: var(--content-bg); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-image" style="font-size: 1.5rem; color: var(--text-muted);"></i>
                            </div>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight: 600;">{{ $product->name() }}</div>
                            @if($product->is_featured)
                            <span class="badge badge-warning" style="font-size: 0.7rem;">{{ __('Featured') }}</span>
                            @endif
                        </td>
                        <td>{{ $product->sku }}</td>
                        <td>{{ $product->category->name() }}</td>
                        <td>
                            <div style="font-weight: 600;">₹{{ number_format($product->price, 2) }}</div>
                            @if($product->compare_price)
                            <small style="text-decoration: line-through; color: var(--text-muted);">
                                ₹{{ number_format($product->compare_price, 2) }}
                            </small>
                            @endif
                        </td>
                        <td>
                            @if($product->stock_quantity > 10)
                            <span class="badge badge-success">{{ $product->stock_quantity }}</span>
                            @elseif($product->stock_quantity > 0)
                            <span class="badge badge-warning">{{ $product->stock_quantity }}</span>
                            @else
                            <span class="badge badge-danger">{{ __('Out of Stock') }}</span>
                            @endif
                        </td>
                        <td>
                            @if($product->is_active)
                            <span class="badge badge-success">{{ __('Active') }}</span>
                            @else
                            <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-sm btn-icon btn-info" title="{{ __('View') }}">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @can('products.edit', auth('admin')->user())
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-icon btn-primary" title="{{ __('Edit') }}">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endcan
                                @can('products.delete', auth('admin')->user())
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this product?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-icon btn-danger" title="{{ __('Delete') }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 50px;">
                            <i class="bi bi-box-seam" style="font-size: 3rem; color: var(--text-muted);"></i>
                            <p class="mt-3" style="color: var(--text-muted);">{{ __('No products found') }}</p>
                            @can('products.create', auth('admin')->user())
                            <a href="{{ route('admin.products.create') }}" class="btn btn-primary mt-2">
                                <i class="bi bi-plus-circle me-2"></i>{{ __('Add Your First Product') }}
                            </a>
                            @endcan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($products->hasPages())
    <div class="card-body">
        {{ $products->links() }}
    </div>
    @endif
</div>
@endsection