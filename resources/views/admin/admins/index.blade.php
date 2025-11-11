@extends('admin.layouts.app')

@section('title', __('Admin Users'))

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">{{ __('Admin Users') }}</h1>
    <p class="page-subtitle">{{ __('Manage admin users and their roles') }}</p>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Admin Users') }}</li>
        </ol>
    </nav>
</div>

<!-- Actions Bar -->
<div class="content-card mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-6">
                <form action="{{ route('admin.admins.index') }}" method="GET" class="d-flex gap-2">
                    <input 
                        type="text" 
                        name="search" 
                        class="form-control" 
                        placeholder="{{ __('Search by name or email...') }}"
                        value="{{ request('search') }}"
                    >
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> {{ __('Search') }}
                    </button>
                    @if(request()->hasAny(['search', 'role', 'status']))
                    <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> {{ __('Clear') }}
                    </a>
                    @endif
                </form>
            </div>
            <div class="col-md-6 text-end">
                @if(auth('admin')->user()->hasPermission('admins.create'))
                <a href="{{ route('admin.admins.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> {{ __('Add New Admin') }}
                </a>
                @endif
            </div>
        </div>

        <!-- Filters -->
        <div class="row mt-3">
            <div class="col-md-12">
                <form action="{{ route('admin.admins.index') }}" method="GET" class="d-flex gap-2 flex-wrap">
                    @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    
                    <!-- Role Filter -->
                    <select name="role" class="form-select" style="width: auto;">
                        <option value="">{{ __('All Roles') }}</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                            {{ $role->display_name }}
                        </option>
                        @endforeach
                    </select>

                    <!-- Status Filter -->
                    <select name="status" class="form-select" style="width: auto;">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                    </select>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel"></i> {{ __('Filter') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Admin Users Table -->
<div class="content-card">
    <div class="card-header">
        <h3 class="card-title">
            {{ __('Admin Users List') }}
            <span class="badge badge-primary ms-2">{{ $admins->total() }}</span>
        </h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">{{ __('ID') }}</th>
                        <th>{{ __('Admin') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Phone') }}</th>
                        <th>{{ __('Role') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Last Login') }}</th>
                        <th style="width: 150px;">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admins as $adminUser)
                    <tr>
                        <td><strong>#{{ $adminUser->id }}</strong></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img 
                                    src="{{ $adminUser->avatar_url }}" 
                                    alt="{{ $adminUser->name }}"
                                    style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; margin-right: 12px;"
                                >
                                <div>
                                    <div style="font-weight: 600;">{{ $adminUser->name }}</div>
                                    @if($adminUser->id === auth('admin')->id())
                                    <small class="badge badge-info">{{ __('You') }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $adminUser->email }}</td>
                        <td>{{ $adminUser->phone ?? '—' }}</td>
                        <td>
                            <span class="badge {{ $adminUser->isSuperAdmin() ? 'badge-danger' : ($adminUser->isAdmin() ? 'badge-warning' : 'badge-secondary') }}">
                                {{ $adminUser->role_name }}
                            </span>
                        </td>
                        <td>
                            @if($adminUser->is_active)
                            <span class="badge badge-success">{{ __('Active') }}</span>
                            @else
                            <span class="badge badge-danger">{{ __('Inactive') }}</span>
                            @endif
                        </td>
                        <td>
                            @if($adminUser->last_login_at)
                            <div style="font-size: 0.9rem;">{{ $adminUser->last_login_at->format('M d, Y') }}</div>
                            <small style="color: var(--text-muted);">{{ $adminUser->last_login_at->format('h:i A') }}</small>
                            @else
                            <span style="color: var(--text-muted);">{{ __('Never') }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.admins.show', $adminUser->id) }}" 
                                   class="btn btn-sm btn-icon btn-info" 
                                   title="{{ __('View') }}">
                                    <i class="bi bi-eye"></i>
                                </a>
                                
                                @if(auth('admin')->user()->hasPermission('admins.edit'))
                                @if(!$adminUser->isSuperAdmin() || auth('admin')->user()->isSuperAdmin())
                                <a href="{{ route('admin.admins.edit', $adminUser->id) }}" 
                                   class="btn btn-sm btn-icon btn-primary"
                                   title="{{ __('Edit') }}">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif
                                @endif

                                @if(auth('admin')->user()->hasPermission('admins.delete'))
                                @if($adminUser->id !== auth('admin')->id() && (!$adminUser->isSuperAdmin() || auth('admin')->user()->isSuperAdmin()))
                                <form action="{{ route('admin.admins.destroy', $adminUser->id) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('{{ __('Are you sure you want to delete this admin user?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-icon btn-danger"
                                            title="{{ __('Delete') }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted" style="padding: 40px;">
                            <i class="bi bi-inbox" style="font-size: 3rem; display: block; margin-bottom: 10px;"></i>
                            {{ __('No admin users found') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($admins->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $admins->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-label">{{ __('Total Admins') }}</div>
                <div class="stat-icon">
                    <i class="bi bi-people"></i>
                </div>
            </div>
            <div class="stat-value">{{ $admins->total() }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card success">
            <div class="stat-header">
                <div class="stat-label">{{ __('Active') }}</div>
                <div class="stat-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
            <div class="stat-value">{{ \App\Models\Admin::where('is_active', true)->count() }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card danger">
            <div class="stat-header">
                <div class="stat-label">{{ __('Inactive') }}</div>
                <div class="stat-icon">
                    <i class="bi bi-x-circle"></i>
                </div>
            </div>
            <div class="stat-value">{{ \App\Models\Admin::where('is_active', false)->count() }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card info">
            <div class="stat-header">
                <div class="stat-label">{{ __('Roles') }}</div>
                <div class="stat-icon">
                    <i class="bi bi-shield-lock"></i>
                </div>
            </div>
            <div class="stat-value">{{ \App\Models\AdminRole::count() }}</div>
        </div>
    </div>
</div>
@endsection