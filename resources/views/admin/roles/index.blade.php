@extends('admin.layouts.app')

@section('title', __('Roles & Permissions'))

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">{{ __('Roles & Permissions') }}</h1>
    <p class="page-subtitle">{{ __('Manage admin roles and their permissions') }}</p>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Roles') }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-12">
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">{{ __('All Roles') }}</h3>
                <div class="card-actions">
                    @if(auth('admin')->user()->hasPermission('roles.create'))
                    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>{{ __('Create New Role') }}
                    </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>{{ __('Role Name') }}</th>
                                <th>{{ __('Description') }}</th>
                                <th>{{ __('Permissions') }}</th>
                                <th>{{ __('Admin Users') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Created') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $role)
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 40px; height: 40px; border-radius: 8px; background: 
                                            @if($role->name === 'super_admin') linear-gradient(135deg, #e74c3c, #c0392b)
                                            @elseif($role->name === 'admin') linear-gradient(135deg, #3498db, #2980b9)
                                            @elseif($role->name === 'manager') linear-gradient(135deg, #f39c12, #e67e22)
                                            @else linear-gradient(135deg, var(--text-muted), #5a6268)
                                            @endif;
                                            display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem;">
                                            <i class="bi bi-shield-lock"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $role->display_name }}</strong>
                                            <div style="font-size: 0.85rem; color: var(--text-muted);">{{ $role->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span style="color: var(--text-muted);">
                                        {{ Str::limit($role->description ?? __('No description'), 50) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-primary" style="font-size: 0.9rem;">
                                        <i class="bi bi-key me-1"></i>{{ $role->permission_count }} {{ __('Permissions') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-info" style="font-size: 0.9rem;">
                                        <i class="bi bi-people me-1"></i>{{ $role->admin_count }} {{ __('Users') }}
                                    </span>
                                </td>
                                <td>
                                    @if($role->is_active)
                                        <span class="badge badge-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                                    @endif
                                </td>
                                <td>{{ $role->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div style="display: flex; gap: 5px;">
                                        @if(auth('admin')->user()->hasPermission('roles.view'))
                                        <a href="{{ route('admin.roles.show', $role->id) }}" 
                                           class="btn btn-sm btn-icon btn-info" 
                                           title="{{ __('View Details') }}">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @endif

                                        @if(auth('admin')->user()->hasPermission('roles.edit') && $role->name !== 'super_admin')
                                        <a href="{{ route('admin.roles.edit', $role->id) }}" 
                                           class="btn btn-sm btn-icon btn-warning" 
                                           title="{{ __('Edit Role') }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @endif

                                        @if(auth('admin')->user()->hasPermission('roles.delete') && !in_array($role->name, ['super_admin', 'admin', 'manager', 'staff']))
                                        <form action="{{ route('admin.roles.destroy', $role->id) }}" 
                                              method="POST" 
                                              style="display: inline;"
                                              onsubmit="return confirm('{{ __('Are you sure you want to delete this role?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-icon btn-danger" 
                                                    title="{{ __('Delete Role') }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted" style="padding: 40px;">
                                    <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p style="margin-top: 10px;">{{ __('No roles found') }}</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Permissions Overview -->
        <div class="content-card mt-4">
            <div class="card-header">
                <h3 class="card-title">{{ __('Permissions Overview') }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    @php
                        $modules = \App\Models\AdminPermission::getModules();
                    @endphp
                    @foreach($modules as $module)
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div style="padding: 20px; border: 1px solid var(--border-color); border-radius: 8px;">
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                                <i class="bi bi-folder" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                                <strong style="text-transform: capitalize;">{{ str_replace('_', ' ', $module) }}</strong>
                            </div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);">
                                {{ \App\Models\AdminPermission::where('module', $module)->count() }} {{ __('permissions') }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection