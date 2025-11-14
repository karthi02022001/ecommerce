@extends('admin.layouts.app')

@section('title', __('Customer Details'))

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">{{ __('Customer Details') }}</h1>
    <p class="page-subtitle">{{ $customer->name }}</p>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">{{ __('Customers') }}</a></li>
            <li class="breadcrumb-item active">{{ $customer->name }}</li>
        </ol>
    </nav>
</div>

<!-- Statistics Cards -->
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-label">{{ __('Total Orders') }}</div>
            <div class="stat-icon">
                <i class="bi bi-cart-check"></i>
            </div>
        </div>
        <div class="stat-value">{{ number_format($stats['total_orders']) }}</div>
        <div class="stat-footer">
            <span class="text-muted">{{ __('All time') }}</span>
        </div>
    </div>

    <div class="stat-card success">
        <div class="stat-header">
            <div class="stat-label">{{ __('Completed Orders') }}</div>
            <div class="stat-icon">
                <i class="bi bi-check-circle"></i>
            </div>
        </div>
        <div class="stat-value">{{ number_format($stats['completed_orders']) }}</div>
        <div class="stat-footer">
            <span class="text-muted">{{ __('Successfully delivered') }}</span>
        </div>
    </div>

    <div class="stat-card warning">
        <div class="stat-header">
            <div class="stat-label">{{ __('Pending Orders') }}</div>
            <div class="stat-icon">
                <i class="bi bi-clock"></i>
            </div>
        </div>
        <div class="stat-value">{{ number_format($stats['pending_orders']) }}</div>
        <div class="stat-footer">
            <span class="text-muted">{{ __('Awaiting processing') }}</span>
        </div>
    </div>

    <div class="stat-card info">
        <div class="stat-header">
            <div class="stat-label">{{ __('Total Spent') }}</div>
            <div class="stat-icon">
                <i class="bi bi-currency-dollar"></i>
            </div>
        </div>
        <div class="stat-value">${{ number_format($stats['total_spent'], 2) }}</div>
        <div class="stat-footer">
            <span class="text-muted">{{ __('Lifetime value') }}</span>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Customer Information -->
    <div class="col-lg-4">
        <!-- Basic Info Card -->
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Customer Information') }}</h3>
                @if(auth('admin')->user()->hasPermission('customers.edit'))
                <div class="card-actions">
                    <button type="button" 
                            class="btn btn-sm btn-primary"
                            onclick="editCustomer({{ $customer->id }}, '{{ $customer->name }}', '{{ $customer->email }}', '{{ $customer->phone }}')">
                        <i class="bi bi-pencil me-2"></i>{{ __('Edit') }}
                    </button>
                </div>
                @endif
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($customer->name) }}&size=120&background=20b2aa&color=ffffff" 
                         alt="{{ $customer->name }}"
                         style="width: 120px; height: 120px; border-radius: 50%; border: 4px solid var(--primary-color);">
                    <h4 class="mt-3 mb-1">{{ $customer->name }}</h4>
                    @if($customer->email_verified_at)
                    <span class="badge badge-success">
                        <i class="bi bi-check-circle"></i> {{ __('Verified') }}
                    </span>
                    @else
                    <span class="badge badge-warning">
                        <i class="bi bi-clock"></i> {{ __('Unverified') }}
                    </span>
                    @endif
                </div>

                <div class="info-list">
                    <div class="info-item">
                        <div class="info-label">
                            <i class="bi bi-envelope"></i>
                            {{ __('Email') }}
                        </div>
                        <div class="info-value">{{ $customer->email }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <i class="bi bi-telephone"></i>
                            {{ __('Phone') }}
                        </div>
                        <div class="info-value">{{ $customer->phone ?? '—' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <i class="bi bi-calendar-check"></i>
                            {{ __('Registered') }}
                        </div>
                        <div class="info-value">{{ $customer->created_at->format('M d, Y') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <i class="bi bi-clock-history"></i>
                            {{ __('Last Updated') }}
                        </div>
                        <div class="info-value">{{ $customer->updated_at->diffForHumans() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Addresses Card -->
        @if($customer->addresses->count() > 0)
        <div class="content-card mt-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('Addresses') }}</h3>
            </div>
            <div class="card-body">
                @foreach($customer->addresses as $address)
                <div class="address-item mb-3 pb-3" style="border-bottom: 1px solid var(--border-color);">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="mb-0">
                            @if($address->address_type === 'shipping')
                            <i class="bi bi-truck"></i> {{ __('Shipping Address') }}
                            @else
                            <i class="bi bi-receipt"></i> {{ __('Billing Address') }}
                            @endif
                        </h6>
                        @if($address->is_default)
                        <span class="badge badge-primary">{{ __('Default') }}</span>
                        @endif
                    </div>
                    <div style="font-size: 0.9rem; color: var(--text-muted);">
                        <div>{{ $address->full_name }}</div>
                        <div>{{ $address->phone }}</div>
                        <div>{{ $address->address_line1 }}</div>
                        @if($address->address_line2)
                        <div>{{ $address->address_line2 }}</div>
                        @endif
                        <div>{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}</div>
                        <div>{{ $address->country }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Order History -->
    <div class="col-lg-8">
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Order History') }}</h3>
                <div class="card-actions">
                    <span class="text-muted">{{ __(':count orders', ['count' => $customer->orders->count()]) }}</span>
                </div>
            </div>
            <div class="card-body">
                @if($customer->orders->count() > 0)
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>{{ __('Order ID') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Items') }}</th>
                                <th>{{ __('Total') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customer->orders->sortByDesc('created_at') as $order)
                            <tr>
                                <td><strong>#{{ $order->id }}</strong></td>
                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                                <td>{{ $order->orderItems->count() }}</td>
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
                                <td>
                                    @if(auth('admin')->user()->hasPermission('orders.view'))
                                    <a href="{{ route('admin.orders.show', $order->id) }}" 
                                       class="btn btn-sm btn-icon btn-primary"
                                       title="{{ __('View Order') }}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-cart-x" style="font-size: 4rem; opacity: 0.3;"></i>
                    <p class="text-muted mt-3">{{ __('This customer has not placed any orders yet.') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Edit Customer Modal -->
@if(auth('admin')->user()->hasPermission('customers.edit'))
<div class="modal fade" id="editCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editCustomerForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Edit Customer') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Name -->
                    <div class="form-group">
                        <label for="edit_name" class="form-label">{{ __('Full Name') }} <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="name" 
                               id="edit_name" 
                               class="form-control" 
                               required>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="edit_email" class="form-label">{{ __('Email') }} <span class="text-danger">*</span></label>
                        <input type="email" 
                               name="email" 
                               id="edit_email" 
                               class="form-control" 
                               required>
                    </div>

                    <!-- Phone -->
                    <div class="form-group">
                        <label for="edit_phone" class="form-label">{{ __('Phone') }}</label>
                        <input type="text" 
                               name="phone" 
                               id="edit_phone" 
                               class="form-control">
                    </div>

                    <!-- Password (Optional) -->
                    <div class="form-group">
                        <label for="edit_password" class="form-label">{{ __('New Password') }}</label>
                        <input type="password" 
                               name="password" 
                               id="edit_password" 
                               class="form-control">
                        <small class="form-text text-muted">{{ __('Leave blank to keep current password') }}</small>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label for="edit_password_confirmation" class="form-label">{{ __('Confirm New Password') }}</label>
                        <input type="password" 
                               name="password_confirmation" 
                               id="edit_password_confirmation" 
                               class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i>{{ __('Update Customer') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
.info-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    background: var(--content-bg);
    border-radius: 8px;
}

.info-label {
    font-weight: 500;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 8px;
}

.info-value {
    font-weight: 600;
    color: var(--text-dark);
    text-align: right;
}

.address-item:last-child {
    border-bottom: none !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}
</style>
@endpush

@push('scripts')
<script>
function editCustomer(id, name, email, phone) {
    const form = document.getElementById('editCustomerForm');
    form.action = `/admin/customers/${id}`;
    
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_phone').value = phone || '';
    document.getElementById('edit_password').value = '';
    document.getElementById('edit_password_confirmation').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('editCustomerModal'));
    modal.show();
}
</script>
@endpush