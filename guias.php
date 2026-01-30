<?php
session_start();
$adminActivo = isset($_SESSION['admin_id']);

require_once "config.php"; // conexión $conn

// ===============================
//  Cargar todas las guías
// ===============================
$guias = [];

$sql = "
    SELECT 
        g.id,
        g.titulo,
        g.descripcion_corta,
        g.informacion,
        g.actividades,
        g.imagen,
        tg.slug       AS tipo_slug,
        tg.nombre     AS tipo_nombre
    FROM guia g
    LEFT JOIN tipo_guia tg ON g.tipo_id = tg.id
    WHERE g.activo = 1
    ORDER BY g.id DESC
";


$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $guias[] = $row;
    }
    $result->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PIDS – Guías</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/styleGuias.css">
    <link rel="icon" href="img/logo.png">
    <!-- SEO -->
    <meta name="description" content="Plataforma de inclusión digital para adultos mayores con guías educativas y de capacitación.">
    <meta name="keywords" content="digital, guías, seniors, educación, accesibilidad, inclusión">
</head>
<body>

<!-- ===== HEADER (igual al index) ===== -->
<header class="pids-header" role="banner" aria-label="Encabezado principal">
    <div class="pids-wrap header-flex">

        <!-- LOGO -->
        <a href="index.php" class="pids-brand" aria-label="Ir al inicio">
            <img src="img/logo2.png" alt="PIDS" class="pids-logo">
            <span class="pids-brand-text pids-brand-text-big">
                Plataforma de Inclusión Digital Senior
            </span>
        </a>

        <!-- Botón hamburguesa -->
        <button class="menu-toggle" id="menuToggle" aria-label="Abrir menú">
            ☰
        </button>

        <!-- NAV -->
        <nav class="pids-nav" id="pidsNav" aria-label="Navegación principal">
            <a href="index.php">Inicio</a>
            <a href="guias.php" class="active">Guías</a>
            <a href="#contacto">Contacto</a>

            <?php if ($adminActivo): ?>
                <a href="editar.php" class="pids-admin-btn admin-mobile">
                    Admin
                </a>
            <?php endif; ?>
        </nav>

        <!-- Botón accesibilidad -->
        <button class="pids-a11y-btn" id="btnA11y" aria-label="Accesibilidad">A+</button>
    </div>
</header>

<!-- ===== TÍTULO + BUSCADOR + FILTROS ===== -->
<section class="titulo-guias">
    <div class="pids-wrap">
        <h1>Guías disponibles</h1>
        <p>Aprende a tu ritmo con guías simples, claras y adaptadas para ti.</p>

        <div class="guias-filtros">
            <input
                type="search"
                id="buscadorGuias"
                class="guias-busqueda"
                placeholder="Buscar guía por nombre o descripción…">

            <div class="filtros-botones" aria-label="Filtrar por tipo de guía">
                <button class="filtro-btn active" data-filtro="todos">Todas</button>
                <button class="filtro-btn" data-filtro="ofimatica">Ofimática</button>
                <button class="filtro-btn" data-filtro="tramites">Trámites</button>
                <button class="filtro-btn" data-filtro="seguridad">Seguridad</button>
            </div>
        </div>
    </div>
</section>

<!-- ===== GRID DE GUÍAS ===== -->
<main class="pids-wrap guias-grid" aria-label="Listado de guías">

<?php if (count($guias) === 0): ?>

    <p class="guias-vacio" aria-live="assertive">Aún no hay guías registradas. Vuelve pronto.</p>

<?php else: ?>

    <?php foreach ($guias as $g): ?>
        <?php
        $tipoSlug   = $g['tipo_slug'] ?? 'otros';
        $tipoNombre = $g['tipo_nombre'] ?? 'Sin categoría';

        // Normalizar ruta de imagen
        $imgBD = trim($g['imagen'] ?? '');
        if ($imgBD === '') {
            $rutaImg = "";
        } elseif (str_starts_with($imgBD, "http") || str_starts_with($imgBD, "img/")) {
            $rutaImg = $imgBD;
        } else {
            $rutaImg = "img/" . $imgBD;
        }
        ?>

        <!-- Toda la tarjeta es clickeable -->
        <a href="guiasCompleta.php?id=<?= $g['id'] ?>"
           class="guia-card-link"
           aria-label="Abrir guía: <?= htmlspecialchars($g['titulo']) ?>">

            <article class="guia-card" data-tipo="<?= htmlspecialchars($tipoSlug) ?>">

                <div class="guia-img-wrapper">
                    <?php if ($rutaImg): ?>
                        <img src="<?= htmlspecialchars($rutaImg) ?>"
                             alt="Imagen de la guía"
                             class="guia-img" loading="lazy">
                    <?php else: ?>
                        <div class="guia-img guia-img-placeholder">
                            <span>Sin imagen disponible</span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="guia-contenido">
                    <span class="guia-tag"><?= htmlspecialchars($tipoNombre) ?></span>

                    <h2 class="guia-title">
                        <?= htmlspecialchars($g['titulo']) ?>
                    </h2>

                    <p class="guia-desc">
                        <?= nl2br(htmlspecialchars($g['descripcion_corta'])) ?>
                    </p>
                </div>

            </article>
        </a>

    <?php endforeach; ?>

