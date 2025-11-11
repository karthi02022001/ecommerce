@extends('admin.layouts.app')

@section('title', __('Theme Settings'))

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">{{ __('Theme Settings') }}</h1>
    <p class="page-subtitle">{{ __('Customize your store appearance with pre-made themes') }}</p>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Theme Settings') }}</li>
        </ol>
    </nav>
</div>

<!-- Current Theme -->
<div class="content-card mb-4">
    <div class="card-header">
        <h3 class="card-title">{{ __('Current Active Theme') }}</h3>
    </div>
    <div class="card-body">
        @if($activeTheme)
        <div class="row align-items-center">
            <div class="col-md-3">
                <div style="width: 100%; height: 150px; border-radius: 12px; overflow: hidden; box-shadow: var(--shadow);">
                    <img src="{{ $activeTheme->preview_url }}" alt="{{ $activeTheme->display_name }}" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
            </div>
            <div class="col-md-6">
                <h4 style="margin-bottom: 10px; color: var(--text-dark);">{{ $activeTheme->display_name }}</h4>
                <p style="color: var(--text-muted); margin-bottom: 15px;">{{ $activeTheme->description }}</p>
                <div class="d-flex gap-2" style="flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 0.85rem; color: var(--text-muted);">{{ __('Primary:') }}</span>
                        <div style="width: 30px; height: 30px; border-radius: 6px; background: {{ $activeTheme->primary_color }}; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 0.85rem; color: var(--text-muted);">{{ __('Accent:') }}</span>
                        <div style="width: 30px; height: 30px; border-radius: 6px; background: {{ $activeTheme->accent_color }}; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 0.85rem; color: var(--text-muted);">{{ __('Success:') }}</span>
                        <div style="width: 30px; height: 30px; border-radius: 6px; background: {{ $activeTheme->success_color }}; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 0.85rem; color: var(--text-muted);">{{ __('Warning:') }}</span>
                        <div style="width: 30px; height: 30px; border-radius: 6px; background: {{ $activeTheme->warning_color }}; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 0.85rem; color: var(--text-muted);">{{ __('Danger:') }}</span>
                        <div style="width: 30px; height: 30px; border-radius: 6px; background: {{ $activeTheme->danger_color }}; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 text-end">
                <span class="badge badge-success" style="font-size: 1rem; padding: 10px 20px;">
                    <i class="bi bi-check-circle me-2"></i>{{ __('Active') }}
                </span>
            </div>
        </div>
        @else
        <p class="text-center text-muted">{{ __('No theme is currently active') }}</p>
        @endif
    </div>
</div>

<!-- Available Themes -->
<div class="content-card">
    <div class="card-header">
        <h3 class="card-title">{{ __('Available Themes') }}</h3>
        <div class="card-actions">
            <span class="badge badge-primary">{{ $themes->count() }} {{ __('Themes') }}</span>
        </div>
    </div>
    <div class="card-body">
        @if($themes->count() > 0)
        <div class="row">
            @foreach($themes as $theme)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="theme-card" style="border: 2px solid {{ $activeTheme && $activeTheme->id === $theme->id ? 'var(--primary-color)' : 'var(--border-color)' }}; border-radius: 12px; overflow: hidden; transition: all 0.3s ease; position: relative;">
                    <!-- Theme Preview -->
                    <div style="width: 100%; height: 200px; position: relative; overflow: hidden;">
                        <img src="{{ $theme->preview_url }}" alt="{{ $theme->display_name }}" style="width: 100%; height: 100%; object-fit: cover;">
                        
                        @if($activeTheme && $activeTheme->id === $theme->id)
                        <div style="position: absolute; top: 10px; right: 10px;">
                            <span class="badge badge-success" style="padding: 8px 15px;">
                                <i class="bi bi-check-circle me-1"></i>{{ __('Active') }}
                            </span>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Theme Info -->
                    <div style="padding: 20px;">
                        <h5 style="margin-bottom: 8px; color: var(--text-dark);">{{ $theme->display_name }}</h5>
                        <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 15px; min-height: 40px;">
                            {{ $theme->description }}
                        </p>
                        
                        <!-- Color Swatches -->
                        <div class="d-flex gap-2 mb-3" style="flex-wrap: wrap;">
                            <div title="{{ __('Primary') }}" style="width: 35px; height: 35px; border-radius: 8px; background: {{ $theme->primary_color }}; border: 2px solid #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.15);"></div>
                            <div title="{{ __('Accent') }}" style="width: 35px; height: 35px; border-radius: 8px; background: {{ $theme->accent_color }}; border: 2px solid #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.15);"></div>
                            <div title="{{ __('Success') }}" style="width: 35px; height: 35px; border-radius: 8px; background: {{ $theme->success_color }}; border: 2px solid #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.15);"></div>
                            <div title="{{ __('Warning') }}" style="width: 35px; height: 35px; border-radius: 8px; background: {{ $theme->warning_color }}; border: 2px solid #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.15);"></div>
                            <div title="{{ __('Danger') }}" style="width: 35px; height: 35px; border-radius: 8px; background: {{ $theme->danger_color }}; border: 2px solid #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.15);"></div>
                        </div>
                        
                        <!-- Action Button -->
                        @if($activeTheme && $activeTheme->id === $theme->id)
                        <button type="button" class="btn btn-secondary w-100" disabled>
                            <i class="bi bi-check-circle me-2"></i>{{ __('Currently Active') }}
                        </button>
                        @else
                        <form action="{{ route('admin.settings.apply-theme') }}" method="POST" class="apply-theme-form">
                            @csrf
                            <input type="hidden" name="theme_id" value="{{ $theme->id }}">
                            <button type="submit" class="btn btn-outline w-100">
                                <i class="bi bi-palette me-2"></i>{{ __('Apply Theme') }}
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-palette" style="font-size: 3rem; color: var(--text-muted);"></i>
            <p class="mt-3 text-muted">{{ __('No themes available') }}</p>
        </div>
        @endif
    </div>
</div>

<!-- Theme Information -->
<div class="content-card mt-4">
    <div class="card-header">
        <h3 class="card-title">{{ __('About Themes') }}</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5><i class="bi bi-info-circle text-primary me-2"></i>{{ __('What are themes?') }}</h5>
                <p style="color: var(--text-muted); line-height: 1.8;">
                    {{ __('Themes are pre-configured color schemes that instantly change the appearance of your store. Each theme includes carefully selected colors for primary elements, accents, and status indicators.') }}
                </p>
            </div>
            <div class="col-md-6">
                <h5><i class="bi bi-lightbulb text-warning me-2"></i>{{ __('How to use themes?') }}</h5>
                <p style="color: var(--text-muted); line-height: 1.8;">
                    {{ __('Simply click "Apply Theme" on any theme card to instantly update your store colors. The changes take effect immediately and apply to both the admin panel and customer-facing store.') }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.theme-card {
    background: var(--card-bg);
    transition: all 0.3s ease;
}

.theme-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
}

.apply-theme-form button:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(var(--primary-rgb), 0.3);
}
</style>
@endpush

@push('scripts')
<script>
    // Confirm theme change
    document.addEventListener('DOMContentLoaded', function() {
        const themeForms = document.querySelectorAll('.apply-theme-form');
        
        themeForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (confirm('{{ __("Are you sure you want to apply this theme? The changes will take effect immediately.") }}')) {
                    this.submit();
                }
            });
        });
    });
</script>
@endpush
