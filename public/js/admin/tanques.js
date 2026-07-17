// ==================== VARIÁVEIS GLOBAIS ====================

const API_BASE = window.location.origin;
let tanqueEditando = null;

// ==================== MODAL ====================

function abrirModalTanque(tanqueId = null) {
    const modal = document.getElementById('modalTanque');
    const form = document.getElementById('formTanque');
    const title = document.getElementById('modalTitle');
    const statusGroup = document.getElementById('statusGroup');
    
    // Limpar formulário
    form.reset();
    document.getElementById('tanqueId').value = '';
    tanqueEditando = null;
    
    // Desmarcar todos os checkboxes
    document.querySelectorAll('input[name="estacoes[]"]').forEach(cb => {
        cb.checked = false;
    });
    
    if (tanqueId) {
        // Modo edição
        title.textContent = 'Editar Tanque';
        statusGroup.style.display = 'block';
        carregarDadosTanque(tanqueId);
    } else {
        // Modo criação
        title.textContent = 'Adicionar Tanque';
        statusGroup.style.display = 'none';
    }
    
    modal.classList.add('show');
}

function fecharModalTanque() {
    const modal = document.getElementById('modalTanque');
    modal.classList.remove('show');
}

// Fechar modal ao clicar fora
document.addEventListener('click', function(e) {
    const modal = document.getElementById('modalTanque');
    if (e.target === modal) {
        fecharModalTanque();
    }
});

// Fechar modal com ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        fecharModalTanque();
    }
});

// ==================== CARREGAR DADOS DO TANQUE ====================

async function carregarDadosTanque(id) {
    try {
        const response = await fetch(`${API_BASE}/api/tanques/${id}`, {
            headers: {
                'Accept': 'application/json',
            }
        });
        
        const tanque = await response.json();
        
        if (tanque) {
            tanqueEditando = id;
            
            document.getElementById('tanqueId').value = tanque.ID_TANQUE;
            document.getElementById('nome').value = tanque.NomeTanque || '';
            document.getElementById('tipo_combustivel').value = tanque.TipoCombustivelTanque || '';
            document.getElementById('capacidade_max').value = tanque.CapacidadeMaxTanque || '';
            document.getElementById('quantidade_atual').value = tanque.QuantidadeAtualTanque || 0;
            document.getElementById('status').value = tanque.StatusTanque ? '1' : '0';
            
            // Marcar as estações vinculadas
            if (tanque.estacoes && tanque.estacoes.length > 0) {
                tanque.estacoes.forEach(estacao => {
                    const checkbox = document.querySelector(`input[name="estacoes[]"][value="${estacao.ID_ESTACAO}"]`);
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });
            }
        }
    } catch (error) {
        console.error('Erro ao carregar tanque:', error);
        alert('❌ Erro ao carregar dados do tanque');
    }
}

// ==================== SALVAR TANQUE ====================

document.getElementById('formTanque').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const tanqueId = document.getElementById('tanqueId').value;
    const capacidade = parseFloat(document.getElementById('capacidade_max').value);
    const quantidade = parseFloat(document.getElementById('quantidade_atual').value);
    
    // Validar quantidade não maior que capacidade
    if (quantidade > capacidade) {
        alert('⚠️ A quantidade atual não pode ser maior que a capacidade máxima');
        return;
    }
    
    // Pegar as estações selecionadas
    const estacoesSelecionadas = [];
    document.querySelectorAll('input[name="estacoes[]"]:checked').forEach(cb => {
        estacoesSelecionadas.push(parseInt(cb.value));
    });
    
    // Montar objeto com dados
    const formData = {
        NomeTanque: document.getElementById('nome').value,
        TipoCombustivelTanque: document.getElementById('tipo_combustivel').value,
        CapacidadeMaxTanque: capacidade,
        QuantidadeAtualTanque: quantidade,
        estacoes: estacoesSelecionadas
    };
    
    // Adicionar Status APENAS se for edição
    if (tanqueId) {
        formData.StatusTanque = document.getElementById('status').value === '1' ? 1 : 0;
    }
    
    console.log('📤 Enviando dados:', formData);
    
    try {
        let url = `${API_BASE}/api/tanques`;
        let method = 'POST';
        
        if (tanqueId) {
            url = `${API_BASE}/api/tanques/${tanqueId}`;
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
            alert(`✅ Tanque ${tanqueId ? 'atualizado' : 'criado'} com sucesso!`);
            fecharModalTanque();
            window.location.reload();
        } else {
            let errorMsg = 'Erro ao salvar tanque:\n\n';
            
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

function toggleMenu(tanqueId) {
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        if (menu.id !== `menu-${tanqueId}`) {
            menu.classList.remove('show');
        }
    });
    
    const menu = document.getElementById(`menu-${tanqueId}`);
    if (menu) {
        menu.classList.toggle('show');
    }
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.card-menu')) {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});

// ==================== EDITAR ====================

function editarTanque(id) {
    abrirModalTanque(id);
}

// ==================== EXCLUIR ====================

async function excluirTanque(id) {
    if (!confirm('⚠️ Tem certeza que deseja excluir este tanque?\n\nEsta ação não pode ser desfeita!')) {
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/api/tanques/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (response.ok) {
            alert('✅ Tanque excluído com sucesso!');
            window.location.reload();
        } else {
            const error = await response.json();
            alert('❌ Erro ao excluir tanque: ' + (error.message || 'Erro desconhecido'));
        }
    } catch (error) {
        console.error('Erro:', error);
        alert('❌ Erro de conexão com o servidor');
    }
}

// ==================== VALIDAÇÕES ====================

// Impede que a quantidade atual seja maior que a capacidade máxima em tempo real
document.getElementById('quantidade_atual').addEventListener('input', function(e) {
    const capacidade = parseFloat(document.getElementById('capacidade_max').value) || 0;
    const quantidade = parseFloat(e.target.value) || 0;
    
    if (quantidade > capacidade && capacidade > 0) {
        e.target.style.borderColor = '#EF4444';
        e.target.style.background = '#FEE2E2';
    } else {
        e.target.style.borderColor = '';
        e.target.style.background = '';
    }
});

document.getElementById('capacidade_max').addEventListener('input', function() {
    // Revalidar quantidade quando mudar a capacidade
    const evento = new Event('input');
    document.getElementById('quantidade_atual').dispatchEvent(evento);
});

// ==================== BUSCA ====================

function filtrarTanques() {
    const input = document.getElementById('searchTanque');
    const filtro = input.value.toLowerCase().trim();
    const cards = document.querySelectorAll('.tanque-card');
    const btnClear = document.getElementById('btnClearSearch');
    const grid = document.querySelector('.tanques-grid');
    
    btnClear.style.display = filtro ? 'flex' : 'none';
    
    let temResultado = false;
    
    cards.forEach(card => {
        const nome = card.getAttribute('data-nome') || '';
        const tipo = card.getAttribute('data-tipo') || '';
        
        if (nome.includes(filtro) || tipo.includes(filtro)) {
            card.style.display = 'block';
            temResultado = true;
        } else {
            card.style.display = 'none';
        }
    });
    
    const emptySearchAnterior = document.getElementById('empty-search-result');
    if (emptySearchAnterior) {
        emptySearchAnterior.remove();
    }
    
    if (!temResultado && filtro) {
        const mensagem = document.createElement('div');
        mensagem.id = 'empty-search-result';
        mensagem.className = 'empty-search';
        mensagem.innerHTML = `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <h3>Nenhum tanque encontrado</h3>
            <p>Tente pesquisar com outro termo</p>
        `;
        grid.appendChild(mensagem);
    }
}

function limparBusca() {
    const input = document.getElementById('searchTanque');
    input.value = '';
    filtrarTanques();
    input.focus();
}

// ==================== LOG INICIAL ====================

console.log('✅ admin/tanques.js carregado com sucesso!');