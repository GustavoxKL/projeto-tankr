let ordenMotoristas = { coluna: 'id', ordem: 'asc' };
let ordenVeiculos = { coluna: 'id', ordem: 'asc' };

window.onload = function() {

    //const usuario = JSON.parse(localStorage.getItem('usuario') || '{}');
    //document.getElementById('companyName').textContent = usuario.empresa_nome || 'Empresa';
    //document.getElementById('userName').textContent = usuario.nome || 'Usuário';

    carregarPortas();
    carregarStatus();
    carregarHistorico();
    carregarEstatisticas();
    conectarWebSocket();

    setInterval(carregarStatus, 5000);
};

function conectarWebSocket() {
    const wsProtocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
    ws = new WebSocket(`${wsProtocol}//${window.location.host}/ws?token=${token}`);
            
    ws.onopen = () => console.log('WebSocket conectado');
    ws.onmessage = (event) => {
        const data = JSON.parse(event.data);
        if (data.tipo === 'atualizacao') {
            atualizarDados(data.dados);
            if (data.logs) {
                data.logs.forEach(log => adicionarLog(log.timestamp, log.mensagem));
            }
        }
    };
    ws.onclose = () => {
        console.log('WebSocket desconectado, reconectando...');
        setTimeout(conectarWebSocket, 5000);
    };
}

function atualizarDados(dados) {
    document.getElementById('litragemAtual').textContent = dados.litragem_atual.toFixed(2) + ' L';
    document.getElementById('totalizador').textContent = dados.totalizador.toFixed(2) + ' L';
    document.getElementById('motorista').textContent = dados.motorista || '---';
    document.getElementById('estado').textContent = dados.estado.replace('_', ' ');
}

