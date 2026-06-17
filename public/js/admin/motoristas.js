// ==================== VARIÁVEIS GLOBAIS ====================

const API_BASE = window.location.origin;
let motoristaEditando = null;

// ==================== MODAL ====================

function abrirModalMotorista(motoristaId = null) {
    const modal = document.getElementById('modalMotorista');
    const form = document.getElementById('formMotorista');
    const title = document.getElementById('modalTitle');
    const statusGroup = document.getElementById('statusGroup');
    
    // Limpar formulário
    form.reset();
    document.getElementById('motoristaId').value = '';
    motoristaEditando = null;
    
    if (motoristaId) {
        // Modo edição
        title.textContent = 'Editar Motorista';
        statusGroup.style.display = 'block';
        carregarDadosMotorista(motoristaId);
    } else {
        // Modo criação
        title.textContent = 'Adicionar Motorista';
        statusGroup.style.display = 'none';
    }
    
    modal.classList.add('show');
}

function fecharModalMotorista() {
    const modal = document.getElementById('modalMotorista');
    modal.classList.remove('show');
}

// Fechar modal ao clicar fora
document.addEventListener('click', function(e) {
    const modal = document.getElementById('modalMotorista');
    if (e.target === modal) {
        fecharModalMotorista();
    }
});

// Fechar modal com ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        fecharModalMotorista();
    }
});

// ==================== CARREGAR DADOS DO MOTORISTA ====================

async function carregarDadosMotorista(id) {
    try {
        const response = await fetch(`${API_BASE}/api/motoristas/${id}`, {
            headers: {
                'Accept': 'application/json',
            }
        });
        
        const motorista = await response.json();
        
        if (motorista) {
            motoristaEditando = id;
            
            document.getElementById('motoristaId').value = motorista.ID_MOTORISTA;
            document.getElementById('nome').value = motorista.NomeMot || '';
            document.getElementById('cnh').value = motorista.CNHMot || '';
            document.getElementById('telefone').value = formatarTelefone(motorista.TelefoneMot || '');
            document.getElementById('status').value = motorista.StatusMot ? '1' : '0';
        }
    } catch (error) {
        console.error('Erro ao carregar motorista:', error);
        alert('❌ Erro ao carregar dados do motorista');
    }
}

// ==================== SALVAR MOTORISTA ====================

document.getElementById('formMotorista').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const motoristaId = document.getElementById('motoristaId').value;
    
    // Montar objeto com dados
    const formData = {
        NomeMot: document.getElementById('nome').value,
        CNHMot: document.getElementById('cnh').value || null,
        TelefoneMot: document.getElementById('telefone').value || null,
        // NÃO envia FK_EMPRESA_ID_EMPRESA - será forçada pelo backend
    };
    
    // Adicionar Status APENAS se for edição
    if (motoristaId) {
        formData.StatusMot = document.getElementById('status').value === '1' ? 1 : 0;
    }
    
    console.log('📤 Enviando dados:', formData);
    
    try {
        let url = `${API_BASE}/api/motoristas`;
        let method = 'POST';
        
        if (motoristaId) {
            url = `${API_BASE}/api/motoristas/${motoristaId}`;
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
            alert(`✅ Motorista ${motoristaId ? 'atualizado' : 'criado'} com sucesso!`);
            fecharModalMotorista();
            window.location.reload();
        } else {
            let errorMsg = 'Erro ao salvar motorista:\n\n';
            
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

function toggleMenu(motoristaId) {
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        if (menu.id !== `menu-${motoristaId}`) {
            menu.classList.remove('show');
        }
    });
    
    const menu = document.getElementById(`menu-${motoristaId}`);
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

function editarMotorista(id) {
    abrirModalMotorista(id);
}

// ==================== EXCLUIR ====================

async function excluirMotorista(id) {
    if (!confirm('⚠️ Tem certeza que deseja excluir este motorista?\n\nEsta ação não pode ser desfeita!')) {
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/api/motoristas/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (response.ok) {
            alert('✅ Motorista excluído com sucesso!');
            window.location.reload();
        } else {
            const error = await response.json();
            alert('❌ Erro ao excluir motorista: ' + (error.message || 'Erro desconhecido'));
        }
    } catch (error) {
        console.error('Erro:', error);
        alert('❌ Erro de conexão com o servidor');
    }
}

// ==================== MÁSCARAS ====================

// Máscara de CNH (apenas números)
document.getElementById('cnh').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 11) value = value.substring(0, 11);
    e.target.value = value;
});

// Máscara de Telefone
document.getElementById('telefone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    
    if (value.length <= 11) {
        if (value.length <= 10) {
            value = value.replace(/^(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
        } else {
            value = value.replace(/^(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
        }
    }
    
    e.target.value = value;
});

// ==================== VALIDAÇÕES ====================

document.getElementById('cnh').addEventListener('blur', function(e) {
    const cnh = e.target.value.replace(/\D/g, '');
    
    if (cnh.length > 0 && cnh.length !== 11) {
        alert('⚠️ CNH deve ter 11 dígitos');
        e.target.focus();
    }
});

// ==================== BUSCA ====================

function filtrarMotoristas() {
    const input = document.getElementById('searchMotorista');
    const filtro = input.value.toLowerCase().trim();
    const cards = document.querySelectorAll('.motorista-card');
    const btnClear = document.getElementById('btnClearSearch');
    const grid = document.querySelector('.motoristas-grid');
    
    btnClear.style.display = filtro ? 'flex' : 'none';
    
    let temResultado = false;
    
    cards.forEach(card => {
        const nome = card.getAttribute('data-nome') || '';
        const cnh = card.getAttribute('data-cnh') || '';
        
        if (nome.includes(filtro) || cnh.includes(filtro)) {
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
            <h3>Nenhum motorista encontrado</h3>
            <p>Tente pesquisar com outro termo</p>
        `;
        grid.appendChild(mensagem);
    }
}

function limparBusca() {
    const input = document.getElementById('searchMotorista');
    input.value = '';
    filtrarMotoristas();
    input.focus();
}

// ==================== LOG INICIAL ====================

console.log('✅ admin/motoristas.js carregado com sucesso!');