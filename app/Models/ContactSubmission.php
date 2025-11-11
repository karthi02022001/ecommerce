<?php
// app/Models/ContactSubmission.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactSubmission extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'status',
        'ip_address',
        'user_agent',
        'replied_at',
        'replied_by',
        'admin_notes',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];

    /**
     * Get the admin who replied
     */
    public function repliedBy()
    {
        return $this->belongsTo(Admin::class, 'replied_by');
    }

    /**
     * Scope for new submissions
     */
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    /**
     * Scope for read submissions
     */
    public function scopeRead($query)
    {
        return $query->where('status', 'read');
    }

    /**
     * Scope for replied submissions
     */
    public function scopeReplied($query)
    {
        return $query->where('status', 'replied');
    }

    /**
     * Scope for archived submissions
     */
    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    /**
     * Mark as read
     */
    public function markAsRead()
    {
        if ($this->status === 'new') {
            $this->update(['status' => 'read']);
        }
    }

    /**
     * Mark as replied
     */
    public function markAsReplied($adminId)
    {
        $this->update([
            'status' => 'replied',
            'replied_at' => now(),
            'replied_by' => $adminId,
        ]);
    }
}