async function carregarPortas() {
    try {
        const res = await fetch(`${API_BASE}/api/serial/portas`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await res.json();
        const select = document.getElementById('portaSerial');
        data.portas.forEach(p => {
            const opt = document.createElement('option');
            opt.value = p.porta;
            opt.textContent = p.porta;
            select.appendChild(opt);
        });
    } catch (error) {
        console.error('Erro ao carregar portas:', error);
    }
}

async function carregarStatus() {
    try {
        const res = await fetch(`${API_BASE}/api/status`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        if (res.status === 401) {
            logout();
            return;
        }
        const data = await res.json();
        const badge = document.getElementById('statusBadge');
        if (data.serial.conectado) {
            badge.className = 'status-badge online';
            badge.textContent = '🟢 Conectado';
        } else {
            badge.className = 'status-badge offline';
            badge.textContent = '🔴 Desconectado';
        }
    } catch (error) {
        console.error('Erro:', error);
    }
}

async function conectar() {
    const porta = document.getElementById('portaSerial').value;
    if (!porta) { alert('Selecione uma porta!'); return; }

    try {
        const res = await fetch(`${API_BASE}/api/serial/conectar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify({ porta, baud: 115200 })
        });
        const data = await res.json();
        alert(data.mensagem);
        carregarStatus();
    } catch (error) {
        alert('Erro: ' + error);
    }
}

async function desconectar() {
    try {
        const res = await fetch(`${API_BASE}/api/serial/desconectar`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await res.json();
        alert(data.mensagem);
        carregarStatus();
    } catch (error) {
        alert('Erro: ' + error);
    }
}

async function zerarLitragem() {
    // Implementar comando D para ESP32
}

async function carregarHistorico() {
    try {
        const res = await fetch(`${API_BASE}/api/historico?limit=50`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await res.json();
        const tbody = document.querySelector('#tabelaHistorico tbody');
        tbody.innerHTML = '';
        data.historico.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${new Date(item.data_hora).toLocaleString('pt-BR')}</td>
                <td>${item.motorista}</td>
                <td>${item.placa}</td>
                <td>${item.km}</td>
                <td><strong>${item.litros.toFixed(2)}L</strong></td>
            `;
            tbody.appendChild(tr);
        });
    } catch (error) {
        console.error('Erro:', error);
    }
}

async function carregarEstatisticas() {
    try {
        const res = await fetch(`${API_BASE}/api/estatisticas`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await res.json();
        document.getElementById('estatTotalLitros').textContent = data.geral.total_litros + ' L';
        document.getElementById('estatTotalAbast').textContent = data.geral.total_abastecimentos;
    } catch (error) {
        console.error('Erro:', error);
    }
}

function adicionarLog(timestamp, mensagem) {
    const console = document.getElementById('console');
    const log = document.createElement('div');
    log.textContent = `[${timestamp}] ${mensagem}`;
    console.appendChild(log);
    console.scrollTop = console.scrollHeight;
}

function abrirTab(tabName) {
    console.log('Abrindo tab:', tabName);

    // Esconder todas as tabs
    const tabs = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => tab.classList.remove('active'));
    
    // Remover active de todos os botões
    const buttons = document.querySelectorAll('.tab-button');
    buttons.forEach(btn => btn.classList.remove('active'));
    
    // Mostrar tab selecionada
    document.getElementById('tab-' + tabName).classList.add('active');
    
    // Ativar botão clicado
    event.target.classList.add('active');
    
    // Carregar dados conforme a tab
    if (tabName === 'historico') {
        carregarHistorico();
    } else if (tabName === 'estatisticas') {
        carregarEstatisticas();
        atualizarEstatisticas();
    } else if (tabName === 'motoristas') {
        carregarMotoristas();
    } else if (tabName === 'veiculos') {
        carregarVeiculos();
    }
}

// ==================== MOTORISTAS ====================

function abrirModalMotorista() {
    document.getElementById('modalMotorista').classList.add('active');
}

function fecharModalMotorista() {
    document.getElementById('modalMotorista').classList.remove('active');
    document.getElementById('formMotorista').reset();
    document.getElementById('motId').value = '';
    document.querySelector('#modalMotorista h3').textContent = 'Novo Motorista';
}

async function salvarMotorista(event) {
    event.preventDefault();

    const cpf = document.getElementById('motCpf').value;
    const telefone = document.getElementById('motTelefone').value;

    // Validar CPF se preenchido
    if (cpf && cpf.trim() !== '') {
        if (!validarCPF(cpf)) {
            alert('❌ CPF inválido!');
            document.getElementById('motCpf').focus();
            return;
        }
    }
    
    const id = document.getElementById('motId').value;
    const motorista = {
        nome: document.getElementById('motNome').value,
        rfid: document.getElementById('motRfid').value || null,
        cpf: cpf ? formatarCPF(cpf) : null,
        telefone: telefone ? formatarTelefone(telefone) : null
    };

    const metodo = id ? 'PUT' : 'POST';
    const url = id ? `${API_BASE}/api/motoristas/${id}` : `${API_BASE}/api/motoristas`;

    try {
        const response = await fetch(url, {
            method: metodo,
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify(motorista)
        });
        
        if (response.ok) {
            alert(id ? '✅ Motorista atualizado!' : '✅ Motorista cadastrado com sucesso!');
            fecharModalMotorista();
            carregarMotoristas();
            atualizarEstatisticas();
        } else {
            const error = await response.json();
            alert('❌ Erro: ' + (error.detail || JSON.stringify(error)));
        }
    } catch (error) {
        alert('❌ Erro de conexão: ' + error.message);
    }
}

async function carregarMotoristas() {
    try {
        const response = await fetch(`${API_BASE}/api/motoristas`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        
        if (response.ok) {
            let motoristas = await response.json();

            //Ordenar
            motoristas.sort((a, b) => {
                let valA = a[ordenMotoristas.coluna];
                let valB = b[ordenMotoristas.coluna];
                
                if (typeof valA === 'string') {
                    valA = valA.toLowerCase();
                    valB = valB.toLowerCase();
                }
                
                if (ordenMotoristas.ordem === 'asc') {
                    return valA > valB ? 1 : -1;
                } else {
                    return valA < valB ? 1 : -1;
                }
            });

            const tbody = document.getElementById('tabelaMotoristas');
            document.getElementById('totalMotoristas').textContent = motoristas.length;
            
            if (motoristas.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 40px; color: #999;">Nenhum motorista cadastrado</td></tr>';
                return;
            }
            
            tbody.innerHTML = motoristas.map(m => `
                <tr>
                    <td>${m.id}</td>
                    <td><strong>${m.nome}</strong></td>
                    <td>${m.rfid || '---'}</td>
                    <td>${m.cpf || '---'}</td>
                    <td>${m.telefone || '---'}</td>
                    <td style="text-align: center;">
                        <button onclick="editarMotorista(${m.id})" class="btn-secondary" style="padding: 5px 10px; margin-right: 5px;">
                            Editar
                        </button>
                        <button onclick="deletarMotorista(${m.id})" class="btn-danger">
                            Excluir
                        </button>
                    </td>
                </tr>
            `).join('');
        }
    } catch (error) {
        console.error('Erro ao carregar motoristas:', error);
    }
}

async function deletarMotorista(id) {
    if (!confirm('⚠️ Deseja realmente deletar este motorista?')) return;
    
    try {
        const response = await fetch(`${API_BASE}/api/motoristas/${id}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });
        
        if (response.ok) {
            alert('✅ Motorista deletado com sucesso!');
            carregarMotoristas();
        } else {
            alert('❌ Erro ao deletar motorista');
        }
    } catch (error) {
        alert('❌ Erro: ' + error.message);
    }
}


// ==================== VEÍCULOS ====================

function abrirModalVeiculo() {
    document.getElementById('modalVeiculo').classList.add('active');
}

function fecharModalVeiculo() {
    document.getElementById('modalVeiculo').classList.remove('active');
    document.getElementById('formVeiculo').reset();
    document.getElementById('veiId').value = '';
    document.getElementById('veiCodigo').value = '';
    document.querySelector('#modalVeiculo h3').textContent = 'Novo Veículo';
}

async function salvarVeiculo(event) {
    event.preventDefault();
    
    const placa = document.getElementById('veiPlaca').value;
    const codigo = document.getElementById('veiCodigo').value;

    // Validar Placa
    if (!validarPlaca(placa)) {
        alert('❌ Placa inválida! Formato antigo ou mercosul errado');
        document.getElementById('veiPlaca').focus();
        return;
    }

    // Validar código
    if (!codigo || codigo.length < 3) {
        alert('❌ Código numérico inválido!\n\nA placa deve conter pelo menos 3 números.\nExemplo: ABC-1234 → código 1234');
        document.getElementById('veiPlaca').focus();
        return;
    }

    const id = document.getElementById('veiId').value;
    const veiculo = {
        placa: formatarPlaca(placa),
        modelo: document.getElementById('veiModelo').value || null,
        km_atual: parseInt(document.getElementById('veiKm').value) || null,
        ano: parseInt(document.getElementById('veiAno').value) || null
    };
    
    const metodo = id ? 'PUT' : 'POST';
    const url = id ? `${API_BASE}/api/veiculos/${id}` : `${API_BASE}/api/veiculos`;

    try {
        const response = await fetch(url, {
            method: metodo,
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify(veiculo)
        });
        
        if (response.ok) {
            alert(id ? '✅ Veículo atualizado!' : '✅ Veículo cadastrado!');
            fecharModalVeiculo();
            carregarVeiculos();
            atualizarEstatisticas();
        } else {
            const error = await response.json();
            alert('❌ Erro: ' + (error.detail || JSON.stringify(error)));
        }
    } catch (error) {
        alert('❌ Erro de conexão: ' + error.message);
    }
}

async function carregarVeiculos() {    
    try {
        const response = await fetch(`${API_BASE}/api/veiculos`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        
        if (response.ok) {
            let veiculos = await response.json();

            // Ordenar
            veiculos.sort((a, b) => {
                let valA = a[ordenVeiculos.coluna];
                let valB = b[ordenVeiculos.coluna];
                
                // Tratar valores nulos
                if (valA === null || valA === undefined) valA = '';
                if (valB === null || valB === undefined) valB = '';
                
                // Converter strings para minúsculo
                if (typeof valA === 'string') {
                    valA = valA.toLowerCase();
                    valB = valB.toLowerCase();
                }
                
                // Ordenar
                if (ordenVeiculos.ordem === 'asc') {
                    return valA > valB ? 1 : -1;
                } else {
                    return valA < valB ? 1 : -1;
                }
            });
            
            const tbody = document.getElementById('tabelaVeiculos');
            const totalElement = document.getElementById('totalVeiculos');
            
            if (totalElement) {
                totalElement.textContent = veiculos.length;
            }
            
            if (veiculos.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 40px; color: #999;">Nenhum veículo cadastrado</td></tr>';
                return;
            }
            
            tbody.innerHTML = veiculos.map(v => `
                <tr>
                    <td>${v.id}</td>
                    <td><strong>${v.placa}</strong></td>
                    <td style="text-align: center; padding: 8px;">
                        <span style="display: inline-block; background: #5a67d8; color: white; padding: 4px 10px; border-radius: 4px; font-weight: 600; font-size: 13px; min-width: 40px;">
                            ${v.codigo_numerico || '---'}
                        </span>
                    </td>
                    <td>${v.modelo || '---'}</td>
                    <td>${v.km_atual ? v.km_atual + ' km' : '---'}</td>
                    <td>${v.ano || '---'}</td>
                    <td style="text-align: center;">
                        <button onclick="editarVeiculo(${v.id})" class="btn-secondary" style="padding: 5px 10px; margin-right: 5px;">
                            Editar
                        </button>
                        <button onclick="deletarVeiculo(${v.id})" class="btn-danger">
                            Excluir
                        </button>
                    </td>
                </tr>
            `).join('');
        }
    } catch (error) {
        console.error('❌ Erro:', error);
    }
}

async function deletarVeiculo(id) {
    if (!confirm('⚠️ Deseja realmente deletar este veículo?')) return;
    
    try {
        const response = await fetch(`${API_BASE}/api/veiculos/${id}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });
        
        if (response.ok) {
            alert('✅ Veículo deletado com sucesso!');
            carregarVeiculos();
        } else {
            alert('❌ Erro ao deletar veículo');
        }
    } catch (error) {
        alert('❌ Erro: ' + error.message);
    }
}


// ==================== BUSCA/FILTRO ====================

function filtrarMotoristas() {
    const busca = document.getElementById('buscaMotorista').value.toLowerCase();
    const linhas = document.querySelectorAll('#tabelaMotoristas tr');
    
    linhas.forEach(linha => {
        const texto = linha.textContent.toLowerCase();
        linha.style.display = texto.includes(busca) ? '' : 'none';
    });
}

function filtrarVeiculos() {
    const busca = document.getElementById('buscaVeiculo').value.toLowerCase();
    const linhas = document.querySelectorAll('#tabelaVeiculos tr');
    
    linhas.forEach(linha => {
        const texto = linha.textContent.toLowerCase();
        linha.style.display = texto.includes(busca) ? '' : 'none';
    });
}


// ==================== ORDENAÇÃO ====================

function ordenarMotoristas(coluna) {
    if (ordenMotoristas.coluna === coluna) {
        ordenMotoristas.ordem = ordenMotoristas.ordem === 'asc' ? 'desc' : 'asc';
    } else {
        ordenMotoristas.coluna = coluna;
        ordenMotoristas.ordem = 'asc';
    }
    atualizarIndicadoresOrdenacao('tabelaMotoristas', coluna, ordenMotoristas.ordem);
    carregarMotoristas();
}

function ordenarVeiculos(coluna) {
    if (ordenVeiculos.coluna === coluna) {
        ordenVeiculos.ordem = ordenVeiculos.ordem === 'asc' ? 'desc' : 'asc';
    } else {
        ordenVeiculos.coluna = coluna;
        ordenVeiculos.ordem = 'asc';
    }
    atualizarIndicadoresOrdenacao('tabelaVeiculos', coluna, ordenVeiculos.ordem);
    carregarVeiculos();
}

function atualizarIndicadoresOrdenacao(tabelaId, colunaOrdenada, ordem) {
    // Busca todos os cabeçalhos da tabela específica
    const tabela = document.querySelector(`#${tabelaId}`).closest('table');
    const ths = tabela.querySelectorAll('thead th');
    
    ths.forEach(th => {
        // Remove indicadores antigos
        let texto = th.textContent.replace(' ▲', '').replace(' ▼', '').replace(' ↕️', '').trim();
        
        // Se a coluna tem onclick, adiciona ↕️
        if (th.hasAttribute('onclick')) {
            th.textContent = texto + ' ↕️';
        } else {
            th.textContent = texto;
        }
    });
    
    // Adiciona indicador na coluna ordenada
    ths.forEach(th => {
        const onclick = th.getAttribute('onclick');
        if (onclick && onclick.includes(`'${colunaOrdenada}'`)) {
            let texto = th.textContent.replace(' ↕️', '').trim();
            th.textContent = texto + (ordem === 'asc' ? ' ▲' : ' ▼');
        }
    });
}

// ==================== VALIDAÇÕES ====================

function validarCPF(cpf) {
    cpf = cpf.replace(/[^\d]/g, '');
    
    if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) return false;
    
    let soma = 0;
    for (let i = 0; i < 9; i++) {
        soma += parseInt(cpf.charAt(i)) * (10 - i);
    }
    let digito1 = 11 - (soma % 11);
    if (digito1 > 9) digito1 = 0;
    
    soma = 0;
    for (let i = 0; i < 10; i++) {
        soma += parseInt(cpf.charAt(i)) * (11 - i);
    }
    let digito2 = 11 - (soma % 11);
    if (digito2 > 9) digito2 = 0;
    
    return parseInt(cpf.charAt(9)) === digito1 && parseInt(cpf.charAt(10)) === digito2;
}

