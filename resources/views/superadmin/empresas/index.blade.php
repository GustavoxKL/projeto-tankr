<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TANKR - Empresas</title>

    <link rel="stylesheet" href="{{ asset('css/dashboard_superadmin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/superadmin/empresas.css') }}">

    <script src="{{ asset('js/helpers.js') }}"></script>
</head>
<body>

    <!-- Sidebar -->
    @include('partials.sidebar_superadmin')

    <!-- Main Content -->
    <main class="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <h1 class="page-title">Empresas Cadastradas</h1>
            
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
            <div class="empresas-container">
                <!-- Header -->
                <div class="page-header">
                    <div class="header-info">
                        <p class="section-subtitle">Gerencie as empresas do sistema</p>
                    </div>

                    <div class="header-actions">
                        <div class="search-box">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input 
                                type="text" 
                                id="searchEmpresa" 
                                placeholder="Pesquisar empresa..." 
                                oninput="filtrarEmpresas()"
                            > 
                            <button class="btn-clear-search" id="btnClearSearch" onclick="limparBusca()" style="display: none;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <button class="btn-add-empresa" onclick="abrirModalEmpresa()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Adicionar Empresa
                        </button>
                    </div>
                </div>

                <!-- Grid de Cards -->
                <div class="empresas-grid">
                    @forelse($empresas as $empresa)
                    <div class="empresa-card" data-nome="{{ strtolower($empresa->NomeEmpresa) }}" data-cnpj="{{ $empresa->CNPJ }}">
                        <!-- Header do Card -->
                        <div class="card-header">
                            <div class="empresa-header-left">
                                <div class="empresa-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2">
                                        <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18"/>
                                        <path d="M6 12H4a2 2 0 0 0-2 2v8h20v-8a2 2 0 0 0-2-2h-2"/>
                                        <path d="M10 6h4"/>
                                        <path d="M10 10h4"/>
                                        <path d="M10 14h4"/>
                                        <path d="M10 18h4"/>
                                    </svg>
                                </div>
                                <div class="empresa-info-header">
                                    <h3 class="empresa-nome">{{ $empresa->NomeEmpresa }}</h3>
                                    <p class="empresa-cnpj">CNPJ: {{ \App\Helpers\Formatador::cnpj($empresa->CNPJ) }}</p>
                                </div>
                            </div>
                            
                            <div class="card-menu">
                                <button class="btn-menu" onclick="toggleMenu({{ $empresa->ID_EMPRESA }})">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                    </svg>
                                </button>
                                <div class="dropdown-menu" id="menu-{{ $empresa->ID_EMPRESA }}">
                                    <a href="#" onclick="event.preventDefault(); editarEmpresa({{ $empresa->ID_EMPRESA }})">Editar</a>
                                    <a href="#" onclick="event.preventDefault(); excluirEmpresa({{ $empresa->ID_EMPRESA }})" class="delete">Excluir</a>
                                </div>
                            </div>
                        </div>

                        <!-- Localização -->
                        <div class="empresa-location">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>{{ $empresa->EnderecoEmpresa ?? 'Endereço não informado' }}</span>
                        </div>

                        <!-- Telefone -->
                        <div class="empresa-telefone">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span>{{ \App\Helpers\Formatador::telefone($empresa->TelefoneEmpresa) }}</span>
                        </div>

                        <!-- Estatísticas -->
                        <div class="empresa-stats">
                            <div class="stat-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                    <path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/>
                                    <path d="M15 18H9"/>
                                    <path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.62l-3.48-4.35A1 1 0 0 0 17.52 8H14"/>
                                    <circle cx="7" cy="18" r="2"/>
                                    <circle cx="17" cy="18" r="2"/>
                                </svg>
                                <div class="stat-info">
                                    <span class="stat-label">Veículos</span>
                                    <span class="stat-value">{{ $empresa->veiculos_count ?? 0 }}</span>
                                </div>
                            </div>

                            <div class="stat-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <div class="stat-info">
                                    <span class="stat-label">Motoristas</span>
                                    <span class="stat-value">{{ $empresa->motoristas_count ?? 0 }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Combustível/mês -->
                        <div class="empresa-fuel">
                            <span class="fuel-label">Combustível/mês:</span>
                            <span class="fuel-value">R$ {{ number_format(rand(50000, 300000), 2, ',', '.') }}</span>
                        </div>

                        <!-- Status Badge -->
                        <div class="empresa-status">
                            <span class="status-badge status-{{ $empresa->StatusEmpresa ? 'ativa' : 'inativa' }}">
                                {{ $empresa->StatusEmpresa ? 'Ativa' : 'Inativa' }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="empty-state">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <h3>Nenhuma empresa cadastrada</h3>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Modal Adicionar/Editar Empresa -->
            <div class="modal" id="modalEmpresa">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 id="modalTitle">Adicionar Empresa</h3>
                        <button class="btn-close" onclick="fecharModalEmpresa()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <form id="formEmpresa" method="POST">
                        @csrf
                        <input type="hidden" id="empresaId" name="id">
                        
                        <div class="form-group">
                            <label for="nome">Nome da Empresa *</label>
                            <input type="text" id="nome" name="nome" required>
                        </div>

                        <div class="form-group">
                            <label for="cnpj">CNPJ *</label>
                            <input type="text" id="cnpj" name="cnpj" required>
                        </div>

                        <div class="form-group">
                            <label for="telefone">Telefone</label>
                            <input type="text" id="telefone" name="telefone">
                        </div>

                        <div class="form-group">
                            <label for="endereco">Endereço</label>
                            <input type="text" id="endereco" name="endereco">
                        </div>

                        <div class="form-group" id="statusGroup" style="display: none;">
                            <label for="status">Status *</label>
                            <select id="status" name="status">
                                <option value="1">Ativa</option>
                                <option value="0">Inativa</option>
                            </select>
                        </div>

                        <div class="modal-actions">
                            <button type="button" class="btn-cancel" onclick="fecharModalEmpresa()">Cancelar</button>
                            <button type="submit" class="btn-save">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="{{ asset('js/superadmin/empresas.js') }}"></script>
</body>
</html>