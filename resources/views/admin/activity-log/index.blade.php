@extends('admin.layouts.app')

@section('title', __('Activity Log'))

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1 class="page-title">{{ __('Activity Log') }}</h1>
            <p class="page-subtitle">{{ __('Monitor all admin activities and system events') }}</p>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <button class="btn btn-outline" onclick="window.print()">
                <i class="bi bi-printer me-2"></i>{{ __('Print Report') }}
            </button>
            <a href="{{ route('admin.activity-log') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-clockwise me-2"></i>{{ __('Refresh') }}
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Activity Log') }}</li>
        </ol>
    </nav>
</div>

<!-- Statistics Cards -->
@if($logs->total() > 0)
<div class="stats-row mb-4">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-label">{{ __('Total Activities') }}</div>
            <div class="stat-icon">
                <i class="bi bi-activity"></i>
            </div>
        </div>
        <div class="stat-value">{{ number_format($logs->total()) }}</div>
        <div class="stat-footer">
            <span class="text-muted">{{ __('All time records') }}</span>
        </div>
    </div>
    
    <div class="stat-card success">
        <div class="stat-header">
            <div class="stat-label">{{ __('Active Admins') }}</div>
            <div class="stat-icon">
                <i class="bi bi-people"></i>
            </div>
        </div>
        <div class="stat-value">{{ $admins->count() }}</div>
        <div class="stat-footer">
            <span class="text-muted">{{ __('System users') }}</span>
        </div>
    </div>
    
    <div class="stat-card info">
        <div class="stat-header">
            <div class="stat-label">{{ __('Modules Tracked') }}</div>
            <div class="stat-icon">
                <i class="bi bi-folder"></i>
            </div>
        </div>
        <div class="stat-value">{{ $modules->count() }}</div>
        <div class="stat-footer">
            <span class="text-muted">{{ __('Different modules') }}</span>
        </div>
    </div>
    
    <div class="stat-card warning">
        <div class="stat-header">
            <div class="stat-label">{{ __('Action Types') }}</div>
            <div class="stat-icon">
                <i class="bi bi-lightning"></i>
            </div>
        </div>
        <div class="stat-value">{{ $actions->count() }}</div>
        <div class="stat-footer">
            <span class="text-muted">{{ __('Distinct actions') }}</span>
        </div>
    </div>
</div>
@endif

