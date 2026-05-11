document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const btnLogin = document.getElementById('btnLogin');
    const errorMessage = document.getElementById('errorMessage');
    
    // Limpa erros anteriores
    errorMessage.classList.remove('show');

    // Desabilita botão e mostra loading
    btnLogin.disabled = true;
    btnLogin.innerHTML = '<span class="loading"></span> Entrando...';

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

        } else {
            alert(data.message);
        }

    } catch (error) {
        alert('Erro ao conectar com o servidor');
        console.error(error);
    }
});