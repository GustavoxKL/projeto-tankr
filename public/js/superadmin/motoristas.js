// ==================== VARIÁVEIS GLOBAIS ====================

const API_BASE = window.location.origin;
let motoristaEditando = null;

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

function abrirModalMotorista(motoristaId = null) {
    const modal = document.getElementById('modalMotorista');
    const form = document.getElementById('formMotorista');
    const title = document.getElementById('modalTitle');
    const statusGroup = document.getElementById('statusGroup');
    
    // Limpar formulário
    form.reset();
    document.getElementById('motoristaId').value = '';
    document.getElementById('empresa_search').value = '';
    document.getElementById('empresa_id').value = '';
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
            
            // Preencher formulário
            document.getElementById('motoristaId').value = motorista.ID_MOTORISTA;
            document.getElementById('nome').value = motorista.NomeMot || '';
            document.getElementById('cpf').value = motorista.CPF || '';
            document.getElementById('cnh').value = motorista.CNHMotorista || '';
            document.getElementById('telefone').value = motorista.TelefoneMot || '';
            document.getElementById('status').value = motorista.StatusMotorista ? '1' : '0';
            
            // Preencher empresa
            if (motorista.FK_EMPRESA_ID_EMPRESA && motorista.empresa) {
                setTimeout(() => {
                    selecionarEmpresa(motorista.FK_EMPRESA_ID_EMPRESA, motorista.empresa.NomeEmpresa);
                }, 100);
            }
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
    const empresaId = document.getElementById('empresa_id').value;
    
    // Validar empresa
    if (!empresaId) {
        alert('⚠️ Selecione uma empresa para o motorista');
        return;
    }
    
    // Montar objeto com dados
    const formData = {
        NomeMotorista: document.getElementById('nome').value,
        CPFMotorista: document.getElementById('cpf').value,
        CNHMotorista: document.getElementById('cnh').value,
        TelefoneMotorista: document.getElementById('telefone').value || null,
        FK_EMPRESA_ID_EMPRESA: parseInt(empresaId)
    };
    
    // Adicionar Status APENAS se for edição
    if (motoristaId) {
        formData.StatusMotorista = document.getElementById('status').value === '1' ? 1 : 0;
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
    // Fechar todos os menus
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        if (menu.id !== `menu-${motoristaId}`) {
            menu.classList.remove('show');
        }
    });
    
    // Toggle do menu clicado
    const menu = document.getElementById(`menu-${motoristaId}`);
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

// ==================== EDITAR MOTORISTA ====================

function editarMotorista(id) {
    abrirModalMotorista(id);
}

// ==================== EXCLUIR MOTORISTA ====================

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

// Máscara de CPF
document.getElementById('cpf').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    
    if (value.length <= 11) {
        value = value.replace(/^(\d{3})(\d)/, '$1.$2');
        value = value.replace(/^(\d{3})\.(\d{3})(\d)/, '$1.$2.$3');
        value = value.replace(/\.(\d{3})(\d)/, '.$1-$2');
    }
    
    e.target.value = value;
});

// Máscara de CNH (apenas números)
document.getElementById('cnh').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    
    if (value.length > 11) {
        value = value.substring(0, 11);
    }
    
    e.target.value = value;
});

// Máscara de Telefone
document.getElementById('telefone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    
    if (value.length <= 11) {
        if (value.length <= 10) {
            // (XX) XXXX-XXXX
            value = value.replace(/^(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
        } else {
            // (XX) XXXXX-XXXX
            value = value.replace(/^(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
        }
    }
    
    e.target.value = value;
});

// ==================== VALIDAÇÕES ====================

// Validar CPF (apenas tamanho)
document.getElementById('cpf').addEventListener('blur', function(e) {
    const cpf = e.target.value.replace(/\D/g, '');
    
    if (cpf.length > 0 && cpf.length !== 11) {
        alert('⚠️ CPF deve ter 11 dígitos');
        e.target.focus();
    }
});

// Validar CNH (apenas tamanho)
document.getElementById('cnh').addEventListener('blur', function(e) {
    const cnh = e.target.value.replace(/\D/g, '');
    
    if (cnh.length > 0 && cnh.length !== 11) {
        alert('⚠️ CNH deve ter 11 dígitos');
        e.target.focus();
    }
});

// ==================== BUSCA DE MOTORISTAS ====================

function filtrarMotoristas() {
    const input = document.getElementById('searchMotorista');
    const filtro = input.value.toLowerCase().trim();
    const cards = document.querySelectorAll('.motorista-card');
    const btnClear = document.getElementById('btnClearSearch');
    const grid = document.querySelector('.motoristas-grid');
    
    // Mostrar/esconder botão de limpar
    btnClear.style.display = filtro ? 'flex' : 'none';
    
    let temResultado = false;
    
    cards.forEach(card => {
        const nome = card.getAttribute('data-nome') || '';
        const cnh = card.getAttribute('data-cnh') || '';
        const cpf = card.getAttribute('data-cpf') || '';
        const empresa = card.getAttribute('data-empresa') || '';
        
        if (
            nome.includes(filtro) || 
            cnh.includes(filtro) || 
            cpf.includes(filtro) ||
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

console.log('✅ motoristas.js carregado com sucesso!');