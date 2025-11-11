@extends('admin.layouts.app')

@section('title', __('Products'))

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">{{ __('Products') }}</h1>
    <p class="page-subtitle">{{ __('Manage your store products') }}</p>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Products') }}</li>
        </ol>
    </nav>
</div>

<!-- Actions Bar -->
<div class="content-card mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-6">
                <form action="{{ route('admin.products.index') }}" method="GET" class="d-flex gap-2">
                    <input 
                        type="text" 
                        name="search" 
                        class="form-control" 
                        placeholder="{{ __('Search by name, SKU...') }}"
                        value="{{ request('search') }}"
                    >
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> {{ __('Search') }}
                    </button>
                    @if(request()->hasAny(['search', 'category', 'status', 'stock']))
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> {{ __('Clear') }}
                    </a>
                    @endif
                </form>
            </div>
            <div class="col-md-6 text-end">
                @if(auth('admin')->user()->hasPermission('products.create'))
                <a href="{{ route('admin.products.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> {{ __('Add New Product') }}
                </a>
                @endif
            </div>
        </div>

        <!-- Filters -->
        <div class="row mt-3">
            <div class="col-md-12">
                <form action="{{ route('admin.products.index') }}" method="GET" class="d-flex gap-2 flex-wrap">
                    @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    
                    <!-- Category Filter -->
                    <select name="category" class="form-select" style="width: auto;">
                        <option value="">{{ __('All Categories') }}</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name() }}
                        </option>
                        @endforeach
                    </select>

                    <!-- Status Filter -->
                    <select name="status" class="form-select" style="width: auto;">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                    </select>

                    <!-- Stock Filter -->
                    <select name="stock" class="form-select" style="width: auto;">
                        <option value="">{{ __('All Stock') }}</option>
                        <option value="in_stock" {{ request('stock') === 'in_stock' ? 'selected' : '' }}>{{ __('In Stock') }}</option>
                        <option value="low_stock" {{ request('stock') === 'low_stock' ? 'selected' : '' }}>{{ __('Low Stock') }}</option>
                        <option value="out_of_stock" {{ request('stock') === 'out_of_stock' ? 'selected' : '' }}>{{ __('Out of Stock') }}</option>
                    </select>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel"></i> {{ __('Filter') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Products Table -->
<div class="content-card">
    <div class="card-header">
        <h3 class="card-title">
            {{ __('Products List') }}
            <span class="badge badge-primary ms-2">{{ $products->total() }}</span>
        </h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 80px;">{{ __('Image') }}</th>
                        <th>{{ __('Product') }}</th>
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
                            <img 
                                src="{{ asset('storage/' . $product->primaryImage->image_path) }}" 
                                alt="{{ $product->name() }}"
                                style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;"
                            >
                            @else
                            <div style="width: 60px; height: 60px; background: var(--content-bg); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-image" style="font-size: 1.5rem; color: var(--text-muted);"></i>
                            </div>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight: 600;">{{ $product->name() }}</div>
                            @if($product->is_featured)
                            <span class="badge badge-warning" style="font-size: 0.75rem;">{{ __('Featured') }}</span>
                            @endif
                        </td>
                        <td><code>{{ $product->sku }}</code></td>
                        <td>{{ $product->category->name() ?? '—' }}</td>
                        <td>
                            <div style="font-weight: 600; color: var(--primary-color);">₹{{ number_format($product->price, 2) }}</div>
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
                            <span class="badge badge-danger">{{ __('Inactive') }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.products.show', $product->id) }}" 
                                   class="btn btn-sm btn-icon btn-info" 
                                   title="{{ __('View') }}">
                                    <i class="bi bi-eye"></i>
                                </a>
                                
                                @if(auth('admin')->user()->hasPermission('products.edit'))
                                <a href="{{ route('admin.products.edit', $product->id) }}" 
                                   class="btn btn-sm btn-icon btn-primary"
                                   title="{{ __('Edit') }}">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif

                                @if(auth('admin')->user()->hasPermission('products.delete'))
                                <form action="{{ route('admin.products.destroy', $product->id) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('{{ __('Are you sure you want to delete this product?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-icon btn-danger"
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
                        <td colspan="8" class="text-center text-muted" style="padding: 40px;">
                            <i class="bi bi-inbox" style="font-size: 3rem; display: block; margin-bottom: 10px;"></i>
                            {{ __('No products found') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-label">{{ __('Total Products') }}</div>
                <div class="stat-icon">
                    <i class="bi bi-box-seam"></i>
                </div>
            </div>
            <div class="stat-value">{{ \App\Models\Product::count() }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card success">
            <div class="stat-header">
                <div class="stat-label">{{ __('In Stock') }}</div>
                <div class="stat-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
            <div class="stat-value">{{ \App\Models\Product::where('stock_quantity', '>', 0)->count() }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card warning">
            <div class="stat-header">
                <div class="stat-label">{{ __('Low Stock') }}</div>
                <div class="stat-icon">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
            </div>
            <div class="stat-value">{{ \App\Models\Product::whereBetween('stock_quantity', [1, 10])->count() }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card danger">
            <div class="stat-header">
                <div class="stat-label">{{ __('Out of Stock') }}</div>
                <div class="stat-icon">
                    <i class="bi bi-x-circle"></i>
                </div>
            </div>
            <div class="stat-value">{{ \App\Models\Product::where('stock_quantity', '<=', 0)->count() }}</div>
        </div>
    </div>
</div>
@endsection