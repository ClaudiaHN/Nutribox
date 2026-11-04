<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#225d3d">
    <title>Nutribox | Tienda de snacks saludables</title>
    <link rel="stylesheet" href="{{ asset('css/nutribox.css') }}">
</head>
<body class="page-store">
    <header class="topbar">
        <a class="brand" href="{{ route('inicio') }}" aria-label="Nutribox, inicio">
            <img src="{{ asset('img/logo.png') }}" alt="Nutribox">
        </a>
        <nav class="topnav" aria-label="Navegación principal">
            <button type="button" class="navlink soon">Catálogo</button>
            <button type="button" class="navlink soon">Beneficios</button>
            <button type="button" class="navlink soon">Orientación</button>
        </nav>
        <div class="header-actions">
            <button type="button" class="round-action soon" aria-label="Favoritos">♡</button>
            <button type="button" class="round-action soon" aria-label="Carrito">♧</button>
            @if(auth()->check() && auth()->user()->rol === 'cliente')
                <span class="hello-user">Hola, {{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST">@csrf
                    <button class="login-link" type="submit">Cerrar sesión</button>
                </form>
            @else
                <a class="login-link" href="{{ route('login') }}">Iniciar sesión <span aria-hidden="true">↗</span></a>
            @endif
        </div>
    </header>

    <main>
        @if(session('status'))
            <div class="flash">{{ session('status') }}</div>
        @endif

        <section class="hero" aria-label="Bienvenida a Nutribox">
            <div class="hero-copy">
                <span class="eyebrow">✦ SNACKS SALUDABLES • BOXES CON ESTILO</span>
                <h1>Tu pausa favorita, <em>más natural.</em></h1>
                <p>Descubre cajas de snacks pensadas para acompañarte en tus días de estudio, trabajo y descanso.</p>
                <button type="button" class="primary-button soon">Explorar nuestros boxes <span aria-hidden="true">→</span></button>
                <div class="hero-caption"><span class="leaf-dot">✓</span> Ingredientes variados &nbsp; <span class="leaf-dot">✓</span> Opciones para tu rutina</div>
            </div>
            <div class="hero-visual">
                <div class="hero-circle"></div>
                <img src="{{ asset('img/snacks_login.png') }}" alt="Tazón de frutos secos" class="hero-snack">
                <span class="floating-note">¡Una pausa llena de sabor!</span>
            </div>
        </section>

        <div class="content-shell">
            <aside class="sidebar" aria-label="Categorías">
                <span class="side-label">EXPLORA</span>
                <button type="button" class="side-button selected soon">☘ &nbsp; Todos los boxes</button>
                <button type="button" class="side-button soon">♧ &nbsp; Proteína</button>
                <button type="button" class="side-button soon">⚡ &nbsp; Energía</button>
                <button type="button" class="side-button soon">✧ &nbsp; Equilibrio</button>
                <button type="button" class="side-button soon">★ &nbsp; Premium</button>
                <div class="side-banner">
                    <strong>Una forma más rica de cuidarte</strong>
                    <p>Descubre Nutribox a tu propio ritmo.</p>
                </div>
            </aside>

            <section class="products" aria-labelledby="products-heading">
                <div class="section-heading">
                    <div><span class="eyebrow">NUESTRA COLECCIÓN</span><h2 id="products-heading">Encuentra tu Nutribox</h2></div>
                    <span class="count-pill">{{ $productos->count() }} boxes</span>
                </div>
                <p class="section-intro">Explora nuestras cajas disponibles. Sus datos y precios se consultan directamente desde nuestra base de datos.</p>

                <div class="product-grid">
                    @forelse($productos as $producto)
                        @php
                            // Priorizar la imagen principal; si no hay, usar imagen ilustrativa.
                            $imagen = $producto->imagenes->firstWhere('es_principal', true)
                                ?? $producto->imagenes->first();
                            $rutaImagen = $imagen
                                ? asset($imagen->ruta)
                                : asset($producto->slug === 'caja-premium'
                                    ? 'img/nutribox_hero.png'
                                    : 'img/snacks_login.png');
                            $estilo = ['mint', 'peach', 'cream', 'dark'][$loop->index % 4];
                        @endphp
                        <article class="product-card">
                            <div class="product-photo {{ $estilo }}">
                                <span class="product-badge">{{ mb_strtoupper($producto->objetivo) }}</span>
                                <img src="{{ $rutaImagen }}"
                                     alt="{{ $imagen?->texto_alternativo ?: 'Imagen de ' . $producto->nombre }}"
                                     loading="lazy">
                            </div>
                            <div class="product-info">
                                <div class="product-meta">
                                    {{ $producto->categoria?->nombre ?? 'Nutribox' }}
                                    <span aria-hidden="true">♡</span>
                                </div>
                                <h3>{{ $producto->nombre }}</h3>
                                <p>{{ $producto->descripcion }}</p>
                                <div class="product-bottom">
                                    <strong>S/ {{ number_format((float) $producto->precio, 2) }}</strong>
                                    <button type="button" class="small-button soon">
                                        {{ $producto->disponible ? 'Ver box +' : 'No disponible' }}
                                    </button>
                                </div>
                            </div>
                        </article>
                    @empty
                        <p>Por el momento no hay cajas disponibles en el catálogo.</p>
                    @endforelse
                </div>
                <p class="demo-note">Las imágenes ilustrativas se reemplazarán por fotos de cada caja cuando se registren en la tabla producto_imagenes.</p>
            </section>

            <aside class="quick-panel" aria-label="Accesos rápidos">
                <div class="quick-top"><span>EN NUTRIBOX</span><strong>Tu espacio saludable</strong></div>
                <button type="button" class="quick-card q-green soon"><span class="quick-icon">◴</span><span><strong>Balance diario</strong><small>Consulta tus aportes</small></span> ↗</button>
                <button type="button" class="quick-card q-blue soon"><span class="quick-icon">♡</span><span><strong>Recomendaciones</strong><small>Conoce tus opciones</small></span> ↗</button>
                <button type="button" class="quick-card q-yellow soon"><span class="quick-icon">✦</span><span><strong>Destacados</strong><small>Explora novedades</small></span> ↗</button>
                <button type="button" class="quick-card q-peach soon"><span class="quick-icon">♧</span><span><strong>Mis pedidos</strong><small>Próximamente</small></span> ↗</button>
                <div class="quick-message"><strong>Bienvenido a una vida con más sabor 🌿</strong><p>Elige lo que te guste. Lo demás lo iremos construyendo.</p></div>
            </aside>
        </div>
    </main>

    <footer class="footer"><span>© {{ date('Y') }} Nutribox. Proyecto académico.</span><a href="{{ route('admin.login') }}">Acceso administrativo</a></footer>
    <div class="toast" id="soon-toast" role="status" aria-live="polite">Esta sección estará disponible próximamente.</div>
    <script>
        const toast = document.querySelector('#soon-toast');
        let closeToast;
        document.querySelectorAll('.soon').forEach(button => button.addEventListener('click', () => {
            toast.classList.add('visible');
            clearTimeout(closeToast);
            closeToast = setTimeout(() => toast.classList.remove('visible'), 2400);
        }));
    </script>
</body>
</html>
