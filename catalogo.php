<?php
/** RF02 — Catálogo: cajas activas con imagen, precio, objetivo, porción, composición y nutrientes. */
require __DIR__ . '/includes/init.php';
require __DIR__ . '/includes/productos.php';

$productos = productosActivos();
$titulo = 'Catálogo de cajas';
require __DIR__ . '/includes/header.php';
?>

<section class="fondo-suave py-4">
    <div class="container">
        <h1 class="seccion-titulo mb-1">Nuestras cajas</h1>
        <p class="texto-suave mb-0">
            Cuatro cajas con su composición, porción de referencia y aporte nutricional por porción.
            Pulsa <strong>Pedir</strong> para elegir cantidad o <strong>Ver balance</strong> para sumar su aporte a tu día.
        </p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <?php if (!$productos): ?>
            <div class="alert alert-warning">No hay cajas activas por el momento.</div>
        <?php endif; ?>

        <div class="row g-4">
            <?php foreach ($productos as $p): ?>
                <div class="col-12 col-sm-6 col-lg-3">
                    <article class="caja-card" id="caja-<?= (int) $p['id_producto'] ?>">
                        <img src="<?= e($p['imagen'] ?: 'img/snacks_login.png') ?>" alt="<?= e($p['nombre']) ?>">
                        <div class="cuerpo">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <h2 class="h5 mb-0 fw-bold"><?= e($p['nombre']) ?></h2>
                                <span class="badge-objetivo"><?= e($p['objetivo']) ?></span>
                            </div>
                            <div class="precio"><?= soles($p['precio']) ?></div>
                            <p class="texto-suave mb-1"><?= e($p['descripcion']) ?></p>
                            <p class="small mb-1"><strong>Porción de referencia:</strong> <?= e($p['porcion_referencia']) ?></p>

                            <dl class="nutrientes mb-1">
                                <dt>Proteínas</dt><dd><?= nutriente($p['proteinas_g'], 'g') ?></dd>
                                <dt>Energía</dt><dd><?= nutriente($p['energia_kcal'], 'kcal') ?></dd>
                                <dt>Hierro</dt><dd><?= nutriente($p['hierro_mg'], 'mg') ?></dd>
                                <dt>Azúcares</dt><dd><?= nutriente($p['azucares_g'], 'g') ?></dd>
                            </dl>

                            <div>
                                <?php if ($p['datos_verificados']): ?>
                                    <span class="badge-verificado" title="<?= e($p['fuente_nutricional']) ?>">Datos documentados</span>
                                <?php else: ?>
                                    <span class="badge-ficticio" title="<?= e($p['fuente_nutricional']) ?>">Datos ficticios de demostración</span>
                                <?php endif; ?>
                            </div>
                            <p class="small text-muted mb-2"><strong>Alérgenos:</strong> <?= e($p['alergenos']) ?></p>

                            <div class="mt-auto d-grid gap-2">
                                <a class="btn btn-nb" href="pedido.php?caja=<?= (int) $p['id_producto'] ?>">Pedir</a>
                                <a class="btn btn-nb-outline" href="orientacion.php?caja=<?= (int) $p['id_producto'] ?>">Ver balance</a>
                            </div>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>

        <p class="aviso mt-4 mb-0">
            Los valores marcados como <em>ficticios de demostración</em> no provienen de una ficha de composición verificada y no habilitan
            recomendaciones de salud. El pago (Yape/Plin) y la entrega se coordinan por WhatsApp después del pedido.
        </p>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
