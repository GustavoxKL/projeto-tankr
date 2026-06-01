// ==================== VARIÁVEIS GLOBAIS ====================

const API_BASE = window.location.origin;
let veiculoEditando = null;

// ==================== SELECT CUSTOMIZADO (EMPRESA) ====================

function abrirDropdownEmpresa() {
    const customSelect = document.getElementById('customSelectEmpresa');
    customSelect.classList.add('open');
}

function fecharDropdownEmpresa() {
    const customSelect = document.getElementById('customSelectEmpresa');
    customSelect.classList.remove('open');
}

function filtrarEmpresas() {
    const input = document.getElementById('empresa_search');
    const filtro = input.value.toLowerCase();
    const opcoes = document.querySelectorAll('#dropdownEmpresas .custom-select-option');
    const noResults = document.getElementById('noResultsEmpresa');
    
    let temResultado = false;
    
    opcoes.forEach(opcao => {
        const nome = opcao.getAttribute('data-nome');
        if (nome.includes(filtro)) {
            opcao.classList.remove('hidden');
            temResultado = true;
        } else {
            opcao.classList.add('hidden');
        }
    });
    
    if (temResultado) {
        noResults.style.display = 'none';
    } else {
        noResults.style.display = 'block';
    }
    
    abrirDropdownEmpresa();
}

function selecionarEmpresa(id, nome) {
    document.getElementById('empresa_id').value = id;
    document.getElementById('empresa_search').value = nome;
    
    document.querySelectorAll('#dropdownEmpresas .custom-select-option').forEach(opt => {
        opt.classList.remove('selected');
    });
    
    const opcaoSelecionada = document.querySelector(`#dropdownEmpresas .custom-select-option[data-id="${id}"]`);
    if (opcaoSelecionada) {
        opcaoSelecionada.classList.add('selected');
    }
    
    fecharDropdownEmpresa();
}

// Fechar dropdown ao clicar fora
document.addEventListener('click', function(e) {
    const customSelect = document.getElementById('customSelectEmpresa');
    if (customSelect && !customSelect.contains(e.target)) {
        fecharDropdownEmpresa();
    }
});

// ==================== MODAL ====================

function abrirModalVeiculo(veiculoId = null) {
    const modal = document.getElementById('modalVeiculo');
    const form = document.getElementById('formVeiculo');
    const title = document.getElementById('modalTitle');
    const statusGroup = document.getElementById('statusGroup');
    
    // Limpar formulário
    form.reset();
    document.getElementById('veiculoId').value = '';
    document.getElementById('empresa_search').value = '';
    document.getElementById('empresa_id').value = '';
    veiculoEditando = null;
    
    if (veiculoId) {
        // Modo edição
        title.textContent = 'Editar Veículo';
        statusGroup.style.display = 'block';
        carregarDadosVeiculo(veiculoId);
    } else {
        // Modo criação
        title.textContent = 'Adicionar Veículo';
        statusGroup.style.display = 'none';
    }
    
    modal.classList.add('show');
}

function fecharModalVeiculo() {
    const modal = document.getElementById('modalVeiculo');
    modal.classList.remove('show');
}

// Fechar modal ao clicar fora
document.addEventListener('click', function(e) {
    const modal = document.getElementById('modalVeiculo');
    if (e.target === modal) {
        fecharModalVeiculo();
    }
});

// Fechar modal com ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        fecharModalVeiculo();
    }
});

// ==================== CARREGAR DADOS DO VEÍCULO ====================

async function carregarDadosVeiculo(id) {
    try {
        const response = await fetch(`${API_BASE}/api/veiculos/${id}`, {
            headers: {
                'Accept': 'application/json',
            }
        });
        
        const veiculo = await response.json();
        
        if (veiculo) {
            veiculoEditando = id;
            
            // Preencher formulário
            document.getElementById('veiculoId').value = veiculo.ID_VEICULO;
            document.getElementById('modelo').value = veiculo.ModeloVei || '';
            document.getElementById('placa').value = veiculo.PlacaVei || '';
            document.getElementById('ano').value = veiculo.AnoVei || '';
            document.getElementById('status').value = veiculo.StatusVei ? '1' : '0';
            
            // Preencher empresa
            if (veiculo.FK_EMPRESA_ID_EMPRESA && veiculo.empresa) {
                setTimeout(() => {
                    selecionarEmpresa(veiculo.FK_EMPRESA_ID_EMPRESA, veiculo.empresa.NomeEmpresa);
                }, 100);
            }
        }
    } catch (error) {
        console.error('Erro ao carregar veículo:', error);
        alert('❌ Erro ao carregar dados do veículo');
    }
}

// ==================== SALVAR VEÍCULO ====================

