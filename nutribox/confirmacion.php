<?php
/**
 * RF05 — Confirmación del pedido recién creado.
 * Solo se muestra el pedido guardado en la sesión (no se acepta un número por URL).
 * Recargar la página no crea un pedido duplicado: aquí solo se lee.
 */
require __DIR__ . '/includes/init.php';

$idPedido = isset($_SESSION['pedido_confirmado']) ? (int) $_SESSION['pedido_confirmado'] : 0;
if ($idPedido <= 0) {
    redirigir('catalogo.php');
}

$st = db()->prepare(
    'SELECT p.id_pedido, p.cantidad, p.precio_unitario, p.total, p.estado, p.fecha_registro, p.observaciones,
            pr.nombre AS caja, c.nombres, c.telefono, c.distrito, c.direccion
       FROM pedido p
       JOIN producto pr ON pr.id_producto = p.id_producto
       JOIN cliente  c  ON c.id_cliente  = p.id_cliente
      WHERE p.id_pedido = :id'
);
$st->execute(['id' => $idPedido]);
$pedido = $st->fetch();
if (!$pedido) {
    unset($_SESSION['pedido_confirmado']);
    redirigir('catalogo.php');
}

$codigo   = codigoPedido((int) $pedido['id_pedido']);
$whatsapp = preg_replace('/\D+/', '', (string) ($CONFIG['whatsapp'] ?? ''));
$mensajeWa = rawurlencode("Hola Nutribox, registré el pedido {$codigo} ({$pedido['cantidad']} × {$pedido['caja']}, total " . soles($pedido['total']) . "). Quiero coordinar pago y entrega.");

$titulo = 'Pedido ' . $codigo;
require __DIR__ . '/includes/header.php';
?>

<section class="py-5">
    <div class="container" style="max-width: 760px;">
        <div class="tarjeta p-4 p-md-5 text-center">
            <div class="confirmacion-icono">✓</div>
            <h1 class="seccion-titulo h3">¡Pedido registrado!</h1>
            <p class="texto-suave">Tu número de pedido es</p>
            <div class="numero-pedido mb-3"><?= e($codigo) ?></div>

            <div class="resumen-pedido text-start mx-auto" style="max-width: 460px;">
                <div class="d-flex justify-content-between"><span>Caja</span><strong><?= e($pedido['caja']) ?></strong></div>
                <div class="d-flex justify-content-between mt-2"><span>Cantidad</span><strong><?= (int) $pedido['cantidad'] ?></strong></div>
                <div class="d-flex justify-content-between mt-2"><span>Precio unitario</span><strong><?= soles($pedido['precio_unitario']) ?></strong></div>
                <hr>
                <div class="d-flex justify-content-between align-items-center"><span>Total</span><span class="total"><?= soles($pedido['total']) ?></span></div>
                <div class="d-flex justify-content-between mt-2 small text-muted">
                    <span>Estado</span><span class="estado estado-<?= e($pedido['estado']) ?>"><?= e($pedido['estado']) ?></span>
                </div>
                <div class="small text-muted mt-2">
                    Entrega en <?= e($pedido['distrito']) ?> · <?= e($pedido['direccion']) ?> · Contacto <?= e($pedido['telefono']) ?>
                </div>
            </div>

            <div class="alert alert-success mt-4 mb-3">
                <strong>Te escribiremos por WhatsApp para coordinar pago y entrega.</strong><br>
                <span class="small">Pago con Yape o Plin al confirmar. Guarda tu número de pedido <?= e($codigo) ?>.</span>
            </div>

            <div class="d-flex flex-wrap justify-content-center gap-2">
                <?php if ($whatsapp !== ''): ?>
                    <a class="btn btn-nb" target="_blank" rel="noopener" href="https://wa.me/<?= e($whatsapp) ?>?text=<?= $mensajeWa ?>">Escribir por WhatsApp</a>
                <?php endif; ?>
                <a class="btn btn-nb-outline" href="catalogo.php">Volver al catálogo</a>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
