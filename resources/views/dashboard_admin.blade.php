<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TANKER - Dashboard')</title>

    <link rel="stylesheet" href="{{ asset('css/dashboard_admin.css') }}">
</head>
<body>
    
    <div class="container">
        <div class="header">
            <h1>Sistema de Abastecimento</h1>
            <div style="display: flex; gap: 20px; align-items: center;">
                <div class="user-info">
                    <div class="company" id="companyName">Empresa</div>
                    <div class="name" id="userName">Usuário</div>
                </div>
                <div id="statusBadge" class="status-badge offline">🔴 Desconectado</div>
                <form method="POST" action="{{ route('logout.web') }}">
                    @csrf
                    <button type="submit" class="btn-danger">Sair</button>
                </form>
            </div>
        </div>

        <div class="connection-bar">
            <select id="portaSerial"><option value="">Porta...</option></select>
            <button onclick="conectar()" class="btn-primary">Conectar</button>
            <button onclick="desconectar()" class="btn-danger">Desconectar</button>
            <button onclick="zerarLitragem()" class="btn-warning">Zerar</button>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>⛽ Litragem Atual</h3>
                <div class="stat-value" id="litragemAtual">0.00 L</div>
            </div>
            <div class="stat-card">
                <h3>📊 Totalizador</h3>
                <div class="stat-value" id="totalizador">0.00 L</div>
            </div>
            <div class="stat-card">
                <h3>👤 Motorista</h3>
                <div class="stat-value" id="motorista" style="font-size: 20px;">---</div>
            </div>
            <div class="stat-card">
                <h3>📍 Estado</h3>
                <div class="stat-value" id="estado" style="font-size: 18px;">AGUARDANDO</div>
            </div>
        </div>

        <div class="tabs">
            <div class="tab-header">
                <button class="tab-button active" onclick="abrirTab('monitor')">📟 Monitor</button>
                <button class="tab-button" onclick="abrirTab('historico')">📋 Histórico</button>
                <button class="tab-button" onclick="abrirTab('estatisticas')">📊 Estatísticas</button>
                <button class="tab-button" onclick="abrirTab('motoristas')">👤 Motoristas</button>
                <button class="tab-button" onclick="abrirTab('veiculos')">🚗 Veículos</button>
            </div>

            <!-- Tab Monitor -->
            <div id="tab-monitor" class="tab-content active">
                <div class="console" id="console"></div>
            </div>
            
            <!-- Tab Historico -->
            <div id="tab-historico" class="tab-content">
                <table id="tabelaHistorico">
                    <thead><tr><th>Data/Hora</th><th>Motorista</th><th>Placa</th><th>KM</th><th>Litros</th></tr></thead>
                    <tbody></tbody>
                </table>
            </div>
            
            <!-- Tab Estatisticas -->
            <div id="tab-estatisticas" class="tab-content">
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Total Litros</h3>
                        <div class="stat-value" id="estatTotalLitros">0 L</div>
                    </div>

                    <div class="stat-card">
                        <h3>Total Abastecimentos</h3>
                        <div class="stat-value" id="estatTotalAbast">0</div>
                    </div>

                    <div class="stat-card">
                        <h3>Total Motoristas</h3>
                        <div class="stat-value" id="estatTotalMotoristas">0</div>
                    </div>

                    <div class="stat-card">
                        <h3>Total Veículos</h3>
                        <div class="stat-value" id="estatTotalVeiculos">0</div>
                    </div>
                </div>
            </div>

            <!-- Tab Motoristas -->
            <div id="tab-motoristas" class="tab-content">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3>Motoristas Cadastrados (<span id="totalMotoristas">0</span>)</h3>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" id="buscaMotorista" placeholder="Buscar motorista..."
                            onkeyup="filtrarMotoristas()"
                            style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; width: 250px;">
                        <button onclick="abrirModalMotorista()" class="btn-success">Novo Motorista</button>
                    </div>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th onclick="ordenarMotoristas('id')" style="cursor: pointer;">ID ↕️</th>
                                <th onclick="ordenarMotoristas('nome')" style="cursor: pointer">Nome ↕️</th>
                                <th>RFID</th>
                                <th>CPF</th>
                                <th>Telefone</th>
                                <th style="text-align: center; width: 180px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="tabelaMotoristas">
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px; color: #999;">
                                    Nenhum motorista cadastrado
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab Veículos -->
            <div id="tab-veiculos" class="tab-content">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3>Veículos Cadastrados (<span id="totalVeiculos">0</span>)</h3>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" id="buscaVeiculo" placeholder="Buscar motorista..."
                            onkeyup="filtrarVeiculos()"
                            style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; width: 250px;">
                        <button onclick="abrirModalVeiculo()" class="btn-success">Novo Veículo</button>
                    </div>
                </div>
                
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th onclick="ordenarVeiculos('id')" style="cursor: pointer;">ID ↕️</th>
                                <th onclick="ordenarVeiculos('placa')" style="cursor: pointer;">Placa ↕️</th>
                                <th onclick="ordenarVeiculos('codigo_numerico')" style="cursor: pointer; text-align: center;">Cod. ESP ↕️</th>
                                <th onclick="ordenarVeiculos('modelo')" style="cursor: pointer;">Modelo ↕️</th>
                                <th onclick="ordenarVeiculos('km_atual')" style="cursor: pointer;">KM Atual ↕️</th>
                                <th onclick="ordenarVeiculos('ano')" style="cursor: pointer; text-align: center;">Ano ↕️</th>
                                <th style="text-align: center; width: 180px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="tabelaVeiculos">
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px; color: #999;">
                                    Nenhum veículo cadastrado
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Novo Motorista -->
    <div id="modalMotorista" class="modal">
        <div class="modal-content">
            <h3>Novo Motorista</h3>
            <form id="formMotorista" onsubmit="salvarMotorista(event)">
                <input type="hidden" id="motId" value="">

                <div class="form-group">
                    <label>Nome *</label>
                    <input type="text" id="motNome" required>
                </div>

                <div class="form-group">
                    <label>RFID</label>
                    <input type="text" id="motRfid" placeholder="Opcional">
                </div>

                <div class="form-group">
                    <label>CPF</label>
                    <input type="text" id="motCpf" placeholder="000.000.000-00"
                            onkeyup="aplicarMascaraCPF(this)" maxlength="14">
                </div>

                <div class="form-group">
                    <label>Telefone</label>
                    <input type="text" id="motTelefone" placeholder="(00) 00000-0000"
                            onkeyup="aplicarMascaraTelefone(this)" maxlength="15">
                </div>

                <div class="modal-buttons">
                    <button type="button" onclick="fecharModalMotorista()" class="btn-secondary">Cancelar</button>
                    <button type="submit" class="btn-success">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Novo Veículo -->
    <div id="modalVeiculo" class="modal">
        <div class="modal-content">
            <h3>Novo Veículo</h3>
            <form id="formVeiculo" onsubmit="salvarVeiculo(event)">
                <input type="hidden" id="veiId" value="">

                <div class="form-group">
                    <label>Placa *</label>
                    <input type="text" id="veiPlaca" required placeholder="ABC-1234 ou ABC1D23"
                            onkeyup="aplicarMascaraPlaca(this)" maxlength="8">
                </div>

                <div class="form-group">
                    <label>Código Numérico (ESP32) *</label>
                    <input type="text" id="veiCodigo" required placeholder="Automático" 
                            readonly style="background: #f5f5f5; font-weight: bold; font-size: 18px; text-align: center; color: #4CAF50;">
                    <small style="color: #666; font-size: 12px;">
                        ℹ️ Este código será usado no teclado da ESP32 para identificar o veículo
                    </small>
                </div>

                <div class="form-group">
                    <label>Modelo</label>
                    <input type="text" id="veiModelo" placeholder="Ex: Caminhão Mercedes 1620">
                </div>

                <div class="form-group">
                    <label>KM Atual</label>
                    <input type="number" id="veiKm" placeholder="Ex: 50000">
                </div>

                <div class="form-group">
                    <label>Ano</label>
                    <input type="number" id="veiAno" placeholder="Ex: 2020">
                </div>
                
                <div class="modal-buttons">
                    <button type="button" onclick="fecharModalVeiculo()" class="btn-secondary">Cancelar</button>
                    <button type="submit" class="btn-success">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('js/dashboard_admin.js') }}"></script>

</body>
</html>