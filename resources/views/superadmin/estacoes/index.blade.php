<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TANKR - Estações</title>

    <link rel="stylesheet" href="{{ asset('css/superadmin/dashboard_superadmin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/superadmin/estacoes.css') }}">

    <script src="{{ asset('js/helpers.js') }}"></script>
</head>
<body>

    <!-- Sidebar -->
    @include('partials.sidebar_superadmin')

    <!-- Main Content -->
    <main class="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <h1 class="page-title">Estações de Abastecimento</h1>
            
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
            <div class="estacoes-container">
                <!-- Header -->
                <div class="page-header">
                    <div class="header-info">
                        <p class="section-subtitle">Gerencie as estações de abastecimento do sistema</p>
                    </div>
                    
                    <div class="header-actions">
                        <!-- Toggle de Visualização -->
                        <div class="view-toggle">
                            <button class="btn-toggle active" id="btnGroupView" onclick="mudarVisualizacao('grupo')">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Por Empresa
                            </button>
                            <button class="btn-toggle" id="btnListView" onclick="mudarVisualizacao('lista')">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                </svg>
                                Todas
                            </button>
                        </div>

                        <!-- Campo de busca -->
                        <div class="search-box">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input 
                                type="text" 
                                id="searchEstacao" 
                                placeholder="Pesquisar estação..." 
                                oninput="filtrarEstacoes()"
                            >
                            <button class="btn-clear-search" id="btnClearSearch" onclick="limparBusca()" style="display: none;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        <button class="btn-add-estacao" onclick="abrirModalEstacao()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Adicionar Estação
                        </button>
                    </div>
                </div>

                <!-- VISUALIZAÇÃO POR EMPRESA (PADRÃO) -->
                <div id="viewGrupo" class="view-content">
                    @forelse($empresas as $empresa)
                        @php
                            $estacoesEmpresa = $estacoesAgrupadas->get($empresa->ID_EMPRESA, collect());
                        @endphp
                        @if($estacoesEmpresa->count() > 0)
                            <div class="empresa-group" data-empresa-nome="{{ strtolower($empresa->NomeEmpresa) }}">
                                <!-- Header da Empresa (clicável) -->
                                <div class="empresa-header" onclick="toggleEmpresa({{ $empresa->ID_EMPRESA }})">
                                    <div class="empresa-header-left">
                                        <svg class="chevron-icon" id="chevron-{{ $empresa->ID_EMPRESA }}" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                        <div class="empresa-icon-small">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="empresa-nome">{{ $empresa->NomeEmpresa }}</h3>
                                            <p class="empresa-info">CNPJ: {{ \App\Helpers\Formatador::cnpj($empresa->CNPJ) }}</p>
                                        </div>
                                    </div>
                                    <div class="empresa-badge">
                                        {{ $estacoesEmpresa->count() }} {{ $estacoesEmpresa->count() == 1 ? 'estação' : 'estações' }}
                                    </div>
                                </div>

                                <!-- Estações da Empresa (expandível) -->
                                <div class="empresas-content" id="content-{{ $empresa->ID_EMPRESA }}">
                                    <div class="estacoes-row">
                                        @foreach($estacoesEmpresa as $estacao)
                                            @include('superadmin.estacoes.partials.card-estacao', ['estacao' => $estacao])
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="empty-state">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18"/>
                            </svg>
                            <h3>Nenhuma estação cadastrada</h3>
                            <p>Clique no botão "Adicionar Estação" para começar</p>
                        </div>
                    @endforelse
                </div>

                <!-- VISUALIZAÇÃO EM LISTA (TODOS) -->
                <div id="viewLista" class="view-content" style="display: none;">
                    <div class="estacoes-grid">
                        @foreach($estacoes as $estacao)
                            @include('superadmin.estacoes.partials.card-estacao', ['estacao' => $estacao])
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Modal Adicionar/Editar Estação -->
            <div class="modal" id="modalEstacao">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 id="modalTitle">Adicionar Estação</h3>
                        <button class="btn-close" onclick="fecharModalEstacao()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <form id="formEstacao" method="POST">
                        @csrf
                        <input type="hidden" id="estacaoId" name="id">
                        
                        <div class="form-group">
                            <label for="token">Token *</label>
                            <input type="text" id="token" name="token" required maxlength="20" placeholder="Ex: GH3, FX1">
                            <small class="form-hint">Identificador único da estação</small>
                        </div>

                        <div class="form-group">
                            <label for="endereco">Endereço</label>
                            <input type="text" id="endereco" name="endereco" placeholder="Endereço completo">
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

                        <div class="modal-actions">
                            <button type="button" class="btn-cancel" onclick="fecharModalEstacao()">Cancelar</button>
                            <button type="submit" class="btn-save">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    
    <script src="{{ asset('js/superadmin/estacoes.js') }}"></script>
</body>
</html>