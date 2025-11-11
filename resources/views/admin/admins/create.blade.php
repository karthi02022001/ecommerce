@extends('admin.layouts.app')

@section('title', __('Create Admin User'))

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">{{ __('Create Admin User') }}</h1>
    <p class="page-subtitle">{{ __('Add a new admin user to the system') }}</p>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.admins.index') }}">{{ __('Admin Users') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Create') }}</li>
        </ol>
    </nav>
</div>   

<div class="row">
    <div class="col-lg-8">
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Admin Information') }}</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.admins.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Name -->
                    <div class="form-group">
                        <label for="name" class="form-label">
                            {{ __('Full Name') }} <span style="color: var(--danger-color);">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}"
                            required
                            autofocus
                        >
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email" class="form-label">
                            {{ __('Email Address') }} <span style="color: var(--danger-color);">*</span>
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}"
                            required
                        >
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div class="form-group">
                        <label for="phone" class="form-label">{{ __('Phone Number') }}</label>
                        <input 
                            type="text" 
                            name="phone" 
                            id="phone" 
                            class="form-control @error('phone') is-invalid @enderror"
                            value="{{ old('phone') }}"
                            placeholder="+91 1234567890"
                        >
                        @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div class="form-group">
                        <label for="role_id" class="form-label">
                            {{ __('Role') }} <span style="color: var(--danger-color);">*</span>
                        </label>
                        <select 
                            name="role_id" 
                            id="role_id" 
                            class="form-select @error('role_id') is-invalid @enderror"
                            required
                        >
                            <option value="">{{ __('Select Role') }}</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}" 
                                    data-display-name="{{ $role->display_name }}"
                                    data-description="{{ $role->description ?? '' }}"
                                    {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->display_name }}
                            </option>
                            @endforeach
                        </select>
                        @error('role_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text">{{ __('Select the role to assign permissions') }}</small>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label for="password" class="form-label">
                            {{ __('Password') }} <span style="color: var(--danger-color);">*</span>
                        </label>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            class="form-control @error('password') is-invalid @enderror"
                            required
                        >
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text">{{ __('Minimum 8 characters') }}</small>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">
                            {{ __('Confirm Password') }} <span style="color: var(--danger-color);">*</span>
                        </label>
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            id="password_confirmation" 
                            class="form-control"
                            required
                        >
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <div class="form-check">
                            <input 
                                type="checkbox" 
                                name="is_active" 
                                id="is_active" 
                                class="form-check-input"
                                {{ old('is_active', true) ? 'checked' : '' }}
                            >
                            <label for="is_active" class="form-check-label">
                                {{ __('Active') }}
                            </label>
                        </div>
                        <small class="form-text">{{ __('Inactive users cannot login') }}</small>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex gap-2 justify-content-end mt-4">
                        <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> {{ __('Create Admin User') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar Information -->
    <div class="col-lg-4">
        <!-- Role Information -->
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Role Information') }}</h3>
            </div>
            <div class="card-body">
                <div id="roleInfo" style="display: none;">
                    <div class="alert alert-info">
                        <strong id="roleDisplayName"></strong>
                        <p id="roleDescription" class="mb-0 mt-2"></p>
                    </div>
                </div>
                <p class="text-muted" id="noRoleSelected">{{ __('Select a role to see details') }}</p>
            </div>
        </div>

        <!-- Help Card -->
        <div class="content-card mt-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('Guidelines') }}</h3>
            </div>
            <div class="card-body">
                <ul style="padding-left: 20px; margin: 0;">
                    <li class="mb-2">{{ __('Choose a strong password with at least 8 characters') }}</li>
                    <li class="mb-2">{{ __('Select appropriate role based on responsibilities') }}</li>
                    <li class="mb-2">{{ __('Inactive users cannot access the admin panel') }}</li>
                    <li class="mb-2">{{ __('Email will be used for login') }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role_id');
    const roleInfo = document.getElementById('roleInfo');
    const noRoleSelected = document.getElementById('noRoleSelected');
    
    roleSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value && selectedOption) {
            const displayName = selectedOption.getAttribute('data-display-name');
            const description = selectedOption.getAttribute('data-description');
            
            document.getElementById('roleDisplayName').textContent = displayName || '';
            document.getElementById('roleDescription').textContent = description || '{{ __("No description available") }}';
            
            roleInfo.style.display = 'block';
            noRoleSelected.style.display = 'none';
        } else {
            roleInfo.style.display = 'none';
            noRoleSelected.style.display = 'block';
        }
    });
});
</script>
@endpush