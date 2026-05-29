const API_BASE = window.location.origin;
let usuarioEditando = null;

// ==================== SELECT CUSTOMIZADO ====================

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
    
    // Mostrar mensagem se não encontrou
    if (temResultado) {
        noResults.style.display = 'none';
    } else {
        noResults.style.display = 'block';
    }
    
    // Abrir dropdown ao digitar
    abrirDropdownEmpresa();
}

function selecionarEmpresa(id, nome) {
    document.getElementById('empresa_id').value = id;
    document.getElementById('empresa_search').value = nome;
    
    // Marcar opção como selecionada
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


// ==================== MENU DROPDOWN DAS LINHAS ====================

function toggleMenuUsuario(usuarioId) {
    // Fechar todos os menus
    document.querySelectorAll('.row-menu .dropdown-menu').forEach(menu => {
        if (menu.id !== `menu-usuario-${usuarioId}`) {
            menu.classList.remove('show');
        }
    });
    
    // Toggle do menu clicado
    const menu = document.getElementById(`menu-usuario-${usuarioId}`);
    if (menu) {
        menu.classList.toggle('show');
    }
}

// Fechar menus ao clicar fora
document.addEventListener('click', function(e) {
    if (!e.target.closest('.row-menu')) {
        document.querySelectorAll('.row-menu .dropdown-menu').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});


// Modal

function abrirModalUsuario(usuarioId = null) {
    const modal = document.getElementById('modalUsuario');
    const form = document.getElementById('formUsuario');
    const title = document.getElementById('modalTitle');
    const passwordHint = document.getElementById('passwordHint');
    const passwordInput = document.getElementById('password');
    const statusGroup = document.getElementById('statusGroup');
    
    form.reset();
    document.getElementById('usuarioId').value = '';
    document.getElementById('empresa_search').value = '';
    document.getElementById('empresa_id').value = '';
    usuarioEditando = null;
    
    document.getElementById('empresaGroup').style.display = 'none';
    document.getElementById('empresa_id').required = false;
    
    if (usuarioId) {
        // Modo edição
        title.textContent = 'Editar Usuário';
        passwordInput.required = false;
        passwordHint.style.display = 'block';
        statusGroup.style.display = 'block';
        carregarDadosUsuario(usuarioId);
    } else {
        // Modo criação
        title.textContent = 'Adicionar Usuário';
        passwordInput.required = true;
        passwordHint.style.display = 'none';
        statusGroup.style.display = 'none';
    }
    
    modal.classList.add('show');
}

function fecharModalUsuario() {
    const modal = document.getElementById('modalUsuario');
    modal.classList.remove('show');
}

document.addEventListener('click', function(e) {
    const modal = document.getElementById('modalUsuario');
    if (e.target === modal) {
        fecharModalUsuario();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        fecharModalUsuario();
    }
});

// ==================== TOGGLE CAMPO EMPRESA ====================

function toggleEmpresaField() {
    const tipo = document.getElementById('tipo').value;
    const empresaGroup = document.getElementById('empresaGroup');
    const empresaSearch = document.getElementById('empresa_search');
    const empresaId = document.getElementById('empresa_id');
    
    if (tipo === 'admin' || tipo === 'user') {
        // Mostrar campo de empresa
        empresaGroup.style.display = 'block';
        empresaSearch.required = true;
    } else {
        // Esconder campo de empresa
        empresaGroup.style.display = 'none';
        empresaSearch.required = false;
        empresaSearch.value = '';
        empresaId.value = '';
    }
}

// Dados do User

async function carregarDadosUsuario(id) {
    try {
        const response = await fetch(`${API_BASE}/api/usuarios/${id}`, {
            headers: {
                'Accept': 'application/json',
            }
        });
        
        const usuario = await response.json();
        
        if (usuario) {
            usuarioEditando = id;
            
            // Preencher formulário
            document.getElementById('usuarioId').value = usuario.ID_USER;
            document.getElementById('nome').value = usuario.NomeUser || '';
            document.getElementById('email').value = usuario.email || '';
            document.getElementById('tipo').value = usuario.TipoUser || '';
            document.getElementById('telefone').value = usuario.TelefoneUser || '';
            document.getElementById('endereco').value = usuario.EnderecoUser || '';
            document.getElementById('status').value = usuario.StatusUser ? '1' : '0';
            
            // IMPORTANTE: Chamar toggleEmpresaField PRIMEIRO para mostrar o campo
            toggleEmpresaField();
            
            // Depois preencher empresa (se tiver)
            if (usuario.FK_EMPRESA_ID_EMPRESA && usuario.empresa) {
                // Pequeno delay para garantir que o campo já está visível
                setTimeout(() => {
                    selecionarEmpresa(usuario.FK_EMPRESA_ID_EMPRESA, usuario.empresa.NomeEmpresa);
                }, 100);
            }
        }
    } catch (error) {
        console.error('Erro ao carregar usuário:', error);
        alert('❌ Erro ao carregar dados do usuário');
    }
}

// Adicionar

document.getElementById('formUsuario').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const usuarioId = document.getElementById('usuarioId').value;
    const tipo = document.getElementById('tipo').value;
    const password = document.getElementById('password').value;
    const empresaId = document.getElementById('empresa_id').value;
    
    // Validar empresa para admin e user
    if ((tipo === 'admin' || tipo === 'user') && !empresaId) {
        alert('⚠️ Selecione uma empresa para este tipo de usuário');
        return;
    }
    
    // Montar objeto com dados (apenas os preenchidos)
    const formData = {
        NomeUser: document.getElementById('nome').value,
        email: document.getElementById('email').value,
        TipoUser: tipo,
        TelefoneUser: document.getElementById('telefone').value || null,
        EnderecoUser: document.getElementById('endereco').value || null,
    };
    
    if (usuarioId) {
        formData.StatusUser = document.getElementById('status').value === '1' ? 1 : 0;
    }

    // Adicionar empresa se for admin ou user
    if (tipo === 'admin' || tipo === 'user') {
        formData.FK_EMPRESA_ID_EMPRESA = parseInt(empresaId);
    } else {
        // Superadmin não tem empresa
        formData.FK_EMPRESA_ID_EMPRESA = null;
    }
    
    // Adicionar senha apenas se foi preenchida
    if (password && password.trim() !== '') {
        formData.password = password;
    }
    
    console.log('📤 Enviando dados:', formData);
    
    try {
        let url = `${API_BASE}/api/usuarios`;
        let method = 'POST';
        
        if (usuarioId) {
            url = `${API_BASE}/api/usuarios/${usuarioId}`;
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
            alert(`✅ Usuário ${usuarioId ? 'atualizado' : 'criado'} com sucesso!`);
            fecharModalUsuario();
            window.location.reload();
        } else {
            let errorMsg = 'Erro ao salvar usuário:\n\n';
            
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

// Editar

function editarUsuario(id) {
    abrirModalUsuario(id);
}

// Excluir

async function excluirUsuario(id) {
    if (!confirm('⚠️ Tem certeza que deseja excluir este usuário?\n\nEsta ação não pode ser desfeita!')) {
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/api/usuarios/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (response.ok) {
            alert('✅ Usuário excluído com sucesso!');
            window.location.reload();
        } else {
            const error = await response.json();
            alert('❌ Erro ao excluir usuário: ' + (error.message || 'Erro desconhecido'));
        }
    } catch (error) {
        console.error('Erro:', error);
        alert('❌ Erro de conexão com o servidor');
    }
}

// ==================== MÁSCARAS ====================

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

// Validar Email
document.getElementById('email').addEventListener('blur', function(e) {
    const email = e.target.value;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (email && !emailRegex.test(email)) {
        alert('⚠️ Email inválido');
        e.target.focus();
    }
});

// Validar senha (mínimo 6 caracteres)
document.getElementById('password').addEventListener('blur', function(e) {
    const password = e.target.value;
    const isEditing = document.getElementById('usuarioId').value !== '';
    
    // Só validar se não for edição OU se preencheu algo na edição
    if (password && password.length < 6) {
        alert('⚠️ A senha deve ter no mínimo 6 caracteres');
        e.target.focus();
    }
});


// ==================== BUSCA DE USUÁRIOS ====================

function filtrarUsuarios() {
    const input = document.getElementById('searchUsuario');
    const filtro = input.value.toLowerCase().trim();
    const linhas = document.querySelectorAll('.usuarios-table tbody tr:not(.no-results-row):not(#empty-row)');
    const btnClear = document.getElementById('btnClearSearch');
    const tbody = document.querySelector('.usuarios-table tbody');
    
    // Mostrar/esconder botão de limpar
    btnClear.style.display = filtro ? 'flex' : 'none';
    
    let temResultado = false;
    
    linhas.forEach(linha => {
        const nome = linha.getAttribute('data-nome') || '';
        const email = linha.getAttribute('data-email') || '';
        const empresa = linha.getAttribute('data-empresa') || '';
        const tipo = linha.getAttribute('data-tipo') || '';
        const status = linha.getAttribute('data-status') || '';
        
        if (
            nome.includes(filtro) || 
            email.includes(filtro) || 
            empresa.includes(filtro) ||
            tipo.includes(filtro) ||
            status.includes(filtro)
        ) {
            linha.style.display = '';
            temResultado = true;
        } else {
            linha.style.display = 'none';
        }
    });
    
    // Remover mensagem antiga
    const noResultsAnterior = document.getElementById('no-results-row');
    if (noResultsAnterior) {
        noResultsAnterior.remove();
    }
    
    // Mostrar mensagem se não encontrou nada
    if (!temResultado && filtro) {
        const linha = document.createElement('tr');
        linha.id = 'no-results-row';
        linha.className = 'no-results-row';
        linha.innerHTML = `
            <td colspan="6">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <h3>Nenhum usuário encontrado</h3>
                <p>Tente pesquisar com outro termo</p>
            </td>
        `;
        tbody.appendChild(linha);
    }
}

function limparBuscaUsuario() {
    const input = document.getElementById('searchUsuario');
    input.value = '';
    filtrarUsuarios();
    input.focus();
}


// Log inicial

console.log('✅ usuarios.js carregado com sucesso!');