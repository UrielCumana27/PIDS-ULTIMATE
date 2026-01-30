<?php
session_start();
$adminActivo = isset($_SESSION['admin_id']);

require_once "config.php";
if (method_exists($conn, 'set_charset')) {
    $conn->set_charset("utf8mb4");
}

// Función para normalizar la ruta de imágenes (portada y pasos)
function normalizarRuta($img) {
    $img = trim($img ?? '');
    if ($img === '') return '';
    if (str_starts_with($img, 'http') || str_starts_with($img, 'img/')) {
        return $img;
    }
    return 'img/' . $img;
}

$guia        = null;
$pasos       = [];
$actividades = [];
$error       = "";

/* ===============================
   Cargar guía + pasos + actividades
================================= */
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Guía principal
    $sqlGuia = "
        SELECT 
            g.id,
            g.titulo,
            g.descripcion_corta,
            g.informacion,
            g.actividades,
            g.imagen,
            g.activo,
            tg.nombre AS tipo_nombre,
            tg.slug   AS tipo_slug
        FROM guia g
        LEFT JOIN tipo_guia tg ON g.tipo_id = tg.id
        WHERE g.id = ? AND g.activo = 1
        LIMIT 1
    ";
    if ($stmt = $conn->prepare($sqlGuia)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $guia = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }

    if ($guia) {
        // Pasos
        $sqlPasos = "
            SELECT id, orden, titulo_paso, texto, imagen
            FROM guia_paso
            WHERE guia_id = ?
            ORDER BY orden ASC, id ASC
        ";
        if ($stmt2 = $conn->prepare($sqlPasos)) {
            $stmt2->bind_param("i", $id);
            $stmt2->execute();
            $res = $stmt2->get_result();
            while ($row = $res->fetch_assoc()) {
                $pasos[] = $row;
            }
            $stmt2->close();
        }

        // Actividades interactivas (ahora con feedback)
        $sqlAct = "
            SELECT id, pregunta, opcionA, opcionB, opcionC, opcionD, correcta, feedback
            FROM guia_actividad
            WHERE guia_id = ?
            ORDER BY orden ASC, id ASC
        ";
        if ($stmt3 = $conn->prepare($sqlAct)) {
            $stmt3->bind_param("i", $id);
            $stmt3->execute();
            $resAct = $stmt3->get_result();
            while ($rowAct = $resAct->fetch_assoc()) {
                $actividades[] = $rowAct;
            }
            $stmt3->close();
        }

    } else {
        $error = "La guía no existe o está desactivada.";
    }
} else {
    $error = "No se especificó ninguna guía.";
}