function validarPlaca(placa) {
    placa = placa.toUpperCase().replace(/[^A-Z0-9]/g, '');
    
    // Placa antiga: ABC1234
    const padraoAntiga = /^[A-Z]{3}[0-9]{4}$/;
    
    // Placa Mercosul: ABC1D23
    const padraoMercosul = /^[A-Z]{3}[0-9][A-Z][0-9]{2}$/;
    
    return padraoAntiga.test(placa) || padraoMercosul.test(placa);
}

function formatarCPF(cpf) {
    cpf = cpf.replace(/[^\d]/g, '');
    return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
}

function formatarPlaca(placa) {
    placa = placa.toUpperCase().replace(/[^A-Z0-9]/g, '');
    
    if (/^[A-Z]{3}[0-9]{4}$/.test(placa)) {
        // Antiga: ABC-1234
        return placa.replace(/([A-Z]{3})([0-9]{4})/, '$1-$2');
    } else if (/^[A-Z]{3}[0-9][A-Z][0-9]{2}$/.test(placa)) {
        // Mercosul: ABC1D23
        return placa.replace(/([A-Z]{3})([0-9][A-Z][0-9]{2})/, '$1-$2');
    }
    return placa;
}

function formatarTelefone(telefone) {
    telefone = telefone.replace(/[^\d]/g, '');
    
    if (telefone.length === 11) {
        // (00) 00000-0000
        return telefone.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    } else if (telefone.length === 10) {
        // (00) 0000-0000
        return telefone.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
    }
    return telefone;
}

