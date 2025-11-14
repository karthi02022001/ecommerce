@extends('admin.layouts.app')

@section('title', __('Products Report'))

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">{{ __('Products Report') }}</h1>
    <p class="page-subtitle">{{ __('Comprehensive overview of product inventory and performance') }}</p>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.reports.sales') }}">{{ __('Reports') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Products Report') }}</li>
        </ol>
    </nav>
</div>

<!-- Statistics Cards -->
<div class="stats-row">
    <!-- Total Products -->
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-label">{{ __('Total Products') }}</div>
            <div class="stat-icon">
                <i class="bi bi-box-seam"></i>
            </div>
        </div>
        <div class="stat-value">{{ number_format($stats['total_products'] ?? 0) }}</div>
        <div class="stat-footer">
            <span class="text-muted">{{ __('Active') }}: {{ number_format($stats['active_products'] ?? 0) }}</span>
        </div>
    </div>
    
    <!-- Featured Products -->
    <div class="stat-card success">
        <div class="stat-header">
            <div class="stat-label">{{ __('Featured Products') }}</div>
            <div class="stat-icon">
                <i class="bi bi-star"></i>
            </div>
        </div>
        <div class="stat-value">{{ number_format($stats['featured_products'] ?? 0) }}</div>
        <div class="stat-footer">
            <span class="text-muted">
                {{ number_format((($stats['featured_products'] ?? 0) / max($stats['total_products'] ?? 1, 1)) * 100, 1) }}% {{ __('of total') }}
            </span>
        </div>
    </div>
    
    <!-- Low Stock -->
    <div class="stat-card warning">
        <div class="stat-header">
            <div class="stat-label">{{ __('Low Stock') }}</div>
            <div class="stat-icon">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
        </div>
        <div class="stat-value">{{ number_format($stats['low_stock'] ?? 0) }}</div>
        <div class="stat-footer">
            <span class="text-muted">{{ __('Needs attention') }}</span>
        </div>
    </div>
    
    <!-- Out of Stock -->
    <div class="stat-card danger">
        <div class="stat-header">
            <div class="stat-label">{{ __('Out of Stock') }}</div>
            <div class="stat-icon">
                <i class="bi bi-x-circle"></i>
            </div>
        </div>
        <div class="stat-value">{{ number_format($stats['out_of_stock'] ?? 0) }}</div>
        <div class="stat-footer">
            <span class="text-muted">{{ __('Immediate action required') }}</span>
        </div>
    </div>
    
    <!-- Inventory Value -->
    <div class="stat-card info">
        <div class="stat-header">
            <div class="stat-label">{{ __('Total Inventory Value') }}</div>
            <div class="stat-icon">
                <i class="bi bi-currency-dollar"></i>
            </div>
        </div>
        <div class="stat-value">${{ number_format($stats['total_inventory_value'] ?? 0, 2) }}</div>
        <div class="stat-footer">
            <span class="text-muted">{{ __('Current stock value') }}</span>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Products by Category -->
    <div class="col-lg-6">
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Products by Category') }}</h3>
            </div>
            <div class="card-body">
                @if(isset($productsByCategory) && count($productsByCategory) > 0)
                    <canvas id="categoryChart" height="300"></canvas>
                    
                    <div class="mt-4">
                        @foreach($productsByCategory as $category)
                        @php
                            $totalProducts = $stats['total_products'] ?? 1;
                            $percentage = $totalProducts > 0 ? ($category->count / $totalProducts) * 100 : 0;
                        @endphp
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3" 
                            style="border-bottom: 1px solid var(--border-color);">
                            <div>
                                <div style="font-weight: 600;">{{ $category->name }}</div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-weight: 600; color: var(--primary-color);">
                                    {{ $category->count }} {{ __('products') }}
                                </div>
                                <div style="font-size: 0.85rem; color: var(--text-muted);">
                                    {{ number_format($percentage, 1) }}%
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-pie-chart" style="font-size: 3rem; opacity: 0.3;"></i>
                        <p class="mt-3">{{ __('No category data available') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Top Performing Products -->
    <div class="col-lg-6">
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Top Performing Products') }}</h3>
                <div class="card-actions">
                    @if(auth('admin')->user()->hasPermission('products.view'))
                    <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-primary">
                        {{ __('View All Products') }}
                    </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                @if(isset($topPerformers) && $topPerformers->isNotEmpty())
                    @foreach($topPerformers as $product)
                    @if($product)
                    <div class="d-flex align-items-center mb-3 pb-3" 
                        style="border-bottom: 1px solid var(--border-color);">
                        <div style="width: 50px; height: 50px; border-radius: 8px; overflow: hidden; margin-right: 15px; background: var(--content-bg);">
                            @if($product->primaryImage)
                                <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" 
                                    style="width: 100%; height: 100%; object-fit: cover;"
                                    alt="{{ $product->name() }}">
                            @else
                                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-image" style="font-size: 1.5rem; color: var(--text-muted);"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <div style="font-weight: 600; margin-bottom: 3px;">{{ $product->name() }}</div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);">
                                {{ __('Sold') }}: {{ number_format($product->total_quantity ?? 0) }} {{ __('units') }}
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-weight: 600; color: var(--success-color);">
                                ${{ number_format($product->total_revenue ?? 0, 2) }}
                            </div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);">
                                {{ __('Revenue') }}
                            </div>
                        </div>
                    </div>
                    @endif
                    @endforeach
                @else
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-graph-up" style="font-size: 3rem; opacity: 0.3;"></i>
                        <p class="mt-3">{{ __('No performance data available') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Low Stock Products -->
@if(isset($lowStockProducts) && $lowStockProducts->isNotEmpty())
<div class="content-card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="bi bi-exclamation-triangle text-warning me-2"></i>
            {{ __('Low Stock Products') }}
        </h3>
        <div class="card-actions">
            @if(auth('admin')->user()->hasPermission('products.edit'))
            <span class="badge badge-warning">{{ $lowStockProducts->count() }} {{ __('products need attention') }}</span>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>{{ __('Product') }}</th>
                        <th>{{ __('Category') }}</th>
                        <th>{{ __('SKU') }}</th>
                        <th>{{ __('Price') }}</th>
                        <th>{{ __('Stock') }}</th>
                        <th>{{ __('Status') }}</th>
                        @if(auth('admin')->user()->hasPermission('products.edit'))
                        <th>{{ __('Actions') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($lowStockProducts as $product)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div style="width: 40px; height: 40px; border-radius: 6px; overflow: hidden; margin-right: 10px; background: var(--content-bg);">
                                    @if($product->primaryImage)
                                        <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" 
                                            style="width: 100%; height: 100%; object-fit: cover;"
                                            alt="{{ $product->name() }}">
                                    @else
                                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-image" style="color: var(--text-muted);"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div style="font-weight: 600;">{{ $product->name() }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $product->category->name() }}</td>
                        <td><code>{{ $product->sku }}</code></td>
                        <td>${{ number_format($product->price, 2) }}</td>
                        <td>
                            <span class="badge badge-warning">
                                {{ $product->stock_quantity }} {{ __('left') }}
                            </span>
                        </td>
                        <td>
                            @if($product->is_active)
                                <span class="badge badge-success">{{ __('Active') }}</span>
                            @else
                                <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                            @endif
                        </td>
                        @if(auth('admin')->user()->hasPermission('products.edit'))
                        <td>
                            <a href="{{ route('admin.products.edit', $product->id) }}" 
                                class="btn btn-sm btn-icon btn-warning"
                                title="{{ __('Update Stock') }}">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Out of Stock Products -->
@if(isset($outOfStockProducts) && $outOfStockProducts->isNotEmpty())
<div class="content-card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="bi bi-x-circle text-danger me-2"></i>
            {{ __('Out of Stock Products') }}
        </h3>
        <div class="card-actions">
            <span class="badge badge-danger">{{ $outOfStockProducts->count() }} {{ __('products unavailable') }}</span>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>{{ __('Product') }}</th>
                        <th>{{ __('Category') }}</th>
                        <th>{{ __('SKU') }}</th>
                        <th>{{ __('Price') }}</th>
                        <th>{{ __('Status') }}</th>
                        @if(auth('admin')->user()->hasPermission('products.edit'))
                        <th>{{ __('Actions') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($outOfStockProducts as $product)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div style="width: 40px; height: 40px; border-radius: 6px; overflow: hidden; margin-right: 10px; background: var(--content-bg);">
                                    @if($product->primaryImage)
                                        <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" 
                                            style="width: 100%; height: 100%; object-fit: cover;"
                                            alt="{{ $product->name() }}">
                                    @else
                                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-image" style="color: var(--text-muted);"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div style="font-weight: 600;">{{ $product->name() }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $product->category->name() }}</td>
                        <td><code>{{ $product->sku }}</code></td>
                        <td>${{ number_format($product->price, 2) }}</td>
                        <td>
                            <span class="badge badge-danger">{{ __('Out of Stock') }}</span>
                        </td>
                        @if(auth('admin')->user()->hasPermission('products.edit'))
                        <td>
                            <a href="{{ route('admin.products.edit', $product->id) }}" 
                                class="btn btn-sm btn-icon btn-danger"
                                title="{{ __('Restock') }}">
                                <i class="bi bi-box-seam"></i>
                            </a>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Products by Category Chart
    @if(isset($productsByCategory) && count($productsByCategory) > 0)
    const categoryCtx = document.getElementById('categoryChart');
    
    if (categoryCtx) {
        // Convert PHP Collection to JavaScript array
        const categoryData = @json($productsByCategory->values());
        
        if (categoryData && categoryData.length > 0) {
            // Extract labels and data
            const labels = categoryData.map(item => item.name || 'Unknown');
            const data = categoryData.map(item => parseInt(item.count) || 0);
            
            // Create the chart
            new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '{{ __("Products") }}',
                        data: data,
                        backgroundColor: [
                            'rgba(32, 178, 170, 0.8)',
                            'rgba(52, 152, 219, 0.8)',
                            'rgba(243, 156, 18, 0.8)',
                            'rgba(231, 76, 60, 0.8)',
                            'rgba(155, 89, 182, 0.8)',
                            'rgba(39, 174, 96, 0.8)',
                            'rgba(241, 196, 15, 0.8)',
                            'rgba(230, 126, 34, 0.8)'
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: {
                                    family: 'Poppins',
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return `${label}: ${value} products (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
    }
    @endif
});
</script>
@endpush