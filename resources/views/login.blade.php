<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>

<h2>Login</h2>

<form id="loginForm">
    <input type="email" id="email" placeholder="Email" required><br><br>
    <input type="password" id="password" placeholder="Senha" required><br><br>
    <button type="submit">Login</button>
</form>

<script>
document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    try {
        const response = await fetch('/api/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email, password })
        });

        const data = await response.json();

        if (response.ok) {
            // salva token
            localStorage.setItem('token', data.token);

            alert('Login realizado com sucesso!');

            // redireciona (cria depois)
            window.location.href = '/dashboard';
        } else {
            alert(data.message);
        }

    } catch (error) {
        alert('Erro ao conectar com o servidor');
        console.error(error);
    }
});
</script>

</body>
</html>