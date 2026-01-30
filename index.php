<?php
session_start();
$adminActivo = isset($_SESSION['admin_id']);
?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="description" content="Plataforma educativa para adultos mayores, ofreciendo capacitación en habilidades digitales y seguridad en línea. Aprenda a usar la tecnología con confianza y autonomía.">
  <meta name="keywords" content="educación digital, adultos mayores, inclusión digital, habilidades tecnológicas, seguridad en línea, tutoriales, guías, trámites en línea, ofimática, tecnología para mayores">
  <meta name="author" content="PIDS - Plataforma de Inclusión Digital Senior, Thatkid, Dientebur">
  
  <title>PIDS – Inclusión Digital Senior</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="icon" href="img/logo.png">
</head>
<body>
  <!-- ===== Header ===== -->
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

  <!-- ===== Hero ===== -->
  <section class="pids-hero" role="region" aria-label="Portada">
    <img src="img/fondo1.jpg" alt="Personas mayores aprendiendo en un taller digital" class="pids-hero-bg">
    <div class="pids-wrap pids-hero-content">
      <h1>Aprende habilidades digitales de forma fácil y guiada</h1>
      <p>
        Empoderamos a personas mayores para conectarse con su familia, realizar trámites en línea
        y usar herramientas como Word y Excel con confianza.
      </p>
      <div class="pids-cta-row">
        <a class="pids-btn" href="guias.php">Explorar guías</a>
      </div>
    </div>
  </section>

  <!-- ===== SOBRE NOSOTROS / MISIÓN / IMPACTO ===== -->
  <section class="about-section" role="region" aria-label="Sobre nosotros">
    <div class="pids-wrap">
      <h2 class="about-title">Sobre Nosotros</h2>
      <p class="about-intro">
        En <strong>PIDS</strong> creemos que la tecnología debe estar al servicio de las personas, sin barreras de edad.
        Nos dedicamos a empoderar a adultos mayores mediante la educación digital:
        acompañamos, enseñamos y diseñamos experiencias que respetan los tiempos,
        intereses y ritmos de aprendizaje de cada persona. Nuestro enfoque combina
        <em>paciencia, claridad</em> y <em>herramientas prácticas</em> para que cada participante
        no solo aprenda, sino que recupere confianza y conexión con su entorno digital.
      </p>

      <div class="about-grid">
        <article class="about-card">
          <h3>Misión</h3>
          <p>
            Nuestra misión es clara: que todos los adultos mayores puedan usar la tecnología con confianza y autonomía.
            Buscamos reducir la brecha digital promoviendo habilidades concretas, desde comunicarse por videollamada
            hasta gestionar trámites en línea y fomentando la seguridad digital para proteger su privacidad y su patrimonio.
          </p>
        </article>

        <article class="about-card">
          <h3>Impacto Esperado</h3>
          <ul class="impact-list">
            <li>Mayor comunicación con familia y redes sociales.</li>
            <li>Reducción de la dependencia para trámites básicos en línea.</li>
            <li>Aumento de la confianza digital y sentido de autonomía.</li>
            <li>Menor riesgo de fraudes gracias al conocimiento de seguridad básica.</li>
          </ul>
        </article>
      </div>
    </div>
  </section>

  <!-- ===== NUESTROS SERVICIOS ===== -->
  <section class="services-section" role="region" aria-label="Nuestros servicios">
    <div class="pids-wrap">
      <h2 class="section-title">Nuestros Servicios</h2>

      <div class="pids-features">
        <article class="pids-card service-card">
          <img src="img/ofimatica.jpg" alt="Guías de aprendizaje" class="service-img">
          <h3>Ofimatica</h3>
          <p>Ofrecemos diversidad de guías para aprender y apoyar el desarrollo individual.</p>
        </article>

        <article class="pids-card service-card">
          <img src="img/seguridad.jpg" alt="Seguridad digital" class="service-img">
          <h3>Seguridad</h3>
          <p>Ayudamos a confiar más en las nuevas tecnologías y cómo afrontarlas.</p>
        </article>

        <article class="pids-card service-card">
          <img src="img/tramites.jpg" alt="Trámites en línea" class="service-img">
          <h3>Trámites</h3>
          <p>Puedes aprender a hacer trámites de manera autónoma y sin mucho esfuerzo.</p>
        </article>
      </div>
    </div>
  </section>

  <!-- ===== Footer ===== -->
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
          <li><a href="#contacto">Contacto</a></li>
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

  <!-- Script -->
<script src="js/index.js"></script>

</body>
</html>
