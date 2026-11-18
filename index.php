<?php
/** RF01 — Página de inicio: propuesta de valor, tres beneficios y botón Ver cajas. */
require __DIR__ . '/includes/init.php';
$titulo = 'Snacks saludables';
require __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <span class="etiqueta">ALIMENTACIÓN PRÁCTICA Y SALUDABLE</span>
                <h1 class="mt-3">Disfruta tus snacks, <span>cuida tu bienestar.</span></h1>
                <p class="lead">
                    En Nutribox encontrarás cuatro cajas de snacks saludables pensadas para acompañar tu día.
                    Revisa su aporte nutricional por porción, arma el balance de tu día y pide la caja que mejor
                    se adapte a ti. Te escribimos por WhatsApp para coordinar pago y entrega.
                </p>
                <a href="catalogo.php" class="btn btn-nb btn-lg">Ver cajas</a>
                <a href="orientacion.php" class="btn btn-nb-outline btn-lg ms-md-2 mt-2 mt-md-0">Balance diario</a>
            </div>
            <div class="col-md-6 text-center">
                <img src="img/snacks_index.png" alt="Selección de snacks saludables de Nutribox">
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <h2 class="seccion-titulo text-center">¿Por qué elegir Nutribox?</h2>
        <p class="text-center texto-suave mb-5">Una forma sencilla de conocer, comparar y elegir tus snacks.</p>
        <div class="row g-4">
            <div class="col-md-4">
                <article class="beneficio">
                    <div class="icono">🥗</div>
                    <h3>Cajas pensadas para ti</h3>
                    <p>Cuatro cajas con un objetivo claro: proteína, energía, bajo en azúcar o selección premium, con su composición y porción de referencia.</p>
                </article>
            </div>
            <div class="col-md-4">
                <article class="beneficio">
                    <div class="icono">📊</div>
                    <h3>Balance diario</h3>
                    <p>Suma proteínas, energía, hierro y azúcares de las porciones que elijas para un día y compáralas con tu meta de proteína.</p>
                </article>
            </div>
            <div class="col-md-4">
                <article class="beneficio">
                    <div class="icono">📦</div>
                    <h3>Pedidos sencillos</h3>
                    <p>Elige una caja, indica la cantidad (1 a 10) y registra tu pedido en menos de dos minutos. Sin cuenta ni tarjeta.</p>
                </article>
            </div>
        </div>
    </div>
</section>

<section class="pb-5">
    <div class="container">
        <div class="llamada">
            <h2 class="seccion-titulo">Encuentra tu próxima Nutribox</h2>
            <p class="texto-suave mb-4">Explora el catálogo y descubre las opciones disponibles para acompañar tu rutina.</p>
            <a href="catalogo.php" class="btn btn-nb">Explorar catálogo</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
