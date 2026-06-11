<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo-small">
            <svg class="fuel-icon" viewBox="0 0 24 24" fill="currentColor">
                <path d="M19.77 7.23l.01-.01-3.72-3.72L15 4.56l2.11 2.11c-.94.36-1.61 1.26-1.61 2.33 0 1.38 1.12 2.5 2.5 2.5.36 0 .69-.08 1-.21v7.21c0 .55-.45 1-1 1s-1-.45-1-1V14c0-1.1-.9-2-2-2h-1V5c0-1.1-.9-2-2-2H6c-1.1 0-2 .9-2 2v16h10v-7.5h1.5v5c0 1.38 1.12 2.5 2.5 2.5s2.5-1.12 2.5-2.5V9c0-.69-.28-1.32-.73-1.77zM12 10H6V5h6v5zm6 0c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1z"/>
            </svg>
            <span class="logo-text-small">TANKR</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('admin.dashboard.index') }}" class="nav-item {{ request()->routeIs('admin.dashboard.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="2" y="1" width="9" height="9" rx="2"/>
                <rect x="16" y="2" width="6" height="6" rx="1.5"/>
                <rect x="3" y="15" width="6" height="6" rx="1.5"/>
                <rect x="14" y="12" width="9" height="9" rx="2"/>
            </svg>
            <span>Dashboard</span>
        </a>

        <a href="#" class="nav-item {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span>Usuários</span>
        </a>

        <a href="#" class="nav-item {{ request()->routeIs('admin.motoristas.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span>Motoristas</span>
        </a>

        <a href="#" class="nav-item {{ request()->routeIs('admin.veiculos.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/>
                <path d="M15 18H9"/>
                <path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.62l-3.48-4.35A1 1 0 0 0 17.52 8H14"/>
                <circle cx="7" cy="18" r="2"/>
                <circle cx="17" cy="18" r="2"/>
            </svg>
            <span>Veículos</span>
        </a>

        <a href="#" class="nav-item {{ request()->routeIs('admin.abastecimentos.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <path d="M3 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18"/>
                <path d="M3 22h12"/>
                <path d="M18 7v10a2 2 0 0 0 4 0v-6a2 2 0 0 0-2-2h-2"/>
                <path d="M15 6h2l3 3"/>
                <path d="M7 6h4"/>
                <path d="M7 10h4"/>
            </svg>
            <span>Abastecimentos</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">
                {{ substr(session('nome', 'U'), 0, 1) }}
            </div>
            <div class="user-details">
                <span class="user-name">{{ session('nome', 'Usuário') }}</span>
                <span class="user-role">{{ session('tipo', 'admin') }}</span>
            </div>
        </div>
            
        <form action="{{ route('logout.web') }}" method="POST" class="logout-form">
            @csrf
            <button type="submit" class="btn-logout">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Sair
            </button>
        </form>
    </div>
</aside>