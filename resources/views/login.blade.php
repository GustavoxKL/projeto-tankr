<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TANKER')</title>

    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    @yield('styles')
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <div class="icon">🚛</div>
            <h1>TANKR</h1>
            <p>Gestão de Frotas Multi-Tenant</p>
        </div>

        @if ($errors->any())
            <div class="error-message show">
                {{ $errors->first() }}
            </div>
        @endif

        <form id="loginForm" method="POST" action="{{ route('login.web') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-wrapper">
                    <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25H4.5 a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5H4.5A2.25 2.25 0 0 0 2.25 6.75m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.909a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                    </svg>
                    <input
                        type="email" 
                        id="email" 
                        name="email"
                        required
                        autocomplete="email"
                        placeholder="seu@email.com"
                        value="{{ old('email') }}"
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="password">Senha</label>
                <div class="input-wrapper">
                    <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <input 
                        type="password" 
                        id="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                    >
                </div>
            </div>
    
            <button type="submit" class="btn-login" id="btnLogin">Entrar</button>
        </form>

        <div class="footer">
            <p></p>
        </div>
    </div>

    <!-- <script src="{{ asset('js/login.js') }}"></script> -->

</body>
</html>