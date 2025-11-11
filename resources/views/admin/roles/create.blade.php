@extends('admin.layouts.app')

@section('title', __('Create Role'))

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">{{ __('Create New Role') }}</h1>
    <p class="page-subtitle">{{ __('Define a new admin role with specific permissions') }}</p>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">{{ __('Roles') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Create') }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf

            <!-- Basic Information -->
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Basic Information') }}</h3>
                </div>
                <div class="card-body">
                    <!-- Display Name -->
                    <div class="form-group">
                        <label for="display_name" class="form-label">{{ __('Role Display Name') }} <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="display_name" 
                               id="display_name" 
                               class="form-control @error('display_name') is-invalid @enderror" 
                               value="{{ old('display_name') }}" 
                               placeholder="{{ __('e.g., Store Manager') }}"
                               required>
                        @error('display_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text">{{ __('A friendly name for the role that will be displayed in the admin panel') }}</small>
                    </div>

                    <!-- Description -->
                    <div class="form-group">
                        <label for="description" class="form-label">{{ __('Description') }}</label>
                        <textarea name="description" 
                                  id="description" 
                                  class="form-control @error('description') is-invalid @enderror" 
                                  rows="3" 
                                  placeholder="{{ __('Describe the purpose and scope of this role...') }}">{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" 
                                   name="is_active" 
                                   id="is_active" 
                                   class="form-check-input" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label for="is_active" class="form-check-label">
                                {{ __('Active') }} - {{ __('Admins can be assigned to this role') }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permissions -->
            <div class="content-card mt-3">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Assign Permissions') }}</h3>
                    <div class="card-actions">
                        <button type="button" class="btn btn-sm btn-outline" onclick="toggleAllPermissions(true)">
                            <i class="bi bi-check-all me-1"></i>{{ __('Select All') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-outline" onclick="toggleAllPermissions(false)">
                            <i class="bi bi-x-circle me-1"></i>{{ __('Clear All') }}
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @foreach($permissions as $module => $modulePermissions)
                    <div class="permission-module mb-4">
                        <div class="module-header" style="padding: 15px; background: var(--content-bg); border-radius: 8px; margin-bottom: 15px;">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <i class="bi bi-folder2-open" style="font-size: 1.3rem; color: var(--primary-color);"></i>
                                    <strong style="font-size: 1.1rem; text-transform: capitalize;">
                                        {{ str_replace('_', ' ', $module) }}
                                    </strong>
                                    <span class="badge badge-secondary">{{ $modulePermissions->count() }} {{ __('permissions') }}</span>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" 
                                           class="form-check-input module-toggle" 
                                           id="module_{{ $module }}" 
                                           onclick="toggleModule('{{ $module }}')">
                                    <label for="module_{{ $module }}" class="form-check-label">
                                        {{ __('Select All') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            @foreach($modulePermissions as $permission)
                            <div class="col-md-6">
                                <div class="form-check" style="padding: 12px; border: 1px solid var(--border-color); border-radius: 6px; margin-bottom: 10px; transition: all 0.2s;">
                                    <input type="checkbox" 
                                           name="permissions[]" 
                                           value="{{ $permission->id }}" 
                                           id="permission_{{ $permission->id }}" 
                                           class="form-check-input permission-checkbox module-{{ $module }}"
                                           {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                    <label for="permission_{{ $permission->id }}" class="form-check-label" style="cursor: pointer;">
                                        <strong>{{ $permission->display_name }}</strong>
                                        @if($permission->description)
                                        <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 3px;">
                                            {{ $permission->description }}
                                        </div>
                                        @endif
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach

                    @error('permissions')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="content-card mt-3">
                <div class="card-body">
                    <div style="display: flex; gap: 10px; justify-content: flex-end;">
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-2"></i>{{ __('Cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>{{ __('Create Role') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Help Sidebar -->
    <div class="col-lg-4">
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Role Guidelines') }}</h3>
            </div>
            <div class="card-body">
                <div style="margin-bottom: 20px;">
                    <i class="bi bi-info-circle" style="color: var(--primary-color); font-size: 1.2rem;"></i>
                    <strong style="margin-left: 8px;">{{ __('What is a Role?') }}</strong>
                    <p style="margin-top: 8px; color: var(--text-muted); font-size: 0.9rem;">
                        {{ __('A role defines a set of permissions that determine what actions an admin user can perform in the system.') }}
                    </p>
                </div>

                <div style="margin-bottom: 20px;">
                    <i class="bi bi-shield-check" style="color: var(--success-color); font-size: 1.2rem;"></i>
                    <strong style="margin-left: 8px;">{{ __('Best Practices') }}</strong>
                    <ul style="margin-top: 8px; color: var(--text-muted); font-size: 0.9rem; padding-left: 30px;">
                        <li>{{ __('Give roles clear, descriptive names') }}</li>
                        <li>{{ __('Only grant necessary permissions') }}</li>
                        <li>{{ __('Review permissions regularly') }}</li>
                        <li>{{ __('Document role purposes in descriptions') }}</li>
                    </ul>
                </div>

                <div style="padding: 15px; background: rgba(var(--warning-color), 0.1); border-left: 3px solid var(--warning-color); border-radius: 6px;">
                    <i class="bi bi-exclamation-triangle" style="color: var(--warning-color);"></i>
                    <strong style="margin-left: 8px; color: var(--warning-color);">{{ __('Important') }}</strong>
                    <p style="margin-top: 8px; font-size: 0.85rem; color: var(--text-dark);">
                        {{ __('Be careful when assigning permissions. Overly permissive roles can pose security risks.') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Permission Modules Info -->
        <div class="content-card mt-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('Permission Modules') }}</h3>
            </div>
            <div class="card-body">
                <div style="font-size: 0.9rem; color: var(--text-muted);">
                    @foreach($permissions as $module => $modulePermissions)
                    <div style="padding: 8px 0; border-bottom: 1px solid var(--border-color);">
                        <strong style="color: var(--text-dark); text-transform: capitalize;">{{ str_replace('_', ' ', $module) }}</strong>
                        <span style="float: right;" class="badge badge-secondary">{{ $modulePermissions->count() }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle all permissions
    function toggleAllPermissions(checked) {
        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.checked = checked;
        });
        document.querySelectorAll('.module-toggle').forEach(checkbox => {
            checkbox.checked = checked;
        });
    }

    // Toggle module permissions
    function toggleModule(module) {
        const moduleToggle = document.getElementById('module_' + module);
        const checkboxes = document.querySelectorAll('.module-' + module);
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = moduleToggle.checked;
        });
    }

    // Update module toggle when individual permissions change
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const module = this.classList[2]; // module-{name}
                const moduleName = module.replace('module-', '');
                const moduleCheckboxes = document.querySelectorAll('.' + module);
                const moduleToggle = document.getElementById('module_' + moduleName);
                
                const allChecked = Array.from(moduleCheckboxes).every(cb => cb.checked);
                moduleToggle.checked = allChecked;
            });
        });

        // Add hover effect to permission boxes
        document.querySelectorAll('.form-check').forEach(box => {
            box.addEventListener('mouseenter', function() {
                this.style.borderColor = 'var(--primary-color)';
                this.style.background = 'rgba(var(--primary-rgb), 0.05)';
            });
            box.addEventListener('mouseleave', function() {
                this.style.borderColor = 'var(--border-color)';
                this.style.background = 'transparent';
            });
        });
    });
</script>
@endpush