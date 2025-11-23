<!DOCTYPE html>
<html lang="en" data-theme-preference="{{ Auth::user()->theme_preference ?? 'auto' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Study Helper')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- App CSS -->
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/topbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main-content.css') }}">
    
    <!-- Theme Script (must load early) -->
    <script src="{{ asset('js/theme.js') }}"></script>
    
    @stack('styles')
    
    @php
        $sidebarColor = \App\Models\Setting::get('sidebar_color', 'linear-gradient(180deg, #667eea 0%, #764ba2 100%)');
        $pagesBgImage = \App\Models\Setting::get('pages_bg_image', '');
        $pagesBgGradient = \App\Models\Setting::get('pages_bg_gradient', '');
        $pagesBgColor = \App\Models\Setting::get('pages_bg_color', '#ffffff');
    @endphp
    
    <style>
        /* Override sidebar background from settings - light mode only */
        html:not([data-bs-theme="dark"]) .sidebar { 
            background: {{ $sidebarColor }} !important; 
        }
        
        /* Override pages background from settings - light mode only */
        @if($pagesBgImage)
            html:not([data-bs-theme="dark"]) body { 
                background-image: url('{{ asset("portal-assets/backgrounds/" . $pagesBgImage) }}') !important;
                background-size: cover !important;
                background-position: center !important;
                background-attachment: fixed !important;
            }
        @elseif($pagesBgGradient)
            html:not([data-bs-theme="dark"]) body { background: {{ $pagesBgGradient }} !important; }
        @elseif($pagesBgColor && $pagesBgColor !== '#ffffff')
            html:not([data-bs-theme="dark"]) body { background-color: {{ $pagesBgColor }} !important; }
        @endif
    </style>
</head>
<body>
    @auth
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <i class="bi bi-book"></i>
                <span class="logo-text">Study Helper</span>
            </div>
            <button class="toggle-btn" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
        </div>
        
        <div class="sidebar-menu">
            @if(Auth::user()->is_superuser)
            <div class="menu-section">
                <span>Superuser</span>
            </div>
            <a href="{{ route('superuser.dashboard') }}" class="menu-item {{ request()->routeIs('superuser.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('superuser.users.index') }}" class="menu-item {{ request()->routeIs('superuser.users.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i>
                <span>All Users</span>
            </a>
            <a href="{{ route('superuser.settings.index') }}" class="menu-item {{ request()->routeIs('superuser.settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear-fill"></i>
                <span>System Settings</span>
            </a>
            @endif
            
            @if(Auth::user()->is_admin || Auth::user()->is_superuser)
            <div class="menu-section">
                <span>Admin</span>
            </div>
            <a href="{{ route('admin.files.index') }}" class="menu-item {{ request()->routeIs('admin.files.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark"></i>
                <span>Files</span>
            </a>
            <a href="{{ route('admin.students.index') }}" class="menu-item {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span>Students</span>
            </a>
            <a href="{{ route('admin.invitations.index') }}" class="menu-item {{ request()->routeIs('admin.invitations.*') ? 'active' : '' }}">
                <i class="bi bi-envelope-plus"></i>
                <span>Invitations</span>
            </a>
            @endif
            
            @if(!Auth::user()->is_admin && !Auth::user()->is_superuser)
            <div class="menu-section">
                <span>Student</span>
            </div>
            <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-house"></i>
                <span>My Lessons</span>
            </a>
            @endif
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="top-bar">
            <div class="user-menu">
                <div class="user-info">
                    <div class="user-avatar">
                        @if(Auth::user()->avatar_small)
                            <img src="{{ asset('avatars/small/' . Auth::user()->avatar_small) }}" alt="{{ Auth::user()->name }}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                        @else
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="user-details">
                        <div class="user-name">{{ Auth::user()->name }}</div>
                        <div class="user-role {{ Auth::user()->is_superuser ? 'superuser' : '' }}">
                            @if(Auth::user()->is_superuser)
                                Super Administrator
                            @elseif(Auth::user()->is_admin)
                                Admin
                            @else
                                Student
                            @endif
                        </div>
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-gear"></i> Settings
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
        <div class="content-wrapper">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @yield('content')
        </div>
    </div>
    @endauth

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sidebar toggle functionality
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const sidebarToggle = document.getElementById('sidebarToggle');
        
        // Load saved state from localStorage
        const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (sidebarCollapsed) {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
        }
        
        // Toggle sidebar
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            
            // Save state to localStorage
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        });
    </script>
    
    @stack('scripts')
</body>
</html>
