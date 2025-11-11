@extends('admin.layouts.app')

@section('title', __('My Profile'))

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">{{ __('My Profile') }}</h1>
    <p class="page-subtitle">{{ __('Manage your account settings and preferences') }}</p>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Profile') }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <!-- Profile Information -->
    <div class="col-lg-8">
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Profile Information') }}</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- Avatar Section -->
                    <div class="mb-4 text-center">
                        <div class="mb-3">
                            <img src="{{ $admin->avatar_url }}" alt="{{ $admin->name }}" 
                                 class="rounded-circle" 
                                 style="width: 120px; height: 120px; object-fit: cover; border: 4px solid var(--primary-color);"
                                 id="avatarPreview">
                        </div>
                        
                        <div class="mb-3">
                            <label for="avatar" class="btn btn-primary btn-sm">
                                <i class="bi bi-upload me-2"></i>{{ __('Upload New Avatar') }}
                            </label>
                            <input type="file" name="avatar" id="avatar" class="d-none" accept="image/*" onchange="previewAvatar(this)">
                        </div>
                        
                        @if($admin->avatar)
                        <div class="form-check form-check-inline">
                            <input type="checkbox" name="remove_avatar" id="remove_avatar" class="form-check-input" value="1">
                            <label for="remove_avatar" class="form-check-label text-danger">
                                <i class="bi bi-trash me-1"></i>{{ __('Remove Avatar') }}
                            </label>
                        </div>
                        @endif
                        
                        @error('avatar')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <!-- Name -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="form-label">
                                    {{ __('Name') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $admin->name) }}" 
                                       required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Email -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="form-label">
                                    {{ __('Email Address') }} <span class="text-danger">*</span>
                                </label>
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email', $admin->email) }}" 
                                       required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Phone -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone" class="form-label">{{ __('Phone Number') }}</label>
                                <input type="text" 
                                       name="phone" 
                                       id="phone" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       value="{{ old('phone', $admin->phone) }}"
                                       placeholder="+91 1234567890">
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Role (Read-only) -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">{{ __('Role') }}</label>
                                <input type="text" 
                                       class="form-control" 
                                       value="{{ $admin->role_name }}" 
                                       readonly
                                       style="background: var(--content-bg);">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Account Status -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">{{ __('Account Status') }}</label>
                                <div>
                                    @if($admin->is_active)
                                    <span class="badge badge-success">
                                        <i class="bi bi-check-circle me-1"></i>{{ __('Active') }}
                                    </span>
                                    @else
                                    <span class="badge badge-danger">
                                        <i class="bi bi-x-circle me-1"></i>{{ __('Inactive') }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">{{ __('Last Login') }}</label>
                                <div style="padding: 8px 0; color: var(--text-muted);">
                                    @if($admin->last_login_at)
                                        <i class="bi bi-clock me-2"></i>{{ $admin->last_login_at->diffForHumans() }}
                                        <br>
                                        <small>{{ $admin->last_login_at->format('F d, Y h:i A') }}</small>
                                    @else
                                        <i class="bi bi-dash-circle me-2"></i>{{ __('Never') }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>{{ __('Update Profile') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Change Password -->
        <div class="content-card mt-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('Change Password') }}</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.profile.update-password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Current Password -->
                    <div class="form-group">
                        <label for="current_password" class="form-label">
                            {{ __('Current Password') }} <span class="text-danger">*</span>
                        </label>
                        <input type="password" 
                               name="current_password" 
                               id="current_password" 
                               class="form-control @error('current_password') is-invalid @enderror" 
                               required>
                        @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <!-- New Password -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password" class="form-label">
                                    {{ __('New Password') }} <span class="text-danger">*</span>
                                </label>
                                <input type="password" 
                                       name="password" 
                                       id="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       required
                                       minlength="8">
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">{{ __('Minimum 8 characters') }}</small>
                            </div>
                        </div>
                        
                        <!-- Confirm Password -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password_confirmation" class="form-label">
                                    {{ __('Confirm Password') }} <span class="text-danger">*</span>
                                </label>
                                <input type="password" 
                                       name="password_confirmation" 
                                       id="password_confirmation" 
                                       class="form-control" 
                                       required
                                       minlength="8">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="mt-4">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-shield-lock me-2"></i>{{ __('Update Password') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Account Summary -->
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Account Summary') }}</h3>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3 pb-3" style="border-bottom: 1px solid var(--border-color);">
                    <div class="me-3">
                        <i class="bi bi-person-badge" style="font-size: 2rem; color: var(--primary-color);"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 3px;">
                            {{ __('Account Type') }}
                        </div>
                        <div style="font-weight: 600; color: var(--text-dark);">
                            {{ $admin->role_name }}
                        </div>
                    </div>
                </div>
                
                <div class="d-flex align-items-center mb-3 pb-3" style="border-bottom: 1px solid var(--border-color);">
                    <div class="me-3">
                        <i class="bi bi-envelope" style="font-size: 2rem; color: var(--info-color);"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 3px;">
                            {{ __('Email Status') }}
                        </div>
                        <div style="font-weight: 600;">
                            @if($admin->email_verified_at)
                            <span class="badge badge-success">
                                <i class="bi bi-check-circle me-1"></i>{{ __('Verified') }}
                            </span>
                            @else
                            <span class="badge badge-warning">
                                <i class="bi bi-exclamation-triangle me-1"></i>{{ __('Not Verified') }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="d-flex align-items-center mb-3 pb-3" style="border-bottom: 1px solid var(--border-color);">
                    <div class="me-3">
                        <i class="bi bi-calendar-check" style="font-size: 2rem; color: var(--success-color);"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 3px;">
                            {{ __('Member Since') }}
                        </div>
                        <div style="font-weight: 600; color: var(--text-dark);">
                            {{ $admin->created_at->format('F d, Y') }}
                        </div>
                    </div>
                </div>
                
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-clock-history" style="font-size: 2rem; color: var(--warning-color);"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 3px;">
                            {{ __('Last Login IP') }}
                        </div>
                        <div style="font-weight: 600; color: var(--text-dark);">
                            {{ $admin->last_login_ip ?? __('N/A') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="content-card mt-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('Recent Activity') }}</h3>
            </div>
            <div class="card-body">
                @forelse($recentActivity as $activity)
                <div class="d-flex align-items-start mb-3 pb-3" style="border-bottom: 1px solid var(--border-color);">
                    <div class="me-3">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(var(--primary-rgb), 0.1); display: flex; align-items: center; justify-content: center;">
                            <i class="bi {{ $activity->action_icon }}" style="color: var(--{{ $activity->action_color }}-color);"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div style="font-size: 0.9rem; font-weight: 500; margin-bottom: 3px;">
                            {{ $activity->description ?? ucfirst($activity->action) }}
                        </div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">
                            <i class="bi bi-folder me-1"></i>{{ ucfirst($activity->module) }}
                            <span class="mx-2">•</span>
                            <i class="bi bi-clock me-1"></i>{{ $activity->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="bi bi-activity" style="font-size: 3rem; opacity: 0.3;"></i>
                    <p class="mt-2 mb-0">{{ __('No recent activity') }}</p>
                </div>
                @endforelse
                
                @if($recentActivity->count() > 0)
                <div class="text-center mt-3">
                    @if(auth('admin')->user()->isSuperAdmin())
                    <a href="{{ route('admin.activity-log') }}" class="btn btn-sm btn-outline">
                        {{ __('View All Activity') }} <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                    @endif
                </div>
                @endif
            </div>
        </div>
        
        <!-- Security Tips -->
        <div class="content-card mt-3">
            <div class="card-header" style="background: rgba(var(--warning-rgb, 243, 156, 18), 0.1);">
                <h3 class="card-title" style="color: var(--warning-color);">
                    <i class="bi bi-shield-exclamation me-2"></i>{{ __('Security Tips') }}
                </h3>
            </div>
            <div class="card-body">
                <ul style="margin: 0; padding-left: 20px; font-size: 0.9rem; line-height: 1.8;">
                    <li>{{ __('Use a strong, unique password') }}</li>
                    <li>{{ __('Change your password regularly') }}</li>
                    <li>{{ __('Never share your login credentials') }}</li>
                    <li>{{ __('Log out when using shared devices') }}</li>
                    <li>{{ __('Monitor your account activity') }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            document.getElementById('avatarPreview').src = e.target.result;
        };
        
        reader.readAsDataURL(input.files[0]);
        
        // Uncheck remove avatar if new file is selected
        document.getElementById('remove_avatar')?.checked = false;
    }
}

// Clear file input if remove avatar is checked
document.getElementById('remove_avatar')?.addEventListener('change', function() {
    if (this.checked) {
        document.getElementById('avatar').value = '';
        document.getElementById('avatarPreview').src = '{{ $admin->avatar_url }}';
    }
});
</script>
@endpush