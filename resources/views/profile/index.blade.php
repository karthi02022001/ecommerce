@extends('layouts.app')

@section('title', __('My Profile'))



@section('content')
<div class="profile-container">
    <div class="container">
        <!-- Page Header -->
        <div class="mb-4">
            <h1 class="mb-2" style="font-weight: 700; color: #1a1a1a;">{{ __('My Profile') }}</h1>
            <p class="text-muted">{{ __('Manage your account settings and preferences') }}</p>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Personal Information -->
                <div class="profile-card">
                    <div class="profile-card-header">
                        <h5><i class="bi bi-person me-2"></i>{{ __('Personal Information') }}</h5>
                    </div>
                    <div class="profile-card-body">
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PATCH')
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">{{ __('Full Name') }}</label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $user->name) }}" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('Email Address') }}</label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $user->email) }}" 
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label for="phone" class="form-label">{{ __('Phone Number') }}</label>
                                <input type="tel" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone', $user->phone) }}"
                                       placeholder="+91 1234567890">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>{{ __('Update Profile') }}
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Change Password -->
                <div class="profile-card">
                    <div class="profile-card-header">
                        <h5><i class="bi bi-lock me-2"></i>{{ __('Change Password') }}</h5>
                    </div>
                    <div class="profile-card-body">
                        <form action="{{ route('profile.password.update') }}" method="POST">
                            @csrf
                            @method('PATCH')
                            
                            <div class="mb-3">
                                <label for="current_password" class="form-label">{{ __('Current Password') }}</label>
                                <input type="password" 
                                       class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" 
                                       name="current_password" 
                                       required>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">{{ __('New Password') }}</label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">{{ __('Minimum 8 characters') }}</small>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label">{{ __('Confirm New Password') }}</label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-shield-check me-2"></i>{{ __('Change Password') }}
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Addresses -->
                <div class="profile-card">
                    <div class="profile-card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-geo-alt me-2"></i>{{ __('Saved Addresses') }}</h5>
                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                            <i class="bi bi-plus-circle me-1"></i>{{ __('Add New') }}
                        </button>
                    </div>
                    <div class="profile-card-body">
                        @if($addresses->count() > 0)
                            <div class="row">
                                @foreach($addresses as $address)
                                    <div class="col-md-6 mb-3">
                                        <div class="address-card {{ $address->is_default ? 'default-address' : '' }}">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <span class="badge bg-secondary">
                                                    <i class="bi bi-{{ $address->address_type === 'shipping' ? 'truck' : 'receipt' }} me-1"></i>
                                                    {{ ucfirst($address->address_type) }}
                                                </span>
                                                @if($address->is_default)
                                                    <span class="badge bg-primary">
                                                        <i class="bi bi-star-fill me-1"></i>{{ __('Default') }}
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <p class="mb-0" style="font-weight: 600; font-size: 15px;">{{ $address->full_name }}</p>
                                            <p class="mb-0 text-muted" style="font-size: 14px;">
                                                <i class="bi bi-telephone me-1"></i>{{ $address->phone }}
                                            </p>
                                            
                                            <p class="mt-2 mb-3" style="font-size: 14px; line-height: 1.6;">
                                                {{ $address->address_line1 }}<br>
                                                @if($address->address_line2)
                                                    {{ $address->address_line2 }}<br>
                                                @endif
                                                {{ $address->city }}, {{ $address->state }}<br>
                                                {{ $address->postal_code }}, {{ $address->country }}
                                            </p>
                                            
                                            <div class="d-flex gap-2">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-primary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editAddressModal{{ $address->id }}">
                                                    <i class="bi bi-pencil me-1"></i>{{ __('Edit') }}
                                                </button>
                                                <form action="{{ route('profile.delete-address', $address->id) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            onclick="return confirm('{{ __('Delete this address?') }}')">
                                                        <i class="bi bi-trash me-1"></i>{{ __('Delete') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Edit Address Modal -->
                                    <div class="modal fade" id="editAddressModal{{ $address->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <form action="{{ route('profile.update-address', $address->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">{{ __('Edit Address') }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">{{ __('Address Type') }}</label>
                                                                <select class="form-select" name="address_type" required>
                                                                    <option value="billing" {{ $address->address_type === 'billing' ? 'selected' : '' }}>{{ __('Billing') }}</option>
                                                                    <option value="shipping" {{ $address->address_type === 'shipping' ? 'selected' : '' }}>{{ __('Shipping') }}</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">{{ __('Full Name') }}</label>
                                                                <input type="text" class="form-control" name="full_name" value="{{ $address->full_name }}" required>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label">{{ __('Phone Number') }}</label>
                                                            <input type="text" class="form-control" name="phone" value="{{ $address->phone }}" required>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label">{{ __('Address Line 1') }}</label>
                                                            <input type="text" class="form-control" name="address_line1" value="{{ $address->address_line1 }}" required>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label">{{ __('Address Line 2') }} ({{ __('Optional') }})</label>
                                                            <input type="text" class="form-control" name="address_line2" value="{{ $address->address_line2 }}">
                                                        </div>
                                                        
                                                        <div class="row">
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">{{ __('City') }}</label>
                                                                <input type="text" class="form-control" name="city" value="{{ $address->city }}" required>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">{{ __('State') }}</label>
                                                                <input type="text" class="form-control" name="state" value="{{ $address->state }}" required>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="row">
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">{{ __('Postal Code') }}</label>
                                                                <input type="text" class="form-control" name="postal_code" value="{{ $address->postal_code }}" required>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">{{ __('Country') }}</label>
                                                                <input type="text" class="form-control" name="country" value="{{ $address->country }}" required>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="is_default" value="1" id="is_default_edit{{ $address->id }}" {{ $address->is_default ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="is_default_edit{{ $address->id }}">
                                                                {{ __('Set as default') }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                                        <button type="submit" class="btn btn-primary">{{ __('Update Address') }}</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="bi bi-geo-alt"></i>
                                <p class="text-muted">{{ __('No saved addresses yet') }}</p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                                    <i class="bi bi-plus-circle me-2"></i>{{ __('Add Your First Address') }}
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="sidebar-card">
                    <div class="profile-card-body text-center">
                        <div class="user-avatar">
                            <i class="bi bi-person"></i>
                        </div>
                        <h5 class="mb-1" style="font-weight: 700;">{{ $user->name }}</h5>
                        <p class="text-muted mb-2" style="font-size: 14px;">{{ $user->email }}</p>
                        @if($user->phone)
                            <p class="text-muted mb-2" style="font-size: 14px;">
                                <i class="bi bi-telephone me-1"></i>{{ $user->phone }}
                            </p>
                        @endif
                        <small class="text-muted">{{ __('Member since') }} {{ $user->created_at->format('M Y') }}</small>
                    </div>
                </div>
                
                <div class="sidebar-card">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('profile.index') }}" class="list-group-item list-group-item-action active">
                            <i class="bi bi-person me-2"></i>{{ __('Profile') }}
                        </a>
                        <a href="{{ route('orders.index') }}" class="list-group-item list-group-item-action">
                            <i class="bi bi-bag me-2"></i>{{ __('My Orders') }}
                        </a>
                        <a href="{{ route('cart.index') }}" class="list-group-item list-group-item-action">
                            <i class="bi bi-cart me-2"></i>{{ __('Shopping Cart') }}
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="list-group-item list-group-item-action text-danger border-0 w-100 text-start">
                                <i class="bi bi-box-arrow-right me-2"></i>{{ __('Logout') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('profile.store-address') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Add New Address') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Address Type') }}</label>
                            <select class="form-select" name="address_type" required>
                                <option value="billing">{{ __('Billing') }}</option>
                                <option value="shipping">{{ __('Shipping') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Full Name') }}</label>
                            <input type="text" class="form-control" name="full_name" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">{{ __('Phone Number') }}</label>
                        <input type="text" class="form-control" name="phone" placeholder="+91 1234567890" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">{{ __('Address Line 1') }}</label>
                        <input type="text" class="form-control" name="address_line1" placeholder="{{ __('Street address, P.O. box, company name') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">{{ __('Address Line 2') }} ({{ __('Optional') }})</label>
                        <input type="text" class="form-control" name="address_line2" placeholder="{{ __('Apartment, suite, unit, building, floor, etc.') }}">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('City') }}</label>
                            <input type="text" class="form-control" name="city" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('State') }}</label>
                            <input type="text" class="form-control" name="state" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Postal Code') }}</label>
                            <input type="text" class="form-control" name="postal_code" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Country') }}</label>
                            <input type="text" class="form-control" name="country" value="India" required>
                        </div>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_default" value="1" id="is_default_add">
                        <label class="form-check-label" for="is_default_add">
                            {{ __('Set as default') }}
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Add Address') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection