<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }} Admin</title>

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Admin CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

    <!-- Dynamic Theme CSS -->
    @php
        $activeTheme = \App\Models\ThemePreset::getActiveTheme();
    @endphp
    @if ($activeTheme)
        <style>
            {!! $activeTheme->css_variables !!}
        </style>
    @endif

    @stack('styles')
</head>

<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <!-- Brand -->
            <div class="sidebar-brand">
                <i class="bi bi-shop sidebar-brand-icon"></i>
                <span class="sidebar-brand-text">{{ config('app.name') }}</span>
            </div>

            <!-- Menu -->
            <ul class="sidebar-menu">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 nav-icon"></i>
                        <span class="nav-text">{{ __('Dashboard') }}</span>
                    </a>
                </li>

                <li class="sidebar-divider"></li>
                <li class="sidebar-heading">{{ __('Store Management') }}</li>

                <!-- Products -->
                @if (auth('admin')->user()->hasAnyPermission(['products.view', 'categories.view']))
                    <li
                        class="nav-item has-submenu {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nav-link">
                            <i class="bi bi-box-seam nav-icon"></i>
                            <span class="nav-text">{{ __('Products') }}</span>
                            <i class="bi bi-chevron-right nav-arrow"></i>
                        </a>
                        <ul class="submenu">
                            @if (auth('admin')->user()->hasPermission('products.view'))
                                <li><a href="{{ route('admin.products.index') }}"
                                        class="nav-link {{ request()->routeIs('admin.products.index') ? 'active' : '' }}">{{ __('All Products') }}</a>
                                </li>
                            @endif

                            @if (auth('admin')->user()->hasPermission('products.create'))
                                <li><a href="{{ route('admin.products.create') }}"
                                        class="nav-link {{ request()->routeIs('admin.products.create') ? 'active' : '' }}">{{ __('Add New') }}</a>
                                </li>
                            @endif

                            @if (auth('admin')->user()->hasPermission('categories.view'))
                                <li><a href="{{ route('admin.categories.index') }}"
                                        class="nav-link {{ request()->routeIs('admin.categories.index') ? 'active' : '' }}">{{ __('Categories') }}</a>
                                </li>
                            @endif



                        </ul>
                    </li>
                @endif

                <!-- Orders -->
                @if (auth('admin')->user()->hasPermission('orders.view'))
                    <li class="nav-item">
                        <a href="{{ route('admin.orders.index') }}"
                            class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                            <i class="bi bi-cart-check nav-icon"></i>
                            <span class="nav-text">{{ __('Orders') }}</span>
                            @php
                                $pendingOrders = \App\Models\Order::where('status', 'pending')->count();
                            @endphp
                            @if ($pendingOrders > 0)
                                <span class="nav-badge">{{ $pendingOrders }}</span>
                            @endif
                        </a>
                    </li>
                @endif

                <!-- Customers -->
                @if (auth('admin')->user()->hasPermission('customers.view'))
                    <li class="nav-item">
                        <a href="{{ route('admin.customers.index') }}"
                            class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                            <i class="bi bi-people nav-icon"></i>
                            <span class="nav-text">{{ __('Customers') }}</span>
                        </a>
                    </li>
                @endif
                @if (auth('admin')->user()->hasPermission('reviews.view'))
                    <li class="nav-item">
                        <a href="{{ route('admin.reviews.index') }}"
                            class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                            <i class="bi bi-star nav-icon"></i>
                            <span class="nav-text">{{ __('Product Reviews') }}</span>


                        </a>
                    </li>
                @endif

                <li class="sidebar-divider"></li>
                <li class="sidebar-heading">{{ __('Administration') }}</li>

                <!-- Admins -->
                @if (auth('admin')->user()->hasPermission('admins.view'))
                    <li class="nav-item">
                        <a href="{{ route('admin.admins.index') }}"
                            class="nav-link {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
                            <i class="bi bi-shield-lock nav-icon"></i>
                            <span class="nav-text">{{ __('Admin Users') }}</span>
                        </a>
                    </li>
                @endif

                <!-- Roles -->
                @if (auth('admin')->user()->hasPermission('roles.view'))
                    <li class="nav-item">
                        <a href="{{ route('admin.roles.index') }}"
                            class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                            <i class="bi bi-key nav-icon"></i>
                            <span class="nav-text">{{ __('Roles & Permissions') }}</span>
                        </a>
                    </li>
                @endif

                @if (auth('admin')->user()->hasPermission('cms.view'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.cms.*') ? 'active' : '' }}"
                            href="{{ route('admin.cms.index') }}">
                            <i class="bi bi-file-earmark-text nav-icon"></i>
                            <span>{{ __('CMS Pages') }}</span>
                        </a>
                    </li>
                @endif

                <!-- Reports -->
                @if (auth('admin')->user()->hasPermission('reports.view'))
                    <li class="nav-item has-submenu {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nav-link">
                            <i class="bi bi-graph-up nav-icon"></i>
                            <span class="nav-text">{{ __('Reports') }}</span>
                            <i class="bi bi-chevron-right nav-arrow"></i>
                        </a>
                        <ul class="submenu">
                            <li><a href="{{ route('admin.reports.sales') }}"
                                    class="nav-link {{ request()->routeIs('admin.reports.sales') ? 'active' : '' }}">{{ __('Sales Report') }}</a>
                            </li>
                            <li><a href="{{ route('admin.reports.products') }}"
                                    class="nav-link {{ request()->routeIs('admin.reports.products') ? 'active' : '' }}">{{ __('Products Report') }}</a>
                            </li>
                            <li><a href="{{ route('admin.reports.customers') }}"
                                    class="nav-link {{ request()->routeIs('admin.reports.customers') ? 'active' : '' }}">{{ __('Customers Report') }}</a>
                            </li>
                        </ul>
                    </li>
                @endif

                <li class="sidebar-divider"></li>
                <li class="sidebar-heading">{{ __('System') }}</li>

                <!-- Settings -->
                @if (auth('admin')->user()->hasPermission('settings.view'))
                    <li class="nav-item has-submenu {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nav-link">
                            <i class="bi bi-gear nav-icon"></i>
                            <span class="nav-text">{{ __('Settings') }}</span>
                            <i class="bi bi-chevron-right nav-arrow"></i>
                        </a>

                        <ul class="submenu">
                            <li><a href="{{ route('admin.settings.general') }}"
                                    class="nav-link {{ request()->routeIs('admin.settings.general') ? 'active' : '' }}">{{ __('General') }}</a>
                            </li>

                            @if (auth('admin')->user()->hasPermission('settings.theme'))
                                <li><a href="{{ route('admin.settings.theme') }}"
                                        class="nav-link {{ request()->routeIs('admin.settings.theme') ? 'active' : '' }}">{{ __('Theme') }}</a>
                                </li>
                            @endif

                            @if (auth('admin')->user()->hasPermission('settings.translations'))
                                <li><a href="{{ route('admin.settings.translations') }}"
                                        class="nav-link {{ request()->routeIs('admin.settings.translations') ? 'active' : '' }}">{{ __('Translations') }}</a>
                                </li>
                            @endif

                        </ul>
                    </li>
                @endif

                <!-- Activity Log -->
                @if (auth('admin')->user()->isSuperAdmin())
                    <li class="nav-item">
                        <a href="{{ route('admin.activity-log') }}"
                            class="nav-link {{ request()->routeIs('admin.activity-log') ? 'active' : '' }}">
                            <i class="bi bi-activity nav-icon"></i>
                            <span class="nav-text">{{ __('Activity Log') }}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </aside>

        <!-- Main Content -->
        <div class="admin-content">
            <!-- Top Bar -->
            <header class="admin-topbar">
                <div class="topbar-left">
                    <button class="sidebar-toggle" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>


                </div>

                <div class="topbar-right">
                    <!-- Visit Store -->
                    <a href="{{ route('home') }}" target="_blank" class="topbar-icon"
                        title="{{ __('Visit Store') }}">
                        <i class="bi bi-shop-window"></i>
                    </a>



                    <!-- Profile Dropdown -->
                    <div class="dropdown">
                        <div class="admin-profile" data-bs-toggle="dropdown">
                            <img src="{{ auth('admin')->user()->avatar_url }}" alt="Admin" class="admin-avatar">
                            <div class="admin-info">
                                <div class="admin-name">{{ auth('admin')->user()->name }}</div>
                                <div class="admin-role">{{ auth('admin')->user()->role_name }}</div>
                            </div>
                            <i class="bi bi-chevron-down"></i>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('admin.profile') }}"><i
                                        class="bi bi-person me-2"></i>{{ __('My Profile') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.settings.general') }}"><i
                                        class="bi bi-gear me-2"></i>{{ __('Settings') }}</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form action="{{ route('admin.logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i
                                            class="bi bi-box-arrow-right me-2"></i>{{ __('Logout') }}</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="content-wrapper">
                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle"></i>
                        <span>{{ session('success') }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-circle"></i>
                        <span>{{ session('error') }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Admin JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar Toggle
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('adminSidebar');

            // Toggle sidebar function
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
            });

            // Restore sidebar state
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                sidebar.classList.add('collapsed');
            }

            // Submenu Toggle
            const submenuParents = document.querySelectorAll('.nav-item.has-submenu > .nav-link');

            submenuParents.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const navItem = this.closest('.nav-item.has-submenu');
                    const wasOpen = navItem.classList.contains('open');

                    // Close all other submenus
                    document.querySelectorAll('.nav-item.has-submenu.open').forEach(item => {
                        if (item !== navItem) {
                            item.classList.remove('open');
                        }
                    });

                    // Toggle current submenu
                    if (wasOpen) {
                        navItem.classList.remove('open');
                    } else {
                        navItem.classList.add('open');
                    }
                });
            });

            // Auto-expand active menu on page load
            const activeSubmenuParent = document.querySelector('.nav-item.has-submenu.active');
            if (activeSubmenuParent) {
                activeSubmenuParent.classList.add('open');
            }

            // Prevent submenu links from closing the submenu
            const submenuLinks = document.querySelectorAll('.submenu .nav-link');
            submenuLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            });

            // Mobile sidebar toggle
            if (window.innerWidth <= 768) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    sidebar.classList.toggle('mobile-active');
                });

                // Close sidebar when clicking outside on mobile
                document.addEventListener('click', function(e) {
                    if (sidebar.classList.contains('mobile-active') &&
                        !sidebar.contains(e.target) &&
                        !sidebarToggle.contains(e.target)) {
                        sidebar.classList.remove('mobile-active');
                    }
                });

                // Prevent closing when clicking inside sidebar
                sidebar.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>

    @stack('scripts')
</body>

</html>
