@extends('admin.layouts.app')

@section('title', __('Order Details') . ' - #' . $order->order_number)

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">{{ __('Orders') }}</a></li>
            <li class="breadcrumb-item active">#{{ $order->order_number }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-receipt text-teal"></i>
                {{ __('Order') }} #{{ $order->order_number }}
            </h2>
            <small class="text-muted">
                {{ __('Placed on') }} {{ $order->created_at->format('M d, Y \a\t h:i A') }}
            </small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.orders.invoice', $order->id) }}" class="btn btn-outline-secondary" target="_blank">
                <i class="bi bi-printer"></i> {{ __('Print Invoice') }}
            </a>
            @if(auth('admin')->user()->hasPermission('orders.refund'))
                @if($order->payment_status === 'paid' && !$order->refund_id && $order->status !== 'cancelled')
                    <a href="{{ route('admin.orders.refund.form', $order->id) }}" class="btn btn-outline-danger">
                        <i class="bi bi-arrow-return-left"></i> {{ __('Process Refund') }}
                    </a>
                @endif
            @endif
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-dark">
                <i class="bi bi-arrow-left"></i> {{ __('Back to Orders') }}
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Order Status Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle text-teal"></i>
                        {{ __('Order Status') }}
                    </h5>
                    @if(auth('admin')->user()->hasPermission('orders.edit'))
                        <button class="btn btn-sm btn-teal" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                            <i class="bi bi-pencil-square"></i> {{ __('Update Status') }}
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="text-muted small mb-2">{{ __('Order Status') }}</label>
                            <div>
                                <span class="badge fs-6 status-{{ $order->status }}">
                                    <i class="bi bi-circle-fill me-1"></i>
                                    {{ __(ucfirst($order->status)) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-2">{{ __('Payment Status') }}</label>
                            <div>
                                <span class="badge fs-6 payment-{{ $order->payment_status }}">
                                    <i class="bi bi-{{ $order->payment_status === 'paid' ? 'check-circle-fill' : ($order->payment_status === 'refunded' ? 'arrow-return-left' : 'clock-fill') }} me-1"></i>
                                    {{ __(ucfirst($order->payment_status)) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Refund Information -->
                    @if($order->refund_id)
                        <div class="alert alert-info mt-3 mb-0">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                                <div>
                                    <strong>{{ __('Refund Processed') }}</strong><br>
                                    <small>
                                        {{ __('Refund ID') }}: {{ $order->refund_id }}<br>
                                        {{ __('Status') }}: 
                                        <span class="badge bg-{{ $order->refund_status === 'succeeded' ? 'success' : 'warning' }}">
                                            {{ __(ucfirst($order->refund_status)) }}
                                        </span>
                                        @if($order->cancelled_at)
                                            <br>{{ __('Cancelled on') }}: {{ $order->cancelled_at->format('M d, Y \a\t h:i A') }}
                                        @endif
                                        @if($order->cancellation_reason)
                                            <br>{{ __('Reason') }}: {{ $order->cancellation_reason }}
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Order Timeline -->
                    <div class="mt-4">
                        <h6 class="text-muted mb-3">{{ __('Order Timeline') }}</h6>
                        <div class="order-timeline">
                            <div class="timeline-item {{ $order->status === 'pending' ? 'active' : 'completed' }}">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <strong>{{ __('Order Placed') }}</strong>
                                    <small class="d-block text-muted">{{ $order->created_at->format('M d, Y h:i A') }}</small>
                                </div>
                            </div>
                            
                            <div class="timeline-item {{ in_array($order->status, ['processing', 'shipped', 'delivered']) ? ($order->status === 'processing' ? 'active' : 'completed') : '' }}">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <strong>{{ __('Processing') }}</strong>
                                    <small class="d-block text-muted">
                                        @if(in_array($order->status, ['processing', 'shipped', 'delivered']))
                                            {{ $order->updated_at->format('M d, Y h:i A') }}
                                        @else
                                            {{ __('Pending') }}
                                        @endif
                                    </small>
                                </div>
                            </div>
                            
                            <div class="timeline-item {{ in_array($order->status, ['shipped', 'delivered']) ? ($order->status === 'shipped' ? 'active' : 'completed') : '' }}">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <strong>{{ __('Shipped') }}</strong>
                                    <small class="d-block text-muted">
                                        @if(in_array($order->status, ['shipped', 'delivered']))
                                            {{ $order->updated_at->format('M d, Y h:i A') }}
                                        @else
                                            {{ __('Pending') }}
                                        @endif
                                    </small>
                                </div>
                            </div>
                            
                            <div class="timeline-item {{ $order->status === 'delivered' ? 'active completed' : '' }}">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <strong>{{ __('Delivered') }}</strong>
                                    <small class="d-block text-muted">
                                        @if($order->status === 'delivered')
                                            {{ $order->updated_at->format('M d, Y h:i A') }}
                                        @else
                                            {{ __('Pending') }}
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-basket text-teal"></i>
                        {{ __('Order Items') }} ({{ $order->orderItems->count() }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 80px;">{{ __('Image') }}</th>
                                    <th>{{ __('Product') }}</th>
                                    <th class="text-center" style="width: 100px;">{{ __('Quantity') }}</th>
                                    <th class="text-end" style="width: 120px;">{{ __('Price') }}</th>
                                    <th class="text-end" style="width: 120px;">{{ __('Total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $item)
                                <tr>
                                    <td>
                                        @if($item->product && $item->product->images->first())
                                            <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" 
                                                 alt="{{ $item->product_name }}" 
                                                 class="img-thumbnail"
                                                 style="width: 60px; height: 60px; object-fit: cover;">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center" 
                                                 style="width: 60px; height: 60px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $item->product_name }}</strong>
                                        @if($item->product && $item->product->sku)
                                            <br><small class="text-muted">{{ __('SKU') }}: {{ $item->product->sku }}</small>
                                        @endif
                                        @if($item->product)
                                            <br><a href="{{ route('admin.products.edit', $item->product->id) }}" 
                                                   class="btn btn-sm btn-link p-0 text-teal" target="_blank">
                                                <i class="bi bi-box-arrow-up-right"></i> {{ __('View Product') }}
                                            </a>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="text-end">{{ App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($item->price, 2) }}</td>
                                    <td class="text-end fw-bold">{{ App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($item->quantity * $item->price, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Shipping & Billing Addresses -->
            <div class="row">
                <!-- Shipping Address -->
                <div class="col-md-6">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">
                                <i class="bi bi-truck text-teal"></i>
                                {{ __('Shipping Address') }}
                            </h6>
                        </div>
                        <div class="card-body">
                            @if($order->shippingAddress)
                                <strong>{{ $order->shippingAddress->full_name }}</strong><br>
                                {{ $order->shippingAddress->address_line1 }}<br>
                                @if($order->shippingAddress->address_line2)
                                    {{ $order->shippingAddress->address_line2 }}<br>
                                @endif
                                {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->zip_code }}<br>
                                {{ $order->shippingAddress->country }}<br>
                                @if($order->shippingAddress->phone)
                                    <i class="bi bi-telephone me-1"></i>{{ $order->shippingAddress->phone }}
                                @endif
                            @else
                                <em class="text-muted">{{ __('No shipping address provided') }}</em>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Billing Address -->
                <div class="col-md-6">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">
                                <i class="bi bi-credit-card text-teal"></i>
                                {{ __('Billing Address') }}
                            </h6>
                        </div>
                        <div class="card-body">
                            @if($order->billingAddress)
                                <strong>{{ $order->billingAddress->full_name }}</strong><br>
                                {{ $order->billingAddress->address_line1 }}<br>
                                @if($order->billingAddress->address_line2)
                                    {{ $order->billingAddress->address_line2 }}<br>
                                @endif
                                {{ $order->billingAddress->city }}, {{ $order->billingAddress->state }} {{ $order->billingAddress->zip_code }}<br>
                                {{ $order->billingAddress->country }}<br>
                                @if($order->billingAddress->phone)
                                    <i class="bi bi-telephone me-1"></i>{{ $order->billingAddress->phone }}
                                @endif
                            @else
                                <em class="text-muted">{{ __('Same as shipping address') }}</em>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Notes -->
            @if($order->notes)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="bi bi-sticky text-teal"></i>
                        {{ __('Order Notes') }}
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $order->notes }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Order Summary -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-teal text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-calculator"></i>
                        {{ __('Order Summary') }}
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">{{ __('Subtotal') }}:</td>
                            <td class="text-end">{{ App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($order->subtotal, 2) }}</td>
                        </tr>
                        @if($order->tax_amount > 0)
                        <tr>
                            <td class="text-muted">{{ __('Tax') }}:</td>
                            <td class="text-end">{{ App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($order->tax_amount, 2) }}</td>
                        </tr>
                        @endif
                        @if($order->shipping_cost > 0)
                        <tr>
                            <td class="text-muted">{{ __('Shipping') }}:</td>
                            <td class="text-end">{{ App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($order->shipping_cost, 2) }}</td>
                        </tr>
                        @endif
                        @if($order->discount_amount > 0)
                        <tr>
                            <td class="text-muted">{{ __('Discount') }}:</td>
                            <td class="text-end text-danger">-{{ App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($order->discount_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="border-top">
                            <td class="fw-bold">{{ __('Total') }}:</td>
                            <td class="text-end fw-bold fs-5 text-teal">{{ App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="bi bi-person text-teal"></i>
                        {{ __('Customer Information') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">{{ __('Name') }}</label>
                        <div class="fw-bold">{{ $order->customer_name }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">{{ __('Email') }}</label>
                        <div>
                            <a href="mailto:{{ $order->customer_email }}" class="text-teal">
                                {{ $order->customer_email }}
                            </a>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">{{ __('Phone') }}</label>
                        <div>
                            <a href="tel:{{ $order->customer_phone }}" class="text-teal">
                                {{ $order->customer_phone }}
                            </a>
                        </div>
                    </div>
                    @if($order->user)
                    <div>
                        <a href="{{ route('admin.customers.show', $order->user->id) }}" class="btn btn-sm btn-outline-teal w-100">
                            <i class="bi bi-person-circle"></i> {{ __('View Customer Profile') }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Payment Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="bi bi-credit-card-2-front text-teal"></i>
                        {{ __('Payment Information') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">{{ __('Payment Method') }}</label>
                        <div class="fw-bold text-capitalize">
                            <i class="bi bi-{{ $order->payment_method === 'stripe' ? 'credit-card' : ($order->payment_method === 'cod' ? 'cash' : 'wallet2') }}"></i>
                            {{ __(ucfirst($order->payment_method)) }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">{{ __('Payment Status') }}</label>
                        <div>
                            <span class="badge payment-{{ $order->payment_status }}">
                                {{ __(ucfirst($order->payment_status)) }}
                            </span>
                        </div>
                    </div>
                    @if($order->transaction_id)
                    <div class="mb-3">
                        <label class="text-muted small">{{ __('Transaction ID') }}</label>
                        <div class="font-monospace small">{{ $order->transaction_id }}</div>
                    </div>
                    @endif
                    @if($order->currency)
                    <div>
                        <label class="text-muted small">{{ __('Currency') }}</label>
                        <div class="fw-bold">{{ strtoupper($order->currency) }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            @if(auth('admin')->user()->hasPermission('orders.edit'))
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="bi bi-lightning text-teal"></i>
                        {{ __('Quick Actions') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                            <i class="bi bi-arrow-repeat"></i> {{ __('Update Status') }}
                        </button>
                        <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                            <i class="bi bi-sticky"></i> {{ __('Add Note') }}
                        </button>
                        <a href="{{ route('admin.orders.invoice', $order->id) }}" class="btn btn-outline-info" target="_blank">
                            <i class="bi bi-file-earmark-pdf"></i> {{ __('View Invoice') }}
                        </a>
                        @if($order->customer_email)
                        <a href="mailto:{{ $order->customer_email }}" class="btn btn-outline-dark">
                            <i class="bi bi-envelope"></i> {{ __('Email Customer') }}
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Update Status Modal -->
@if(auth('admin')->user()->hasPermission('orders.edit'))
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.orders.update-status', $order->id) }}">
                @csrf
                <div class="modal-header bg-teal text-white">
                    <h5 class="modal-title">{{ __('Update Order Status') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">{{ __('Order Status') }}</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>{{ __('Processing') }}</option>
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>{{ __('Shipped') }}</option>
                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>{{ __('Delivered') }}</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="payment_status" class="form-label">{{ __('Payment Status') }}</label>
                        <select class="form-select" id="payment_status" name="payment_status" required>
                            <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                            <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                            <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>{{ __('Refunded') }}</option>
                            <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>{{ __('Failed') }}</option>
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <small>
                            <i class="bi bi-info-circle"></i>
                            {{ __('Customers will be notified of status changes via email.') }}
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-teal">{{ __('Update Status') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Note Modal -->
<div class="modal fade" id="addNoteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.orders.addNote', $order->id) }}">
                @csrf
                @method('PATCH')
                <div class="modal-header bg-teal text-white">
                    <h5 class="modal-title">{{ __('Add Order Note') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="notes" class="form-label">{{ __('Note') }}</label>
                        <textarea class="form-control" id="notes" name="notes" rows="4" placeholder="{{ __('Add internal note about this order...') }}">{{ $order->notes }}</textarea>
                    </div>
                    <div class="alert alert-warning">
                        <small>
                            <i class="bi bi-exclamation-triangle"></i>
                            {{ __('This note is for internal use only and will not be visible to the customer.') }}
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-teal">{{ __('Save Note') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('styles')
<style>
    .text-teal {
        color: #20b2aa;
    }
    
    .bg-teal {
        background-color: #20b2aa;
    }
    
    .btn-teal {
        background-color: #20b2aa;
        color: white;
        border: none;
    }
    
    .btn-teal:hover {
        background-color: #008b8b;
        color: white;
    }
    
    .btn-outline-teal {
        color: #20b2aa;
        border-color: #20b2aa;
    }
    
    .btn-outline-teal:hover {
        background-color: #20b2aa;
        border-color: #20b2aa;
        color: white;
    }
    
    /* Status Badges */
    .status-pending {
        background-color: #ffc107;
        color: #000;
    }
    
    .status-processing {
        background-color: #0dcaf0;
        color: #000;
    }
    
    .status-shipped {
        background-color: #0d6efd;
        color: #fff;
    }
    
    .status-delivered {
        background-color: #198754;
        color: #fff;
    }
    
    .status-cancelled {
        background-color: #dc3545;
        color: #fff;
    }
    
    .payment-pending {
        background-color: #ffc107;
        color: #000;
    }
    
    .payment-paid {
        background-color: #198754;
        color: #fff;
    }
    
    .payment-refunded {
        background-color: #6c757d;
        color: #fff;
    }
    
    .payment-failed {
        background-color: #dc3545;
        color: #fff;
    }
    
    /* Timeline */
    .order-timeline {
        position: relative;
        padding-left: 30px;
    }
    
    .order-timeline::before {
        content: '';
        position: absolute;
        left: 9px;
        top: 10px;
        bottom: 10px;
        width: 2px;
        background: #e9ecef;
    }
    
    .timeline-item {
        position: relative;
        padding-bottom: 25px;
    }
    
    .timeline-item:last-child {
        padding-bottom: 0;
    }
    
    .timeline-marker {
        position: absolute;
        left: -26px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #e9ecef;
        border: 3px solid #fff;
        box-shadow: 0 0 0 2px #e9ecef;
    }
    
    .timeline-item.active .timeline-marker {
        background: #ffc107;
        box-shadow: 0 0 0 2px #ffc107;
    }
    
    .timeline-item.completed .timeline-marker {
        background: #20b2aa;
        box-shadow: 0 0 0 2px #20b2aa;
    }
    
    .timeline-content strong {
        color: #1a1a1a;
        font-size: 0.95rem;
    }
    
    /* Card Hover Effects */
    .card {
        transition: all 0.3s ease;
    }
    
    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(32, 178, 170, 0.15) !important;
    }
    
    /* Table Styling */
    .table-hover tbody tr:hover {
        background-color: rgba(32, 178, 170, 0.05);
    }
    
    /* Image Thumbnail */
    .img-thumbnail {
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .img-thumbnail:hover {
        border-color: #20b2aa;
        transform: scale(1.05);
    }
    
    /* Breadcrumb */
    .breadcrumb-item + .breadcrumb-item::before {
        color: #20b2aa;
    }
    
    .breadcrumb a {
        color: #20b2aa;
        text-decoration: none;
    }
    
    .breadcrumb a:hover {
        color: #008b8b;
        text-decoration: underline;
    }
</style>
@endpush

@push('scripts')
<script>
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>
@endpush