<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TANKR - Usuários</title>

    <link rel="stylesheet" href="{{ asset('css/dashboard_superadmin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/superadmin/usuarios.css') }}">
</head>
<body>

    <!-- Sidebar -->
    @include('partials.sidebar_superadmin')

    <!-- Main Content -->
    <main class="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <h1 class="page-title">Usuários do Sistema</h1>
            
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
            <div class="usuarios-container">
                <!-- Header -->
                <div class="page-header">
                    <div class="header-info">
                        <p class="section-subtitle">Gerencie os usuários com acesso ao sistema</p>
                    </div>

                    <div class="header-actions">
                        <div class="search-box">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" id="searchUsuario" placeholder="Pesquisar por nome, email..." oninput="filtrarUsuarios()">

                            <button class="btn-clear-search" id="btnClearSearch" onclick="limparBuscaUsuario()" style="display: none;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <button class="btn-add-usuario" onclick="abrirModalUsuario()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Adicionar Usuário
                        </button>
                    </div>
                </div>

                <!-- Tabela de Usuários -->
                <div class="table-container">
                    <table class="usuarios-table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Empresa</th>
                                <th>Tipo</th>
                                <th>Status</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($usuarios as $usuario)
                            <tr data-nome="{{ strtolower($usuario->NomeUser) }}" 
                                data-email="{{ strtolower($usuario->email) }}" 
                                data-empresa="{{ strtolower($usuario->empresa->NomeEmpresa ?? 'sistema') }}" 
                                data-tipo="{{ $usuario->TipoUser }}" 
                                data-status="{{ $usuario->StatusUser ? 'ativo' : 'inativo' }}">
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar-small">
                                            {{ substr($usuario->NomeUser, 0, 1) }}
                                        </div>
                                        <span class="user-name-table">{{ $usuario->NomeUser }}</span>
                                    </div>
                                </td>

                                <td>{{ $usuario->email }}</td>

                                <td>
                                    @if($usuario->empresa)
                                        <span class="empresa-tag">{{ $usuario->empresa->NomeEmpresa }}</span>
                                    @else
                                        <span class="empresa-tag sistema">Sistema</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="tipo-badge tipo-{{ $usuario->TipoUser }}">
                                        {{ ucfirst($usuario->TipoUser) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="status-badge status-{{ $usuario->StatusUser ? 'ativo' : 'inativo' }}">
                                        {{ $usuario->StatusUser ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    <div class="action-buttons">
                                        <button class="btn-action btn-edit" onclick="editarUsuario({{ $usuario->ID_USER }})" title="Editar">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>

                                        <button class="btn-action btn-delete" onclick="excluirUsuario({{ $usuario->ID_USER }})" title="Excluir">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr class="empty-row">
                                <td colspan="6" class="text-center empty-table">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <p>Nenhum usuário cadastrado</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Adicionar/Editar Usuário -->
            <div class="modal" id="modalUsuario">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 id="modalTitle">Adicionar Usuário</h3>
                        <button class="btn-close" onclick="fecharModalUsuario()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <form id="formUsuario" method="POST">
                        @csrf
                        <input type="hidden" id="usuarioId" name="id">
                        
                        <div class="form-group">
                            <label for="nome">Nome Completo *</label>
                            <input type="text" id="nome" name="nome" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Senha *</label>
                            <input type="password" id="password" name="password" minlength="6">
                            <small class="form-hint" id="passwordHint">Deixe em branco para manter a senha atual</small>
                        </div>

                        <div class="form-group">
                            <label for="tipo">Tipo de Usuário *</label>
                            <select id="tipo" name="tipo" required onchange="toggleEmpresaField()">
                                <option value="">Selecione...</option>
                                <option value="superadmin">Super Admin</option>
                                <option value="admin">Admin de Empresa</option>
                            </select>
                        </div>

                        <div class="form-group" id="empresaGroup" style="display: none;">
                            <label for="empresa_search">Empresa *</label>
                            <div class="custom-select" id="customSelectEmpresa">
                                <input 
                                    type="text" 
                                    id="empresa_search" 
                                    placeholder="Digite para pesquisar empresa..." 
                                    autocomplete="off"
                                    onfocus="abrirDropdownEmpresa()"
                                    oninput="filtrarEmpresas()"
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

                        <div class="form-group">
                            <label for="telefone">Telefone</label>
                            <input type="text" id="telefone" name="telefone">
                        </div>

                        <div class="form-group">
                            <label for="endereco">Endereço</label>
                            <input type="text" id="endereco" name="endereco">
                        </div>

                        <div class="form-group">
                            <label for="status">Status *</label>
                            <select id="status" name="status" required>
                                <option value="1">Ativo</option>
                                <option value="0">Inativo</option>
                            </select>
                        </div>

                        <div class="modal-actions">
                            <button type="button" class="btn-cancel" onclick="fecharModalUsuario()">Cancelar</button>
                            <button type="submit" class="btn-save">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    
    <script src="{{ asset('js/superadmin/usuarios.js') }}"></script>
</body>
</html>