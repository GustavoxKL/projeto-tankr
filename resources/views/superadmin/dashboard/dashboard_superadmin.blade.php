<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TANKR - Dashboard')</title>
    
    <link rel="stylesheet" href="{{ asset('css/superadmin/dashboard_superadmin.css') }}">
    @yield('styles')
</head>
<body>

    <!-- Sidebar -->
    @include('partials.sidebar_superadmin')

    <!-- Main Content -->
    <main class="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <h1 class="page-title">Dashboard</h1>
            
            <div class="topbar-actions">
                <button class="btn-notification">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="notification-badge">3</span>
                </button>
            </div>
        </header>

        <!-- Page Content -->
        <div class="page-content">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Scripts -->
    <script src="{{ asset('js/superadmin/dashboard_superadmin.js') }}"></script>
    @yield('scripts')
    
</body>
</html>