@extends('admin.layouts.app')

@section('title', __('Admin User Details'))

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">{{ __('Admin User Details') }}</h1>
    <p class="page-subtitle">{{ __('View complete admin user information') }}</p>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.admins.index') }}">{{ __('Admin Users') }}</a></li>
            <li class="breadcrumb-item active">{{ $adminUser->name }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <!-- Profile Card -->
    <div class="col-lg-4">
        <div class="content-card">
            <div class="card-body text-center">
                <img 
                    src="{{ $adminUser->avatar_url }}" 
                    alt="{{ $adminUser->name }}"
                    style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid var(--primary-color); margin-bottom: 20px;"
                >
                
                <h3 style="margin-bottom: 5px;">{{ $adminUser->name }}</h3>
                <p style="color: var(--text-muted); margin-bottom: 10px;">{{ $adminUser->email }}</p>
                
                <span class="badge {{ $adminUser->isSuperAdmin() ? 'badge-danger' : ($adminUser->isAdmin() ? 'badge-warning' : 'badge-secondary') }}" style="font-size: 0.9rem;">
                    {{ $adminUser->role_name }}
                </span>
                
                @if($adminUser->is_active)
                <span class="badge badge-success ms-2" style="font-size: 0.9rem;">{{ __('Active') }}</span>
                @else
                <span class="badge badge-danger ms-2" style="font-size: 0.9rem;">{{ __('Inactive') }}</span>
                @endif
                
                @if($adminUser->id === auth('admin')->id())
                <span class="badge badge-info ms-2" style="font-size: 0.9rem;">{{ __('You') }}</span>
                @endif

                <!-- Action Buttons -->
                <div class="d-flex gap-2 justify-content-center mt-4">
                    @if(auth('admin')->user()->hasPermission('admins.edit'))
                    @if(!$adminUser->isSuperAdmin() || auth('admin')->user()->isSuperAdmin())
                    <a href="{{ route('admin.admins.edit', $adminUser->id) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> {{ __('Edit') }}
                    </a>
                    @endif
                    @endif

                    @if(auth('admin')->user()->hasPermission('admins.delete'))
                    @if($adminUser->id !== auth('admin')->id() && (!$adminUser->isSuperAdmin() || auth('admin')->user()->isSuperAdmin()))
                    <form action="{{ route('admin.admins.destroy', $adminUser->id) }}" 
                          method="POST" 
                          onsubmit="return confirm('{{ __('Are you sure you want to delete this admin user?') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> {{ __('Delete') }}
                        </button>
                    </form>
                    @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="content-card mt-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('Contact Information') }}</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong><i class="bi bi-envelope me-2"></i>{{ __('Email') }}:</strong>
                    <div class="mt-1">{{ $adminUser->email }}</div>
                </div>
                <div class="mb-3">
                    <strong><i class="bi bi-phone me-2"></i>{{ __('Phone') }}:</strong>
                    <div class="mt-1">{{ $adminUser->phone ?? __('Not provided') }}</div>
                </div>
                <div>
                    <strong><i class="bi bi-check-circle me-2"></i>{{ __('Email Verified') }}:</strong>
                    <div class="mt-1">
                        @if($adminUser->email_verified_at)
                        <span class="badge badge-success">{{ __('Yes') }}</span>
                        <small class="text-muted d-block mt-1">
                            {{ $adminUser->email_verified_at->format('M d, Y') }}
                        </small>
                        @else
                        <span class="badge badge-warning">{{ __('No') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Information -->
        <div class="content-card mt-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('Account Information') }}</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong><i class="bi bi-calendar-plus me-2"></i>{{ __('Member Since') }}:</strong>
                    <div class="mt-1">{{ $adminUser->created_at->format('M d, Y') }}</div>
                </div>
                <div class="mb-3">
                    <strong><i class="bi bi-clock-history me-2"></i>{{ __('Last Login') }}:</strong>
                    <div class="mt-1">
                        @if($adminUser->last_login_at)
                        {{ $adminUser->last_login_at->format('M d, Y h:i A') }}
                        @else
                        {{ __('Never') }}
                        @endif
                    </div>
                </div>
                <div>
                    <strong><i class="bi bi-geo-alt me-2"></i>{{ __('Last Login IP') }}:</strong>
                    <div class="mt-1">
                        @if($adminUser->last_login_ip)
                        <code>{{ $adminUser->last_login_ip }}</code>
                        @else
                        {{ __('Unknown') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Role & Permissions -->
        @if(auth('admin')->user()->hasPermission('roles.view'))
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Role & Permissions') }}</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h5 class="mb-2">
                        <i class="bi bi-shield-lock me-2"></i>{{ $adminUser->role->display_name }}
                    </h5>
                    @if($adminUser->role->description)
                    <p class="mb-0">{{ $adminUser->role->description }}</p>
                    @endif
                </div>

                @php
                    $permissionsCount = $adminUser->role->permissions->count();
                @endphp

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="stat-card">
                            <div class="stat-label">{{ __('Total Permissions') }}</div>
                            <div class="stat-value" style="font-size: 1.5rem;">
                                {{ $permissionsCount }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="stat-card success">
                            <div class="stat-label">{{ __('Active Permissions') }}</div>
                            <div class="stat-value" style="font-size: 1.5rem;">
                                {{ $permissionsCount }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissions List -->
                @if($permissionsCount > 0)
                <div class="mt-4">
                    <h5 class="mb-3">{{ __('Assigned Permissions') }}</h5>
                    
                    @php
                        $permissionsByModule = $adminUser->role->permissions->groupBy('module');
                    @endphp
                    
                    @foreach($permissionsByModule as $module => $permissions)
                    <div class="mb-4">
                        <h6 class="text-uppercase" style="color: var(--primary-color); font-size: 0.85rem; margin-bottom: 10px;">
                            {{ __(ucfirst($module)) }}
                        </h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($permissions as $permission)
                            <span class="badge badge-primary" style="padding: 6px 12px; font-size: 0.85rem;">
                                <i class="bi bi-check-circle me-1"></i>{{ $permission->display_name }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Recent Activity -->
        <div class="content-card mt-4">
            <div class="card-header">
                <h3 class="card-title">{{ __('Recent Activity') }}</h3>
            </div>
            <div class="card-body">
                @if($recentActivity->count() > 0)
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;"></th>
                                <th>{{ __('Action') }}</th>
                                <th>{{ __('Module') }}</th>
                                <th>{{ __('Description') }}</th>
                                <th>{{ __('Date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentActivity as $activity)
                            <tr>
                                <td>
                                    <i class="bi {{ $activity->action_icon }} text-{{ $activity->action_color }}"></i>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $activity->action_color }}">
                                        {{ __(ucfirst($activity->action)) }}
                                    </span>
                                </td>
                                <td>{{ __(ucfirst($activity->module)) }}</td>
                                <td>{{ $activity->description ?? '—' }}</td>
                                <td style="font-size: 0.9rem; color: var(--text-muted);">
                                    {{ $activity->created_at->format('M d, Y h:i A') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 text-center">
                    @if(auth('admin')->user()->isSuperAdmin())
                    <a href="{{ route('admin.activity-log', ['admin' => $adminUser->id]) }}" class="btn btn-sm btn-outline-primary">
                        {{ __('View All Activity') }}
                    </a>
                    @endif
                </div>
                @else
                <p class="text-center text-muted" style="padding: 40px;">
                    <i class="bi bi-activity" style="font-size: 3rem; display: block; margin-bottom: 10px;"></i>
                    {{ __('No recent activity') }}
                </p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection