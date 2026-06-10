// ==================== VARIÁVEIS GLOBAIS ====================

const API_BASE = window.location.origin;
let estacaoEditando = null;

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
    
    noResults.style.display = temResultado ? 'none' : 'block';
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

document.addEventListener('click', function(e) {
    const customSelect = document.getElementById('customSelectEmpresa');
    if (customSelect && !customSelect.contains(e.target)) {
        fecharDropdownEmpresa();
    }
});

// ==================== TOGGLE EMPRESA (ACCORDION) ====================

function toggleEmpresa(empresaId) {
    const grupo = document.querySelector(`#chevron-${empresaId}`).closest('.empresa-group');
    grupo.classList.toggle('collapsed');
}

// ==================== TOGGLE DE VISUALIZAÇÃO ====================

function mudarVisualizacao(tipo) {
    const viewGrupo = document.getElementById('viewGrupo');
    const viewLista = document.getElementById('viewLista');
    const btnGroup = document.getElementById('btnGroupView');
    const btnList = document.getElementById('btnListView');
    
    if (tipo === 'grupo') {
        viewGrupo.style.display = 'block';
        viewLista.style.display = 'none';
        btnGroup.classList.add('active');
        btnList.classList.remove('active');
    } else {
        viewGrupo.style.display = 'none';
        viewLista.style.display = 'block';
        btnGroup.classList.remove('active');
        btnList.classList.add('active');
    }
}

// ==================== MODAL ====================

function abrirModalEstacao(estacaoId = null) {
    const modal = document.getElementById('modalEstacao');
    const form = document.getElementById('formEstacao');
    const title = document.getElementById('modalTitle');
    
    form.reset();
    document.getElementById('estacaoId').value = '';
    document.getElementById('empresa_search').value = '';
    document.getElementById('empresa_id').value = '';
    estacaoEditando = null;
    
    if (estacaoId) {
        title.textContent = 'Editar Estação';
        carregarDadosEstacao(estacaoId);
    } else {
        title.textContent = 'Adicionar Estação';
    }
    
    modal.classList.add('show');
}

function fecharModalEstacao() {
    const modal = document.getElementById('modalEstacao');
    modal.classList.remove('show');
}

document.addEventListener('click', function(e) {
    const modal = document.getElementById('modalEstacao');
    if (e.target === modal) {
        fecharModalEstacao();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        fecharModalEstacao();
    }
});

// ==================== CARREGAR DADOS DA ESTAÇÃO ====================

async function carregarDadosEstacao(id) {
    try {
        const response = await fetch(`${API_BASE}/api/estacoes/${id}`, {
            headers: {
                'Accept': 'application/json',
            }
        });
        
        const estacao = await response.json();
        
        if (estacao) {
            estacaoEditando = id;
            
            document.getElementById('estacaoId').value = estacao.ID_ESTACAO;
            document.getElementById('token').value = estacao.Token || '';
            document.getElementById('endereco').value = estacao.EnderecoEst || '';
            
            if (estacao.FK_EMPRESA_ID_EMPRESA && estacao.empresa) {
                setTimeout(() => {
                    selecionarEmpresa(estacao.FK_EMPRESA_ID_EMPRESA, estacao.empresa.NomeEmpresa);
                }, 100);
            }
        }
    } catch (error) {
        console.error('Erro ao carregar estação:', error);
        alert('❌ Erro ao carregar dados da estação');
    }
}

// ==================== SALVAR ESTAÇÃO ====================

document.getElementById('formEstacao').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const estacaoId = document.getElementById('estacaoId').value;
    const empresaId = document.getElementById('empresa_id').value;
    
    if (!empresaId) {
        alert('⚠️ Selecione uma empresa para a estação');
        return;
    }
    
    const formData = {
        Token: document.getElementById('token').value.toUpperCase(),
        EnderecoEst: document.getElementById('endereco').value || null,
        FK_EMPRESA_ID_EMPRESA: parseInt(empresaId)
    };
    
    console.log('📤 Enviando dados:', formData);
    
    try {
        let url = `${API_BASE}/api/estacoes`;
        let method = 'POST';
        
        if (estacaoId) {
            url = `${API_BASE}/api/estacoes/${estacaoId}`;
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
            alert(`✅ Estação ${estacaoId ? 'atualizada' : 'criada'} com sucesso!`);
            fecharModalEstacao();
            window.location.reload();
        } else {
            let errorMsg = 'Erro ao salvar estação:\n\n';
            
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

// ==================== MENU DROPDOWN DA ESTAÇÃO ====================

function toggleMenu(estacaoId) {
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        if (menu.id !== `menu-${estacaoId}` && !menu.classList.contains('custom-select-dropdown')) {
            menu.classList.remove('show');
        }
    });
    
    const menu = document.getElementById(`menu-${estacaoId}`);
    if (menu) {
        menu.classList.toggle('show');
    }
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.card-menu')) {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            if (!menu.classList.contains('custom-select-dropdown')) {
                menu.classList.remove('show');
            }
        });
    }
});

// ==================== EDITAR ESTAÇÃO ====================

function editarEstacao(id) {
    abrirModalEstacao(id);
}

// ==================== EXCLUIR ESTAÇÃO ====================

async function excluirEstacao(id) {
    if (!confirm('⚠️ Tem certeza que deseja excluir esta estação?\n\nEsta ação não pode ser desfeita!')) {
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/api/estacoes/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (response.ok) {
            alert('✅ Estação excluída com sucesso!');
            window.location.reload();
        } else {
            const error = await response.json();
            alert('❌ Erro ao excluir estação: ' + (error.message || 'Erro desconhecido'));
        }
    } catch (error) {
        console.error('Erro:', error);
        alert('❌ Erro de conexão com o servidor');
    }
}

// ==================== VER HISTÓRICO ====================

function verHistorico(estacaoId) {
    // FASE 2: redirecionar para a página de histórico
    alert(`⏳ Página de histórico em construção!\n\nEstação ID: ${estacaoId}\n\nEm breve você poderá ver todos os abastecimentos desta estação.`);
    
    // Quando implementarmos a fase 2:
    // window.location.href = `${API_BASE}/superadmin/estacoes/${estacaoId}/historico`;
}

// ==================== BUSCA ====================

function filtrarEstacoes() {
    const input = document.getElementById('searchEstacao');
    const filtro = input.value.toLowerCase().trim();
    const btnClear = document.getElementById('btnClearSearch');
    
    btnClear.style.display = filtro ? 'flex' : 'none';
    
    // Filtra cards individuais
    const cards = document.querySelectorAll('.estacao-card');
    cards.forEach(card => {
        const token = card.getAttribute('data-token') || '';
        const endereco = card.getAttribute('data-endereco') || '';
        const empresa = card.getAttribute('data-empresa') || '';
        
        if (
            token.includes(filtro) || 
            endereco.includes(filtro) || 
            empresa.includes(filtro)
        ) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
    
    // Esconde grupos de empresas que não têm estações visíveis
    const grupos = document.querySelectorAll('.empresa-group');
    grupos.forEach(grupo => {
        const cardsVisiveis = grupo.querySelectorAll('.estacao-card:not([style*="display: none"])');
        const empresaNome = grupo.getAttribute('data-empresa-nome') || '';
        
        if (cardsVisiveis.length === 0 && filtro && !empresaNome.includes(filtro)) {
            grupo.style.display = 'none';
        } else {
            grupo.style.display = 'block';
            // Se a busca der match na empresa, expande automaticamente
            if (filtro && empresaNome.includes(filtro)) {
                grupo.classList.remove('collapsed');
            }
        }
    });
}

function limparBusca() {
    const input = document.getElementById('searchEstacao');
    input.value = '';
    filtrarEstacoes();
    input.focus();
}

// ==================== LOG INICIAL ====================

console.log('✅ estacoes.js carregado com sucesso!');