$rutaImgPrincipal = normalizarRuta($guia['imagen'] ?? '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PIDS – Guía completa</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/guiasCompleta.css">
    <link rel="icon" href="img/logo.png">

    <!-- SEO Mejorado -->
    <meta name="description" content="Plataforma de inclusión digital para adultos mayores con guías educativas y de capacitación.">
    <meta name="keywords" content="digital, guías, seniors, educación, accesibilidad, inclusión">
</head>
<body>

<!-- ===== HEADER ===== -->
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

<?php if ($error): ?>
<main class="pids-main">
    <div class="pids-wrap">
        <section class="guide-container">
            <h2><?= htmlspecialchars($error) ?></h2>
            <p><a href="guias.php" class="volver">← Volver al listado de guías</a></p>
        </section>
    </div>
</main>
<?php else: ?>
<!-- ===== HERO ===== -->
<section class="guide-hero">
    <?php if ($rutaImgPrincipal): ?>
        <div class="guide-hero-bg" style="background-image:url('<?= htmlspecialchars($rutaImgPrincipal) ?>');"></div>
    <?php endif; ?>
    <div class="guide-hero-overlay"></div>
    <div class="guide-hero-content pids-wrap">
        <div class="guide-hero-text">
            <h1><?= htmlspecialchars($guia['titulo']) ?></h1>
            <p class="guide-hero-desc"><?= nl2br(htmlspecialchars($guia['descripcion_corta'])) ?></p>
            <a href="#contenido-guia" class="guide-hero-btn">Comenzar guía</a>
        </div>
    </div>
</section>

<!-- ===== CONTENIDO PRINCIPAL ===== -->
<main class="pids-main" id="contenido-guia">
    <div class="pids-wrap">
        <section class="guide-container">

            <!-- Qué aprenderás -->
            <section class="guide-section">
                <h2 class="section-title">¿Qué aprenderás?</h2>
                <p><?= nl2br(htmlspecialchars($guia['informacion'] ?? "")) ?></p>
            </section>

            <!-- ============================
                 1) PASO A PASO (PRIMERO)
            ============================ -->
            <?php if (count($pasos) > 0): ?>
            <section class="guide-section">
                <h2 class="section-title">Paso a paso</h2>

                <ol class="pasos-list">
                    <?php foreach ($pasos as $i => $p): 
                        $numPaso = $i + 1;
                        $imgPaso = normalizarRuta($p['imagen'] ?? '');
                    ?>
                    <li class="paso-item">
                        <div class="paso-info">
                            <span class="paso-num"><?= $numPaso ?></span>
                            <h3><?= htmlspecialchars($p['titulo_paso']) ?></h3>
                            <p><?= nl2br(htmlspecialchars($p['texto'] ?? "")) ?></p>
                        </div>

                        <?php if ($imgPaso): ?>
                        <div class="paso-img">
                            <img src="<?= htmlspecialchars($imgPaso) ?>" alt="Paso <?= $numPaso ?>" loading="lazy">
                        </div>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                </ol>
            </section>
            <?php endif; ?>


            <!-- ============================
                 2) ACTIVIDADES (SEGUNDO)
            ============================ -->
            <?php if (count($actividades) > 0): ?>
            <section class="guide-section">
                <h2 class="section-title">Actividades interactivas</h2>

                <form id="quizForm">
                    <ol class="actividades-list">
                        <?php foreach ($actividades as $i => $a): ?>
                        <li class="actividad-item">
                            <h3><?= htmlspecialchars($a['pregunta']) ?></h3>

                            <ul class="actividad-opciones">
                                <li>
                                    <label>
                                        <input type="radio" name="respuesta_<?= $i ?>" value="A">
                                        A) <?= htmlspecialchars($a['opcionA']) ?>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input type="radio" name="respuesta_<?= $i ?>" value="B">
                                        B) <?= htmlspecialchars($a['opcionB']) ?>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input type="radio" name="respuesta_<?= $i ?>" value="C">
                                        C) <?= htmlspecialchars($a['opcionC']) ?>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input type="radio" name="respuesta_<?= $i ?>" value="D">
                                        D) <?= htmlspecialchars($a['opcionD']) ?>
                                    </label>
                                </li>
                            </ul>

                            <p class="actividad-feedback" id="feedback_<?= $i ?>"></p>

                            <?php if (!empty($a['feedback'])): ?>
                                <p class="actividad-explicacion" id="explicacion_<?= $i ?>" style="display:none;">
                                    <?= nl2br(htmlspecialchars($a['feedback'])) ?>
                                </p>
                            <?php endif; ?>

                            <input type="hidden" id="correcta_<?= $i ?>" value="<?= htmlspecialchars($a['correcta']) ?>">
                        </li>
                        <?php endforeach; ?>
                    </ol>

                    <button type="button" id="btnVerResultado">Ver resultado</button>
                    <button type="button" id="btnReintentar" style="display:none;">Reintentar</button>

                    <p id="resultadoFinal" style="font-weight:bold; margin-top:10px;"></p>
                </form>
            </section>
            <?php endif; ?>


            <p><a href="guias.php" class="volver">← Volver a guías</a></p>

        </section>
    </div>
</main>

<?php endif; ?>

<!-- ===== FOOTER ===== -->
<footer class="pids-footer" role="contentinfo">
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
            <h3>Contáctanos</h3>
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

<script src="js/index.js"></script>
<script src="js/guias.js"></script>
</body>
</html>
