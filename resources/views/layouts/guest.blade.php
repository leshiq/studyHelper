<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Study Helper')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    @php
        $loginBgImage = \App\Models\Setting::get('login_bg_image', '');
        $loginBgGradient = \App\Models\Setting::get('login_bg_gradient', 'linear-gradient(135deg, #667eea, #764ba2)');
        $loginBgColor = \App\Models\Setting::get('login_bg_color', '#667eea');
    @endphp
    
    <style>
        /* CSS Custom Properties for dynamic theming */
        :root {
            @if($loginBgImage)
                --login-bg: url('{{ asset("portal-assets/backgrounds/" . $loginBgImage) }}');
            @elseif($loginBgGradient)
                --login-bg: {{ $loginBgGradient }};
            @else
                --login-bg: {{ $loginBgColor }};
            @endif
        }
        
        /* Apply background image styles if set */
        @if($loginBgImage)
            body {
                background-image: var(--login-bg) !important;
                background-size: cover !important;
                background-position: center !important;
                background-repeat: no-repeat !important;
            }
        @endif
    </style>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    <main class="flex-grow-1 d-flex align-items-center py-4">
        @if(session('success'))
        <div class="container mb-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="container mb-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
