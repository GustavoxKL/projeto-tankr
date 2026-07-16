// ==================== VARIÁVEIS GLOBAIS ====================

const API_BASE = window.location.origin;
let frentistaEditando = null;

// ==================== MODAL ====================

function abrirModalFrentista(frentistaId = null) {
    const modal = document.getElementById('modalFrentista');
    const form = document.getElementById('formFrentista');
    const title = document.getElementById('modalTitle');
    const statusGroup = document.getElementById('statusGroup');
    const rfidInput = document.getElementById('rfid');
    const modoEdicao = document.getElementById('modoEdicao');
    
    // Limpar formulário
    form.reset();
    frentistaEditando = null;
    
    if (frentistaId) {
        // Modo edição
        title.textContent = 'Editar Frentista';
        statusGroup.style.display = 'block';
        rfidInput.disabled = true; // Não deixa editar o RFID
        modoEdicao.value = '1';
        carregarDadosFrentista(frentistaId);
    } else {
        // Modo criação
        title.textContent = 'Adicionar Frentista';
        statusGroup.style.display = 'none';
        rfidInput.disabled = false;
        modoEdicao.value = '0';
    }
    
    modal.classList.add('show');
}

function fecharModalFrentista() {
    const modal = document.getElementById('modalFrentista');
    modal.classList.remove('show');
    document.getElementById('rfid').disabled = false;
}

// Fechar modal ao clicar fora
document.addEventListener('click', function(e) {
    const modal = document.getElementById('modalFrentista');
    if (e.target === modal) {
        fecharModalFrentista();
    }
});

// Fechar modal com ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        fecharModalFrentista();
    }
});

// ==================== CARREGAR DADOS DO FRENTISTA ====================

async function carregarDadosFrentista(id) {
    try {
        const response = await fetch(`${API_BASE}/api/frentistas/${id}`, {
            headers: {
                'Accept': 'application/json',
            }
        });
        
        const frentista = await response.json();
        
        if (frentista) {
            frentistaEditando = id;
            
            document.getElementById('rfid').value = frentista.ID_FRENTISTA || '';
            document.getElementById('nome').value = frentista.NomeFren || '';
            document.getElementById('status').value = frentista.StatusFren ? '1' : '0';
        }
    } catch (error) {
        console.error('Erro ao carregar frentista:', error);
        alert('❌ Erro ao carregar dados do frentista');
    }
}

// ==================== SALVAR FRENTISTA ====================

document.getElementById('formFrentista').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const modoEdicao = document.getElementById('modoEdicao').value === '1';
    const rfid = document.getElementById('rfid').value.toUpperCase().trim();
    const nome = document.getElementById('nome').value.trim();
    
    if (!nome) {
        alert('⚠️ Preencha o nome do frentista');
        return;
    }
    
    if (!modoEdicao && !rfid) {
        alert('⚠️ Preencha o RFID do frentista');
        return;
    }
    
    // Montar objeto com dados
    const formData = {
        NomeFren: nome,
    };
    
    // Se for criação, adicionar RFID
    if (!modoEdicao) {
        formData.ID_FRENTISTA = rfid;
    }
    
    // Se for edição, adicionar Status
    if (modoEdicao) {
        formData.StatusFren = document.getElementById('status').value === '1' ? 1 : 0;
    }
    
    console.log('📤 Enviando dados:', formData);
    
    try {
        let url = `${API_BASE}/api/frentistas`;
        let method = 'POST';
        
        if (modoEdicao) {
            url = `${API_BASE}/api/frentistas/${frentistaEditando}`;
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
            alert(`✅ Frentista ${modoEdicao ? 'atualizado' : 'criado'} com sucesso!`);
            fecharModalFrentista();
            window.location.reload();
        } else {
            let errorMsg = 'Erro ao salvar frentista:\n\n';
            
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

function toggleMenu(frentistaId) {
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        if (menu.id !== `menu-${frentistaId}`) {
            menu.classList.remove('show');
        }
    });
    
    const menu = document.getElementById(`menu-${frentistaId}`);
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

function editarFrentista(id) {
    abrirModalFrentista(id);
}

// ==================== EXCLUIR ====================

async function excluirFrentista(id) {
    if (!confirm('⚠️ Tem certeza que deseja excluir este frentista?\n\nEsta ação não pode ser desfeita!')) {
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/api/frentistas/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (response.ok) {
            alert('✅ Frentista excluído com sucesso!');
            window.location.reload();
        } else {
            const error = await response.json();
            alert('❌ Erro ao excluir frentista: ' + (error.message || 'Erro desconhecido'));
        }
    } catch (error) {
        console.error('Erro:', error);
        alert('❌ Erro de conexão com o servidor');
    }
}

// ==================== MÁSCARAS ====================

// RFID: só letras/números, uppercase
document.getElementById('rfid').addEventListener('input', function(e) {
    let value = e.target.value.toUpperCase();
    value = value.replace(/[^A-Z0-9]/g, '');
    e.target.value = value;
});

// ==================== BUSCA ====================

function filtrarFrentistas() {
    const input = document.getElementById('searchFrentista');
    const filtro = input.value.toLowerCase().trim();
    const cards = document.querySelectorAll('.frentista-card');
    const btnClear = document.getElementById('btnClearSearch');
    const grid = document.querySelector('.frentistas-grid');
    
    btnClear.style.display = filtro ? 'flex' : 'none';
    
    let temResultado = false;
    
    cards.forEach(card => {
        const nome = card.getAttribute('data-nome') || '';
        const rfid = card.getAttribute('data-rfid') || '';
        
        if (nome.includes(filtro) || rfid.includes(filtro)) {
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
            <h3>Nenhum frentista encontrado</h3>
            <p>Tente pesquisar com outro termo</p>
        `;
        grid.appendChild(mensagem);
    }
}

function limparBusca() {
    const input = document.getElementById('searchFrentista');
    input.value = '';
    filtrarFrentistas();
    input.focus();
}

// ==================== LOG INICIAL ====================

console.log('✅ admin/frentistas.js carregado com sucesso!');