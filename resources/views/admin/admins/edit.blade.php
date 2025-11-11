@extends('admin.layouts.app')

@section('title', __('Edit Admin User'))

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">{{ __('Edit Admin User') }}</h1>
    <p class="page-subtitle">{{ __('Update admin user information') }}</p>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.admins.index') }}">{{ __('Admin Users') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Edit') }}</li>
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
                <form action="{{ route('admin.admins.update', $adminUser->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Avatar -->
                    <div class="form-group">
                        <label class="form-label">{{ __('Profile Picture') }}</label>
                        <div class="d-flex align-items-center gap-3">
                            <img 
                                src="{{ $adminUser->avatar_url }}" 
                                alt="{{ $adminUser->name }}"
                                id="avatarPreview"
                                style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid var(--border-color);"
                            >
                            <div>
                                <input 
                                    type="file" 
                                    name="avatar" 
                                    id="avatar" 
                                    class="form-control @error('avatar') is-invalid @enderror"
                                    accept="image/*"
                                    style="max-width: 300px;"
                                >
                                @error('avatar')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($adminUser->avatar)
                                <div class="form-check mt-2">
                                    <input 
                                        type="checkbox" 
                                        name="remove_avatar" 
                                        id="remove_avatar" 
                                        class="form-check-input"
                                    >
                                    <label for="remove_avatar" class="form-check-label">
                                        {{ __('Remove current avatar') }}
                                    </label>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

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
                            value="{{ old('name', $adminUser->name) }}"
                            required
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
                            value="{{ old('email', $adminUser->email) }}"
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
                            value="{{ old('phone', $adminUser->phone) }}"
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
                            {{ $adminUser->isSuperAdmin() && !auth('admin')->user()->isSuperAdmin() ? 'disabled' : '' }}
                        >
                            <option value="">{{ __('Select Role') }}</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $adminUser->role_id) == $role->id ? 'selected' : '' }}>
                                {{ $role->display_name }}
                            </option>
                            @endforeach
                        </select>
                        @error('role_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label for="password" class="form-label">
                            {{ __('New Password') }}
                        </label>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            class="form-control @error('password') is-invalid @enderror"
                        >
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text">{{ __('Leave blank to keep current password') }}</small>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">
                            {{ __('Confirm New Password') }}
                        </label>
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            id="password_confirmation" 
                            class="form-control"
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
                                {{ old('is_active', $adminUser->is_active) ? 'checked' : '' }}
                                {{ $adminUser->id === auth('admin')->id() ? 'disabled' : '' }}
                            >
                            <label for="is_active" class="form-check-label">
                                {{ __('Active') }}
                            </label>
                        </div>
                        @if($adminUser->id === auth('admin')->id())
                        <small class="form-text text-muted">{{ __('You cannot deactivate your own account') }}</small>
                        @else
                        <small class="form-text">{{ __('Inactive users cannot login') }}</small>
                        @endif
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex gap-2 justify-content-end mt-4">
                        <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> {{ __('Update Admin User') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar Information -->
    <div class="col-lg-4">
        <!-- Admin Stats -->
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Account Statistics') }}</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>{{ __('Member Since') }}:</strong>
                    <div class="text-muted">{{ $adminUser->created_at->format('M d, Y') }}</div>
                </div>
                <div class="mb-3">
                    <strong>{{ __('Last Login') }}:</strong>
                    <div class="text-muted">
                        @if($adminUser->last_login_at)
                        {{ $adminUser->last_login_at->format('M d, Y h:i A') }}
                        @else
                        {{ __('Never') }}
                        @endif
                    </div>
                </div>
                <div class="mb-3">
                    <strong>{{ __('Last Login IP') }}:</strong>
                    <div class="text-muted">{{ $adminUser->last_login_ip ?? __('Unknown') }}</div>
                </div>
                <div>
                    <strong>{{ __('Email Verified') }}:</strong>
                    <div>
                        @if($adminUser->email_verified_at)
                        <span class="badge badge-success">{{ __('Yes') }}</span>
                        @else
                        <span class="badge badge-warning">{{ __('No') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Role Information -->
        <div class="content-card mt-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('Current Role') }}</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>{{ $adminUser->role->display_name }}</strong>
                    @if($adminUser->role->description)
                    <p class="mb-0 mt-2">{{ $adminUser->role->description }}</p>
                    @endif
                </div>
                
                <div>
                    <strong>{{ __('Permissions') }}:</strong>
                    <span class="badge badge-primary">{{ $adminUser->role->permissions->count() }}</span>
                </div>
            </div>
        </div>

        <!-- Help Card -->
        <div class="content-card mt-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('Help') }}</h3>
            </div>
            <div class="card-body">
                <ul style="padding-left: 20px; margin: 0;">
                    <li class="mb-2">{{ __('Leave password fields empty to keep current password') }}</li>
                    <li class="mb-2">{{ __('Changing role will update permissions') }}</li>
                    <li class="mb-2">{{ __('Deactivated users cannot login') }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Avatar preview
    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('avatarPreview');
    const removeAvatar = document.getElementById('remove_avatar');
    
    if (avatarInput) {
        avatarInput.addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarPreview.src = e.target.result;
                };
                reader.readAsDataURL(e.target.files[0]);
                
                if (removeAvatar) {
                    removeAvatar.checked = false;
                }
            }
        });
    }
    
    // Role information
    const roleSelect = document.getElementById('role_id');
    
    // Prepare roles data safely
    const roles = {!! json_encode($roles->mapWithKeys(function($role) {
        return [$role->id => [
            'id' => $role->id,
            'name' => $role->name,
            'display_name' => $role->display_name,
            'description' => $role->description
        ]];
    })) !!};
    
    if (roleSelect) {
        roleSelect.addEventListener('change', function() {
            const roleId = this.value;
            if (roleId && roles[roleId]) {
                console.log('Selected role:', roles[roleId].display_name);
            }
        });
    }
});
</script>
@endpush