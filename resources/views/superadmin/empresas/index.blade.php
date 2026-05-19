@extends('dashboard_superadmin')

@section('page-title', 'Empresas Cadastradas')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/superadmin/empresas.css') }}">
@endsection

@section('content')
<div class="empresas-container">
    <!-- Header com botão adicionar -->
    <div class="page-header">
        <div class="header-info">
            <p class="section-subtitle">Gerencie todas as empresas do sistema</p>
        </div>
        <button class="btn-add-empresa" onclick="abrirModalEmpresa()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Adicionar Empresa
        </button>
    </div>

    <!-- Grid de Cards -->
    <div class="empresas-grid">
        @forelse($empresas as $empresa)
        <div class="empresa-card">
            <!-- Header do Card -->
            <div class="card-header">
                <div class="empresa-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/>
                    </svg>
                </div>
                <div class="card-menu">
                    <button class="btn-menu" onclick="toggleMenu({{ $empresa->ID_EMPRESA }})">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                        </svg>
                    </button>
                    <div class="dropdown-menu" id="menu-{{ $empresa->ID_EMPRESA }}">
                        <a href="#" onclick="editarEmpresa({{ $empresa->ID_EMPRESA }})">Editar</a>
                        <a href="#" onclick="excluirEmpresa({{ $empresa->ID_EMPRESA }})" class="delete">Excluir</a>
                    </div>
                </div>
            </div>

            <!-- Informações da Empresa -->
            <div class="empresa-info">
                <h3 class="empresa-nome">{{ $empresa->NomeEmpresa }}</h3>
                <p class="empresa-cnpj">CNPJ: {{ $empresa->CNPJ }}</p>
            </div>

            <!-- Localização -->
            <div class="empresa-location">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>{{ $empresa->EnderecoEmpresa ?? 'Não informado' }}</span>
            </div>

            <!-- Estatísticas -->
            <div class="empresa-stats">
                <div class="stat-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
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
            <p>Clique no botão "Adicionar Empresa" para começar</p>
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
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="telefone">Telefone</label>
                <input type="text" id="telefone" name="telefone">
            </div>

            <div class="form-group">
                <label for="endereco">Endereço</label>
                <input type="text" id="endereco" name="endereco">
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="fecharModalEmpresa()">Cancelar</button>
                <button type="submit" class="btn-save">Salvar</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/superadmin/empresas.js') }}"></script>
@endsection