function gerarCodigoNumerico() {
    const placa = document.getElementById('veiPlaca').value;
    // Extrai apenas números da placa
    const codigo = placa.replace(/[^0-9]/g, '');
    const campoCodigo = document.getElementById('veiCodigo');
    
    if (codigo.length >= 3) {
        campoCodigo.value = codigo;
        campoCodigo.style.color = '#4CAF50'; // Verde se válido
    } else {
        campoCodigo.value = codigo || 'Aguardando...';
        campoCodigo.style.color = '#999'; // Cinza se incompleto
    }
}


// ==================== MÁSCARAS ====================

// Aplicar máscaras em tempo real
function aplicarMascaraCPF(input) {
    let valor = input.value.replace(/[^\d]/g, '');
    if (valor.length > 11) valor = valor.substr(0, 11);
    
    if (valor.length > 9) {
        input.value = valor.replace(/(\d{3})(\d{3})(\d{3})(\d{0,2})/, '$1.$2.$3-$4');
    } else if (valor.length > 6) {
        input.value = valor.replace(/(\d{3})(\d{3})(\d{0,3})/, '$1.$2.$3');
    } else if (valor.length > 3) {
        input.value = valor.replace(/(\d{3})(\d{0,3})/, '$1.$2');
    } else {
        input.value = valor;
    }
}

