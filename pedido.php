<?php
/**
 * RF03 — Seleccionar caja y cantidad · RF04 — Registrar pedido.
 * Flujo: GET pedido.php?caja=<id> (caja precargada) o pedido.php (lista desplegable, flujo alternativo B).
 * POST: valida, crea/reutiliza cliente por correo (RN06), guarda pedido Pendiente (RN04) y redirige a confirmacion.php.
 */
require __DIR__ . '/includes/init.php';
require __DIR__ . '/includes/productos.php';

$productos = productosActivos();
$porId     = array_column($productos, null, 'id_producto');

$distritos = [
    'Ate', 'Barranco', 'Breña', 'Carabayllo', 'Cercado de Lima', 'Chorrillos', 'Comas', 'El Agustino', 'Independencia',
    'Jesús María', 'La Molina', 'La Victoria', 'Lince', 'Los Olivos', 'Magdalena del Mar', 'Miraflores', 'Pueblo Libre',
    'Puente Piedra', 'Rímac', 'San Borja', 'San Isidro', 'San Juan de Lurigancho', 'San Juan de Miraflores', 'San Luis',
    'San Martín de Porres', 'San Miguel', 'Santa Anita', 'Santiago de Surco', 'Surquillo', 'Villa El Salvador',
    'Villa María del Triunfo', 'Callao', 'Bellavista', 'Carmen de la Legua', 'La Perla', 'La Punta', 'Ventanilla', 'Mi Perú',
];

/* ---------- Valores del formulario (se conservan ante error) ---------- */
$v = [
    'caja'          => '',
    'cantidad'      => '1',
    'nombres'       => '',
    'telefono'      => '',
    'correo'        => '',
    'distrito'      => '',
    'direccion'     => '',
    'observaciones' => '',
    'acepta_datos'  => '',
];
$errores = [];
$errorGeneral = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $cajaGet = $_GET['caja'] ?? '';
    if (ctype_digit((string) $cajaGet) && isset($porId[(int) $cajaGet])) {
        $v['caja'] = (string) (int) $cajaGet;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($v as $k => $_) {
        $v[$k] = is_string($_POST[$k] ?? null) ? trim($_POST[$k]) : '';
    }

    if (!csrfValido()) {
        $errorGeneral = 'La sesión expiró o el formulario no es válido. Vuelve a intentarlo.';
    }

    /* Caja (RN08) */
    if (!ctype_digit($v['caja']) || !isset($porId[(int) $v['caja']])) {
        $errores['caja'] = 'Elige una de las cajas disponibles.';
    }
    /* Cantidad (RN01) */
    if (!cantidadValida($v['cantidad'])) {
        $errores['cantidad'] = 'La cantidad debe ser un número entero entre 1 y 10.';
    }
    /* Datos de contacto (RNF02) */
    if ($v['nombres'] === '' || mb_strlen($v['nombres']) < 3 || mb_strlen($v['nombres']) > 100) {
        $errores['nombres'] = 'Ingresa tus nombres y apellidos (3 a 100 caracteres).';
    }
    if (!telefonoValido($v['telefono'])) {
        $errores['telefono'] = 'El teléfono debe tener 9 dígitos y empezar con 9.';
    }
    if (!correoValido($v['correo'])) {
        $errores['correo'] = 'Ingresa un correo electrónico válido.';
    }
    if ($v['distrito'] === '' || mb_strlen($v['distrito']) > 60) {
        $errores['distrito'] = 'Indica el distrito de entrega (Lima o Callao).';
    }
    if ($v['direccion'] === '' || mb_strlen($v['direccion']) < 5 || mb_strlen($v['direccion']) > 200) {
        $errores['direccion'] = 'Indica la dirección o una referencia de entrega (5 a 200 caracteres).';
    }
    if (mb_strlen($v['observaciones']) > 300) {
        $errores['observaciones'] = 'Las observaciones no pueden superar 300 caracteres.';
    }
    if ($v['acepta_datos'] !== '1') {
        $errores['acepta_datos'] = 'Debes aceptar el uso de tus datos de contacto para registrar el pedido.';
    }

    if (!$errores && $errorGeneral === '') {
        $producto = $porId[(int) $v['caja']];
        $cantidad = (int) $v['cantidad'];
        $precio   = (float) $producto['precio'];
        $total    = round($precio * $cantidad, 2);           // RN02
        $correo   = mb_strtolower($v['correo']);

        $pdo = db();
        try {
            $pdo->beginTransaction();

            /* RN06: reutilizar cliente por correo y actualizar contacto */
            $st = $pdo->prepare('SELECT id_cliente FROM cliente WHERE correo = :correo FOR UPDATE');
            $st->execute(['correo' => $correo]);
            $idCliente = $st->fetchColumn();

            if ($idCliente) {
                $pdo->prepare('UPDATE cliente SET nombres = :n, telefono = :t, distrito = :d, direccion = :dir WHERE id_cliente = :id')
                    ->execute(['n' => $v['nombres'], 't' => $v['telefono'], 'd' => $v['distrito'], 'dir' => $v['direccion'], 'id' => $idCliente]);
            } else {
                $pdo->prepare('INSERT INTO cliente (nombres, telefono, correo, distrito, direccion) VALUES (:n, :t, :c, :d, :dir)')
                    ->execute(['n' => $v['nombres'], 't' => $v['telefono'], 'c' => $correo, 'd' => $v['distrito'], 'dir' => $v['direccion']]);
                $idCliente = (int) $pdo->lastInsertId();
            }

            /* RN03, RN04: precio copiado y estado Pendiente */
            $pdo->prepare(
                'INSERT INTO pedido (id_cliente, id_producto, cantidad, precio_unitario, total, observaciones, acepta_datos, estado)
                 VALUES (:cli, :pro, :cant, :pu, :tot, :obs, 1, \'Pendiente\')'
            )->execute([
                'cli'  => $idCliente,
                'pro'  => (int) $producto['id_producto'],
                'cant' => $cantidad,
                'pu'   => number_format($precio, 2, '.', ''),
                'tot'  => number_format($total, 2, '.', ''),
                'obs'  => $v['observaciones'] !== '' ? $v['observaciones'] : null,
            ]);
            $idPedido = (int) $pdo->lastInsertId();
            $pdo->commit();

            /* RF05: solo el pedido recién creado se puede ver, vía sesión (no por número en URL) */
            $_SESSION['pedido_confirmado'] = $idPedido;
            unset($_SESSION['csrf']);                         // token de un solo uso: evita reenvíos duplicados
            redirigir('confirmacion.php');
        } catch (Throwable $ex) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $errorGeneral = 'No se pudo registrar el pedido. Inténtalo nuevamente.';
        }
    }
}

