<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | Nutribox</title>
    <link rel="stylesheet" href="{{ asset('css/nutribox.css') }}">
</head>
<body class="page-auth">
    <header class="auth-header"><a href="{{ route('inicio') }}"><img src="{{ asset('img/logo.png') }}" alt="Nutribox"></a><a href="{{ route('inicio') }}">← Volver a la tienda</a></header>
    <main class="auth-layout">
        <section class="auth-card">
            <div class="auth-brand-panel">
                <img src="{{ asset('img/logo.png') }}" alt="Nutribox" class="auth-brand-logo">
                <h2>Snacks saludables, una vida en equilibrio.</h2>
                <p>Inicia sesión para continuar disfrutando de la experiencia Nutribox.</p>
                <img src="{{ asset('img/snacks_login.png') }}" class="auth-snack" alt="Snacks saludables">
            </div>
            <div class="auth-form-panel">
                <div class="auth-avatar">♙</div>
                <span class="eyebrow">TU CUENTA NUTRIBOX</span>
                <h1>Iniciar sesión</h1>
                <p class="auth-subtitle">Ingresa tu correo y contraseña.</p>
                @if(session('status'))<div class="alert success">{{ session('status') }}</div>@endif
                @if($errors->any())<div class="alert error">{{ $errors->first() }}</div>@endif
                <form method="POST" action="{{ route('login.procesar') }}">
                    @csrf
                    <label for="correo">Correo electrónico</label>
                    <input id="correo" name="correo" type="email" value="{{ old('correo') }}" placeholder="ejemplo@correo.com" required autofocus autocomplete="email">
                    <label for="contrasena">Contraseña</label>
                    <div class="password-field"><input id="contrasena" name="contrasena" type="password" placeholder="Tu contraseña" required autocomplete="current-password"><button type="button" onclick="togglePassword('contrasena',this)">Mostrar</button></div>
                    <button type="submit" class="form-submit">Iniciar sesión →</button>
                </form>
                <p class="auth-bottom">¿Todavía no tienes cuenta? <a href="{{ route('registro') }}">Regístrate aquí</a></p>
            </div>
        </section>
    </main>
    <script>
    function togglePassword(id,button){const input=document.getElementById(id);input.type=input.type==='password'?'text':'password';button.textContent=input.type==='password'?'Mostrar':'Ocultar';}
    </script>
</body></html>
