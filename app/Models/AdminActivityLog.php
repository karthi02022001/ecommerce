<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'action',
        'module',
        'description',
        'ip_address',
        'user_agent',
    ];

    public $timestamps = false;

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relationships
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    // Scopes
    public function scopeByAdmin($query, int $adminId)
    {
        return $query->where('admin_id', $adminId);
    }

    public function scopeByModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    public function scopeRecent($query, int $limit = 50)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // Accessors
    public function getActionIconAttribute(): string
    {
        $icons = [
            'create' => 'bi-plus-circle',
            'update' => 'bi-pencil-square',
            'delete' => 'bi-trash',
            'view' => 'bi-eye',
            'login' => 'bi-box-arrow-in-right',
            'logout' => 'bi-box-arrow-right',
        ];

        foreach ($icons as $action => $icon) {
            if (str_contains(strtolower($this->action), $action)) {
                return $icon;
            }
        }

        return 'bi-activity';
    }

    public function getActionColorAttribute(): string
    {
        $colors = [
            'create' => 'success',
            'update' => 'primary',
            'delete' => 'danger',
            'view' => 'info',
            'login' => 'success',
            'logout' => 'secondary',
        ];

        foreach ($colors as $action => $color) {
            if (str_contains(strtolower($this->action), $action)) {
                return $color;
            }
        }

        return 'secondary';
    }
}
