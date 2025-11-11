@extends('admin.layouts.app')

@section('title', __('Orders'))

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">{{ __('Orders Management') }}</h1>
    <p class="page-subtitle">{{ __('View and manage all customer orders') }}</p>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Orders') }}</li>
        </ol>
    </nav>
</div>

<!-- Statistics Cards -->
<div class="stats-row">
    <!-- Total Orders -->
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-label">{{ __('Total Orders') }}</div>
            <div class="stat-icon">
                <i class="bi bi-cart-check"></i>
            </div>
        </div>
        <div class="stat-value">{{ number_format($stats['total']) }}</div>
        <div class="stat-footer">
            <span class="text-muted">{{ __('All Time') }}</span>
        </div>
    </div>
    
    <!-- Pending Orders -->
    <div class="stat-card warning">
        <div class="stat-header">
            <div class="stat-label">{{ __('Pending') }}</div>
            <div class="stat-icon">
                <i class="bi bi-clock-history"></i>
            </div>
        </div>
        <div class="stat-value">{{ number_format($stats['pending']) }}</div>
        <div class="stat-footer">
            <span class="text-muted">{{ __('Awaiting Processing') }}</span>
        </div>
    </div>
    
    <!-- Processing Orders -->
    <div class="stat-card info">
        <div class="stat-header">
            <div class="stat-label">{{ __('Processing') }}</div>
            <div class="stat-icon">
                <i class="bi bi-arrow-repeat"></i>
            </div>
        </div>
        <div class="stat-value">{{ number_format($stats['processing']) }}</div>
        <div class="stat-footer">
            <span class="text-muted">{{ __('Being Prepared') }}</span>
        </div>
    </div>
    
    <!-- Completed Orders -->
    <div class="stat-card success">
        <div class="stat-header">
            <div class="stat-label">{{ __('Completed') }}</div>
            <div class="stat-icon">
                <i class="bi bi-check-circle"></i>
            </div>
        </div>
        <div class="stat-value">{{ number_format($stats['completed']) }}</div>
        <div class="stat-footer">
            <span class="text-muted">{{ __('Successfully Delivered') }}</span>
        </div>
    </div>
</div>

<!-- Filters & Search -->
<div class="content-card">
    <div class="card-body">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-3">
            <!-- Search -->
            <div class="col-md-3">
                <label for="search" class="form-label">{{ __('Search') }}</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input 
                        type="text" 
                        name="search" 
                        id="search" 
                        class="form-control" 
                        placeholder="{{ __('Order ID, Customer...') }}"
                        value="{{ request('search') }}"
                    >
                </div>
            </div>
            
            <!-- Status Filter -->
            <div class="col-md-2">
                <label for="status" class="form-label">{{ __('Status') }}</label>
                <select name="status" id="status" class="form-select">
                    <option value="">{{ __('All Status') }}</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>{{ __('Processing') }}</option>
                    <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>{{ __('Shipped') }}</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>{{ __('Delivered') }}</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                </select>
            </div>
            
            <!-- Payment Status Filter -->
            <div class="col-md-2">
                <label for="payment_status" class="form-label">{{ __('Payment') }}</label>
                <select name="payment_status" id="payment_status" class="form-select">
                    <option value="">{{ __('All Payment Status') }}</option>
                    <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                    <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>{{ __('Failed') }}</option>
                    <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>{{ __('Refunded') }}</option>
                </select>
            </div>
            
            <!-- Date From -->
            <div class="col-md-2">
                <label for="date_from" class="form-label">{{ __('From Date') }}</label>
                <input 
                    type="date" 
                    name="date_from" 
                    id="date_from" 
                    class="form-control"
                    value="{{ request('date_from') }}"
                >
            </div>
            
            <!-- Date To -->
            <div class="col-md-2">
                <label for="date_to" class="form-label">{{ __('To Date') }}</label>
                <input 
                    type="date" 
                    name="date_to" 
                    id="date_to" 
                    class="form-control"
                    value="{{ request('date_to') }}"
                >
            </div>
            
            <!-- Filter Buttons -->
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel"></i>
                    </button>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Orders Table -->
