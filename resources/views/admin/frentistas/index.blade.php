<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TANKR - Frentistas</title>
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard_admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/frentistas.css') }}">
    <script src="{{ asset('js/helpers.js') }}"></script>
</head>
<body>

    <!-- Sidebar -->
    @include('partials.sidebar_admin')

    <!-- Main Content -->
    <main class="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <h1 class="page-title">Frentistas</h1>
            
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
            <div class="frentistas-container">
                <!-- Header com botão adicionar -->
                <div class="page-header">
                    <div class="header-info">
                        <p class="section-subtitle">Gerencie os frentistas da sua empresa</p>
                    </div>
                    
                    <div class="header-actions">
                        <!-- Campo de busca -->
                        <div class="search-box">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input 
                                type="text" 
                                id="searchFrentista" 
                                placeholder="Pesquisar frentista..." 
                                oninput="filtrarFrentistas()"
                            >
                            <button class="btn-clear-search" id="btnClearSearch" onclick="limparBusca()" style="display: none;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        <button class="btn-add-frentista" onclick="abrirModalFrentista()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Adicionar Frentista
                        </button>
                    </div>
                </div>

                <!-- Grid de Cards -->
                <div class="frentistas-grid">
                    @forelse($frentistas as $frentista)
                    <div class="frentista-card" 
                         data-nome="{{ strtolower($frentista->NomeFren) }}"
                         data-rfid="{{ strtolower($frentista->ID_FRENTISTA) }}">
                        
                        <!-- Header do Card -->
                        <div class="card-header">
                            <div class="frentista-header-left">
                                <div class="frentista-avatar">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                    </svg>
                                </div>
                                <div class="frentista-info-header">
                                    <h3 class="frentista-nome">{{ $frentista->NomeFren }}</h3>
                                    <p class="frentista-rfid">RFID: <span>{{ $frentista->ID_FRENTISTA }}</span></p>
                                </div>
                            </div>
                            
                            <div class="card-menu">
                                <button class="btn-menu" onclick="toggleMenu('{{ $frentista->ID_FRENTISTA }}')">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                    </svg>
                                </button>
                                <div class="dropdown-menu" id="menu-{{ $frentista->ID_FRENTISTA }}">
                                    <a href="#" onclick="event.preventDefault(); editarFrentista('{{ $frentista->ID_FRENTISTA }}')">
                                        Editar
                                    </a>
                                    <a href="#" onclick="event.preventDefault(); excluirFrentista('{{ $frentista->ID_FRENTISTA }}')" class="delete">
                                        Excluir
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Informações -->
                        <div class="frentista-info">
                            <div class="info-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>Cadastrado em: {{ \Carbon\Carbon::parse($frentista->DataCadastroFren)->format('d/m/Y') }}</span>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="frentista-status">
                            <span class="status-badge status-{{ $frentista->StatusFren ? 'ativo' : 'inativo' }}">
                                {{ $frentista->StatusFren ? 'Ativo' : 'Inativo' }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="empty-state">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4" stroke-width="2"/>
                        </svg>
                        <h3>Nenhum frentista cadastrado</h3>
                        <p>Clique no botão "Adicionar Frentista" para começar</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Modal Adicionar/Editar Frentista -->
            <div class="modal" id="modalFrentista">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 id="modalTitle">Adicionar Frentista</h3>
                        <button class="btn-close" onclick="fecharModalFrentista()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <form id="formFrentista" method="POST">
                        @csrf
                        <input type="hidden" id="modoEdicao" value="0">
                        
                        <div class="form-group">
                            <label for="rfid">RFID (ID do cartão) *</label>
                            <input type="text" id="rfid" name="rfid" required maxlength="50" placeholder="Ex: 04A7B3F2">
                            <small class="form-hint">Este será o identificador único do frentista</small>
                        </div>

                        <div class="form-group">
                            <label for="nome">Nome Completo *</label>
                            <input type="text" id="nome" name="nome" required maxlength="100">
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
                            <button type="button" class="btn-cancel" onclick="fecharModalFrentista()">Cancelar</button>
                            <button type="submit" class="btn-save">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    
    <script src="{{ asset('js/admin/frentistas.js') }}"></script>
</body>
</html>