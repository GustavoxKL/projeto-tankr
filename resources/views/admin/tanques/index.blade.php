<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TANKR - Tanques</title>
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard_admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/tanques.css') }}">
    <script src="{{ asset('js/helpers.js') }}"></script>
</head>
<body>

    <!-- Sidebar -->
    @include('partials.sidebar_admin')

    <!-- Main Content -->
    <main class="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <h1 class="page-title">Tanques</h1>
            
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
            <div class="tanques-container">
                <!-- Header com botão adicionar -->
                <div class="page-header">
                    <div class="header-info">
                        <p class="section-subtitle">Gerencie os tanques da sua empresa</p>
                    </div>
                    
                    <div class="header-actions">
                        <!-- Campo de busca -->
                        <div class="search-box">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input 
                                type="text" 
                                id="searchTanque" 
                                placeholder="Pesquisar tanque..." 
                                oninput="filtrarTanques()"
                            >
                            <button class="btn-clear-search" id="btnClearSearch" onclick="limparBusca()" style="display: none;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        <button class="btn-add-tanque" onclick="abrirModalTanque()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Adicionar Tanque
                        </button>
                    </div>
                </div>

                <!-- Grid de Cards -->
                <div class="tanques-grid">
                    @forelse($tanques as $tanque)
                        @php
                            $porcentagem = $tanque->CapacidadeMaxTanque > 0 
                                ? round(($tanque->QuantidadeAtualTanque / $tanque->CapacidadeMaxTanque) * 100, 1) 
                                : 0;
                            
                            // Definir cor com base na porcentagem
                            if ($porcentagem >= 60) {
                                $corBarra = 'verde';
                            } elseif ($porcentagem >= 30) {
                                $corBarra = 'amarelo';
                            } else {
                                $corBarra = 'vermelho';
                            }
                        @endphp
                        
                        <div class="tanque-card" 
                             data-nome="{{ strtolower($tanque->NomeTanque) }}"
                             data-tipo="{{ strtolower($tanque->TipoCombustivelTanque) }}">
                            
                            <!-- Header do Card -->
                            <div class="card-header">
                                <div class="tanque-header-left">
                                    <div class="tanque-avatar">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <ellipse cx="12" cy="5" rx="9" ry="3"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5v14a9 3 0 0 0 18 0V5"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12a9 3 0 0 0 18 0"/>
                                        </svg>
                                    </div>
                                    <div class="tanque-info-header">
                                        <h3 class="tanque-nome">{{ $tanque->NomeTanque }}</h3>
                                        <p class="tanque-tipo">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 19h16M4 15h16M9 15v4M15 15v4M13 4v7m-2-4v4"/>
                                            </svg>
                                            {{ $tanque->TipoCombustivelTanque }}
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="card-menu">
                                    <button class="btn-menu" onclick="toggleMenu({{ $tanque->ID_TANQUE }})">
                                        <svg viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                        </svg>
                                    </button>
                                    <div class="dropdown-menu" id="menu-{{ $tanque->ID_TANQUE }}">
                                        <a href="#" onclick="event.preventDefault(); editarTanque({{ $tanque->ID_TANQUE }})">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Editar
                                        </a>
                                        <a href="#" onclick="event.preventDefault(); excluirTanque({{ $tanque->ID_TANQUE }})" class="delete">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Excluir
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Capacidade + Barra de Progresso -->
                            <div class="tanque-capacidade">
                                <div class="capacidade-info">
                                    <span class="capacidade-label">Nível do Tanque</span>
                                    <span class="capacidade-porcentagem porcentagem-{{ $corBarra }}">{{ $porcentagem }}%</span>
                                </div>
                                
                                <div class="progress-bar">
                                    <div class="progress-fill progress-{{ $corBarra }}" style="width: {{ $porcentagem }}%"></div>
                                </div>
                                
                                <div class="capacidade-litros">
                                    <span class="litros-atual">{{ number_format($tanque->QuantidadeAtualTanque, 0, ',', '.') }} L</span>
                                    <span class="litros-separator">/</span>
                                    <span class="litros-max">{{ number_format($tanque->CapacidadeMaxTanque, 0, ',', '.') }} L</span>
                                </div>
                            </div>

                            <!-- Info adicional -->
                            <div class="tanque-info-extra">
                                <div class="info-extra-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>Último abast.: 
                                        @if($tanque->DataUltAbastecimentoTanque)
                                            {{ \Carbon\Carbon::parse($tanque->DataUltAbastecimentoTanque)->format('d/m/Y') }}
                                        @else
                                            <span class="text-muted">Nunca</span>
                                        @endif
                                    </span>
                                </div>

                                <div class="info-extra-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 22h12"/>
                                    </svg>
                                    <span>{{ $tanque->estacoes->count() }} 
                                        {{ $tanque->estacoes->count() == 1 ? 'estação vinculada' : 'estações vinculadas' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="tanque-status">
                                <span class="status-badge status-{{ $tanque->StatusTanque ? 'ativo' : 'inativo' }}">
                                    {{ $tanque->StatusTanque ? 'Ativo' : 'Inativo' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <ellipse cx="12" cy="5" rx="9" ry="3"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5v14a9 3 0 0 0 18 0V5"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12a9 3 0 0 0 18 0"/>
                            </svg>
                            <h3>Nenhum tanque cadastrado</h3>
                            <p>Clique no botão "Adicionar Tanque" para começar</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Modal Adicionar/Editar Tanque -->
            <div class="modal" id="modalTanque">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 id="modalTitle">Adicionar Tanque</h3>
                        <button class="btn-close" onclick="fecharModalTanque()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <form id="formTanque" method="POST">
                        @csrf
                        <input type="hidden" id="tanqueId" name="id">
                        
                        <div class="form-group">
                            <label for="nome">Nome do Tanque *</label>
                            <input type="text" id="nome" name="nome" required maxlength="50" placeholder="Ex: Tanque Principal">
                        </div>

                        <div class="form-group">
                            <label for="tipo_combustivel">Tipo de Combustível *</label>
                            <select id="tipo_combustivel" name="tipo_combustivel" required>
                                <option value="">Selecione...</option>
                                <option value="Diesel S10">Diesel S10</option>
                                <option value="Diesel S500">Diesel S500</option>
                                <option value="Gasolina Comum">Gasolina Comum</option>
                                <option value="Gasolina Aditivada">Gasolina Aditivada</option>
                                <option value="Etanol">Etanol</option>
                                <option value="GNV">GNV</option>
                            </select>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="capacidade_max">Capacidade Máxima (L) *</label>
                                <input type="number" id="capacidade_max" name="capacidade_max" required min="1" step="0.01" placeholder="50000">
                            </div>

                            <div class="form-group">
                                <label for="quantidade_atual">Quantidade Atual (L) *</label>
                                <input type="number" id="quantidade_atual" name="quantidade_atual" required min="0" step="0.01" placeholder="0">
                            </div>
                        </div>

                        <!-- Estações vinculadas -->
                        <div class="form-group">
                            <label>Estações Vinculadas</label>
                            <small class="form-hint">Selecione as estações que este tanque alimenta</small>
                            <div class="estacoes-checkboxes">
                                @forelse($estacoes as $estacao)
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="estacoes[]" value="{{ $estacao->ID_ESTACAO }}">
                                        <span class="checkbox-custom"></span>
                                        <span class="checkbox-label">
                                            <strong>{{ $estacao->Token }}</strong>
                                            @if($estacao->EnderecoEst)
                                                <span class="checkbox-endereco">{{ $estacao->EnderecoEst }}</span>
                                            @endif
                                        </span>
                                    </label>
                                @empty
                                    <p class="no-estacoes">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px; display: inline; vertical-align: middle;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Nenhuma estação cadastrada. Você pode vincular depois.
                                    </p>
                                @endforelse
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
                            <button type="button" class="btn-cancel" onclick="fecharModalTanque()">Cancelar</button>
                            <button type="submit" class="btn-save">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    
    <script src="{{ asset('js/admin/tanques.js') }}"></script>
</body>
</html>