$cajaSel  = ($v['caja'] !== '' && isset($porId[(int) $v['caja']])) ? $porId[(int) $v['caja']] : null;
$titulo   = 'Registrar pedido';
$scriptsExtra = ['assets/js/pedido.js'];
require __DIR__ . '/includes/header.php';

function err(array $errores, string $campo): string
{
    return isset($errores[$campo]) ? '<div class="invalid-feedback d-block">' . e($errores[$campo]) . '</div>' : '';
}
function inv(array $errores, string $campo): string
{
    return isset($errores[$campo]) ? ' is-invalid' : '';
}
?>

<section class="fondo-suave py-4">
    <div class="container">
        <h1 class="seccion-titulo mb-1">Registrar pedido</h1>
        <p class="texto-suave mb-0">Un pedido contiene una sola caja (1 a 10 unidades). Te escribiremos por WhatsApp para coordinar pago y entrega.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <?php if ($errorGeneral !== ''): ?>
            <div class="alert alert-danger"><?= e($errorGeneral) ?></div>
        <?php elseif ($errores): ?>
            <div class="alert alert-danger">Revisa los campos marcados. No se guardó ningún pedido.</div>
        <?php endif; ?>

        <form method="post" action="pedido.php" novalidate class="row g-4" id="formPedido">
            <?= csrfCampo() ?>

            <div class="col-lg-7">
                <div class="tarjeta p-4">
                    <h2 class="h5 fw-bold mb-3">1. Tu caja</h2>
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="caja" class="form-label">Caja</label>
                            <select name="caja" id="caja" class="form-select<?= inv($errores, 'caja') ?>" required>
                                <option value="">— Elige una caja —</option>
                                <?php foreach ($productos as $p): ?>
                                    <option value="<?= (int) $p['id_producto'] ?>" data-precio="<?= e($p['precio']) ?>"
                                        <?= $v['caja'] === (string) $p['id_producto'] ? 'selected' : '' ?>>
                                        <?= e($p['nombre']) ?> · <?= e($p['objetivo']) ?> · <?= soles($p['precio']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?= err($errores, 'caja') ?>
                        </div>
                        <div class="col-md-4">
                            <label for="cantidad" class="form-label">Cantidad (1 a 10)</label>
                            <input type="number" name="cantidad" id="cantidad" class="form-control<?= inv($errores, 'cantidad') ?>"
                                   min="1" max="10" step="1" inputmode="numeric" value="<?= e($v['cantidad']) ?>" required>
                            <?= err($errores, 'cantidad') ?>
                        </div>
                    </div>

                    <h2 class="h5 fw-bold mt-4 mb-3">2. Datos de contacto</h2>
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="nombres" class="form-label">Nombres y apellidos</label>
                            <input type="text" name="nombres" id="nombres" class="form-control<?= inv($errores, 'nombres') ?>"
                                   maxlength="100" autocomplete="name" value="<?= e($v['nombres']) ?>" required>
                            <?= err($errores, 'nombres') ?>
                        </div>
                        <div class="col-md-5">
                            <label for="telefono" class="form-label">Teléfono (9 dígitos)</label>
                            <input type="tel" name="telefono" id="telefono" class="form-control<?= inv($errores, 'telefono') ?>"
                                   maxlength="9" inputmode="numeric" pattern="9[0-9]{8}" placeholder="9XXXXXXXX"
                                   autocomplete="tel" value="<?= e($v['telefono']) ?>" required>
                            <?= err($errores, 'telefono') ?>
                        </div>
                        <div class="col-md-7">
                            <label for="correo" class="form-label">Correo electrónico</label>
                            <input type="email" name="correo" id="correo" class="form-control<?= inv($errores, 'correo') ?>"
                                   maxlength="120" autocomplete="email" placeholder="ejemplo@correo.com" value="<?= e($v['correo']) ?>" required>
                            <?= err($errores, 'correo') ?>
                        </div>
                        <div class="col-md-5">
                            <label for="distrito" class="form-label">Distrito (Lima o Callao)</label>
                            <input type="text" name="distrito" id="distrito" list="listaDistritos" class="form-control<?= inv($errores, 'distrito') ?>"
                                   maxlength="60" value="<?= e($v['distrito']) ?>" required>
                            <datalist id="listaDistritos">
                                <?php foreach ($distritos as $d): ?><option value="<?= e($d) ?>"></option><?php endforeach; ?>
                            </datalist>
                            <?= err($errores, 'distrito') ?>
                        </div>
                        <div class="col-md-7">
                            <label for="direccion" class="form-label">Dirección o referencia de entrega</label>
                            <input type="text" name="direccion" id="direccion" class="form-control<?= inv($errores, 'direccion') ?>"
                                   maxlength="200" autocomplete="street-address" value="<?= e($v['direccion']) ?>" required>
                            <?= err($errores, 'direccion') ?>
                        </div>
                        <div class="col-12">
                            <label for="observaciones" class="form-label">Observaciones <span class="text-muted">(opcional: snacks extra, horario, etc.)</span></label>
                            <textarea name="observaciones" id="observaciones" class="form-control<?= inv($errores, 'observaciones') ?>" rows="2"
                                      maxlength="300"><?= e($v['observaciones']) ?></textarea>
                            <?= err($errores, 'observaciones') ?>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input<?= inv($errores, 'acepta_datos') ?>" type="checkbox" name="acepta_datos" id="acepta_datos" value="1"
                                    <?= $v['acepta_datos'] === '1' ? 'checked' : '' ?> required>
                                <label class="form-check-label" for="acepta_datos">
                                    Acepto que Nutribox use mis datos de contacto únicamente para atender este pedido
                                    (Ley N.º 29733, Protección de Datos Personales).
                                </label>
                            </div>
                            <?= err($errores, 'acepta_datos') ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="tarjeta p-4 position-sticky" style="top: 96px;">
                    <h2 class="h5 fw-bold mb-3">Resumen</h2>
                    <div class="resumen-pedido">
                        <div class="d-flex justify-content-between"><span>Caja</span><strong id="resCaja"><?= $cajaSel ? e($cajaSel['nombre']) : '—' ?></strong></div>
                        <div class="d-flex justify-content-between mt-2"><span>Precio unitario</span><strong id="resPrecio"><?= $cajaSel ? soles($cajaSel['precio']) : '—' ?></strong></div>
                        <div class="d-flex justify-content-between mt-2"><span>Cantidad</span><strong id="resCantidad"><?= e($v['cantidad']) ?></strong></div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center"><span>Total</span><span class="total" id="resTotal">
                            <?= ($cajaSel && cantidadValida($v['cantidad'])) ? soles((float) $cajaSel['precio'] * (int) $v['cantidad']) : '—' ?>
                        </span></div>
                    </div>
                    <button type="submit" class="btn btn-nb w-100 mt-3">Registrar pedido</button>
                    <p class="aviso mt-3 mb-0">Registrar el pedido no implica un cobro. El pago se coordina por WhatsApp con Yape o Plin.</p>
                </div>
            </div>
        </form>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