document.getElementById('formVeiculo').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const veiculoId = document.getElementById('veiculoId').value;
    const empresaId = document.getElementById('empresa_id').value;
    
    // Validar empresa
    if (!empresaId) {
        alert('⚠️ Selecione uma empresa para o veículo');
        return;
    }
    
    // Montar objeto com dados
    const formData = {
        ModeloVeiculo: document.getElementById('modelo').value,
        PlacaVeiculo: document.getElementById('placa').value.toUpperCase(),
        AnoVeiculo: parseInt(document.getElementById('ano').value),
        FK_EMPRESA_ID_EMPRESA: parseInt(empresaId)
    };
    
    // Adicionar Status APENAS se for edição
    if (veiculoId) {
        formData.StatusVeiculo = document.getElementById('status').value === '1' ? 1 : 0;
    }
    
    console.log('📤 Enviando dados:', formData);
    
    try {
        let url = `${API_BASE}/api/veiculos`;
        let method = 'POST';
        
        if (veiculoId) {
            url = `${API_BASE}/api/veiculos/${veiculoId}`;
            method = 'PUT';
        }
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        console.log('📥 Resposta:', data);
        
        if (response.ok) {
            alert(`✅ Veículo ${veiculoId ? 'atualizado' : 'criado'} com sucesso!`);
            fecharModalVeiculo();
            window.location.reload();
        } else {
            let errorMsg = 'Erro ao salvar veículo:\n\n';
            
            if (data.errors) {
                Object.entries(data.errors).forEach(([campo, erros]) => {
                    erros.forEach(err => {
                        errorMsg += `• ${err}\n`;
                    });
                });
            } else if (data.message) {
                errorMsg += data.message;
            }
            
            alert(errorMsg);
        }
    } catch (error) {
        console.error('❌ Erro:', error);
        alert('❌ Erro de conexão com o servidor');
    }
});

// ==================== MENU DROPDOWN ====================

function toggleMenu(veiculoId) {
    // Fechar todos os menus
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        if (menu.id !== `menu-${veiculoId}`) {
            menu.classList.remove('show');
        }
    });
    
    // Toggle do menu clicado
    const menu = document.getElementById(`menu-${veiculoId}`);
    if (menu) {
        menu.classList.toggle('show');
    }
}

// Fechar menus ao clicar fora
document.addEventListener('click', function(e) {
    if (!e.target.closest('.card-menu')) {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            // Não fechar o dropdown do select
            if (!menu.classList.contains('custom-select-dropdown')) {
                menu.classList.remove('show');
            }
        });
    }
});

// ==================== EDITAR VEÍCULO ====================

function editarVeiculo(id) {
    abrirModalVeiculo(id);
}

// ==================== EXCLUIR VEÍCULO ====================

async function excluirVeiculo(id) {
    if (!confirm('⚠️ Tem certeza que deseja excluir este veículo?\n\nEsta ação não pode ser desfeita!')) {
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/api/veiculos/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (response.ok) {
            alert('✅ Veículo excluído com sucesso!');
            window.location.reload();
        } else {
            const error = await response.json();
            alert('❌ Erro ao excluir veículo: ' + (error.message || 'Erro desconhecido'));
        }
    } catch (error) {
        console.error('Erro:', error);
        alert('❌ Erro de conexão com o servidor');
    }
}

// ==================== MÁSCARAS E VALIDAÇÕES ====================

// Máscara de Placa (ABC-1234 ou ABC1D23)
document.getElementById('placa').addEventListener('input', function(e) {
    let value = e.target.value.toUpperCase();
    
    // Remove caracteres especiais
    value = value.replace(/[^A-Z0-9]/g, '');
    
    if (value.length <= 7) {
        // Aplica formato ABC-1234 (placa antiga)
        if (value.length > 3) {
            value = value.substring(0, 3) + '-' + value.substring(3);
        }
    }
    
    e.target.value = value;
});

// Validar Ano (não pode ser maior que ano atual + 1)
document.getElementById('ano').addEventListener('blur', function(e) {
    const ano = parseInt(e.target.value);
    const anoAtual = new Date().getFullYear();
    
    if (ano && (ano < 1900 || ano > anoAtual + 1)) {
        alert(`⚠️ Ano deve estar entre 1900 e ${anoAtual + 1}`);
        e.target.focus();
    }
});

// ==================== BUSCA DE VEÍCULOS ====================

function filtrarVeiculos() {
    const input = document.getElementById('searchVeiculo');
    const filtro = input.value.toLowerCase().trim();
    const cards = document.querySelectorAll('.veiculo-card');
    const btnClear = document.getElementById('btnClearSearch');
    const grid = document.querySelector('.veiculos-grid');
    
    // Mostrar/esconder botão de limpar
    btnClear.style.display = filtro ? 'flex' : 'none';
    
    let temResultado = false;
    
    cards.forEach(card => {
        const modelo = card.getAttribute('data-modelo') || '';
        const placa = card.getAttribute('data-placa') || '';
        const empresa = card.getAttribute('data-empresa') || '';
        
        if (
            modelo.includes(filtro) || 
            placa.includes(filtro) || 
            empresa.includes(filtro)
        ) {
            card.style.display = 'block';
            temResultado = true;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Remover mensagem antiga
    const emptySearchAnterior = document.getElementById('empty-search-result');
    if (emptySearchAnterior) {
        emptySearchAnterior.remove();
    }
    
    // Mostrar mensagem se não encontrou nada
    if (!temResultado && filtro) {
        const mensagem = document.createElement('div');
        mensagem.id = 'empty-search-result';
        mensagem.className = 'empty-search';
        mensagem.innerHTML = `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <h3>Nenhum veículo encontrado</h3>
            <p>Tente pesquisar com outro termo</p>
        `;
        grid.appendChild(mensagem);
    }
}

function limparBusca() {
    const input = document.getElementById('searchVeiculo');
    input.value = '';
    filtrarVeiculos();
    input.focus();
}

// ==================== LOG INICIAL ====================

console.log('✅ veiculos.js carregado com sucesso!');