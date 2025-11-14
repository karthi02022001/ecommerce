@extends('admin.layouts.app')

@section('title', __('Dashboard'))

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">{{ __('Dashboard') }}</h1>
    <p class="page-subtitle">{{ __('Welcome back, :name!', ['name' => auth('admin')->user()->name]) }}</p>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">{{ __('Dashboard') }}</li>
        </ol>
    </nav>
</div>

<!-- Statistics Cards -->
<div class="stats-row">
    <!-- Total Revenue -->
    @if(auth('admin')->user()->hasAnyPermission(['dashboard.analytics', 'reports.sales']))
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-label">{{ __('Total Revenue') }}</div>
            <div class="stat-icon">
                <i class="bi bi-currency-dollar"></i>
            </div>
        </div>
        <div class="stat-value">${{ number_format($stats['total_revenue'], 2) }}</div>
        <div class="stat-footer">
            <span class="stat-change positive">
                <i class="bi bi-arrow-up"></i> {{ __('This Month') }}: ${{ number_format($stats['monthly_revenue'], 2) }}
            </span>
        </div>
    </div>
    @endif
    
    <!-- Total Orders -->
    @if(auth('admin')->user()->hasPermission('orders.view'))
    <div class="stat-card success">
        <div class="stat-header">
            <div class="stat-label">{{ __('Total Orders') }}</div>
            <div class="stat-icon">
                <i class="bi bi-cart-check"></i>
            </div>
        </div>
        <div class="stat-value">{{ number_format($stats['total_orders']) }}</div>
        <div class="stat-footer">
            <span class="text-muted">{{ __('Pending') }}: {{ $stats['pending_orders'] }}</span>
        </div>
    </div>
    @endif
    
    <!-- Total Products -->
    @if(auth('admin')->user()->hasPermission('products.view'))
    <div class="stat-card warning">
        <div class="stat-header">
            <div class="stat-label">{{ __('Total Products') }}</div>
            <div class="stat-icon">
                <i class="bi bi-box-seam"></i>
            </div>
        </div>
        <div class="stat-value">{{ number_format($stats['total_products']) }}</div>
        <div class="stat-footer">
            <span class="text-muted">{{ __('Low Stock') }}: {{ $stats['low_stock_count'] }}</span>
        </div>
    </div>
    @endif
    
    <!-- Total Customers -->
    @if(auth('admin')->user()->hasPermission('customers.view'))
    <div class="stat-card info">
        <div class="stat-header">
            <div class="stat-label">{{ __('Total Customers') }}</div>
            <div class="stat-icon">
                <i class="bi bi-people"></i>
            </div>
        </div>
        <div class="stat-value">{{ number_format($stats['total_customers']) }}</div>
        <div class="stat-footer">
            <span class="text-muted">{{ __('New This Month') }}: {{ $stats['new_customers'] }}</span>
        </div>
    </div>
    @endif
</div>

<!-- Content Row -->
<div class="row">
    <!-- Recent Orders -->
    @if(auth('admin')->user()->hasPermission('orders.view'))
    <div class="col-lg-8">
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Recent Orders') }}</h3>
                <div class="card-actions">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-primary">
                        {{ __('View All') }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>{{ __('Order ID') }}</th>
                                <th>{{ __('Customer') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr>
                                <td><strong>#{{ $order->id }}</strong></td>
                                <td>{{ $order->customer->name ?? 'Guest' }}</td>
                                <td>${{ number_format($order->total_amount, 2) }}</td>
                                <td>
                                    @if($order->status === 'pending')
                                        <span class="badge badge-warning">{{ __('Pending') }}</span>
                                    @elseif($order->status === 'processing')
                                        <span class="badge badge-info">{{ __('Processing') }}</span>
                                    @elseif($order->status === 'shipped')
                                        <span class="badge badge-primary">{{ __('Shipped') }}</span>
                                    @elseif($order->status === 'delivered')
                                        <span class="badge badge-success">{{ __('Delivered') }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ __('Cancelled') }}</span>
                                    @endif
                                </td>
                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-icon btn-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">{{ __('No orders found') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Top Products & Alerts -->
    <div class="col-lg-4">
        <!-- Top Products -->
        @if(auth('admin')->user()->hasPermission('products.view'))
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Top Selling Products') }}</h3>
            </div>
            <div class="card-body">
                @forelse($topProducts as $product)
                <div class="d-flex align-items-center mb-3 pb-3" style="border-bottom: 1px solid var(--border-color);">
                    <div style="width: 50px; height: 50px; border-radius: 8px; overflow: hidden; margin-right: 15px; background: var(--content-bg);">
                        @if($product->primaryImage)
                            <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-image" style="font-size: 1.5rem; color: var(--text-muted);"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <div style="font-weight: 600; margin-bottom: 3px;">{{ $product->name() }}</div>
                        <div style="font-size: 0.85rem; color: var(--text-muted);">
                            {{ __('Sales') }}: {{ $product->order_items_count }}
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-weight: 600; color: var(--primary-color);">
                            ${{ number_format($product->price, 2) }}
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-center text-muted">{{ __('No data available') }}</p>
                @endforelse
            </div>
        </div>
        @endif
        
        <!-- Low Stock Alert -->
        @if(auth('admin')->user()->hasPermission('products.edit') && $lowStockProducts->count() > 0)
        <div class="content-card mt-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('Low Stock Alert') }}</h3>
            </div>
            <div class="card-body">
                @foreach($lowStockProducts as $product)
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div>
                        <div style="font-weight: 500;">{{ $product->name() }}</div>
                        <div style="font-size: 0.85rem; color: var(--text-muted);">
                            {{ __('Stock') }}: <span style="color: var(--danger-color); font-weight: 600;">{{ $product->stock_quantity }}</span>
                        </div>
                    </div>
                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-warning">
                        <i class="bi bi-pencil"></i>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection