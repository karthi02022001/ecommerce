@extends('admin.layouts.app')

@section('title', __('Sales Report'))

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">{{ __('Sales Report') }}</h1>
    <p class="page-subtitle">{{ __('View detailed sales analytics and performance metrics') }}</p>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Reports') }}</li>
            <li class="breadcrumb-item active">{{ __('Sales') }}</li>
        </ol>
    </nav>
</div>

<!-- Date Range Filter -->
<div class="content-card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.reports.sales') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="date_from" class="form-label">{{ __('From Date') }}</label>
                <input type="date" name="date_from" id="date_from" class="form-control" 
                    value="{{ $dateFrom }}" max="{{ now()->format('Y-m-d') }}">
            </div>
            <div class="col-md-4">
                <label for="date_to" class="form-label">{{ __('To Date') }}</label>
                <input type="date" name="date_to" id="date_to" class="form-control" 
                    value="{{ $dateTo }}" max="{{ now()->format('Y-m-d') }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-funnel"></i> {{ __('Apply Filter') }}
                </button>
                <a href="{{ route('admin.reports.sales') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-clockwise"></i> {{ __('Reset') }}
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Sales Statistics -->
<div class="stats-row">
    <!-- Total Sales -->
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-label">{{ __('Total Sales') }}</div>
            <div class="stat-icon">
                <i class="bi bi-currency-dollar"></i>
            </div>
        </div>
        <div class="stat-value">₹{{ number_format($stats['total_sales'] ?? 0, 2) }}</div>
        <div class="stat-footer">
            <span class="text-muted">{{ __('Revenue from completed orders') }}</span>
        </div>
    </div>
    
    <!-- Total Orders -->
    <div class="stat-card success">
        <div class="stat-header">
            <div class="stat-label">{{ __('Total Orders') }}</div>
            <div class="stat-icon">
                <i class="bi bi-cart-check"></i>
            </div>
        </div>
        <div class="stat-value">{{ number_format($stats['total_orders'] ?? 0) }}</div>
        <div class="stat-footer">
            <span class="text-muted">{{ __('Orders in date range') }}</span>
        </div>
    </div>
    
    <!-- Average Order Value -->
    <div class="stat-card warning">
        <div class="stat-header">
            <div class="stat-label">{{ __('Average Order') }}</div>
            <div class="stat-icon">
                <i class="bi bi-graph-up"></i>
            </div>
        </div>
        <div class="stat-value">₹{{ number_format($stats['average_order'] ?? 0, 2) }}</div>
        <div class="stat-footer">
            <span class="text-muted">{{ __('Average order value') }}</span>
        </div>
    </div>
    
    <!-- Total Tax Collected -->
    <div class="stat-card info">
        <div class="stat-header">
            <div class="stat-label">{{ __('Total Tax') }}</div>
            <div class="stat-icon">
                <i class="bi bi-receipt"></i>
            </div>
        </div>
        <div class="stat-value">₹{{ number_format($stats['total_tax'] ?? 0, 2) }}</div>
        <div class="stat-footer">
            <span class="text-muted">{{ __('Tax collected') }}</span>
        </div>
    </div>
</div>

<!-- Additional Stats Row -->
<div class="stats-row">
    <!-- Total Shipping -->
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-label">{{ __('Shipping Revenue') }}</div>
            <div class="stat-icon">
                <i class="bi bi-truck"></i>
            </div>
        </div>
        <div class="stat-value">₹{{ number_format($stats['total_shipping'] ?? 0, 2) }}</div>
        <div class="stat-footer">
            <span class="text-muted">{{ __('From shipping fees') }}</span>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Daily Sales Chart -->
    <div class="col-lg-8 mb-4">
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Daily Sales Trend') }}</h3>
                <div class="card-actions">
                    <button class="btn btn-sm btn-outline" onclick="window.print()">
                        <i class="bi bi-printer"></i> {{ __('Print') }}
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if($dailySales->count() > 0)
                    <canvas id="dailySalesChart" height="300"></canvas>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-graph-up" style="font-size: 3rem; opacity: 0.3;"></i>
                        <p class="mt-3">{{ __('No sales data available for the selected period') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Sales by Status -->
    <div class="col-lg-4 mb-4">
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Sales by Status') }}</h3>
            </div>
            <div class="card-body">
                @if($salesByStatus->count() > 0)
                    <canvas id="statusChart" height="300"></canvas>
                    
                    <div class="mt-4">
                        @foreach($salesByStatus as $status)
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3" 
                            style="border-bottom: 1px solid var(--border-color);">
                            <div>
                                <div style="font-weight: 600; text-transform: capitalize;">
                                    {{ __($status->status) }}
                                </div>
                                <div style="font-size: 0.85rem; color: var(--text-muted);">
                                    {{ $status->count }} {{ __('orders') }}
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-weight: 600; color: var(--primary-color);">
                                    ₹{{ number_format($status->total ?? 0, 2) }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-pie-chart" style="font-size: 2.5rem; opacity: 0.3;"></i>
                        <p class="mt-2">{{ __('No order data available') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Top Selling Products -->
<div class="content-card">
    <div class="card-header">
        <h3 class="card-title">{{ __('Top Selling Products') }}</h3>
        <div class="card-actions">
            <span class="text-muted" style="font-size: 0.9rem;">
                {{ __('Based on revenue in selected period') }}
            </span>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">{{ __('Rank') }}</th>
                        <th>{{ __('Product') }}</th>
                        <th>{{ __('Category') }}</th>
                        <th class="text-end">{{ __('Units Sold') }}</th>
                        <th class="text-end">{{ __('Revenue') }}</th>
                        <th class="text-end">{{ __('Avg. Price') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topProducts as $index => $product)
                    <tr>
                        <td>
                            <div style="width: 40px; height: 40px; border-radius: 50%; 
                                background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); 
                                display: flex; align-items: center; justify-content: center; 
                                color: white; font-weight: 700; font-size: 1.1rem;">
                                {{ $index + 1 }}
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div style="width: 50px; height: 50px; border-radius: 8px; 
                                    overflow: hidden; margin-right: 15px; background: var(--content-bg);">
                                    @if($product->primaryImage)
                                        <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" 
                                            style="width: 100%; height: 100%; object-fit: cover;" 
                                            alt="{{ $product->name() }}">
                                    @else
                                        <div style="width: 100%; height: 100%; display: flex; 
                                            align-items: center; justify-content: center;">
                                            <i class="bi bi-image" style="font-size: 1.5rem; color: var(--text-muted);"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div style="font-weight: 600;">{{ $product->name() }}</div>
                                    <div style="font-size: 0.85rem; color: var(--text-muted);">
                                        SKU: {{ $product->sku }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-secondary">
                                {{ $product->category ? $product->category->name() : __('N/A') }}
                            </span>
                        </td>
                        <td class="text-end">
                            <strong>{{ number_format($product->orderItems->sum('quantity') ?? 0) }}</strong>
                        </td>
                        <td class="text-end">
                            <strong style="color: var(--success-color);">
                                ₹{{ number_format($product->total_revenue ?? 0, 2) }}
                            </strong>
                        </td>
                        <td class="text-end">
                            <span class="text-muted">
                                ₹{{ number_format($product->orderItems->avg('price') ?? 0, 2) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted" style="padding: 40px;">
                            <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                            <div class="mt-2">{{ __('No sales data available for the selected period') }}</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Wrap everything in document ready to ensure DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        
        @if($dailySales->count() > 0)
        // Daily Sales Chart - FIXED with instance management
        (function() {
            const canvas = document.getElementById('dailySalesChart');
            if (!canvas) return;
            
            // Destroy existing instance if exists
            if (window.dailySalesChartInstance) {
                window.dailySalesChartInstance.destroy();
            }
            
            const dailySalesCtx = canvas.getContext('2d');
            const dailySalesData = {
                labels: [
                    @foreach($dailySales as $day)
                        '{{ date("M d", strtotime($day->date)) }}',
                    @endforeach
                ],
                datasets: [
                    {
                        label: '{{ __("Orders") }}',
                        data: [
                            @foreach($dailySales as $day)
                                {{ $day->orders ?? 0 }},
                            @endforeach
                        ],
                        borderColor: 'rgba(32, 178, 170, 1)',
                        backgroundColor: 'rgba(32, 178, 170, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y'
                    },
                    {
                        label: '{{ __("Revenue (₹)") }}',
                        data: [
                            @foreach($dailySales as $day)
                                {{ $day->revenue ?? 0 }},
                            @endforeach
                        ],
                        borderColor: 'rgba(243, 156, 18, 1)',
                        backgroundColor: 'rgba(243, 156, 18, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y1'
                    }
                ]
            };
            
            window.dailySalesChartInstance = new Chart(dailySalesCtx, {
                type: 'line',
                data: dailySalesData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 1000
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        if (context.datasetIndex === 1) {
                                            label += '₹' + context.parsed.y.toFixed(2);
                                        } else {
                                            label += context.parsed.y;
                                        }
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: '{{ __("Number of Orders") }}'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: '{{ __("Revenue (₹)") }}'
                            },
                            grid: {
                                drawOnChartArea: false,
                            }
                        }
                    }
                }
            });
        })();
        @endif
        
        @if($salesByStatus->count() > 0)
        // Status Chart (Doughnut) - FIXED with instance management and IIFE
        (function() {
            const canvas = document.getElementById('statusChart');
            if (!canvas) return;
            
            // Destroy existing instance if exists
            if (window.statusChartInstance) {
                window.statusChartInstance.destroy();
            }
            
            const statusCtx = canvas.getContext('2d');
            const statusData = {
                labels: [
                    @foreach($salesByStatus as $status)
                        '{{ __(ucfirst($status->status)) }}',
                    @endforeach
                ],
                datasets: [{
                    data: [
                        @foreach($salesByStatus as $status)
                            {{ $status->count ?? 0 }},
                        @endforeach
                    ],
                    backgroundColor: [
                        'rgba(243, 156, 18, 0.8)',  // pending
                        'rgba(52, 152, 219, 0.8)',  // processing
                        'rgba(155, 89, 182, 0.8)',  // shipped
                        'rgba(39, 174, 96, 0.8)',   // completed
                        'rgba(231, 76, 60, 0.8)'    // cancelled
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            };
            
            window.statusChartInstance = new Chart(statusCtx, {
                type: 'doughnut',
                data: statusData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        animateRotate: true,
                        animateScale: false,
                        duration: 1000
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        tooltip: {
                            enabled: true,
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += context.parsed + ' orders';
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        })();
        @endif
        
    }); // End DOMContentLoaded
</script>
@endpush