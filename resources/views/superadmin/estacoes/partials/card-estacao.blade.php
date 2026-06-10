<div class="estacao-card" 
     data-token="{{ strtolower($estacao->Token) }}"
     data-endereco="{{ strtolower($estacao->EnderecoEst ?? '') }}"
     data-empresa="{{ strtolower($estacao->empresa->NomeEmpresa ?? '') }}">
    
    <!-- Header do Card -->
    <div class="card-header">
        <div class="estacao-header-left">
            <div class="estacao-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 22h12"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 7v10a2 2 0 0 0 4 0v-6a2 2 0 0 0-2-2h-2"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 6h2l3 3"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 6h4"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 10h4"/>
                </svg>
            </div>
            <div class="estacao-info-header">
                <h3 class="estacao-token">{{ $estacao->Token }}</h3>
                <p class="estacao-empresa">{{ $estacao->empresa->NomeEmpresa ?? 'Sem empresa' }}</p>
            </div>
        </div>
        
        <div class="card-menu">
            <button class="btn-menu" onclick="toggleMenu({{ $estacao->ID_ESTACAO }})">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                </svg>
            </button>
            <div class="dropdown-menu" id="menu-{{ $estacao->ID_ESTACAO }}">
                <a href="#" onclick="event.preventDefault(); editarEstacao({{ $estacao->ID_ESTACAO }})">
                    Editar
                </a>
                <a href="#" onclick="event.preventDefault(); excluirEstacao({{ $estacao->ID_ESTACAO }})" class="delete">
                    Excluir
                </a>
            </div>
        </div>
    </div>

    <!-- Endereço -->
    <div class="estacao-info-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <span>{{ $estacao->EnderecoEst ?? 'Endereço não informado' }}</span>
    </div>

    <!-- Info adicional (placeholders para fase 2) -->
    <div class="estacao-info-extra">
        <div class="info-extra-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <span>Abastecimentos: <span class="text-muted">--</span></span>
        </div>

        <div class="info-extra-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span>Último abast.: <span class="text-muted">--</span></span>
        </div>
    </div>

    <!-- Botão Ver Histórico -->
    <div class="estacao-footer">
        <button class="btn-historico" onclick="verHistorico({{ $estacao->ID_ESTACAO }})">
            Ver Histórico
        </button>
    </div>
</div>