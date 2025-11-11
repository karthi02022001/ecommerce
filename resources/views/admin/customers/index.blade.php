@extends('admin.layouts.app')

@section('title', __('Customers'))

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">{{ __('Customers') }}</h1>
    <p class="page-subtitle">{{ __('Manage your customer accounts') }}</p>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Customers') }}</li>
        </ol>
    </nav>
</div>

<!-- Statistics Cards -->
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-label">{{ __('Total Customers') }}</div>
            <div class="stat-icon">
                <i class="bi bi-people"></i>
            </div>
        </div>
        <div class="stat-value">{{ number_format($customers->total()) }}</div>
        <div class="stat-footer">
            <span class="text-muted">{{ __('All registered customers') }}</span>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="content-card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.customers.index') }}" class="row g-3">
            <!-- Search -->
            <div class="col-md-6">
                <label for="search" class="form-label">{{ __('Search') }}</label>
                <input 
                    type="text" 
                    name="search" 
                    id="search" 
                    class="form-control" 
                    placeholder="{{ __('Search by name, email, or phone') }}"
                    value="{{ request('search') }}"
                >
            </div>

            <!-- Sort By -->
            <div class="col-md-3">
                <label for="sort" class="form-label">{{ __('Sort By') }}</label>
                <select name="sort" id="sort" class="form-select">
                    <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>{{ __('Registration Date') }}</option>
                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>{{ __('Name') }}</option>
                    <option value="email" {{ request('sort') == 'email' ? 'selected' : '' }}>{{ __('Email') }}</option>
                </select>
            </div>

            <!-- Order -->
            <div class="col-md-3">
                <label for="order" class="form-label">{{ __('Order') }}</label>
                <select name="order" id="order" class="form-select">
                    <option value="desc" {{ request('order') == 'desc' ? 'selected' : '' }}>{{ __('Descending') }}</option>
                    <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>{{ __('Ascending') }}</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-2"></i>{{ __('Search') }}
                </button>
                <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-counterclockwise me-2"></i>{{ __('Reset') }}
                </a>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createCustomerModal">
                    <i class="bi bi-plus-lg me-2"></i>{{ __('Add Customer') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Customers Table -->
<div class="content-card">
    <div class="card-header">
        <h3 class="card-title">{{ __('Customer List') }}</h3>
        <div class="card-actions">
            <span class="text-muted">{{ __('Showing :from to :to of :total customers', [
                'from' => $customers->firstItem() ?? 0,
                'to' => $customers->lastItem() ?? 0,
                'total' => $customers->total()
            ]) }}</span>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>{{ __('ID') }}</th>
                        <th>{{ __('Customer') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Phone') }}</th>
                        <th>{{ __('Orders') }}</th>
                        <th>{{ __('Registered') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr>
                        <td><strong>#{{ $customer->id }}</strong></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden; margin-right: 12px; background: var(--content-bg);">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($customer->name) }}&background=20b2aa&color=ffffff" 
                                         style="width: 100%; height: 100%; object-fit: cover;"
                                         alt="{{ $customer->name }}">
                                </div>
                                <div>
                                    <div style="font-weight: 600;">{{ $customer->name }}</div>
                                    @if($customer->email_verified_at)
                                    <span class="badge badge-success" style="font-size: 0.7rem;">
                                        <i class="bi bi-check-circle"></i> {{ __('Verified') }}
                                    </span>
                                    @else
                                    <span class="badge badge-warning" style="font-size: 0.7rem;">
                                        <i class="bi bi-clock"></i> {{ __('Unverified') }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->phone ?? '—' }}</td>
                        <td>
                            <span class="badge badge-info">{{ $customer->orders_count }}</span>
                        </td>
                        <td>{{ $customer->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.customers.show', $customer->id) }}" 
                                   class="btn btn-sm btn-icon btn-primary" 
                                   title="{{ __('View Details') }}">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(auth('admin')->user()->hasPermission('customers.edit'))
                                <button type="button" 
                                        class="btn btn-sm btn-icon btn-warning" 
                                        title="{{ __('Edit Customer') }}"
                                        onclick="editCustomer({{ $customer->id }}, '{{ $customer->name }}', '{{ $customer->email }}', '{{ $customer->phone }}')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                @endif
                                @if(auth('admin')->user()->hasPermission('customers.delete'))
                                <form action="{{ route('admin.customers.destroy', $customer->id) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('{{ __('Are you sure you want to delete this customer?') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-icon btn-danger" 
                                            title="{{ __('Delete Customer') }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-people" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p class="mt-3">{{ __('No customers found') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($customers->hasPages())
        <div class="mt-4">
            {{ $customers->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Create Customer Modal -->
@if(auth('admin')->user()->hasPermission('customers.create'))
<div class="modal fade" id="createCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.customers.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Add New Customer') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Name -->
                    <div class="form-group">
                        <label for="create_name" class="form-label">{{ __('Full Name') }} <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="name" 
                               id="create_name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               required
                               value="{{ old('name') }}">
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="create_email" class="form-label">{{ __('Email') }} <span class="text-danger">*</span></label>
                        <input type="email" 
                               name="email" 
                               id="create_email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               required
                               value="{{ old('email') }}">
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div class="form-group">
                        <label for="create_phone" class="form-label">{{ __('Phone') }}</label>
                        <input type="text" 
                               name="phone" 
                               id="create_phone" 
                               class="form-control @error('phone') is-invalid @enderror"
                               value="{{ old('phone') }}">
                        @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label for="create_password" class="form-label">{{ __('Password') }} <span class="text-danger">*</span></label>
                        <input type="password" 
                               name="password" 
                               id="create_password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               required>
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label for="create_password_confirmation" class="form-label">{{ __('Confirm Password') }} <span class="text-danger">*</span></label>
                        <input type="password" 
                               name="password_confirmation" 
                               id="create_password_confirmation" 
                               class="form-control" 
                               required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-2"></i>{{ __('Create Customer') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

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

// Show create modal if there are validation errors
@if($errors->any() && old('_method') !== 'PUT')
document.addEventListener('DOMContentLoaded', function() {
    const modal = new bootstrap.Modal(document.getElementById('createCustomerModal'));
    modal.show();
});
@endif

// Show edit modal if there are validation errors for update
@if($errors->any() && old('_method') === 'PUT')
document.addEventListener('DOMContentLoaded', function() {
    // You would need to pass the customer data back from controller
    const modal = new bootstrap.Modal(document.getElementById('editCustomerModal'));
    modal.show();
});
@endif
</script>
@endpush