<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAddress extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'type',
        'first_name',
        'last_name',
        'company',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country',
        'phone'
    ];

    /**
     * Relationship: Address belongs to an order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get full name
     */
    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Get formatted address
     */
    public function getFormattedAddressAttribute()
    {
        $lines = [
            $this->full_name,
            $this->company,
            $this->address_line_1,
            $this->address_line_2,
            "{$this->city}, {$this->state} {$this->postal_code}",
            $this->country,
            $this->phone
        ];

        return implode("\n", array_filter($lines));
    }

    /**
     * Scope: Billing addresses
     */
    public function scopeBilling($query)
    {
        return $query->where('type', 'billing');
    }

    /**
     * Scope: Shipping addresses
     */
    public function scopeShipping($query)
    {
        return $query->where('type', 'shipping');
    }
}
