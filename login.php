<?php
session_start();

// Si ya está logueado, lo mandamos al panel
if (isset($_SESSION['admin_id'])) {
    header("Location: editar.php");
    exit;
}

require_once "config.php"; // $conn

$error = "";

// ===============================
//  Procesar login
// ===============================
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario  = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validar que no haya caracteres especiales en usuario y contraseña
    if (!preg_match("/^[a-zA-Z0-9]+$/", $usuario)) {
        $error = "El usuario solo puede contener letras y números, sin caracteres especiales.";
    } elseif (!preg_match("/^[a-zA-Z0-9]+$/", $password)) {
        $error = "La contraseña solo puede contener letras y números, sin caracteres especiales.";
    } elseif ($usuario === '' || $password === '') {
        $error = "Por favor, completa usuario y contraseña.";
    } else {
        $sql = "SELECT id, usuario, password, nombre FROM admin WHERE usuario = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $usuario);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows === 1) {
                $stmt->bind_result($id, $usuarioDB, $passwordDB, $nombreDB);
                $stmt->fetch();

                $loginOK = false;

                // 1) Caso contraseña hasheada
                if (password_verify($password, $passwordDB)) {
                    $loginOK = true;
                }
                // 2) Caso contraseña en texto plano (como en tu dump actual)
                elseif (hash_equals($passwordDB, $password)) {
                    $loginOK = true;
                }

                if ($loginOK) {
                    $_SESSION['admin_id'] = $id;
                    $_SESSION['admin_nombre'] = $nombreDB;

                    // Reinicia los intentos fallidos
                    $sqlUpdate = "UPDATE admin SET intentos_fallidos = 0 WHERE id = ?";
                    $stmtUpdate = $conn->prepare($sqlUpdate);
                    $stmtUpdate->bind_param("i", $id);
                    $stmtUpdate->execute();

                    header("Location: editar.php");
                    exit;
                } else {
                    // Incrementa el contador de intentos fallidos
                    $intentosFallidos++;
                    $bloqueoHasta = ($intentosFallidos >= 3) ? date("Y-m-d H:i:s", time() + 900) : null;

                    // Actualiza los intentos fallidos y el bloqueo
                    $sqlUpdate = "UPDATE admin SET intentos_fallidos = ?, bloqueo_hasta = ? WHERE id = ?";
                    $stmtUpdate = $conn->prepare($sqlUpdate);
                    $stmtUpdate->bind_param("isi", $intentosFallidos, $bloqueoHasta, $id);
                    $stmtUpdate->execute();

                    $error = "Usuario o contraseña incorrectos.";
                }
            } else {
                $error = "Usuario o contraseña incorrectos.";
            }

            $stmt->close();
        } else {
            $error = "Error al conectar con la base de datos.";
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PIDS – Login Admin</title>
  <link rel="stylesheet" href="css/admin.css">
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
        </nav>

        <!-- Botón accesibilidad -->
        <button class="pids-a11y-btn" id="btnA11y" aria-label="Accesibilidad">A+</button>
    </div>
</header>

  <!-- ===== SECCIÓN LOGIN ===== -->
  <section class="services-section" aria-label="Acceso administración">
    <div class="pids-wrap">
      <h2 class="section-title">Acceso administración</h2>

      <div class="pids-card login-card">
        <h3>Iniciar sesión</h3>

        <?php if ($error): ?>
          <p class="error-msg"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="post" class="login-form" autocomplete="off">
          <label for="usuario">Usuario</label>
          <input
            type="text"
            id="usuario"
            name="usuario"
            placeholder="Ingresa tu usuario"
            required
          >

          <label for="password">Contraseña</label>
          <input
            type="password"
            id="password"
            name="password"
            placeholder="Ingresa tu contraseña"
            required
          >

          <div class="buttons">
            <button type="submit" class="pids-btn">Ingresar</button>
            <a href="index.php" class="pids-btn pids-btn-outline">Volver</a>
          </div>
        </form>
      </div>
    </div>
  </section>

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
          <li id="contacto"><a href="">Contacto</a></li>
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

  <!-- JS accesibilidad admin -->
  <script src="js/admin.js"></script>
  <script src="js/index.js"></script>
</body>
</html>