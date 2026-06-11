document.getElementById('loginForm').addEventListener('submit', (e) => {
  const btnLogin = document.getElementById('btnLogin');

  btnLogin.disabled = true;
  btnLogin.innerHTML = '<span class="loading"></span> Entrando...';
});

