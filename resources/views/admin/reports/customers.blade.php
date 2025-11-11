@extends('admin.layouts.app')

@section('title', __('Customers Report'))

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">{{ __('Customers Report') }}</h1>
    <p class="page-subtitle">{{ __('Analyze customer behavior, growth, and lifetime value') }}</p>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Reports') }}</li>
            <li class="breadcrumb-item active">{{ __('Customers') }}</li>
        </ol>
    </nav>
</div>

<!-- Date Range Filter -->
<div class="content-card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.reports.customers') }}" method="GET" class="row g-3">
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
                <a href="{{ route('admin.reports.customers') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-clockwise"></i> {{ __('Reset') }}
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Customer Statistics -->
<div class="stats-row">
    <!-- Total Customers -->
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-label">{{ __('Total Customers') }}</div>
            <div class="stat-icon">
                <i class="bi bi-people"></i>
            </div>
        </div>
        <div class="stat-value">{{ number_format($stats['total_customers'] ?? 0) }}</div>
        <div class="stat-footer">
            <span class="text-muted">{{ __('All registered customers') }}</span>
        </div>
    </div>
    
    <!-- New Customers -->
    <div class="stat-card success">
        <div class="stat-header">
            <div class="stat-label">{{ __('New Customers') }}</div>
            <div class="stat-icon">
                <i class="bi bi-person-plus"></i>
            </div>
        </div>
        <div class="stat-value">{{ number_format($stats['new_customers'] ?? 0) }}</div>
        <div class="stat-footer">
            <span class="text-muted">{{ __('In selected period') }}</span>
        </div>
    </div>
    
    <!-- Customers with Orders -->
    <div class="stat-card info">
        <div class="stat-header">
            <div class="stat-label">{{ __('Active Customers') }}</div>
            <div class="stat-icon">
                <i class="bi bi-cart-check"></i>
            </div>
        </div>
        <div class="stat-value">{{ number_format($stats['customers_with_orders'] ?? 0) }}</div>
        <div class="stat-footer">
            <span class="text-muted">
                {{ number_format((($stats['customers_with_orders'] ?? 0) / max($stats['total_customers'] ?? 1, 1)) * 100, 1) }}% 
                {{ __('have placed orders') }}
            </span>
        </div>
    </div>
    
    <!-- Average Customer Value -->
    <div class="stat-card warning">
        <div class="stat-header">
            <div class="stat-label">{{ __('Avg. Customer Value') }}</div>
            <div class="stat-icon">
                <i class="bi bi-currency-dollar"></i>
            </div>
        </div>
        <div class="stat-value">${{ number_format($stats['average_customer_value'] ?? 0, 2) }}</div>
        <div class="stat-footer">
            <span class="text-muted">{{ __('Lifetime value per customer') }}</span>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Customer Growth Chart -->
    <div class="col-lg-8 mb-4">
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Customer Growth Trend') }}</h3>
                <div class="card-actions">
                    <button class="btn btn-sm btn-outline" onclick="window.print()">
                        <i class="bi bi-printer"></i> {{ __('Print') }}
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if($customerGrowth->count() > 0)
                    <canvas id="customerGrowthChart" height="300"></canvas>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-graph-up" style="font-size: 3rem; opacity: 0.3;"></i>
                        <p class="mt-3">{{ __('No customer growth data for the selected period') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Quick Stats -->
    <div class="col-lg-4 mb-4">
        <div class="content-card mb-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('Quick Insights') }}</h3>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span style="font-size: 0.9rem; color: var(--text-muted);">{{ __('Conversion Rate') }}</span>
                        <span style="font-weight: 600; font-size: 1.1rem; color: var(--primary-color);">
                            {{ number_format((($stats['customers_with_orders'] ?? 0) / max($stats['total_customers'] ?? 1, 1)) * 100, 1) }}%
                        </span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar" role="progressbar" 
                            style="width: {{ (($stats['customers_with_orders'] ?? 0) / max($stats['total_customers'] ?? 1, 1)) * 100 }}%; 
                            background: var(--primary-color);">
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span style="font-size: 0.9rem; color: var(--text-muted);">{{ __('Growth Rate') }}</span>
                        <span style="font-weight: 600; font-size: 1.1rem; color: var(--success-color);">
                            {{ number_format((($stats['new_customers'] ?? 0) / max($stats['total_customers'] ?? 1, 1)) * 100, 1) }}%
                        </span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                            style="width: {{ (($stats['new_customers'] ?? 0) / max($stats['total_customers'] ?? 1, 1)) * 100 }}%;">
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-info" style="margin-bottom: 0;">
                    <i class="bi bi-info-circle me-2"></i>
                    <small>
                        {{ __('Focus on customer retention to increase lifetime value and repeat purchases.') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Customers -->
<div class="row">
    <!-- Top Customers by Orders -->
    <div class="col-lg-6 mb-4">
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-trophy text-warning me-2"></i>
                    {{ __('Top Customers by Orders') }}
                </h3>
            </div>
            <div class="card-body">
                @forelse($topCustomersByOrders as $index => $customer)
                <div class="d-flex align-items-center mb-3 pb-3" 
                    style="border-bottom: 1px solid var(--border-color);">
                    <div style="width: 35px; height: 35px; border-radius: 50%; 
                        background: linear-gradient(135deg, var(--warning-color), #e67e22); 
                        display: flex; align-items: center; justify-content: center; 
                        color: white; font-weight: 700; font-size: 1rem; margin-right: 15px;">
                        {{ $index + 1 }}
                    </div>
                    <div class="flex-grow-1">
                        <div style="font-weight: 600;">{{ $customer->name }}</div>
                        <div style="font-size: 0.85rem; color: var(--text-muted);">
                            {{ $customer->email }}
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-weight: 700; color: var(--warning-color); font-size: 1.1rem;">
                            {{ $customer->orders_count ?? 0 }}
                        </div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">
                            {{ __('orders') }}
                        </div>
                    </div>
                    <div class="ms-3">
                        @if(auth('admin')->user()->hasPermission('customers.view'))
                        <a href="{{ route('admin.customers.show', $customer->id) }}" 
                            class="btn btn-sm btn-icon btn-primary" title="{{ __('View Customer') }}">
                            <i class="bi bi-eye"></i>
                        </a>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="bi bi-inbox" style="font-size: 2.5rem; opacity: 0.3;"></i>
                    <p class="mt-2">{{ __('No customer data available') }}</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <!-- Top Customers by Spending -->
    <div class="col-lg-6 mb-4">
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-gem text-success me-2"></i>
                    {{ __('Top Customers by Spending') }}
                </h3>
            </div>
            <div class="card-body">
                @forelse($topCustomersBySpending as $index => $customer)
                <div class="d-flex align-items-center mb-3 pb-3" 
                    style="border-bottom: 1px solid var(--border-color);">
                    <div style="width: 35px; height: 35px; border-radius: 50%; 
                        background: linear-gradient(135deg, var(--success-color), #229954); 
                        display: flex; align-items: center; justify-content: center; 
                        color: white; font-weight: 700; font-size: 1rem; margin-right: 15px;">
                        {{ $index + 1 }}
                    </div>
                    <div class="flex-grow-1">
                        <div style="font-weight: 600;">{{ $customer->name }}</div>
                        <div style="font-size: 0.85rem; color: var(--text-muted);">
                            {{ $customer->email }}
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-weight: 700; color: var(--success-color); font-size: 1.1rem;">
                            ${{ number_format($customer->orders_sum_total_amount ?? 0, 0) }}
                        </div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">
                            {{ __('total spent') }}
                        </div>
                    </div>
                    <div class="ms-3">
                        @if(auth('admin')->user()->hasPermission('customers.view'))
                        <a href="{{ route('admin.customers.show', $customer->id) }}" 
                            class="btn btn-sm btn-icon btn-primary" title="{{ __('View Customer') }}">
                            <i class="bi bi-eye"></i>
                        </a>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="bi bi-inbox" style="font-size: 2.5rem; opacity: 0.3;"></i>
                    <p class="mt-2">{{ __('No customer data available') }}</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Recent Customers -->
<div class="content-card">
    <div class="card-header">
        <h3 class="card-title">{{ __('Recent Customers') }}</h3>
        <div class="card-actions">
            @if(auth('admin')->user()->hasPermission('customers.view'))
            <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-primary">
                {{ __('View All Customers') }}
            </a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>{{ __('Customer') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Phone') }}</th>
                        <th class="text-center">{{ __('Orders') }}</th>
                        <th class="text-end">{{ __('Total Spent') }}</th>
                        <th>{{ __('Joined Date') }}</th>
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentCustomers as $customer)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div style="width: 40px; height: 40px; border-radius: 50%; 
                                    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); 
                                    display: flex; align-items: center; justify-content: center; 
                                    color: white; font-weight: 600; margin-right: 12px;">
                                    {{ strtoupper(substr($customer->name, 0, 2)) }}
                                </div>
                                <div style="font-weight: 600;">{{ $customer->name }}</div>
                            </div>
                        </td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->phone ?? '—' }}</td>
                        <td class="text-center">
                            <span class="badge badge-info">{{ $customer->orders_count ?? 0 }}</span>
                        </td>
                        <td class="text-end">
                            <strong style="color: var(--success-color);">
                                ${{ number_format($customer->orders ? $customer->orders->sum('total_amount') : 0, 2) }}
                            </strong>
                        </td>
                        <td>
                            <span style="font-size: 0.9rem;">{{ $customer->created_at->format('M d, Y') }}</span>
                            <br>
                            <small class="text-muted">{{ $customer->created_at->diffForHumans() }}</small>
                        </td>
                        <td class="text-center">
                            @if(auth('admin')->user()->hasPermission('customers.view'))
                            <a href="{{ route('admin.customers.show', $customer->id) }}" 
                                class="btn btn-sm btn-icon btn-primary" title="{{ __('View Details') }}">
                                <i class="bi bi-eye"></i>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted" style="padding: 40px;">
                            <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                            <div class="mt-2">{{ __('No customers found') }}</div>
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
    // Wrap in DOMContentLoaded to ensure DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        
        @if($customerGrowth->count() > 0)
        // Customer Growth Chart - FIXED with instance management
        (function() {
            const canvas = document.getElementById('customerGrowthChart');
            if (!canvas) return;
            
            // Destroy existing instance if exists
            if (window.customerGrowthChartInstance) {
                window.customerGrowthChartInstance.destroy();
            }
            
            const growthCtx = canvas.getContext('2d');
            const growthData = {
                labels: [
                    @foreach($customerGrowth as $day)
                        '{{ date("M d", strtotime($day->date)) }}',
                    @endforeach
                ],
                datasets: [{
                    label: '{{ __("New Customers") }}',
                    data: [
                        @foreach($customerGrowth as $day)
                            {{ $day->count ?? 0 }},
                        @endforeach
                    ],
                    borderColor: 'rgba(32, 178, 170, 1)',
                    backgroundColor: 'rgba(32, 178, 170, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'rgba(32, 178, 170, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            };
            
            window.customerGrowthChartInstance = new Chart(growthCtx, {
                type: 'line',
                data: growthData,
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
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: {
                                size: 14
                            },
                            bodyFont: {
                                size: 13
                            },
                            callbacks: {
                                label: function(context) {
                                    let label = context.parsed.y + ' new customer';
                                    if (context.parsed.y !== 1) {
                                        label += 's';
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            },
                            title: {
                                display: true,
                                text: '{{ __("Number of New Customers") }}'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: '{{ __("Date") }}'
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