<!-- Filters Card -->
<div class="content-card mb-4">
    <div class="card-header">
        <h3 class="card-title">
            <i class="bi bi-funnel me-2"></i>{{ __('Filter Activities') }}
        </h3>
        <button class="btn btn-sm btn-outline" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
            <i class="bi bi-chevron-down"></i>
        </button>
    </div>
    <div class="collapse show" id="filterCollapse">
        <div class="card-body">
            <form action="{{ route('admin.activity-log') }}" method="GET" id="filterForm">
                <div class="row g-3">
                    <!-- Search -->
                    <div class="col-md-4">
                        <label for="search" class="form-label">
                            <i class="bi bi-search me-1"></i>{{ __('Search') }}
                        </label>
                        <input type="text" 
                               name="search" 
                               id="search" 
                               class="form-control" 
                               placeholder="{{ __('Search in activities...') }}"
                               value="{{ request('search') }}">
                    </div>
                    
                    <!-- Admin Filter -->
                    <div class="col-md-4">
                        <label for="admin" class="form-label">
                            <i class="bi bi-person me-1"></i>{{ __('Admin User') }}
                        </label>
                        <select name="admin" id="admin" class="form-select">
                            <option value="">{{ __('All Admins') }}</option>
                            @foreach($admins as $admin)
                            <option value="{{ $admin->id }}" {{ request('admin') == $admin->id ? 'selected' : '' }}>
                                {{ $admin->name }} ({{ $admin->email }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Module Filter -->
                    <div class="col-md-4">
                        <label for="module" class="form-label">
                            <i class="bi bi-folder me-1"></i>{{ __('Module') }}
                        </label>
                        <select name="module" id="module" class="form-select">
                            <option value="">{{ __('All Modules') }}</option>
                            @foreach($modules as $module)
                            <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>
                                {{ ucfirst($module) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Action Filter -->
                    <div class="col-md-4">
                        <label for="action" class="form-label">
                            <i class="bi bi-lightning me-1"></i>{{ __('Action') }}
                        </label>
                        <select name="action" id="action" class="form-select">
                            <option value="">{{ __('All Actions') }}</option>
                            @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                {{ ucfirst($action) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Date From -->
                    <div class="col-md-4">
                        <label for="date_from" class="form-label">
                            <i class="bi bi-calendar-event me-1"></i>{{ __('Date From') }}
                        </label>
                        <input type="date" 
                               name="date_from" 
                               id="date_from" 
                               class="form-control"
                               value="{{ request('date_from') }}">
                    </div>
                    
                    <!-- Date To -->
                    <div class="col-md-4">
                        <label for="date_to" class="form-label">
                            <i class="bi bi-calendar-check me-1"></i>{{ __('Date To') }}
                        </label>
                        <input type="date" 
                               name="date_to" 
                               id="date_to" 
                               class="form-control"
                               value="{{ request('date_to') }}">
                    </div>
                </div>
                
                <!-- Filter Buttons -->
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel me-2"></i>{{ __('Apply Filters') }}
                    </button>
                    <a href="{{ route('admin.activity-log') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-2"></i>{{ __('Clear All') }}
                    </a>
                    @if(request()->hasAny(['search', 'admin', 'module', 'action', 'date_from', 'date_to']))
                    <span class="badge badge-primary d-flex align-items-center ms-2" style="height: fit-content; align-self: center; padding: 8px 12px;">
                        <i class="bi bi-filter-circle me-1"></i>{{ __('Filters Active') }}
                    </span>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Activity Log Table -->
<div class="content-card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="bi bi-list-ul me-2"></i>{{ __('Activity Entries') }}
            <span class="badge badge-primary ms-2">{{ $logs->total() }}</span>
        </h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">{{ __('ID') }}</th>
                        <th style="min-width: 200px;">{{ __('Admin User') }}</th>
                        <th style="width: 120px;">{{ __('Action') }}</th>
                        <th style="width: 120px;">{{ __('Module') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th style="width: 140px;">{{ __('IP Address') }}</th>
                        <th style="width: 180px;">{{ __('Date & Time') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td>
                            <strong style="color: var(--text-muted);">#{{ $log->id }}</strong>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="{{ $log->admin->avatar_url }}" 
                                     alt="{{ $log->admin->name }}"
                                     class="rounded-circle me-2"
                                     style="width: 36px; height: 36px; object-fit: cover; border: 2px solid var(--border-color);">
                                <div>
                                    <div style="font-weight: 600; color: var(--text-dark);">
                                        {{ $log->admin->name }}
                                    </div>
                                    <small class="text-muted">{{ $log->admin->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-{{ $log->action_color }}" style="min-width: 100px;">
                                <i class="bi {{ $log->action_icon }} me-1"></i>{{ ucfirst($log->action) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-secondary" style="min-width: 100px;">
                                <i class="bi bi-folder me-1"></i>{{ ucfirst($log->module) }}
                            </span>
                        </td>
                        <td>
                            @if($log->description)
                                <span class="text-truncate d-inline-block" style="max-width: 300px;" title="{{ $log->description }}">
                                    {{ $log->description }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <code style="font-size: 0.85rem; background: var(--content-bg); padding: 4px 10px; border-radius: 6px; display: inline-block;">
                                {{ $log->ip_address ?? 'N/A' }}
                            </code>
                        </td>
                        <td>
                            <div style="font-size: 0.9rem; color: var(--text-dark); margin-bottom: 2px;">
                                <i class="bi bi-calendar3 me-1"></i>{{ $log->created_at->format('M d, Y') }}
                            </div>
                            <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 2px;">
                                <i class="bi bi-clock me-1"></i>{{ $log->created_at->format('h:i A') }}
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-stopwatch me-1"></i>{{ $log->created_at->diffForHumans() }}
                            </small>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="empty-state">
                                <i class="bi bi-activity" style="font-size: 4rem; opacity: 0.2; color: var(--text-muted);"></i>
                                <h5 class="mt-3 mb-2">{{ __('No Activity Logs Found') }}</h5>
                                <p class="text-muted mb-3">
                                    @if(request()->hasAny(['search', 'admin', 'module', 'action', 'date_from', 'date_to']))
                                        {{ __('No activities match your filter criteria.') }}
                                    @else
                                        {{ __('No activities have been logged yet.') }}
                                    @endif
                                </p>
                                @if(request()->hasAny(['search', 'admin', 'module', 'action', 'date_from', 'date_to']))
                                <a href="{{ route('admin.activity-log') }}" class="btn btn-primary">
                                    <i class="bi bi-x-circle me-2"></i>{{ __('Clear Filters') }}
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($logs->hasPages())
        <div class="px-4 py-3 border-top">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <!-- Pagination Info -->
                <div class="pagination-info">
                    {{ __('Showing') }}
                    <strong>{{ $logs->firstItem() ?? 0 }}</strong>
                    {{ __('to') }}
                    <strong>{{ $logs->lastItem() ?? 0 }}</strong>
                    {{ __('of') }}
                    <strong>{{ $logs->total() }}</strong>
                    {{ __('entries') }}
                </div>
                
                <!-- Pagination Links -->
                <nav aria-label="Page navigation">
                    <ul class="pagination mb-0" role="navigation">
                        {{-- Previous Page Link --}}
                        @if ($logs->onFirstPage())
                            <li class="page-item disabled" aria-disabled="true">
                                <span class="page-link">
                                    <i class="bi bi-chevron-left"></i>
                                    <span class="d-none d-md-inline ms-1">{{ __('Previous') }}</span>
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $logs->appends(request()->except('page'))->previousPageUrl() }}" rel="prev">
                                    <i class="bi bi-chevron-left"></i>
                                    <span class="d-none d-md-inline ms-1">{{ __('Previous') }}</span>
                                </a>
                            </li>
                        @endif

                        {{-- Page Numbers with Smart Display --}}
                        @php
                            $currentPage = $logs->currentPage();
                            $lastPage = $logs->lastPage();
                            $start = max($currentPage - 2, 1);
                            $end = min($currentPage + 2, $lastPage);
                        @endphp

                        {{-- First Page --}}
                        @if($start > 1)
                            <li class="page-item">
                                <a class="page-link" href="{{ $logs->appends(request()->except('page'))->url(1) }}">1</a>
                            </li>
                            @if($start > 2)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                        @endif

                        {{-- Page Number Range --}}
                        @for ($page = $start; $page <= $end; $page++)
                            @if ($page == $currentPage)
                                <li class="page-item active" aria-current="page">
                                    <span class="page-link">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $logs->appends(request()->except('page'))->url($page) }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endfor

                        {{-- Last Page --}}
                        @if($end < $lastPage)
                            @if($end < $lastPage - 1)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                            <li class="page-item">
                                <a class="page-link" href="{{ $logs->appends(request()->except('page'))->url($lastPage) }}">{{ $lastPage }}</a>
                            </li>
                        @endif

                        {{-- Next Page Link --}}
                        @if ($logs->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $logs->appends(request()->except('page'))->nextPageUrl() }}" rel="next">
                                    <span class="d-none d-md-inline me-1">{{ __('Next') }}</span>
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        @else
                            <li class="page-item disabled" aria-disabled="true">
                                <span class="page-link">
                                    <span class="d-none d-md-inline me-1">{{ __('Next') }}</span>
                                    <i class="bi bi-chevron-right"></i>
                                </span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Activity Timeline (Recent) - Only show when no filters applied -->
@if(!request()->hasAny(['search', 'admin', 'module', 'action', 'date_from', 'date_to']) && $logs->count() > 0)
<div class="content-card mt-4">
    <div class="card-header">
        <h3 class="card-title">
            <i class="bi bi-clock-history me-2"></i>{{ __('Recent Activity Timeline') }}
        </h3>
    </div>
    <div class="card-body">
        <div class="activity-timeline">
            @foreach($logs->take(10) as $log)
            <div class="timeline-item">
                <div class="timeline-marker" style="background: var(--{{ $log->action_color }}-color);">
                    <i class="bi {{ $log->action_icon }}"></i>
                </div>
                <div class="timeline-content">
                    <div class="d-flex flex-column flex-md-row align-items-start justify-content-between gap-3">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-2">
                                <img src="{{ $log->admin->avatar_url }}" 
                                     alt="{{ $log->admin->name }}"
                                     class="rounded-circle me-2"
                                     style="width: 28px; height: 28px; object-fit: cover;">
                                <h5 class="mb-0">
                                    <strong>{{ $log->admin->name }}</strong> 
                                    <span class="text-muted">{{ $log->action }}</span>
                                </h5>
                            </div>
                            <p class="mb-2" style="color: var(--text-muted);">
                                @if($log->description)
                                    {{ $log->description }}
                                @else
                                    {{ ucfirst($log->action) }} {{ __('in') }} {{ ucfirst($log->module) }} {{ __('module') }}
                                @endif
                            </p>
                            <div class="d-flex flex-wrap gap-3" style="font-size: 0.85rem; color: var(--text-muted);">
                                <span>
                                    <i class="bi bi-folder me-1"></i>{{ ucfirst($log->module) }}
                                </span>
                                <span>
                                    <i class="bi bi-geo-alt me-1"></i>{{ $log->ip_address }}
                                </span>
                                <span class="badge badge-{{ $log->action_color }}">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </div>
                        </div>
                        <div class="text-md-end" style="min-width: 150px;">
                            <div style="font-size: 0.95rem; color: var(--text-dark); font-weight: 600;">
                                {{ $log->created_at->format('M d, Y') }}
                            </div>
                            <div style="font-size: 0.9rem; color: var(--text-muted);">
                                {{ $log->created_at->format('h:i A') }}
                            </div>
                            <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 4px;">
                                <i class="bi bi-stopwatch me-1"></i>{{ $log->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
/* Activity Timeline Styles */
.activity-timeline {
    position: relative;
    padding: 20px 0;
}

.timeline-item {
    position: relative;
    padding-left: 60px;
    padding-bottom: 40px;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 45px;
    bottom: -15px;
    width: 2px;
    background: linear-gradient(to bottom, var(--border-color), transparent);
}

.timeline-item:last-child::before {
    display: none;
}

.timeline-marker {
    position: absolute;
    left: 0;
    top: 0;
    width: 42px;
    height: 42px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.1rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    z-index: 1;
}

.timeline-content {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.timeline-content:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
    border-color: var(--primary-light);
}

.timeline-content h5 {
    font-size: 1rem;
    margin: 0;
}

/* Empty State */
.empty-state {
    padding: 40px 20px;
}

.empty-state h5 {
    color: var(--text-dark);
    font-weight: 600;
}

/* Table Enhancements */
.admin-table tbody tr {
    transition: all 0.2s ease;
}

.admin-table tbody tr:hover {
    background: rgba(var(--primary-rgb), 0.03);
    transform: scale(1.001);
}

/* Badge Improvements */
.badge {
    font-weight: 600;
    padding: 6px 12px;
    font-size: 0.8rem;
}

/* Pagination Styles */
.pagination {
    display: flex;
    gap: 5px;
    list-style: none;
    margin: 0;
    padding: 0;
}

.pagination .page-item {
    list-style: none;
}

.pagination .page-link {
    padding: 10px 15px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    color: var(--text-dark);
    text-decoration: none;
    background: var(--card-bg);
    font-weight: 500;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    transition: all 0.3s ease;
}

.pagination .page-link:hover {
    background: var(--primary-light);
    color: white;
    border-color: var(--primary-light);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(var(--primary-rgb), 0.25);
}

.pagination .page-item.active .page-link {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(var(--primary-rgb), 0.3);
}

.pagination .page-item.disabled .page-link {
    color: var(--text-muted);
    background: var(--content-bg);
    border-color: var(--border-color);
    cursor: not-allowed;
    opacity: 0.5;
}

.pagination .page-item.disabled .page-link:hover {
    background: var(--content-bg);
    color: var(--text-muted);
    transform: none;
    box-shadow: none;
}

/* Chevron Icon Sizing */
.pagination .page-link i.bi-chevron-left,
.pagination .page-link i.bi-chevron-right {
    font-size: 0.85rem;
    line-height: 1;
}

/* Previous/Next Button Spacing */
.pagination .page-link[rel="prev"],
.pagination .page-link[rel="next"] {
    padding: 10px 16px;
    gap: 6px;
}

/* Dots Separator */
.pagination .page-item.disabled .page-link {
    border: none;
    background: transparent;
    min-width: auto;
}

/* Pagination Info */
.pagination-info {
    font-size: 0.9rem;
    color: var(--text-muted);
    margin: 0;
}

.pagination-info strong {
    color: var(--text-dark);
    font-weight: 600;
}

/* Filter Collapse Animation */
#filterCollapse {
    transition: all 0.3s ease;
}

/* Responsive Improvements */
@media (max-width: 768px) {
    .timeline-item {
        padding-left: 50px;
    }
    
    .timeline-marker {
        width: 36px;
        height: 36px;
        font-size: 0.9rem;
    }
    
    .timeline-item::before {
        left: 17px;
    }
    
    .admin-table {
        font-size: 0.85rem;
    }
    
    .admin-table th,
    .admin-table td {
        padding: 10px 8px;
    }
    
    .pagination .page-link {
        padding: 8px 12px;
        min-width: 36px;
        height: 36px;
        font-size: 0.85rem;
    }
    
    .pagination .page-link[rel="prev"],
    .pagination .page-link[rel="next"] {
        padding: 8px 12px;
    }
}

/* Print Styles */
@media print {
    .page-header .d-flex,
    .stats-row,
    .content-card:first-of-type,
    .card-actions,
    .pagination,
    .activity-timeline,
    .admin-sidebar,
    .admin-topbar,
    .breadcrumb {
        display: none !important;
    }
    
    .admin-content {
        margin-left: 0 !important;
    }
    
    .content-card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
    
    .admin-table {
        font-size: 0.85rem;
    }
    
    .badge {
        border: 1px solid #333;
        color: #333 !important;
        background: #fff !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Date range validation
    const dateFrom = document.getElementById('date_from');
    const dateTo = document.getElementById('date_to');
    
    if (dateFrom && dateTo) {
        dateFrom.addEventListener('change', function() {
            if (dateTo.value && this.value > dateTo.value) {
                dateTo.value = this.value;
            }
            dateTo.min = this.value;
        });
        
        dateTo.addEventListener('change', function() {
            if (dateFrom.value && this.value < dateFrom.value) {
                dateFrom.value = this.value;
            }
            dateFrom.max = this.value;
        });
    }
    
    // Auto-expand filter if filters are active
    @if(request()->hasAny(['search', 'admin', 'module', 'action', 'date_from', 'date_to']))
    const filterCollapse = document.getElementById('filterCollapse');
    if (filterCollapse && !filterCollapse.classList.contains('show')) {
        new bootstrap.Collapse(filterCollapse, { show: true });
    }
    @endif
    
    // Smooth scroll to table after filter submit
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.toString() && document.querySelector('.admin-table')) {
        setTimeout(() => {
            document.querySelector('.admin-table').scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start' 
            });
        }, 100);
    }
});
</script>
@endpush