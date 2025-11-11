<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Invoice') }} #{{ $order->order_number }}</title>
    
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Poppins Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        
        .invoice-container {
            background: white;
            max-width: 900px;
            margin: 0 auto;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        
        .invoice-header {
            border-bottom: 3px solid #20b2aa;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .invoice-header h1 {
            color: #1a1a1a;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .invoice-number {
            color: #20b2aa;
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .company-info {
            text-align: right;
        }
        
        .company-name {
            color: #1a1a1a;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .info-section {
            margin-bottom: 30px;
        }
        
        .info-title {
            color: #20b2aa;
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 5px;
        }
        
        .info-content {
            color: #555;
            line-height: 1.8;
        }
        
        .invoice-table {
            margin-top: 30px;
        }
        
        .invoice-table table {
            border: 1px solid #e9ecef;
        }
        
        .invoice-table thead {
            background-color: #20b2aa;
            color: white;
        }
        
        .invoice-table thead th {
            font-weight: 600;
            padding: 12px;
            border: none;
        }
        
        .invoice-table tbody td {
            padding: 12px;
            vertical-align: middle;
            border-color: #e9ecef;
        }
        
        .invoice-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .totals-section {
            margin-top: 30px;
            border-top: 2px solid #e9ecef;
            padding-top: 20px;
        }
        
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 1rem;
        }
        
        .totals-label {
            font-weight: 500;
            color: #555;
        }
        
        .totals-value {
            font-weight: 600;
            color: #1a1a1a;
        }
        
        .grand-total {
            border-top: 2px solid #20b2aa;
            margin-top: 10px;
            padding-top: 10px;
        }
        
        .grand-total .totals-label {
            font-size: 1.2rem;
            color: #20b2aa;
            font-weight: 700;
        }
        
        .grand-total .totals-value {
            font-size: 1.3rem;
            color: #20b2aa;
            font-weight: 700;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-processing {
            background-color: #cfe2ff;
            color: #084298;
        }
        
        .status-shipped {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        
        .status-delivered {
            background-color: #d1e7dd;
            color: #0a3622;
        }
        
        .status-cancelled {
            background-color: #f8d7da;
            color: #842029;
        }
        
        .footer-note {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
            text-align: center;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .action-buttons {
            margin-bottom: 20px;
            text-align: right;
        }
        
        .btn-teal {
            background-color: #20b2aa;
            color: white;
            border: none;
            padding: 10px 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-teal:hover {
            background-color: #008b8b;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(32, 178, 170, 0.3);
        }
        
        .btn-dark-custom {
            background-color: #1a1a1a;
            color: white;
            border: none;
            padding: 10px 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-dark-custom:hover {
            background-color: #000000;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(26, 26, 26, 0.3);
        }
        
        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .invoice-container {
                box-shadow: none;
                border-radius: 0;
                padding: 20px;
            }
            
            .action-buttons {
                display: none;
            }
            
            .no-print {
                display: none;
            }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .invoice-container {
                padding: 20px;
            }
            
            .company-info {
                text-align: left;
                margin-top: 20px;
            }
            
            .invoice-table {
                overflow-x: auto;
            }
            
            .action-buttons {
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <!-- Action Buttons -->
    <div class="action-buttons no-print">
        <button onclick="window.print()" class="btn btn-teal me-2">
            <i class="bi bi-printer"></i> {{ __('Print Invoice') }}
        </button>
        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-dark-custom">
            <i class="bi bi-arrow-left"></i> {{ __('Back to Order') }}
        </a>
    </div>

    <!-- Invoice Container -->
    <div class="invoice-container">
        <!-- Invoice Header -->
        <div class="invoice-header">
            <div class="row">
                <div class="col-md-6">
                    <h1>{{ __('INVOICE') }}</h1>
                    <div class="invoice-number">#{{ $order->order_number }}</div>
                    <div class="mt-2">
                        <small class="text-muted">{{ __('Order Date') }}:</small><br>
                        <strong>{{ $order->created_at->format('F d, Y') }}</strong>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">{{ __('Status') }}:</small><br>
                        <span class="status-badge status-{{ $order->status }}">
                            {{ __(ucfirst($order->status)) }}
                        </span>
                    </div>
                </div>
                <div class="col-md-6 company-info">
                    <div class="company-name">
                        {{ App\Models\Setting::get('store_name', config('app.name')) }}
                    </div>
                    <div class="text-muted">
                        {{ App\Models\Setting::get('store_address', '') }}<br>
                        {{ App\Models\Setting::get('store_city', '') }}, 
                        {{ App\Models\Setting::get('store_state', '') }} 
                        {{ App\Models\Setting::get('store_zip', '') }}<br>
                        {{ __('Phone') }}: {{ App\Models\Setting::get('store_phone', '') }}<br>
                        {{ __('Email') }}: {{ App\Models\Setting::get('store_email', '') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer & Shipping Information -->
        <div class="row">
            <!-- Billing Address -->
            <div class="col-md-6 info-section">
                <div class="info-title">{{ __('Bill To') }}</div>
                <div class="info-content">
                    @if($order->billingAddress)
                        <strong>{{ $order->billingAddress->full_name }}</strong><br>
                        {{ $order->billingAddress->address_line1 }}<br>
                        @if($order->billingAddress->address_line2)
                            {{ $order->billingAddress->address_line2 }}<br>
                        @endif
                        {{ $order->billingAddress->city }}, 
                        {{ $order->billingAddress->state }} 
                        {{ $order->billingAddress->zip_code }}<br>
                        {{ $order->billingAddress->country }}<br>
                        @if($order->billingAddress->phone)
                            {{ __('Phone') }}: {{ $order->billingAddress->phone }}
                        @endif
                    @else
                        <strong>{{ $order->customer_name }}</strong><br>
                        {{ $order->customer_email }}<br>
                        {{ $order->customer_phone }}
                    @endif
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="col-md-6 info-section">
                <div class="info-title">{{ __('Ship To') }}</div>
                <div class="info-content">
                    @if($order->shippingAddress)
                        <strong>{{ $order->shippingAddress->full_name }}</strong><br>
                        {{ $order->shippingAddress->address_line1 }}<br>
                        @if($order->shippingAddress->address_line2)
                            {{ $order->shippingAddress->address_line2 }}<br>
                        @endif
                        {{ $order->shippingAddress->city }}, 
                        {{ $order->shippingAddress->state }} 
                        {{ $order->shippingAddress->zip_code }}<br>
                        {{ $order->shippingAddress->country }}<br>
                        @if($order->shippingAddress->phone)
                            {{ __('Phone') }}: {{ $order->shippingAddress->phone }}
                        @endif
                    @else
                        <em class="text-muted">{{ __('Same as billing address') }}</em>
                    @endif
                </div>
            </div>
        </div>

        <!-- Order Items Table -->
        <div class="invoice-table">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>{{ __('Product') }}</th>
                        <th style="width: 100px;" class="text-center">{{ __('Quantity') }}</th>
                        <th style="width: 120px;" class="text-end">{{ __('Price') }}</th>
                        <th style="width: 120px;" class="text-end">{{ __('Total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderItems as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $item->product_name }}</strong>
                            @if($item->product && $item->product->sku)
                                <br><small class="text-muted">{{ __('SKU') }}: {{ $item->product->sku }}</small>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-end">{{ App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($item->price, 2) }}</td>
                        <td class="text-end">{{ App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($item->quantity * $item->price, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals Section -->
        <div class="row">
            <div class="col-md-6 offset-md-6">
                <div class="totals-section">
                    <div class="totals-row">
                        <span class="totals-label">{{ __('Subtotal') }}:</span>
                        <span class="totals-value">{{ App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    
                    @if($order->tax_amount > 0)
                    <div class="totals-row">
                        <span class="totals-label">{{ __('Tax') }}:</span>
                        <span class="totals-value">{{ App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($order->tax_amount, 2) }}</span>
                    </div>
                    @endif
                    
                    @if($order->shipping_cost > 0)
                    <div class="totals-row">
                        <span class="totals-label">{{ __('Shipping') }}:</span>
                        <span class="totals-value">{{ App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($order->shipping_cost, 2) }}</span>
                    </div>
                    @endif
                    
                    @if($order->discount_amount > 0)
                    <div class="totals-row">
                        <span class="totals-label">{{ __('Discount') }}:</span>
                        <span class="totals-value text-danger">-{{ App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($order->discount_amount, 2) }}</span>
                    </div>
                    @endif
                    
                    <div class="totals-row grand-total">
                        <span class="totals-label">{{ __('Grand Total') }}:</span>
                        <span class="totals-value">{{ App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Information -->
        @if($order->payment_method)
        <div class="row mt-4">
            <div class="col-12">
                <div class="info-section">
                    <div class="info-title">{{ __('Payment Information') }}</div>
                    <div class="info-content">
                        <strong>{{ __('Payment Method') }}:</strong> {{ __(ucfirst($order->payment_method)) }}<br>
                        <strong>{{ __('Payment Status') }}:</strong> 
                        <span class="badge {{ $order->payment_status === 'paid' ? 'bg-success' : 'bg-warning' }}">
                            {{ __(ucfirst($order->payment_status)) }}
                        </span>
                        @if($order->transaction_id)
                            <br><strong>{{ __('Transaction ID') }}:</strong> {{ $order->transaction_id }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Notes -->
        @if($order->notes)
        <div class="row mt-4">
            <div class="col-12">
                <div class="info-section">
                    <div class="info-title">{{ __('Order Notes') }}</div>
                    <div class="info-content">
                        {{ $order->notes }}
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer-note">
            <p class="mb-1">{{ __('Thank you for your business!') }}</p>
            <small>
                {{ __('This is a computer-generated invoice and does not require a signature.') }}<br>
                {{ __('If you have any questions, please contact us at') }} {{ App\Models\Setting::get('store_email', '') }}
            </small>
        </div>
    </div>

    <!-- Bootstrap Icons (for print button) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>