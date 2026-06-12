<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TANKR - Veículos</title>

    <link rel="stylesheet" href="{{ asset('css/superadmin/dashboard_superadmin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/superadmin/veiculos.css') }}">
</head>
<body>

    <!-- Sidebar -->
    @include('partials.sidebar_superadmin')

    <!-- Main Content -->
    <main class="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <h1 class="page-title">Veículos Cadastradas</h1>
            
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
            <div class="veiculos-container">
                <!-- Header com botão adicionar -->
                <div class="page-header">
                    <div class="header-info">
                        <p class="section-subtitle">Gerencie os veículos do sistema</p>
                    </div>
                    
                    <div class="header-actions">
                        <!-- Campo de busca -->
                        <div class="search-box">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input 
                                type="text" 
                                id="searchVeiculo" 
                                placeholder="Pesquisar veículo..." 
                                oninput="filtrarVeiculos()"
                            >
                            <button class="btn-clear-search" id="btnClearSearch" onclick="limparBusca()" style="display: none;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        <button class="btn-add-veiculo" onclick="abrirModalVeiculo()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Adicionar Veículo
                        </button>
                    </div>
                </div>

                <!-- Grid de Cards -->
                <div class="veiculos-grid">
                    @forelse($veiculos as $veiculo)
                    <div class="veiculo-card" 
                         data-modelo="{{ strtolower($veiculo->ModeloVei ?? '') }}"
                         data-placa="{{ strtolower($veiculo->PlacaVei ?? '') }}"
                         data-empresa="{{ strtolower($veiculo->empresa->NomeEmpresa ?? '') }}">
                        
                        <!-- Header do Card -->
                        <div class="card-header">
                            <div class="veiculo-header-left">
                                <div class="veiculo-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 18H9"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.62l-3.48-4.35A1 1 0 0 0 17.52 8H14"/>
                                        <circle cx="7" cy="18" r="2"/>
                                        <circle cx="17" cy="18" r="2"/>
                                    </svg>
                                </div>
                                <div class="veiculo-info-header">
                                    <h3 class="veiculo-modelo">{{ $veiculo->ModeloVei ?? 'Sem modelo' }}</h3>
                                    <p class="veiculo-placa">{{ $veiculo->PlacaVei ?? 'Sem placa' }}</p>
                                </div>
                            </div>
                            
                            <div class="card-menu">
                                <button class="btn-menu" onclick="toggleMenu({{ $veiculo->ID_VEICULO }})">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                    </svg>
                                </button>
                                <div class="dropdown-menu" id="menu-{{ $veiculo->ID_VEICULO }}">
                                    <a href="#" onclick="event.preventDefault(); editarVeiculo({{ $veiculo->ID_VEICULO }})">
                                        Editar
                                    </a>
                                    <a href="#" onclick="event.preventDefault(); excluirVeiculo({{ $veiculo->ID_VEICULO }})" class="delete">
                                        Excluir
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Modelo e Ano -->
                        <div class="veiculo-info-main">
                            <div class="info-row">
                                <span class="info-label">Modelo:</span>
                                <span class="info-value">{{ $veiculo->ModeloVei ?? 'Não informado' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Ano:</span>
                                <span class="info-value">{{ $veiculo->AnoVei ?? '----' }}</span>
                            </div>
                        </div>

                        <!-- Info adicional (placeholders por enquanto) -->
                        <div class="veiculo-info-extra">
                            <div class="info-extra-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                <span>Consumo: <span class="text-muted">-- km/L</span></span>
                            </div>

                            <div class="info-extra-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>Último abast.: <span class="text-muted">--</span></span>
                            </div>

                            <div class="info-extra-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span>KM: <span class="text-muted">--</span></span>
                            </div>
                        </div>

                        <!-- Empresa -->
                        <div class="veiculo-empresa">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span>{{ $veiculo->empresa->NomeEmpresa ?? 'Sem empresa' }}</span>
                        </div>

                        <!-- Status -->
                        <div class="veiculo-status">
                            <span class="status-badge status-{{ $veiculo->StatusVei ? 'ativo' : 'inativo' }}">
                                {{ $veiculo->StatusVei ? 'Ativo' : 'Inativo' }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="empty-state">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 18H9"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.62l-3.48-4.35A1 1 0 0 0 17.52 8H14"/>
                        </svg>
                        <h3>Nenhum veículo cadastrado</h3>
                        <p>Clique no botão "Adicionar Veículo" para começar</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Modal Adicionar/Editar Veículo -->
            <div class="modal" id="modalVeiculo">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 id="modalTitle">Adicionar Veículo</h3>
                        <button class="btn-close" onclick="fecharModalVeiculo()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <form id="formVeiculo" method="POST">
                        @csrf
                        <input type="hidden" id="veiculoId" name="id">
                        
                        <div class="form-group">
                            <label for="modelo">Modelo *</label>
                            <input type="text" id="modelo" name="modelo" required placeholder="Ex: Volvo FH 540">
                        </div>

                        <div class="form-group">
                            <label for="placa">Placa *</label>
                            <input type="text" id="placa" name="placa" required maxlength="8" placeholder="ABC-1234">
                        </div>

                        <div class="form-group">
                            <label for="ano">Ano *</label>
                            <input type="number" id="ano" name="ano" required min="1900" max="2030" placeholder="2024">
                        </div>

                        <div class="form-group">
                            <label for="empresa_search">Empresa *</label>
                            <div class="custom-select" id="customSelectEmpresa">
                                <input 
                                    type="text" 
                                    id="empresa_search" 
                                    placeholder="Digite para pesquisar empresa..." 
                                    autocomplete="off"
                                    onfocus="abrirDropdownEmpresa()"
                                    oninput="filtrarEmpresas()"
                                    required
                                >
                                <input type="hidden" id="empresa_id" name="empresa_id">
        
                                <div class="custom-select-arrow">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
        
                                <div class="custom-select-dropdown" id="dropdownEmpresas">
                                    @foreach($empresas as $empresa)
                                        <div class="custom-select-option" 
                                            data-id="{{ $empresa->ID_EMPRESA }}" 
                                            data-nome="{{ strtolower($empresa->NomeEmpresa) }}"
                                            onclick="selecionarEmpresa({{ $empresa->ID_EMPRESA }}, '{{ $empresa->NomeEmpresa }}')">
                                                {{ $empresa->NomeEmpresa }}
                                        </div>
                                    @endforeach
                                    <div class="custom-select-no-results" id="noResultsEmpresa" style="display: none;">
                                        Nenhuma empresa encontrada
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Campo Status: visível apenas na edição -->
                        <div class="form-group" id="statusGroup" style="display: none;">
                            <label for="status">Status *</label>
                            <select id="status" name="status">
                                <option value="1">Ativo</option>
                                <option value="0">Inativo</option>
                            </select>
                        </div>

                        <div class="modal-actions">
                            <button type="button" class="btn-cancel" onclick="fecharModalVeiculo()">Cancelar</button>
                            <button type="submit" class="btn-save">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="{{ asset('js/superadmin/veiculos.js') }}"></script>
</body>
</html>