function aplicarMascaraTelefone(input) {
    let valor = input.value.replace(/[^\d]/g, '');
    if (valor.length > 11) valor = valor.substr(0, 11);
    
    if (valor.length > 10) {
        input.value = valor.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
    } else if (valor.length > 6) {
        input.value = valor.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
    } else if (valor.length > 2) {
        input.value = valor.replace(/(\d{2})(\d{0,5})/, '($1) $2');
    } else {
        input.value = valor;
    }
}

function aplicarMascaraPlaca(input) {
    let valor = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    if (valor.length > 7) valor = valor.substr(0, 7);
    
    if (valor.length > 3) {
        input.value = valor.replace(/([A-Z]{3})(.{0,4})/, '$1-$2');
    } else {
        input.value = valor;
    }
}


// ==================== EDITAR ====================

function editarMotorista(id) {
    // Buscar dados do motorista
    fetch(`${API_BASE}/api/motoristas`, {
        headers: { 'Authorization': `Bearer ${token}` }
    })
    .then(res => res.json())
    .then(motoristas => {
        const motorista = motoristas.find(m => m.id === id);
        if (motorista) {
            document.getElementById('motId').value = motorista.id;
            document.getElementById('motNome').value = motorista.nome;
            document.getElementById('motRfid').value = motorista.rfid || '';
            document.getElementById('motCpf').value = motorista.cpf || '';
            document.getElementById('motTelefone').value = motorista.telefone || '';
            
            document.querySelector('#modalMotorista h3').textContent = '✏️ Editar Motorista';
            abrirModalMotorista();
        }
    });
}

