@extends('admin.layouts.app')

@section('title', __('Role Details'))

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">{{ $role->display_name }}</h1>
    <p class="page-subtitle">{{ __('Complete role information and permissions') }}</p>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">{{ __('Roles') }}</a></li>
            <li class="breadcrumb-item active">{{ $role->display_name }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Role Information -->
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Role Information') }}</h3>
                <div class="card-actions">
                    @if(auth('admin')->user()->hasPermission('roles.edit') && $role->name !== 'super_admin')
                    <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-warning">
                        <i class="bi bi-pencil me-2"></i>{{ __('Edit Role') }}
                    </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div style="margin-bottom: 20px;">
                            <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 5px;">{{ __('Display Name') }}</div>
                            <div style="font-size: 1.1rem; font-weight: 600;">{{ $role->display_name }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="margin-bottom: 20px;">
                            <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 5px;">{{ __('System Name') }}</div>
                            <div style="font-family: monospace; background: var(--content-bg); padding: 5px 10px; border-radius: 4px; display: inline-block;">
                                {{ $role->name }}
                            </div>
                        </div>
                    </div>
                </div>

                @if($role->description)
                <div style="margin-bottom: 20px;">
                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 5px;">{{ __('Description') }}</div>
                    <div style="padding: 12px; background: var(--content-bg); border-radius: 6px;">
                        {{ $role->description }}
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-3">
                        <div style="text-align: center; padding: 20px; border: 2px solid var(--primary-color); border-radius: 8px;">
                            <div style="font-size: 2rem; font-weight: 700; color: var(--primary-color);">
                                {{ $role->permission_count }}
                            </div>
                            <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 5px;">
                                {{ __('Permissions') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div style="text-align: center; padding: 20px; border: 2px solid var(--success-color); border-radius: 8px;">
                            <div style="font-size: 2rem; font-weight: 700; color: var(--success-color);">
                                {{ $role->admin_count }}
                            </div>
                            <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 5px;">
                                {{ __('Admin Users') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div style="text-align: center; padding: 20px; border: 2px solid {{ $role->is_active ? 'var(--success-color)' : 'var(--danger-color)' }}; border-radius: 8px;">
                            <div style="font-size: 1.5rem; margin-top: 10px;">
                                @if($role->is_active)
                                    <i class="bi bi-check-circle" style="color: var(--success-color);"></i>
                                @else
                                    <i class="bi bi-x-circle" style="color: var(--danger-color);"></i>
                                @endif
                            </div>
                            <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 5px;">
                                {{ $role->is_active ? __('Active') : __('Inactive') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div style="text-align: center; padding: 20px; border: 2px solid var(--info-color); border-radius: 8px;">
                            <div style="font-size: 1.3rem; font-weight: 700; color: var(--info-color);">
                                {{ $role->created_at->format('M Y') }}
                            </div>
                            <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 5px;">
                                {{ __('Created') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permissions by Module -->
        <div class="content-card mt-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('Permissions') }} ({{ $role->permissions->count() }})</h3>
            </div>
            <div class="card-body">
                @if($role->name === 'super_admin')
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        {{ __('Super Admin role has all system permissions by default.') }}
                    </div>
                @else
                    @if($permissionsByModule->count() > 0)
                        @foreach($permissionsByModule as $module => $permissions)
                        <div class="permission-module mb-4">
                            <div style="padding: 12px 15px; background: var(--content-bg); border-radius: 8px; border-left: 4px solid var(--primary-color); margin-bottom: 15px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <i class="bi bi-folder2-open" style="font-size: 1.2rem; color: var(--primary-color);"></i>
                                    <strong style="font-size: 1.05rem; text-transform: capitalize;">
                                        {{ str_replace('_', ' ', $module) }}
                                    </strong>
                                    <span class="badge badge-primary">{{ $permissions->count() }}</span>
                                </div>
                            </div>

                            <div class="row">
                                @foreach($permissions as $permission)
                                <div class="col-md-6 mb-2">
                                    <div style="display: flex; align-items: start; gap: 10px; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px; background: white;">
                                        <i class="bi bi-check-circle-fill" style="color: var(--success-color); font-size: 1.2rem; margin-top: 2px;"></i>
                                        <div style="flex: 1;">
                                            <div style="font-weight: 500;">{{ $permission->display_name }}</div>
                                            @if($permission->description)
                                            <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 3px;">
                                                {{ $permission->description }}
                                            </div>
                                            @endif>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div style="text-align: center; padding: 40px;">
                            <i class="bi bi-shield-x" style="font-size: 3rem; color: var(--text-muted); opacity: 0.3;"></i>
                            <p style="margin-top: 15px; color: var(--text-muted);">{{ __('No permissions assigned to this role') }}</p>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <!-- Admin Users with this Role -->
        @if($role->admins->count() > 0)
        <div class="content-card mt-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('Admin Users with this Role') }} ({{ $role->admins->count() }})</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>{{ __('Admin') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Last Login') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($role->admins as $admin)
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <img src="{{ $admin->avatar_url }}" 
                                             style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid var(--primary-color);">
                                        <strong>{{ $admin->name }}</strong>
                                    </div>
                                </td>
                                <td>{{ $admin->email }}</td>
                                <td>
                                    @if($admin->is_active)
                                        <span class="badge badge-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($admin->last_login_at)
                                        {{ $admin->last_login_at->diffForHumans() }}
                                    @else
                                        <span class="text-muted">{{ __('Never') }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
        <div class="content-card mt-3">
            <div class="card-body">
                <div style="text-align: center; padding: 40px;">
                    <i class="bi bi-people" style="font-size: 3rem; color: var(--text-muted); opacity: 0.3;"></i>
                    <p style="margin-top: 15px; color: var(--text-muted);">{{ __('No admin users assigned to this role yet') }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Quick Actions') }}</h3>
            </div>
            <div class="card-body">
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    @if(auth('admin')->user()->hasPermission('roles.edit') && $role->name !== 'super_admin')
                    <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-warning btn-block">
                        <i class="bi bi-pencil me-2"></i>{{ __('Edit Role') }}
                    </a>
                    @endif

                    @if(auth('admin')->user()->hasPermission('admins.view'))
                    <a href="{{ route('admin.admins.index', ['role' => $role->id]) }}" class="btn btn-info btn-block">
                        <i class="bi bi-people me-2"></i>{{ __('View Admin Users') }}
                    </a>
                    @endif

                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary btn-block">
                        <i class="bi bi-arrow-left me-2"></i>{{ __('Back to Roles') }}
                    </a>

                    @if(auth('admin')->user()->hasPermission('roles.delete') && !in_array($role->name, ['super_admin', 'admin', 'manager', 'staff']))
                    <hr style="margin: 10px 0;">
                    <form action="{{ route('admin.roles.destroy', $role->id) }}" 
                          method="POST" 
                          onsubmit="return confirm('{{ __('Are you sure you want to delete this role? This action cannot be undone.') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="bi bi-trash me-2"></i>{{ __('Delete Role') }}
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="content-card mt-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('Timeline') }}</h3>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-icon" style="background: var(--success-color);">
                            <i class="bi bi-plus-circle"></i>
                        </div>
                        <div class="timeline-content">
                            <div style="font-weight: 600;">{{ __('Role Created') }}</div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);">
                                {{ $role->created_at->format('F d, Y') }}
                            </div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);">
                                {{ $role->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>

                    @if($role->created_at != $role->updated_at)
                    <div class="timeline-item">
                        <div class="timeline-icon" style="background: var(--primary-color);">
                            <i class="bi bi-pencil"></i>
                        </div>
                        <div class="timeline-content">
                            <div style="font-weight: 600;">{{ __('Last Updated') }}</div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);">
                                {{ $role->updated_at->format('F d, Y') }}
                            </div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);">
                                {{ $role->updated_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Role Statistics -->
        <div class="content-card mt-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('Statistics') }}</h3>
            </div>
            <div class="card-body">
                <div style="display: flex; justify-content: space-between; padding: 15px; border-bottom: 1px solid var(--border-color);">
                    <span style="color: var(--text-muted);">{{ __('Total Permissions') }}</span>
                    <strong style="color: var(--primary-color);">{{ $role->permission_count }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 15px; border-bottom: 1px solid var(--border-color);">
                    <span style="color: var(--text-muted);">{{ __('Active Admins') }}</span>
                    <strong style="color: var(--success-color);">{{ $role->admins->where('is_active', true)->count() }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 15px; border-bottom: 1px solid var(--border-color);">
                    <span style="color: var(--text-muted);">{{ __('Inactive Admins') }}</span>
                    <strong style="color: var(--text-muted);">{{ $role->admins->where('is_active', false)->count() }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 15px;">
                    <span style="color: var(--text-muted);">{{ __('Permission Modules') }}</span>
                    <strong>{{ $permissionsByModule->count() }}</strong>
                </div>
            </div>
        </div>

        <!-- Warnings -->
        @if(in_array($role->name, ['super_admin', 'admin', 'manager', 'staff']))
        <div class="content-card mt-3">
            <div class="card-body">
                <div style="padding: 15px; background: rgba(var(--info-color), 0.1); border-left: 3px solid var(--info-color); border-radius: 6px;">
                    <i class="bi bi-info-circle" style="color: var(--info-color);"></i>
                    <strong style="margin-left: 8px; color: var(--info-color);">{{ __('System Role') }}</strong>
                    <p style="margin-top: 8px; font-size: 0.85rem; color: var(--text-dark);">
                        {{ __('This is a default system role and cannot be deleted.') }}
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 40px;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -24px;
    top: 30px;
    width: 2px;
    height: calc(100% - 10px);
    background: var(--border-color);
}

.timeline-icon {
    position: absolute;
    left: -35px;
    top: 0;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
}

.timeline-content {
    padding: 5px 0;
}

.btn-block {
    width: 100%;
    justify-content: center;
}
</style>
@endsection