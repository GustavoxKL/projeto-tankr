  document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const btnLogin = document.getElementById('btnLogin');

    // Desabilita botão e mostra loading
    btnLogin.disabled = true;
    btnLogin.innerHTML = '<span class="loading"></span> Entrando...';
});
