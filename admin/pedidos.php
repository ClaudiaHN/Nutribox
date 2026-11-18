<?php
/**
 * RF07 — Gestionar pedidos: lista del más reciente al más antiguo, filtro solo por estado,
 * cambio de estado según RN05 (Pendiente → Confirmado → Entregado; Pendiente/Confirmado → Cancelado).
 */
require __DIR__ . '/../includes/init.php';
require __DIR__ . '/../includes/auth_admin.php';

$mensaje = '';
$tipo    = 'success';

/* ---------- Cambio de estado (POST + CSRF) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id    = (string) ($_POST['id_pedido'] ?? '');
    $nuevo = (string) ($_POST['estado'] ?? '');
    $filtroVolver = (string) ($_POST['filtro'] ?? '');

    if (!csrfValido()) {
        $mensaje = 'Formulario no válido. Vuelve a intentarlo.';
        $tipo = 'danger';
    } elseif (!ctype_digit($id) || !in_array($nuevo, ESTADOS, true)) {
        $mensaje = 'Datos no válidos.';
        $tipo = 'danger';
    } else {
        $pdo = db();
        $pdo->beginTransaction();
        $st = $pdo->prepare('SELECT estado FROM pedido WHERE id_pedido = :id FOR UPDATE');
        $st->execute(['id' => (int) $id]);
        $actual = $st->fetchColumn();

        if ($actual === false) {
            $pdo->rollBack();
            $mensaje = 'El pedido no existe.';
            $tipo = 'danger';
        } elseif (!transicionValida((string) $actual, $nuevo)) {
            $pdo->rollBack();
            $mensaje = 'Transición no permitida: un pedido ' . $actual . ' no puede pasar a ' . $nuevo . ' (RN05).';
            $tipo = 'danger';
        } else {
            $pdo->prepare('UPDATE pedido SET estado = :e WHERE id_pedido = :id')->execute(['e' => $nuevo, 'id' => (int) $id]);
            $pdo->commit();
            $_SESSION['flash'] = 'Pedido ' . codigoPedido((int) $id) . ' actualizado a ' . $nuevo . '.';
            redirigir('pedidos.php' . (in_array($filtroVolver, ESTADOS, true) ? '?estado=' . urlencode($filtroVolver) : ''));
        }
    }
}

if (!empty($_SESSION['flash'])) {
    $mensaje = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

/* ---------- Listado con filtro por estado ---------- */
$filtro = (string) ($_GET['estado'] ?? '');
if (!in_array($filtro, ESTADOS, true)) {
    $filtro = '';
}

$sql = 'SELECT p.id_pedido, p.fecha_registro, p.cantidad, p.total, p.estado, p.observaciones,
               c.nombres, c.telefono, c.distrito, pr.nombre AS caja
          FROM pedido p
          JOIN cliente  c  ON c.id_cliente  = p.id_cliente
          JOIN producto pr ON pr.id_producto = p.id_producto';
$params = [];
if ($filtro !== '') {
    $sql .= ' WHERE p.estado = :estado';
    $params['estado'] = $filtro;
}
$sql .= ' ORDER BY p.id_pedido DESC';
$st = db()->prepare($sql);
$st->execute($params);
$pedidos = $st->fetchAll();

$conteos = ['' => 0];
foreach (ESTADOS as $e) { $conteos[$e] = 0; }
foreach (db()->query('SELECT estado, COUNT(*) n FROM pedido GROUP BY estado') as $r) {
    $conteos[$r['estado']] = (int) $r['n'];
    $conteos[''] += (int) $r['n'];
}

$titulo = 'Pedidos';
$base   = '../';
require __DIR__ . '/../includes/header.php';
?>

<section class="fondo-suave py-4">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h1 class="seccion-titulo mb-1">Pedidos</h1>
                <p class="texto-suave mb-0">Sesión: <strong><?= e($_SESSION['admin']) ?></strong> · Del más reciente al más antiguo.</p>
            </div>
            <div class="filtro-estado d-flex flex-wrap gap-2">
                <a class="btn btn-sm <?= $filtro === '' ? 'btn-nb' : 'btn-nb-outline' ?>" href="pedidos.php">Todos (<?= $conteos[''] ?>)</a>
                <?php foreach (ESTADOS as $e): ?>
                    <a class="btn btn-sm <?= $filtro === $e ? 'btn-nb' : 'btn-nb-outline' ?>" href="pedidos.php?estado=<?= e($e) ?>"><?= e($e) ?> (<?= $conteos[$e] ?>)</a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<section class="py-4">
    <div class="container">
        <?php if ($mensaje !== ''): ?>
            <div class="alert alert-<?= e($tipo) ?> py-2"><?= e($mensaje) ?></div>
        <?php endif; ?>

        <?php if (!$pedidos): ?>
            <div class="alert alert-light border">No hay pedidos<?= $filtro !== '' ? ' en estado ' . e($filtro) : ' registrados' ?>.</div>
        <?php else: ?>
        <div class="table-responsive tarjeta">
            <table class="table table-hover tabla-pedidos mb-0">
                <thead>
                    <tr>
                        <th>N.º</th><th>Fecha</th><th>Cliente</th><th>Teléfono</th><th>Caja</th>
                        <th class="text-center">Cant.</th><th class="text-end">Total</th><th>Estado</th><th>Cambiar estado</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($pedidos as $p):
                    $siguientes = TRANSICIONES[$p['estado']] ?? []; ?>
                    <tr>
                        <td><strong><?= e(codigoPedido((int) $p['id_pedido'])) ?></strong></td>
                        <td class="small"><?= e(date('d/m/Y H:i', strtotime($p['fecha_registro']))) ?></td>
                        <td>
                            <?= e($p['nombres']) ?><br>
                            <span class="small text-muted"><?= e($p['distrito']) ?></span>
                            <?php if ($p['observaciones']): ?><br><span class="small text-muted" title="<?= e($p['observaciones']) ?>">Obs.: <?= e(mb_strimwidth($p['observaciones'], 0, 40, '…')) ?></span><?php endif; ?>
                        </td>
                        <td><?= e($p['telefono']) ?></td>
                        <td><?= e($p['caja']) ?></td>
                        <td class="text-center"><?= (int) $p['cantidad'] ?></td>
                        <td class="text-end fw-bold"><?= soles($p['total']) ?></td>
                        <td><span class="estado estado-<?= e($p['estado']) ?>"><?= e($p['estado']) ?></span></td>
                        <td>
                            <?php if ($siguientes): ?>
                                <form method="post" action="pedidos.php" class="d-flex gap-1">
                                    <?= csrfCampo() ?>
                                    <input type="hidden" name="id_pedido" value="<?= (int) $p['id_pedido'] ?>">
                                    <input type="hidden" name="filtro" value="<?= e($filtro) ?>">
                                    <select name="estado" class="form-select form-select-sm" aria-label="Nuevo estado">
                                        <?php foreach ($siguientes as $s): ?><option value="<?= e($s) ?>"><?= e($s) ?></option><?php endforeach; ?>
                                    </select>
                                    <button class="btn btn-nb btn-sm" type="submit">Guardar</button>
                                </form>
                            <?php else: ?>
                                <span class="small text-muted">Estado final</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <p class="aviso mt-3 mb-0">RN05: Pendiente → Confirmado → Entregado; Pendiente o Confirmado → Cancelado. Entregado y Cancelado no cambian más.</p>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
