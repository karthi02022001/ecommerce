<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'module',
    ];

    // Relationships
    public function roles()
    {
        return $this->belongsToMany(
            AdminRole::class,
            'admin_role_permissions',
            'permission_id',
            'role_id'
        )->withTimestamps();
    }

    // Scopes
    public function scopeByModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    // Static helper to get all modules
    public static function getModules(): array
    {
        return self::distinct('module')
            ->pluck('module')
            ->toArray();
    }

    // Static helper to get permissions by module
    public static function getByModule(string $module)
    {
        return self::where('module', $module)->get();
    }

    // Accessors
    public function getRoleCountAttribute(): int
    {
        return $this->roles()->count();
    }
}
