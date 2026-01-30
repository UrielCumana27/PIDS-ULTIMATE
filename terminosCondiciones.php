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
    <img src="img/terminos.jpg" alt="Personas mayores aprendiendo en un taller digital" class="pids-hero-bg">
    <div class="pids-wrap pids-hero-content">
      <h1>Terminos y condiciones</h1>
      <p>
        Bienvenido a PIDS - Plataforma de Inclusión Digital Senior. 
        Al acceder o utilizar nuestro sitio web, aceptas los siguientes términos y condiciones. 
        Si no estás de acuerdo con estos términos, por favor, no utilices el sitio web.
      </p>
      <div class="pids-cta-row">
        <a class="pids-btn" href="index.php">Ir a inicio</a>
      </div>
    </div>
  </section>

  <section class="about-section" role="region" aria-label="Sobre nosotros">
    <div class="pids-wrap">
      <h2 class="about-title">Terminos y condiciones</h2>
      <p class="about-intro">
        En <strong>PIDS</strong> creemos que la tecnología debe estar al servicio de las personas, sin barreras de edad.
        Nos dedicamos a empoderar a adultos mayores mediante la educación digital:
        acompañamos, enseñamos y diseñamos experiencias que respetan los tiempos,
        intereses y ritmos de aprendizaje de cada persona. Nuestro enfoque combina
        <em>paciencia, claridad</em> y <em>herramientas prácticas</em> para que cada participante
        no solo aprenda, sino que recupere confianza y conexión con su entorno digital.
      </p>

      <section>
        <div>
          <h2 class="about-title">1.Aceptación de los terminos</h2>
          <p class="about-intro"> Al acceder o utilizar este sitio web, aceptas cumplir con estos términos y condiciones,
             así como con todas las leyes y normativas aplicables.
             Si no aceptas estos términos, no uses nuestro sitio.
          </p>
        </div>
        <div>
          <h2 class="about-title">2. Uso del sitio web</h2>
          <p class="about-intro">El sitio web se proporciona para fines legales y lícitos. 
            No podrás usar el sitio web de manera que infrinja las leyes locales, nacionales o internacionales. 
            Estás de acuerdo en no utilizar el sitio web para:
          </p>
            <ul class="impact-list">
              <li><strong>Violar derechos de propiedad intelectual.</strong></li>
              <li><strong>Realizar actividades fraudulentas o engañosas.</strong></li>
              <li><strong>Transmitir contenido ilegal, ofensivo o difamatorio.</strong></li>
            </ul>
        </div>
        <div>
          <h2 class="about-title">3. Propiedad intelectual</h2>
          <p class="about-intro">Todo el contenido del sitio web, incluyendo texto, imágenes, logotipos, gráficos, software y cualquier otro material, es propiedad de PIDS - Plataforma de Inclusión Digital Senior o está licenciado para su uso.
             Queda prohibido reproducir, distribuir, modificar o crear trabajos derivados de este contenido sin el permiso expreso y por escrito de PIDS - Plataforma de Inclusión Digital Senior.</p>
        </div>
        <div>
          <h2 class="about-title">4. Politica de privacidad y proteccion de datos</h2>
          <p class="about-intro">Tu privacidad es importante para nosotros. Al utilizar nuestros servicios, aceptas el tratamiento de tus datos personales de acuerdo con nuestra Política de Privacidad.
             Esta política describe cómo recopilamos, usamos, almacenamos y protegemos tu información personal.
             En cumplimiento con la Ley 19.496 de Protección al Consumidor de Chile y otras leyes locales e internacionales, garantizamos que tus datos serán tratados de manera segura y solo se utilizarán para fines específicos relacionados con el uso de nuestra plataforma.
          </p>
          <ul class="impact-list">
            <li><strong>Consentimiento Expreso: </strong> Al utilizar el sitio web, nos das tu consentimiento expreso para la recolección, almacenamiento y procesamiento de tus datos personales de acuerdo con los fines indicados en nuestra Política de Privacidad.</li>
            <li><strong>Cookies:</strong>Este sitio web utiliza cookies para mejorar la experiencia del usuario. Al acceder a nuestro sitio, aceptas el uso de cookies según lo establecido en nuestra <strong>Política de Cookies.</strong>  Puedes gestionar las cookies a través de la configuración de tu navegador.</li>
            <li><strong>Derechos de usuario:</strong>Tienes el derecho a acceder, corregir, eliminar o solicitar la limitación del uso de tus datos personales. Si deseas ejercer alguno de estos derechos, puedes ponerte en contacto con nosotros a través de PIDS@gmail.com.</li>
          </ul>
        </div>
        <div>
          <h2 class="about-title">5. Politica de cookies</h2>
          <p class="about-intro">Este sitio web utiliza cookies para mejorar tu experiencia de navegación, proporcionar funcionalidades personalizadas y analizar el uso del sitio.
             Las cookies son pequeños archivos que se almacenan en tu dispositivo.
             Utilizamos cookies propias y de terceros para:
          </p>
          <ul class="impact-list">
            <li><strong>Recordar tus preferencias de usuario.</strong></li>
            <li><strong>Analizar el trafico del sitio web.</strong></li>
            <li><strong>Mejorar la funcionalidad del sitio.</strong></li>
          </ul>
          <p class="about-intro">Al acceder a este sitio web, aceptas el uso de cookies conforme a nuestra Política de Privacidad y Cookies.
             Si prefieres desactivar las cookies, puedes hacerlo a través de la configuración de tu navegador, pero ten en cuenta que esto podría afectar la funcionalidad de ciertas partes del sitio.
          </p>
        </div>
        <div>
          <h2 class="about-title">6. Accesibilidad web</h2>
          <p class="about-intro">Nos comprometemos a ofrecer un sitio web accesible para todas las personas, incluidas aquellas con discapacidades.
             El sitio web está diseñado de acuerdo con las <strong>Pautas de Accesibilidad para el Contenido Web (WCAG 2.1).</strong> 
             Si encuentras dificultades para acceder a algún contenido o funcionalidad, te invitamos a contactarnos para que podamos asistir en mejorar tu experiencia de usuario.
          </p>
        </div>
        <div>
          <h2 class="about-title">7. Modificaciones de los terminos</h2>
          <p class="about-intro">Nos reservamos el derecho de modificar estos términos y condiciones en cualquier momento. 
            Las modificaciones entrarán en vigor en el momento de su publicación en esta página. Es tu responsabilidad revisar regularmente estos términos para estar al tanto de cualquier cambio.
            Si continúas utilizando el sitio después de la modificación de los términos, se considerará que aceptas dichos cambios.
          </p>
        </div>
        <div>
          <h2 class="about-title">8. Exencion de responsabilidad</h2>
          <p class="about-intro">El contenido de este sitio web se proporciona "tal cual" y "según disponibilidad".
             No garantizamos que el sitio esté libre de errores o interrupciones, ni que los resultados obtenidos a través de su uso sean precisos o confiables.
             No seremos responsables de ningún daño directo, indirecto, incidental o consecuente que surja del uso o la imposibilidad de usar el sitio web.
             Esto incluye, pero no se limita a, problemas técnicos, fallos de hardware o software, o interrupciones en el acceso al sitio web.
          </p>
        </div>
        <div>
          <h2 class="about-title">9. Ley aplicable</h2>
          <p class="about-intro">Estos términos y condiciones se regirán e interpretarán de acuerdo con las leyes de <strong>Chile.</strong>
           Cualquier disputa relacionada con estos términos se resolverá en los tribunales competentes de <strong>Chile.</strong>
           En caso de que no se pueda llegar a una resolución por medios amistosos, ambas partes acuerdan someterse a la jurisdicción de los tribunales chilenos.
          </p>
        </div>
        <div>
          <h2 class="about-title">10. Responsabilidad de usuario</h2>
          <p class="about-intro">Como usuaario, eres responsable de:</p>
          <ul class="impact-list">
            <li><strong>Proporcionar información veraz y actualizada durante el uso del sitio.</strong></li>
            <li><strong>No infringir los derechos de propiedad intelectual de la plataforma ni de terceros.</strong></li>
            <li><strong>No realizar actividades ilegales o que afecten negativamente a la operatividad del sitio.</strong></li>
          </ul>
        </div>
        <div>
          <h2 class="about-title">11. Contacto</h2>
          <p class="about-intro">Si tienes alguna pregunta sobre estos terminos y condiciones, puedes contactarnos a traves de:</p>
          <ul class="impact-list">
            <li><strong>Email: PIDS@gmail.com</strong></li>
        </div>
      </section>

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
