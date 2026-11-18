<?php
/**
 * RF08 — Balance diario · RF09 — Sugerencias según perfil.
 * Todo el cálculo ocurre en el navegador (assets/js/orientacion.js). Peso, talla y condición declarada
 * nunca se envían al servidor ni se guardan en cookies/localStorage (RNF06).
 */
require __DIR__ . '/includes/init.php';
require __DIR__ . '/includes/productos.php';

$productos = productosActivos();
$cajaInicial = (isset($_GET['caja']) && ctype_digit((string) $_GET['caja'])) ? (int) $_GET['caja'] : 0;

$titulo = 'Balance diario y orientación';
$scriptsExtra = ['assets/js/orientacion.js'];
require __DIR__ . '/includes/header.php';
?>

<section class="fondo-suave py-4">
    <div class="container">
        <h1 class="seccion-titulo mb-1">Balance diario y orientación</h1>
        <p class="texto-suave mb-0">
            Suma el aporte de las porciones que elijas para un día y, si quieres, recibe una sugerencia orientativa según tu objetivo.
            Nada de lo que ingreses aquí se guarda ni sale de tu navegador.
        </p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">

            <!-- ======== RF08: Balance diario ======== -->
            <div class="col-lg-7">
                <div class="tarjeta p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 fw-bold mb-0">Balance diario de snacks</h2>
                        <button type="button" class="btn btn-nb-outline btn-sm" id="btnReiniciar">Reiniciar</button>
                    </div>
                    <p class="aviso">Indica cuántas porciones de cada caja piensas consumir en el día (0 a 10). Una porción es la cantidad de referencia de cada caja, no toda la caja.</p>

                    <div class="table-responsive">
                        <table class="table align-middle mb-3" id="tablaPorciones">
                            <thead class="small text-muted">
                                <tr><th>Caja</th><th>Porción de referencia</th><th class="text-center">Porciones</th></tr>
                            </thead>
                            <tbody>
                            <?php foreach ($productos as $p): ?>
                                <tr>
                                    <td>
                                        <strong><?= e($p['nombre']) ?></strong><br>
                                        <span class="small text-muted"><?= e($p['objetivo']) ?></span>
                                        <?php if (!$p['datos_verificados']): ?><br><span class="badge-ficticio">Datos ficticios</span><?php endif; ?>
                                    </td>
                                    <td class="small"><?= e($p['porcion_referencia']) ?></td>
                                    <td class="text-center">
                                        <input type="number" class="form-control porcion-input mx-auto porcion" data-id="<?= (int) $p['id_producto'] ?>"
                                               min="0" max="10" step="1" inputmode="numeric" value="<?= $cajaInicial === (int) $p['id_producto'] ? 1 : 0 ?>"
                                               aria-label="Porciones de <?= e($p['nombre']) ?>">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6 col-md-3"><div class="balance-total"><div class="valor" id="totProteinas">0 g</div><div class="etiqueta">Proteínas</div></div></div>
                        <div class="col-6 col-md-3"><div class="balance-total"><div class="valor" id="totEnergia">0 kcal</div><div class="etiqueta">Energía</div></div></div>
                        <div class="col-6 col-md-3"><div class="balance-total"><div class="valor" id="totHierro">0 mg</div><div class="etiqueta">Hierro</div></div></div>
                        <div class="col-6 col-md-3"><div class="balance-total"><div class="valor" id="totAzucares">0 g</div><div class="etiqueta">Azúcares</div></div></div>
                    </div>
                    <div id="avisoParcial" class="alert alert-warning py-2 small d-none"></div>

                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label for="metaProteina" class="form-label">Meta de proteína del día (g) <span class="text-muted">opcional</span></label>
                            <input type="number" id="metaProteina" class="form-control" min="1" step="1" inputmode="numeric" placeholder="Ej. 60">
                            <div class="invalid-feedback">La meta debe ser un número mayor que cero.</div>
                        </div>
                        <div class="col-md-7">
                            <div class="d-flex justify-content-between small mb-1">
                                <span>Proteína frente a tu meta</span><strong id="metaTexto">Sin meta</strong>
                            </div>
                            <div class="progress" role="progressbar" aria-label="Proteína frente a la meta">
                                <div class="progress-bar" id="barraMeta" style="width: 0%">0 %</div>
                            </div>
                        </div>
                    </div>
                    <p class="aviso mt-3 mb-0">
                        El balance resume solo los snacks seleccionados, no toda tu alimentación. Un porcentaje mayor a 100 % no implica exceso clínico.
                        No se guarda historial.
                    </p>
                </div>
            </div>

            <!-- ======== RF09: Sugerencias ======== -->
            <div class="col-lg-5">
                <div class="tarjeta p-4 h-100">
                    <h2 class="h5 fw-bold mb-2">Sugerencia según tu objetivo</h2>
                    <p class="aviso">Orientación general con reglas fijas. No es un diagnóstico ni un tratamiento. Los datos opcionales se usan solo en tu navegador y desaparecen al recargar.</p>

                    <form id="formPerfil" novalidate autocomplete="off" onsubmit="return false;">
                        <div class="mb-3">
                            <label for="objetivo" class="form-label">¿Qué buscas en tus snacks?</label>
                            <select id="objetivo" class="form-select">
                                <option value="proteina">Más proteína</option>
                                <option value="energia">Energía para el día</option>
                                <option value="porciones">Control de porciones / bajo en azúcar</option>
                            </select>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label for="peso" class="form-label">Peso (kg) <span class="text-muted">opcional</span></label>
                                <input type="number" id="peso" class="form-control" min="1" step="0.1" inputmode="decimal" placeholder="Ej. 70">
                            </div>
                            <div class="col-6">
                                <label for="talla" class="form-label">Talla (m) <span class="text-muted">opcional</span></label>
                                <input type="number" id="talla" class="form-control" min="0.5" max="2.5" step="0.01" inputmode="decimal" placeholder="Ej. 1.70">
                            </div>
                        </div>
                        <div class="form-check"><input class="form-check-input" type="checkbox" id="adulto"><label class="form-check-label small" for="adulto">Confirmo que tengo 18 años o más</label></div>
                        <div class="form-check"><input class="form-check-input" type="checkbox" id="noGestante"><label class="form-check-label small" for="noGestante">Confirmo que no estoy en gestación</label></div>
                        <div class="form-check mt-2"><input class="form-check-input" type="checkbox" id="anemia"><label class="form-check-label small" for="anemia">Declaro voluntariamente que tengo un diagnóstico previo de anemia</label></div>

                        <button type="button" class="btn btn-nb w-100 mt-3" id="btnSugerir">Ver sugerencia</button>
                    </form>

                    <div id="resultadoSugerencia" class="mt-4 d-grid gap-3"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<script id="datosProductos" type="application/json"><?= productosParaJs($productos) ?></script>

<?php require __DIR__ . '/includes/footer.php'; ?>
