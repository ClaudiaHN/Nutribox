<?php
/**
 * Encabezado común. Variables opcionales antes de incluir:
 *   $titulo  (string)  — título de la pestaña
 *   $base    (string)  — prefijo relativo a la raíz ('' en raíz, '../' dentro de /admin)
 */
$titulo = $titulo ?? 'Nutribox';
$base   = $base ?? '';
$esAdmin = !empty($_SESSION['admin']);
$actual = basename($_SERVER['SCRIPT_NAME']);
function navActivo(string $archivo, string $actual): string
{
    return $archivo === $actual ? ' active' : '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo) ?> | Nutribox</title>
    <link rel="icon" href="<?= $base ?>img/logo.png" type="image/png">
    <link rel="stylesheet" href="<?= $base ?>assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= $base ?>assets/css/estilos.css">
</head>
<body>

<header class="nb-header">
    <nav class="navbar navbar-expand-md">
        <div class="container">
            <a class="navbar-brand" href="<?= $base ?>index.php">
                <img src="<?= $base ?>img/logo.png" alt="Nutribox">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nbNav"
                    aria-controls="nbNav" aria-expanded="false" aria-label="Abrir menú">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="nbNav">
                <ul class="navbar-nav ms-auto align-items-md-center gap-md-2">
                    <li class="nav-item"><a class="nav-link<?= navActivo('index.php', $actual) ?>" href="<?= $base ?>index.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link<?= navActivo('catalogo.php', $actual) ?>" href="<?= $base ?>catalogo.php">Catálogo</a></li>
                    <li class="nav-item"><a class="nav-link<?= navActivo('orientacion.php', $actual) ?>" href="<?= $base ?>orientacion.php">Balance y orientación</a></li>
                    <li class="nav-item"><a class="nav-link<?= navActivo('pedido.php', $actual) ?>" href="<?= $base ?>pedido.php">Pedir</a></li>
                    <?php if ($esAdmin): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= $base ?>admin/pedidos.php">Pedidos</a></li>
                        <li class="nav-item"><a class="btn btn-nb-outline btn-sm ms-md-2" href="<?= $base ?>admin/logout.php">Cerrar sesión</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="btn btn-nb btn-sm ms-md-2" href="<?= $base ?>admin/login.php">Administración</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main class="nb-main">