<div class="content-card">
    <div class="card-header">
        <h3 class="card-title">{{ __('Orders List') }}</h3>
        <div class="card-actions">
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-outline dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-sort-down"></i> {{ __('Sort') }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('admin.orders.index', array_merge(request()->all(), ['sort' => 'created_at', 'order' => 'desc'])) }}">{{ __('Newest First') }}</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.orders.index', array_merge(request()->all(), ['sort' => 'created_at', 'order' => 'asc'])) }}">{{ __('Oldest First') }}</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.orders.index', array_merge(request()->all(), ['sort' => 'total_amount', 'order' => 'desc'])) }}">{{ __('Highest Amount') }}</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.orders.index', array_merge(request()->all(), ['sort' => 'total_amount', 'order' => 'asc'])) }}">{{ __('Lowest Amount') }}</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if($orders->count() > 0)
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>{{ __('Order ID') }}</th>
                        <th>{{ __('Customer') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Items') }}</th>
                        <th>{{ __('Amount') }}</th>
                        <th>{{ __('Payment') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td>
                            <strong>#{{ $order->order_number ?? $order->id }}</strong>
                        </td>
                        <td>
                            @if($order->user)
                                <div style="font-weight: 500;">{{ $order->user->name }}</div>
                                <div style="font-size: 0.85rem; color: var(--text-muted);">{{ $order->user->email }}</div>
                            @else
                                <div style="font-weight: 500;">{{ $order->customer_name }}</div>
                                <div style="font-size: 0.85rem; color: var(--text-muted);">{{ $order->customer_email }}</div>
                            @endif
                        </td>
                        <td>
                            <div>{{ $order->created_at->format('M d, Y') }}</div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);">{{ $order->created_at->format('h:i A') }}</div>
                        </td>
                        <td>
                            <span class="badge badge-secondary">{{ $order->orderItems->count() }} {{ __('items') }}</span>
                        </td>
                        <td>
                            <strong style="color: var(--primary-color);">{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($order->total_amount, 2) }}</strong>
                            @if($order->discount_amount > 0)
                                <div style="font-size: 0.85rem; color: var(--success-color);">
                                    <i class="bi bi-tag"></i> -{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($order->discount_amount, 2) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($order->payment_status === 'paid')
                                <span class="badge badge-success">
                                    <i class="bi bi-check-circle"></i> {{ __('Paid') }}
                                </span>
                            @elseif($order->payment_status === 'pending')
                                <span class="badge badge-warning">
                                    <i class="bi bi-clock"></i> {{ __('Pending') }}
                                </span>
                            @elseif($order->payment_status === 'failed')
                                <span class="badge badge-danger">
                                    <i class="bi bi-x-circle"></i> {{ __('Failed') }}
                                </span>
                            @else
                                <span class="badge badge-info">
                                    <i class="bi bi-arrow-return-left"></i> {{ __('Refunded') }}
                                </span>
                            @endif
                            @if($order->payment_method)
                                <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 2px;">
                                    {{ ucfirst($order->payment_method) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($order->status === 'pending')
                                <span class="badge badge-warning">
                                    <i class="bi bi-clock-history"></i> {{ __('Pending') }}
                                </span>
                            @elseif($order->status === 'processing')
                                <span class="badge badge-info">
                                    <i class="bi bi-arrow-repeat"></i> {{ __('Processing') }}
                                </span>
                            @elseif($order->status === 'shipped')
                                <span class="badge badge-primary">
                                    <i class="bi bi-truck"></i> {{ __('Shipped') }}
                                </span>
                            @elseif($order->status === 'delivered')
                                <span class="badge badge-success">
                                    <i class="bi bi-check-circle-fill"></i> {{ __('Delivered') }}
                                </span>
                            @else
                                <span class="badge badge-danger">
                                    <i class="bi bi-x-circle-fill"></i> {{ __('Cancelled') }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <!-- View Order -->
                                @if(auth('admin')->user()->hasPermission('orders.view'))
                                <a 
                                    href="{{ route('admin.orders.show', $order->id) }}" 
                                    class="btn btn-sm btn-icon btn-primary"
                                    title="{{ __('View Details') }}"
                                >
                                    <i class="bi bi-eye"></i>
                                </a>
                                @endif
                                
                                <!-- Quick Status Update -->
                                @if(auth('admin')->user()->hasPermission('orders.edit'))
                                <div class="dropdown">
                                    <button 
                                        class="btn btn-sm btn-icon btn-info dropdown-toggle" 
                                        type="button" 
                                        data-bs-toggle="dropdown"
                                        title="{{ __('Update Status') }}"
                                    >
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="pending">
                                                <button type="submit" class="dropdown-item">
                                                    <i class="bi bi-clock-history text-warning"></i> {{ __('Pending') }}
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="processing">
                                                <button type="submit" class="dropdown-item">
                                                    <i class="bi bi-arrow-repeat text-info"></i> {{ __('Processing') }}
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="shipped">
                                                <button type="submit" class="dropdown-item">
                                                    <i class="bi bi-truck text-primary"></i> {{ __('Shipped') }}
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="delivered">
                                                <button type="submit" class="dropdown-item">
                                                    <i class="bi bi-check-circle-fill text-success"></i> {{ __('Delivered') }}
                                                </button>
                                            </form>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="dropdown-item">
                                                    <i class="bi bi-x-circle-fill text-danger"></i> {{ __('Cancelled') }}
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                                @endif
                                
                                <!-- Delete (Only for cancelled orders) -->
                                @if(auth('admin')->user()->hasPermission('orders.delete') && $order->status === 'cancelled')
                                <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this order?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-icon btn-danger" title="{{ __('Delete') }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
        @else
        <!-- Empty State -->
        <div class="text-center py-5">
            <div style="font-size: 4rem; color: var(--text-muted); margin-bottom: 1rem;">
                <i class="bi bi-cart-x"></i>
            </div>
            <h5 style="color: var(--text-muted); margin-bottom: 0.5rem;">{{ __('No Orders Found') }}</h5>
            <p style="color: var(--text-muted);">
                @if(request()->hasAny(['search', 'status', 'payment_status', 'date_from', 'date_to']))
                    {{ __('No orders match your filters. Try adjusting your search criteria.') }}
                @else
                    {{ __('No orders have been placed yet.') }}
                @endif
            </p>
            @if(request()->hasAny(['search', 'status', 'payment_status', 'date_from', 'date_to']))
            <a href="{{ route('admin.orders.index') }}" class="btn btn-primary mt-3">
                <i class="bi bi-arrow-clockwise me-2"></i>{{ __('Clear Filters') }}
            </a>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-submit status changes (optional enhancement)
    document.querySelectorAll('.status-form').forEach(form => {
        form.addEventListener('change', function() {
            if (confirm('{{ __("Are you sure you want to update this order status?") }}')) {
                this.submit();
            }
        });
    });
</script>
@endpush