<?php endif; ?>

</main>

<!-- ===== FOOTER ===== -->
<footer class="pids-footer" role="contentinfo" aria-label="Pie de página">
    <div class="pids-wrap pids-footer-grid">
      <div>
        <h3>Sobre el proyecto</h3>
        <p>Plataforma de capacitación digital para adultos mayores. Enfoque en habilidades básicas, autonomía y seguridad.</p>
      </div>
      <div>
        <h3>Enlaces</h3>
        <ul class="pids-list">
          <li><a href="guias.php">Guías</a></li>
          <li><a href="terminosCondiciones.php">Términos y privacidad</a></li>
          <li id="contacto"><a href="#contacto">Contacto</a></li>
        </ul>
      </div>
      <div>
        <h3 id="contacto">Contáctanos</h3>
        <ul class="pids-list">
          <li><a href="mailto:PIDS@gmail.com">PIDS@gmail.com</a></li>
          <li><a href="https://www.instagram.com/" target="_blank" rel="noopener">Instagram</a></li>
          <li><a href="https://www.facebook.com/" target="_blank" rel="noopener">Facebook</a></li>
        </ul>
      </div>
      <div>
        <h3>Administración</h3>
        <li><a href="login.php">Login Admin</a></li>
      </div>
    </div>
    <div class="pids-wrap pids-footer-copy">
      <p>&copy; <span id="copyYear"></span> PIDS — Inclusión digital para personas mayores.</p>
    </div>
</footer>


<!-- JS general (A+ y año dinámico) -->
<script src="js/index.js"></script>
<script>
// ===============================
// FUNCIÓN PARA QUITAR TILDES
// ===============================
function normalizar(str) {
    return str
        .normalize("NFD")               // separa acentos
        .replace(/[\u0300-\u036f]/g, '') // elimina acentos
        .toLowerCase()
        .trim();
}

document.addEventListener('DOMContentLoaded', () => {
    const buscador = document.getElementById('buscadorGuias');
    const tarjetasLink = document.querySelectorAll('.guia-card-link');
    const botonesFiltro = document.querySelectorAll('.filtro-btn');

    const filtrosPermitidos = ['todos', 'ofimatica', 'tramites', 'seguridad'];
    let filtroActual = 'todos';

    function sanitize(texto) {
        const div = document.createElement('div');
        div.textContent = texto;
        return div.textContent;
    }

    function aplicarFiltros() {
        const termino = normalizar(buscador?.value || '');
        let visibleAlMenosUna = false;

        tarjetasLink.forEach(link => {
            if (!link) return;

            const card = link.querySelector('.guia-card');
            if (!card) return;

            let tipo = normalizar(card.dataset.tipo || 'otros');
            if (!filtrosPermitidos.includes(tipo)) tipo = 'otros';

            const tituloOriginal = sanitize(card.querySelector('.guia-title')?.textContent || '');
            const descOriginal   = sanitize(card.querySelector('.guia-desc')?.textContent || '');

            const titulo = normalizar(tituloOriginal);
            const desc   = normalizar(descOriginal);

            let visible = true;

            if (filtroActual !== 'todos' && tipo !== filtroActual) {
                visible = false;
            }

            if (termino && !titulo.includes(termino) && !desc.includes(termino)) {
                visible = false;
            }

            link.style.display = visible ? '' : 'none';

            if (visible) visibleAlMenosUna = true;
        });

        const msg = document.querySelector('.guias-vacio-busqueda');
        if (msg) msg.style.display = visibleAlMenosUna ? 'none' : 'block';
    }

    // Buscador seguro: límite de caracteres
    buscador?.addEventListener('input', () => {
        if (buscador.value.length > 60) {
            buscador.value = buscador.value.slice(0, 60);
        }
        aplicarFiltros();
    });

    botonesFiltro.forEach(btn => {
        btn.addEventListener('click', () => {
            botonesFiltro.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            let filtro = normalizar(btn.dataset.filtro || 'todos');
            if (!filtrosPermitidos.includes(filtro)) filtro = 'todos';

            filtroActual = filtro;
            aplicarFiltros();
        });
    });

    aplicarFiltros();
});
</script>

<!-- MENSAJE SIN RESULTADOS -->
<p class="guias-vacio-busqueda" style="display:none; text-align:center; margin:2rem 0;">
    No se encontraron guías que coincidan con la búsqueda o el filtro seleccionado.
</p>

</body>
</html>