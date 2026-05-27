// ==================== VARIÁVEIS GLOBAIS ====================

const API_BASE = window.location.origin;
let empresaEditando = null;

// ==================== MODAL ====================

function abrirModalEmpresa(empresaId = null) {
    const modal = document.getElementById('modalEmpresa');
    const form = document.getElementById('formEmpresa');
    const title = document.getElementById('modalTitle');
    
    // Limpar formulário
    form.reset();
    document.getElementById('empresaId').value = '';
    empresaEditando = null;
    
    if (empresaId) {
        // Modo edição
        title.textContent = 'Editar Empresa';
        carregarDadosEmpresa(empresaId);
    } else {
        // Modo criação
        title.textContent = 'Adicionar Empresa';
    }
    
    modal.classList.add('show');
}

function fecharModalEmpresa() {
    const modal = document.getElementById('modalEmpresa');
    modal.classList.remove('show');
}

// Fechar modal ao clicar fora
document.addEventListener('click', function(e) {
    const modal = document.getElementById('modalEmpresa');
    if (e.target === modal) {
        fecharModalEmpresa();
    }
});

// Fechar modal com ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        fecharModalEmpresa();
    }
});


// ==================== CARREGAR DADOS DA EMPRESA ====================

async function carregarDadosEmpresa(id) {
    try {
        const response = await fetch(`${API_BASE}/api/empresas/${id}`, {
            headers: {
                'Accept': 'application/json',
            }
        });
        
        const empresa = await response.json();
        
        if (empresa) {
            empresaEditando = id;
            
            // Preencher formulário
            document.getElementById('empresaId').value = empresa.ID_EMPRESA;
            document.getElementById('nome').value = empresa.NomeEmpresa || '';
            document.getElementById('cnpj').value = empresa.CNPJ || '';
            document.getElementById('telefone').value = empresa.TelefoneEmpresa || '';
            document.getElementById('endereco').value = empresa.EnderecoEmpresa || '';
            document.getElementById('status').value = empresa.StatusEmpresa ? '1' : '0';
        }
    } catch (error) {
        console.error('Erro ao carregar empresa:', error);
        alert('❌ Erro ao carregar dados da empresa');
    }
}


// ==================== SALVAR EMPRESA ====================

document.getElementById('formEmpresa').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const empresaId = document.getElementById('empresaId').value;
    const formData = {
        NomeEmpresa: document.getElementById('nome').value,
        CNPJ: document.getElementById('cnpj').value,
        TelefoneEmpresa: document.getElementById('telefone').value,
        EnderecoEmpresa: document.getElementById('endereco').value,
        StatusEmpresa: document.getElementById('status').value === '1'
    };
    
    try {
        let url = `${API_BASE}/api/empresas`;
        let method = 'POST';
        
        if (empresaId) {
            // Atualizar
            url = `${API_BASE}/api/empresas/${empresaId}`;
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
        
        if (response.ok) {
            alert(`✅ Empresa ${empresaId ? 'atualizada' : 'criada'} com sucesso!`);
            fecharModalEmpresa();
            window.location.reload();
        } else {
            const error = await response.json();
            let errorMsg = 'Erro ao salvar empresa:\n';
            
            if (error.errors) {
                Object.values(error.errors).forEach(errors => {
                    errors.forEach(err => {
                        errorMsg += `• ${err}\n`;
                    });
                });
            }
            
            alert(errorMsg);
        }
    } catch (error) {
        console.error('Erro:', error);
        alert('❌ Erro de conexão com o servidor');
    }
});

// ==================== MENU DROPDOWN ====================

function toggleMenu(empresaId) {
    // Fechar todos os menus
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        if (menu.id !== `menu-${empresaId}`) {
            menu.classList.remove('show');
        }
    });
    
    // Toggle do menu clicado
    const menu = document.getElementById(`menu-${empresaId}`);
    menu.classList.toggle('show');
}

// Fechar menus ao clicar fora
document.addEventListener('click', function(e) {
    if (!e.target.closest('.btn-menu')) {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});

// ==================== EDITAR EMPRESA ====================

function editarEmpresa(id) {
    abrirModalEmpresa(id);
}

// ==================== EXCLUIR EMPRESA ====================

async function excluirEmpresa(id) {
    if (!confirm('⚠️ Tem certeza que deseja excluir esta empresa?\n\nTodos os dados relacionados (motoristas, veículos, abastecimentos) serão excluídos permanentemente!')) {
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/api/empresas/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (response.ok) {
            alert('✅ Empresa excluída com sucesso!');
            window.location.reload();
        } else {
            alert('❌ Erro ao excluir empresa');
        }
    } catch (error) {
        console.error('Erro:', error);
        alert('❌ Erro de conexão com o servidor');
    }
}

// ==================== MÁSCARAS ====================

// Máscara de CNPJ
document.getElementById('cnpj').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    
    if (value.length <= 14) {
        value = value.replace(/^(\d{2})(\d)/, '$1.$2');
        value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
        value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
        value = value.replace(/(\d{4})(\d)/, '$1-$2');
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

// Validar CNPJ (básico)
document.getElementById('cnpj').addEventListener('blur', function(e) {
    const cnpj = e.target.value.replace(/\D/g, '');
    
    if (cnpj.length > 0 && cnpj.length !== 14) {
        alert('⚠️ CNPJ deve ter 14 dígitos');
        e.target.focus();
    }
});


// ==================== BUSCA DE EMPRESAS ====================

function filtrarEmpresas() {
    const input = document.getElementById('searchEmpresa');
    const filtro = input.value.toLowerCase().trim();
    const cards = document.querySelectorAll('.empresa-card');
    const btnClear = document.getElementById('btnClearSearch');
    const grid = document.querySelector('.empresas-grid');
    
    // Mostrar/esconder botão de limpar
    btnClear.style.display = filtro ? 'flex' : 'none';
    
    let temResultado = false;
    
    cards.forEach(card => {
        const nome = card.getAttribute('data-nome') || '';
        const cnpj = card.getAttribute('data-cnpj') || '';
        
        if (nome.includes(filtro) || cnpj.includes(filtro)) {
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
            <h3>Nenhuma empresa encontrada</h3>
            <p>Tente pesquisar com outro termo</p>
        `;
        grid.appendChild(mensagem);
    }
}

function limparBusca() {
    const input = document.getElementById('searchEmpresa');
    input.value = '';
    filtrarEmpresas();
    input.focus();
}



// ==================== LOG INICIAL ====================

console.log('✅ empresas.js carregado com sucesso!');