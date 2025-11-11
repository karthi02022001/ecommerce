<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'customer_id',
        'address_type',
        'full_name',
        'phone',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user (customer) that owns the address
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Scope for shipping addresses
     */
    public function scopeShipping($query)
    {
        return $query->where('address_type', 'shipping');
    }

    /**
     * Scope for billing addresses
     */
    public function scopeBilling($query)
    {
        return $query->where('address_type', 'billing');
    }

    /**
     * Scope for default addresses
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', 1);
    }

    /**
     * Get the full address formatted
     */
    public function getFullAddressAttribute()
    {
        $parts = [
            $this->address_line1,
            $this->address_line2,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country
        ];

        return implode(', ', array_filter($parts));
    }

    /**
     * Set address as default and unset others
     */
    public function setAsDefault()
    {
        // First, unset all other default addresses of the same type for this customer
        self::where('customer_id', $this->customer_id)
            ->where('address_type', $this->address_type)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => 0]);

        // Then set this address as default
        $this->update(['is_default' => 1]);
    }
}
