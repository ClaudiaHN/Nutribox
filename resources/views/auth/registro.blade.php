<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta | Nutribox</title>
    <link rel="stylesheet" href="{{ asset('css/nutribox.css') }}">
</head>
<body class="page-auth">
    <header class="auth-header"><a href="{{ route('inicio') }}"><img src="{{ asset('img/logo.png') }}" alt="Nutribox"></a><a href="{{ route('inicio') }}">← Volver a la tienda</a></header>
    <main class="auth-layout">
        <section class="auth-card register-card">
            <div class="auth-brand-panel">
                <img src="{{ asset('img/logo.png') }}" alt="Nutribox" class="auth-brand-logo">
                <h2>Únete a Nutribox</h2><p>Descubre tus opciones favoritas de snacks saludables.</p>
                <img src="{{ asset('img/snacks_login.png') }}" class="auth-snack" alt="Snacks saludables">
            </div>
            <div class="auth-form-panel register-panel">
                <span class="eyebrow">COMIENZA AQUÍ</span><h1>Crear una cuenta</h1>
                <p class="auth-subtitle">Completa tus datos para registrarte.</p>
                @if($errors->any())<div class="alert error"><strong>Revisa los datos:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
                <form method="POST" action="{{ route('registro.procesar') }}">
                    @csrf
                    <label for="nombres">Nombres y apellidos</label>
                    <input id="nombres" name="nombres" value="{{ old('nombres') }}" maxlength="100" required autocomplete="name">
                    <div class="form-two">
                        <div><label for="telefono">Teléfono</label><input id="telefono" name="telefono" type="tel" value="{{ old('telefono') }}" maxlength="9" pattern="9[0-9]{8}" inputmode="numeric" placeholder="9XXXXXXXX" required></div>
                        <div><label for="correo">Correo electrónico</label><input id="correo" name="correo" type="email" value="{{ old('correo') }}" maxlength="120" required autocomplete="email"></div>
                    </div>
                    <div class="form-two">
                        <div><label for="distrito">Distrito</label><input id="distrito" name="distrito" value="{{ old('distrito') }}" maxlength="60" required></div>
                        <div><label for="direccion">Dirección o referencia</label><input id="direccion" name="direccion" value="{{ old('direccion') }}" maxlength="200" required></div>
                    </div>
                    <div class="form-two">
                        <div><label for="contrasena">Contraseña</label><input id="contrasena" type="password" name="contrasena" minlength="8" required autocomplete="new-password"></div>
                        <div><label for="confirmar_contrasena">Repetir contraseña</label><input id="confirmar_contrasena" type="password" name="confirmar_contrasena" minlength="8" required autocomplete="new-password"></div>
                    </div>
                    <button type="submit" class="form-submit">Crear cuenta →</button>
                </form>
                <p class="auth-bottom">¿Ya tienes una cuenta? <a href="{{ route('login') }}">Inicia sesión aquí</a></p>
            </div>
        </section>
    </main>
</body></html>
