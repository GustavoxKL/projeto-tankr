<!--<div class="dash-principal">
    <div class="cabecalho">
        <h2>Dashboard</h2>
    </div>
    
    <div class="cards-principal">
        <div class="card">
            <h4>Abastecimentos (Mês)</h4>
            <p>56</p>
        </div>

        <div class="card">
            <h4>Custo Totoal (Mês)</h4>
            <p>R$ 79.820</p>
        </div>

        <div class="card">
            <h4>Consumo Médio</h4>
            <p>6.8 km/L</p>
        </div>

        <div class="card">
            <h4>Economia Planejada</h4>
            <p>R$ 4.200</p>
        </div>
    </div>

    <div class="graficos">
        <div class="grafic-ConsumoMensal">
            <h3>Consumo Mensal (Litros)</h3>
            <img src="" alt="">
        </div>

        <div class="grafic-CustoMensal">
            <h3>Custo Mensal (R$)</h3>
            <img src="" alt="">
        </div>
    </div>

    <div class="table-abastecimentos">
        <h3>Abastecimentos Recentes</h3>
    </div>

    <div class="table-veiculos">
        <h3>Veiculos da Frota</h3>
    </div>

</div>



<div class="dash-empresas">
    <div class="cabecalho">
        <h2>Empresas Cadastradas</h2>
        <button class="botao">+ Adicionar Empresa</button>
    </div>
        
    <div class="cards">
        <div class="card-empresa"></div>
        <div class="card-empresa"></div>
        <div class="card-empresa"></div>
        <div class="card-empresa"></div>
        <div class="card-empresa"></div>
        <div class="card-empresa"></div>
    </div>
</div>



<div class="dash-motoristas">
    <div class="cabecalho">
        <h2>Motoristas</h2>
        <button class="botao">+ Adicionar Motorista</button>
    </div>

    <div class="cards">
        <div class="card-mot"></div>
        <div class="card-mot"></div>
        <div class="card-mot"></div>
        <div class="card-mot"></div>
        <div class="card-mot"></div>
        <div class="card-mot"></div>
    </div>
</div>



<div class="dash-veiculos">
    <div class="cabecalho">
        <h2>Veículos</h2>
        <button class="botao">+ Adicionar Veículo</button>
    </div>

    <div class="cards">
        <div class="card-vei"></div>
        <div class="card-vei"></div>
        <div class="card-vei"></div>
        <div class="card-vei"></div>
        <div class="card-vei"></div>
        <div class="card-vei"></div>
    </div>
</div>
-->

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>

<h1>Dashboard</h1>

<h2 id="userName"></h2>

<button onclick="logout()">Logout</button>

<script>
// 🔒 Verifica se existe token
const token = localStorage.getItem('token');

if (!token) {
    window.location.href = '/login';
}

// 🔍 Valida token no backend
fetch('/api/me', {
    headers: {
        'Authorization': 'Bearer ' + token,
        'Accept': 'application/json'
    }
})
.then(response => {
    if (!response.ok) {
        // Token inválido ou expirado
        localStorage.removeItem('token');
        window.location.href = '/login';
        return;
    }
    return response.json();
})
.then(data => {
    if (data) {
        // 👤 Mostra nome do usuário
        document.getElementById('userName').innerText =
            'Olá, ' + data.user.NomeUser;
    }
})
.catch(error => {
    console.error(error);
    alert('Erro ao validar sessão');
});

// 🚪 Logout
function logout() {
    fetch('/api/logout', {
        method: 'POST',
        headers: {
            'Authorization': 'Bearer ' + token,
            'Accept': 'application/json'
        }
    });

    localStorage.removeItem('token');
    window.location.href = '/login';
}
</script>

</body>
</html>