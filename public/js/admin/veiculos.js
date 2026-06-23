// ==================== VARIÁVEIS GLOBAIS ====================

const API_BASE = window.location.origin;
let veiculoEditando = null;

// ==================== MODAL ====================

function abrirModalVeiculo(veiculoId = null) {
    const modal = document.getElementById('modalVeiculo');
    const form = document.getElementById('formVeiculo');
    const title = document.getElementById('modalTitle');
    const statusGroup = document.getElementById('statusGroup');
    
    // Limpar formulário
    form.reset();
    document.getElementById('veiculoId').value = '';
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
            
            document.getElementById('veiculoId').value = veiculo.ID_VEICULO;
            document.getElementById('modelo').value = veiculo.ModeloVei || '';
            document.getElementById('placa').value = veiculo.PlacaVei || '';
            document.getElementById('ano').value = veiculo.AnoVei || '';
            document.getElementById('status').value = veiculo.StatusVei ? '1' : '0';
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
    const placa = document.getElementById('placa').value.toUpperCase();
    
    // Validar tamanho da placa
    if (placa.length !== 7) {
        alert('⚠️ A placa deve ter exatamente 7 caracteres');
        return;
    }
    
    // Montar objeto com dados (sem FK_EMPRESA_ID_EMPRESA - backend força)
    const formData = {
        ModeloVei: document.getElementById('modelo').value,
        PlacaVei: placa,
        AnoVei: parseInt(document.getElementById('ano').value),
    };
    
    // Adicionar Status APENAS se for edição
    if (veiculoId) {
        formData.StatusVei = document.getElementById('status').value === '1' ? 1 : 0;
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
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        if (menu.id !== `menu-${veiculoId}`) {
            menu.classList.remove('show');
        }
    });
    
    const menu = document.getElementById(`menu-${veiculoId}`);
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

function editarVeiculo(id) {
    abrirModalVeiculo(id);
}

// ==================== EXCLUIR ====================

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

// Máscara de Placa: só letras/números, uppercase, máximo 7 caracteres
document.getElementById('placa').addEventListener('input', function(e) {
    let value = e.target.value.toUpperCase();
    
    // Remove caracteres especiais (só letras e números)
    value = value.replace(/[^A-Z0-9]/g, '');
    
    // Limita a 7 caracteres
    if (value.length > 7) {
        value = value.substring(0, 7);
    }
    
    e.target.value = value;
});

// Bloquear teclas que não sejam letras/números
document.getElementById('placa').addEventListener('keypress', function(e) {
    const char = String.fromCharCode(e.which);
    const valid = /[A-Za-z0-9]/;
    
    if (!valid.test(char)) {
        e.preventDefault();
    }
    
    // Bloquear se já tiver 7 caracteres
    if (e.target.value.length >= 7) {
        e.preventDefault();
    }
});

// Validar Ano
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
    
    btnClear.style.display = filtro ? 'flex' : 'none';
    
    let temResultado = false;
    
    cards.forEach(card => {
        const modelo = card.getAttribute('data-modelo') || '';
        const placa = card.getAttribute('data-placa') || '';
        
        if (modelo.includes(filtro) || placa.includes(filtro)) {
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

console.log('✅ admin/veiculos.js carregado com sucesso!');