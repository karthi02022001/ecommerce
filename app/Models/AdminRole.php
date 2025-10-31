<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminRole extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function admins()
    {
        return $this->hasMany(Admin::class, 'role_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(
            AdminPermission::class,
            'admin_role_permissions',
            'role_id',
            'permission_id'
        )->withTimestamps();
    }

    // Permission Management
    public function givePermissionTo($permission)
    {
        $permissionModel = $permission instanceof AdminPermission 
            ? $permission 
            : AdminPermission::where('name', $permission)->firstOrFail();

        $this->permissions()->syncWithoutDetaching([$permissionModel->id]);
        
        return $this;
    }

    public function revokePermissionTo($permission)
    {
        $permissionModel = $permission instanceof AdminPermission 
            ? $permission 
            : AdminPermission::where('name', $permission)->firstOrFail();

        $this->permissions()->detach($permissionModel->id);
        
        return $this;
    }

    public function syncPermissions(array $permissions)
    {
        $permissionIds = AdminPermission::whereIn('name', $permissions)
            ->pluck('id')
            ->toArray();

        $this->permissions()->sync($permissionIds);
        
        return $this;
    }

    public function hasPermission(string $permission): bool
    {
        return $this->permissions()
            ->where('name', $permission)
            ->exists();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Accessors
    public function getAdminCountAttribute(): int
    {
        return $this->admins()->count();
    }

    public function getPermissionCountAttribute(): int
    {
        return $this->permissions()->count();
    }
}
