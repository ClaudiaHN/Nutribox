<?php
/**
 * RF06 — Autenticar administrador.
 * Usuario y hash viven en config/config.php (fuera del directorio servido por .htaccess).
 * password_verify + regeneración del id de sesión al ingresar (RNF03).
 */
require __DIR__ . '/../includes/init.php';

if (!empty($_SESSION['admin'])) {
    redirigir('pedidos.php');
}

$error   = '';
$usuario = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim((string) ($_POST['usuario'] ?? ''));
    $clave   = (string) ($_POST['clave'] ?? '');

    /* Pequeño freno ante intentos repetidos (sin tabla extra) */
    $_SESSION['intentos_login'] = ($_SESSION['intentos_login'] ?? 0);
    if ($_SESSION['intentos_login'] >= 8) {
        usleep(800000);
    }

    if (!csrfValido()) {
        $error = 'La sesión expiró. Vuelve a intentarlo.';
    } elseif ($usuario === '' || $clave === '') {
        $error = 'Ingresa usuario y contraseña.';
    } else {
        $admin = $CONFIG['admin'];
        if (hash_equals($admin['usuario'], $usuario) && password_verify($clave, $admin['hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin'] = $usuario;
            unset($_SESSION['intentos_login'], $_SESSION['csrf']);
            redirigir('pedidos.php');
        }
        $_SESSION['intentos_login']++;
        $error = 'Usuario o contraseña incorrectos.';
    }
}

$titulo = 'Acceso administrador';
$base   = '../';
require __DIR__ . '/../includes/header.php';
?>

<section class="login-fondo py-4">
    <div class="container">
        <div class="tarjeta overflow-hidden mx-auto" style="max-width: 900px;">
            <div class="row g-0">
                <div class="col-md-6 login-panel">
                    <img src="../img/logo.png" alt="Nutribox" class="logo-grande mb-3">
                    <h2 class="h4 fw-bold" style="color:#2f7045;">Snacks saludables,<br>una vida en equilibrio</h2>
                    <p class="texto-suave small">Vista interna para listar los pedidos y actualizar su estado.</p>
                    <img src="../img/snacks_login.png" alt="" class="snacks">
                </div>
                <div class="col-md-6 p-4 p-md-5">
                    <h1 class="h3 seccion-titulo text-center mb-1">Iniciar sesión</h1>
                    <p class="texto-suave text-center small mb-4">Accede al panel administrativo de Nutribox.</p>

                    <?php if ($error !== ''): ?>
                        <div class="alert alert-danger py-2"><?= e($error) ?></div>
                    <?php endif; ?>

                    <form method="post" action="login.php" novalidate>
                        <?= csrfCampo() ?>
                        <div class="mb-3">
                            <label for="usuario" class="form-label">Usuario</label>
                            <input type="text" name="usuario" id="usuario" class="form-control" autocomplete="username" value="<?= e($usuario) ?>" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label for="clave" class="form-label">Contraseña</label>
                            <div class="input-group">
                                <input type="password" name="clave" id="clave" class="form-control" autocomplete="current-password" required>
                                <button class="btn btn-outline-secondary" type="button" id="btnVer" aria-label="Mostrar contraseña">Mostrar</button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-nb w-100">Ingresar</button>
                    </form>
                    <p class="text-center small text-muted mt-4 mb-0"><a href="../index.php" class="text-decoration-none">← Volver al sitio</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('btnVer').addEventListener('click', function () {
    const c = document.getElementById('clave');
    c.type = c.type === 'password' ? 'text' : 'password';
    this.textContent = c.type === 'password' ? 'Mostrar' : 'Ocultar';
});
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>