function editarVeiculo(id) {
    // Buscar dados do veículo
    fetch(`${API_BASE}/api/veiculos`, {
        headers: { 'Authorization': `Bearer ${token}` }
    })
    .then(res => res.json())
    .then(veiculos => {
        const veiculo = veiculos.find(v => v.id === id);
        if (veiculo) {
            document.getElementById('veiId').value = veiculo.id;
            document.getElementById('veiPlaca').value = veiculo.placa;
            document.getElementById('veiCodigo').value = veiculo.codigo_numerico || '';
            document.getElementById('veiModelo').value = veiculo.modelo || '';
            document.getElementById('veiKm').value = veiculo.km_atual || '';
            document.getElementById('veiAno').value = veiculo.ano || '';
            
            document.querySelector('#modalVeiculo h3').textContent = '✏️ Editar Veículo';
            abrirModalVeiculo();
        }
    });
}


// ==================== ATUALIZAR ESTATÍSTICAS ====================

async function atualizarEstatisticas() {
    try {
        // Total motoristas
        const resMotoristas = await fetch(`${API_BASE}/api/motoristas`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const motoristas = await resMotoristas.json();
        document.getElementById('estatTotalMotoristas').textContent = motoristas.length;
        
        // Total veículos
        const resVeiculos = await fetch(`${API_BASE}/api/veiculos`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const veiculos = await resVeiculos.json();
        document.getElementById('estatTotalVeiculos').textContent = veiculos.length;
        
    } catch (error) {
        console.error('Erro ao atualizar estatísticas:', error);
    }
}