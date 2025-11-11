@extends('emails.layout')

@section('content')
<h2 class="email-title">⚠️ Low Stock Alert</h2>

<p class="email-text">
    One of your products is running low on stock and needs attention.
</p>

<div class="info-box" style="border-left-color: #ffc107; background-color: #fff3cd;">
    <p style="margin: 0; font-weight: 700; color: #856404; font-size: 18px;">
        Stock Level Warning
    </p>
    <p style="margin: 10px 0 0 0; font-size: 14px; color: #856404;">
        This product has reached or fallen below the minimum stock threshold of {{ $threshold }} units.
    </p>
</div>

<!-- Product Information -->
<div class="order-details">
    <h3 style="margin: 0 0 15px 0; font-size: 18px; color: #1a1a1a;">Product Details</h3>
    
    <table style="width: 100%;">
        <tr>
            <td style="width: 150px; padding: 12px 0; color: #555; vertical-align: top;">Product Name:</td>
            <td style="padding: 12px 0; font-weight: 600;">{{ $product->name }}</td>
        </tr>
        @if($product->sku)
        <tr>
            <td style="padding: 12px 0; color: #555; vertical-align: top;">SKU:</td>
            <td style="padding: 12px 0; font-weight: 600;">{{ $product->sku }}</td>
        </tr>
        @endif
        <tr>
            <td style="padding: 12px 0; color: #555; vertical-align: top;">Current Stock:</td>
            <td style="padding: 12px 0;">
                <span style="font-size: 24px; font-weight: 700; color: #dc3545;">{{ $product->stock }}</span>
                <span style="color: #777;"> units</span>
            </td>
        </tr>
        <tr>
            <td style="padding: 12px 0; color: #555; vertical-align: top;">Price:</td>
            <td style="padding: 12px 0; font-weight: 600;">${{ number_format($product->price, 2) }}</td>
        </tr>
        <tr>
            <td style="padding: 12px 0; color: #555; vertical-align: top;">Category:</td>
            <td style="padding: 12px 0; font-weight: 600;">{{ $product->category->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="padding: 12px 0; color: #555; vertical-align: top;">Status:</td>
            <td style="padding: 12px 0;">
                <span style="display: inline-block; padding: 4px 12px; background-color: {{ $product->status === 'active' ? '#28a745' : '#6c757d' }}; color: #ffffff; border-radius: 3px; font-size: 12px; font-weight: 600;">
                    {{ ucfirst($product->status) }}
                </span>
            </td>
        </tr>
    </table>
</div>

<!-- Product Image (if available) -->
@if($product->main_image)
<div style="text-align: center; margin: 30px 0;">
    <img src="{{ asset('storage/' . $product->main_image) }}" alt="{{ $product->name }}" style="max-width: 300px; height: auto; border-radius: 5px; border: 1px solid #e0e0e0;">
</div>
@endif

<!-- Stock Status -->
<div class="order-details">
    <h3 style="margin: 0 0 15px 0; font-size: 18px; color: #1a1a1a;">Stock Analysis</h3>
    
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin: 15px 0;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; padding: 10px; text-align: center; border-right: 1px solid #dee2e6;">
                    <div style="font-size: 32px; font-weight: 700; color: #dc3545;">{{ $product->stock }}</div>
                    <div style="font-size: 14px; color: #777; margin-top: 5px;">Current Stock</div>
                </td>
                <td style="width: 50%; padding: 10px; text-align: center;">
                    <div style="font-size: 32px; font-weight: 700; color: #ffc107;">{{ $threshold }}</div>
                    <div style="font-size: 14px; color: #777; margin-top: 5px;">Minimum Threshold</div>
                </td>
            </tr>
        </table>
    </div>
    
    @if($product->stock == 0)
    <div class="info-box" style="border-left-color: #dc3545; background-color: #f8d7da;">
        <p style="margin: 0; font-weight: 700; color: #721c24;">
            🚫 Out of Stock
        </p>
        <p style="margin: 8px 0 0 0; font-size: 14px; color: #721c24;">
            This product is currently out of stock. Customers cannot purchase it until stock is replenished.
        </p>
    </div>
    @elseif($product->stock <= $threshold / 2)
    <div class="info-box" style="border-left-color: #dc3545; background-color: #f8d7da;">
        <p style="margin: 0; font-weight: 700; color: #721c24;">
            🔴 Critical Stock Level
        </p>
        <p style="margin: 8px 0 0 0; font-size: 14px; color: #721c24;">
            Stock level is critically low. Immediate restocking is recommended to avoid stockouts.
        </p>
    </div>
    @else
    <div class="info-box" style="border-left-color: #ffc107; background-color: #fff3cd;">
        <p style="margin: 0; font-weight: 700; color: #856404;">
            🟡 Low Stock Level
        </p>
        <p style="margin: 8px 0 0 0; font-size: 14px; color: #856404;">
            Stock level is below the minimum threshold. Consider restocking soon to meet customer demand.
        </p>
    </div>
    @endif
</div>

<!-- Recommended Actions -->
<div class="order-details">
    <h3 style="margin: 0 0 15px 0; font-size: 18px; color: #1a1a1a;">Recommended Actions</h3>
    
    <div style="padding: 15px 0; border-bottom: 1px solid #f0f0f0;">
        <div style="display: flex; align-items: start;">
            <div style="font-size: 24px; margin-right: 15px;">📦</div>
            <div>
                <h4 style="margin: 0 0 5px 0; font-size: 16px; color: #1a1a1a;">Restock Product</h4>
                <p style="margin: 0; font-size: 14px; color: #555;">
                    Contact your supplier or warehouse to order more inventory for this product.
                </p>
            </div>
        </div>
    </div>
    
    <div style="padding: 15px 0; border-bottom: 1px solid #f0f0f0;">
        <div style="display: flex; align-items: start;">
            <div style="font-size: 24px; margin-right: 15px;">🏷️</div>
            <div>
                <h4 style="margin: 0 0 5px 0; font-size: 16px; color: #1a1a1a;">Update Product Status</h4>
                <p style="margin: 0; font-size: 14px; color: #555;">
                    Consider marking the product as "Out of Stock" or "Coming Soon" to manage customer expectations.
                </p>
            </div>
        </div>
    </div>
    
    <div style="padding: 15px 0;">
        <div style="display: flex; align-items: start;">
            <div style="font-size: 24px; margin-right: 15px;">📊</div>
            <div>
                <h4 style="margin: 0 0 5px 0; font-size: 16px; color: #1a1a1a;">Review Sales Data</h4>
                <p style="margin: 0; font-size: 14px; color: #555;">
                    Analyze recent sales trends to determine optimal reorder quantity.
                </p>
            </div>
        </div>
    </div>
</div>

<div style="text-align: center; margin-top: 30px;">
    <a href="{{ route('admin.products.edit', $product->id) }}" class="email-button">
        Update Product Stock
    </a>
</div>

<p class="email-text" style="margin-top: 30px; text-align: center; font-size: 14px; color: #777;">
    <strong>Note:</strong> This alert was automatically generated based on your configured stock threshold settings.
</p>
@endsection