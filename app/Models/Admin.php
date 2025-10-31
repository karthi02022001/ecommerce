<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'admin';

    protected $fillable = [
        'role_id',
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'is_active',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    // Relationships
    public function role()
    {
        return $this->belongsTo(AdminRole::class, 'role_id');
    }

    public function activityLogs()
    {
        return $this->hasMany(AdminActivityLog::class, 'admin_id');
    }

    // Permission Checking
    public function hasPermission(string $permission): bool
    {
        if (!$this->is_active) {
            return false;
        }

        return $this->role->permissions()
            ->where('name', $permission)
            ->exists();
    }

    public function hasAnyPermission(array $permissions): bool
    {
        if (!$this->is_active) {
            return false;
        }

        return $this->role->permissions()
            ->whereIn('name', $permissions)
            ->exists();
    }

    public function hasAllPermissions(array $permissions): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $count = $this->role->permissions()
            ->whereIn('name', $permissions)
            ->count();

        return $count === count($permissions);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role->name === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role->name, ['super_admin', 'admin']);
    }

    public function isManager(): bool
    {
        return $this->role->name === 'manager';
    }

    public function isStaff(): bool
    {
        return $this->role->name === 'staff';
    }

    // Accessors
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=20b2aa&color=ffffff';
    }

    public function getRoleNameAttribute(): string
    {
        return $this->role->display_name ?? 'Unknown';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, string $roleName)
    {
        return $query->whereHas('role', function ($q) use ($roleName) {
            $q->where('name', $roleName);
        });
    }

    // Log Activity
    public function logActivity(string $action, string $module, ?string $description = null)
    {
        return AdminActivityLog::create([
            'admin_id' => $this->id,
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    // Update Last Login
    public function updateLastLogin()
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);
    }
}
