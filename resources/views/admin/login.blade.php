<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Administración | Nutribox</title><link rel="stylesheet" href="{{ asset('css/nutribox.css') }}"></head>
<body class="page-auth">
    <header class="auth-header"><a href="{{ route('inicio') }}"><img src="{{ asset('img/logo.png') }}" alt="Nutribox"></a><a href="{{ route('inicio') }}">← Volver a la tienda</a></header>
    <main class="auth-layout"><section class="auth-card"><div class="auth-brand-panel"><img src="{{ asset('img/logo.png') }}" alt="Nutribox" class="auth-brand-logo"><h2>Panel administrativo</h2><p>Esta entrada es exclusiva para la gestión interna de Nutribox.</p><img src="{{ asset('img/snacks_login.png') }}" alt="Snacks" class="auth-snack"></div>
    <div class="auth-form-panel"><div class="auth-avatar">♙</div><span class="eyebrow">ACCESO DEL EQUIPO</span><h1>Administración</h1><p class="auth-subtitle">El diseño está preparado. La autenticación de administradores se incorporará después.</p>
    <form onsubmit="event.preventDefault();document.getElementById('mensaje-admin').hidden=false;"><label for="usuario">Usuario</label><input id="usuario" name="usuario" required autocomplete="username"><label for="password">Contraseña</label><input id="password" type="password" name="password" required autocomplete="current-password"><button class="form-submit" type="submit">Ingresar →</button></form><div id="mensaje-admin" class="alert error" hidden>Acceso administrativo en construcción.</div></div></section></main>
</body></html>
