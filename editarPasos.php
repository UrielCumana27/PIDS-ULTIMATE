<?php
session_start();
$adminActivo = isset($_SESSION['admin_id']);
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require_once "config.php";
if (method_exists($conn, 'set_charset')) {
    $conn->set_charset("utf8mb4");
}

$mensaje = '';

$guiaId = isset($_GET['guia_id']) ? (int)$_GET['guia_id'] : 0;
if ($guiaId <= 0) {
    header("Location: editar.php");
    exit;
}

// Datos de la guía principal
$guia = null;
$stmt = $conn->prepare("SELECT id, titulo, descripcion_corta FROM guia WHERE id = ?");
$stmt->bind_param("i", $guiaId);
$stmt->execute();
$res = $stmt->get_result();
$guia = $res->fetch_assoc();
$stmt->close();

if (!$guia) {
    die("La guía indicada no existe.");
}

// Variables de formulario de paso
$modoPaso         = "crear";
$pasoIdForm       = 0;
$ordenPasoForm    = 1;
$tituloPasoForm   = "";
$textoPasoForm    = "";
$imagenActualForm = "";

// Editar paso específico
if (isset($_GET['paso_edit'])) {
    $pasoIdForm = (int)$_GET['paso_edit'];
    if ($pasoIdForm > 0) {
        $stmt = $conn->prepare("
            SELECT id, orden, titulo_paso, texto, imagen
            FROM guia_paso
            WHERE id = ? AND guia_id = ?
        ");
        $stmt->bind_param("ii", $pasoIdForm, $guiaId);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $modoPaso         = "actualizar";
            $ordenPasoForm    = (int)$row['orden'];
            $tituloPasoForm   = $row['titulo_paso'];
            $textoPasoForm    = $row['texto'];
            $imagenActualForm = $row['imagen'];
        }
        $stmt->close();
    }
}

// Procesar formulario de pasos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accionPaso       = $_POST['accion_paso'] ?? '';
    $guiaIdPost       = isset($_POST['guia_id']) ? (int)$_POST['guia_id'] : 0;
    $pasoIdPost       = isset($_POST['paso_id']) ? (int)$_POST['paso_id'] : 0;
    $ordenPasoPost    = isset($_POST['orden']) ? (int)$_POST['orden'] : 1;
    $tituloPasoPost   = trim($_POST['titulo_paso'] ?? '');
    $textoPasoPost    = trim($_POST['texto'] ?? '');
    $imagenActualPost = trim($_POST['imagen_actual'] ?? '');

    // Repoblar formulario
    $modoPaso         = ($accionPaso === 'actualizar' && $pasoIdPost > 0) ? 'actualizar' : 'crear';
    $pasoIdForm       = $pasoIdPost;
    $ordenPasoForm    = $ordenPasoPost;
    $tituloPasoForm   = $tituloPasoPost;
    $textoPasoForm    = $textoPasoPost;
    $imagenActualForm = $imagenActualPost;

    if ($guiaIdPost !== $guiaId) {
        $mensaje = "Error en la guía seleccionada.";
    } else {

        // Eliminar
        if ($accionPaso === 'eliminar' && $pasoIdPost > 0) {
            $stmt = $conn->prepare("DELETE FROM guia_paso WHERE id = ? AND guia_id = ?");
            $stmt->bind_param("ii", $pasoIdPost, $guiaId);
            if ($stmt->execute()) {
                $mensaje         = "Paso eliminado correctamente.";
                $modoPaso        = "crear";
                $pasoIdForm      = 0;
                $ordenPasoForm   = 1;
                $tituloPasoForm  = "";
                $textoPasoForm   = "";
                $imagenActualForm= "";
            } else {
                $mensaje = "Error al eliminar el paso.";
            }
            $stmt->close();

        } else {
            // Crear / actualizar
            if ($tituloPasoPost === '') {
                $mensaje = "Ingresa un título para el paso.";
            } else {
                $rutaImagenPaso = $imagenActualPost;

                // Subir nueva imagen (si hay)
                if (isset($_FILES['imagen_paso']) && $_FILES['imagen_paso']['error'] === UPLOAD_ERR_OK) {
                    $tmp           = $_FILES['imagen_paso']['tmp_name'];
                    $nombreArchivo = basename($_FILES['imagen_paso']['name']);
                    $destinoCarpeta= 'img/';
                    if (!is_dir($destinoCarpeta)) {
                        @mkdir($destinoCarpeta, 0777, true);
                    }
                    $rutaDestino = $destinoCarpeta . $nombreArchivo;
                    if (move_uploaded_file($tmp, $rutaDestino)) {
                        $rutaImagenPaso = $rutaDestino;
                    } else {
                        $mensaje = "Error al subir la imagen del paso.";
                    }
                }

                if ($mensaje === "" || $mensaje === "Ingresa un título para el paso.") {
                    // Crear
                    if ($accionPaso === 'crear') {
                        $stmt = $conn->prepare("
                            INSERT INTO guia_paso (guia_id, orden, titulo_paso, texto, imagen)
                            VALUES (?, ?, ?, ?, ?)
                        ");
                        $stmt->bind_param("iisss",
                            $guiaId,
                            $ordenPasoPost,
                            $tituloPasoPost,
                            $textoPasoPost,
                            $rutaImagenPaso
                        );
                        if ($stmt->execute()) {
                            $mensaje         = "Paso creado correctamente.";
                            $modoPaso        = "crear";
                            $pasoIdForm      = 0;
                            $ordenPasoForm   = $ordenPasoPost + 1;
                            $tituloPasoForm  = "";
                            $textoPasoForm   = "";
                            $imagenActualForm= "";
                        } else {
                            $mensaje = "Error al crear el paso.";
                        }
                        $stmt->close();

                    // Actualizar
                    } elseif ($accionPaso === 'actualizar' && $pasoIdPost > 0) {
                        $stmt = $conn->prepare("
                            UPDATE guia_paso
                            SET orden = ?, titulo_paso = ?, texto = ?, imagen = ?
                            WHERE id = ? AND guia_id = ?
                        ");
                        $stmt->bind_param("isssii",
                            $ordenPasoPost,
                            $tituloPasoPost,
                            $textoPasoPost,
                            $rutaImagenPaso,
                            $pasoIdPost,
                            $guiaId
                        );
                        if ($stmt->execute()) {
                            $mensaje = "Paso actualizado correctamente.";
                        } else {
                            $mensaje = "Error al actualizar el paso.";
                        }
                        $stmt->close();
                    }
                }
            }
        }
    }
}

// Listar pasos
$pasos = [];
$stmt = $conn->prepare("
    SELECT id, orden, titulo_paso, texto, imagen
    FROM guia_paso
    WHERE guia_id = ?
    ORDER BY orden ASC, id ASC
");
$stmt->bind_param("i", $guiaId);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $pasos[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PIDS – Pasos de la guía</title>
  <link rel="stylesheet" href="css/editar.css">
  <link rel="icon" type="image/x-icon" href="img/logo.png">
</head>
<body>

  <!-- HEADER -->
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

    <?php if (isset($_SESSION['admin_id'])): ?>
        <a href="logout.php" class="pids-admin-btn admin-mobile">
            Cerrar
        </a>
    <?php endif; ?>
</nav>

<!-- Botón accesibilidad -->
<button class="pids-a11y-btn" id="btnA11y" aria-label="Accesibilidad">A+</button>
    </div>
</header>

  <main class="panel-main">
    <div class="pids-wrap">

      <section class="panel-header">
        <h1>Pasos de la guía</h1>
        <p>
          Estás editando los pasos de: 
          <strong><?php echo htmlspecialchars($guia['titulo']); ?></strong>
        </p>
      </section>

      <?php if ($mensaje !== ""): ?>
        <div class="alert">
          <?php echo htmlspecialchars($mensaje); ?>
        </div>
      <?php endif; ?>

      <!-- Formulario de paso -->
      <section class="panel-card" aria-label="Formulario de paso">
        <h2><?php echo ($modoPaso === 'actualizar') ? "Editar paso" : "Agregar nuevo paso"; ?></h2>

        <form method="post" enctype="multipart/form-data" class="crud-form">
          <input type="hidden" name="guia_id" value="<?php echo (int)$guiaId; ?>">
          <input type="hidden" name="paso_id" value="<?php echo (int)$pasoIdForm; ?>">
          <input type="hidden" name="imagen_actual" value="<?php echo htmlspecialchars($imagenActualForm); ?>">

          <div class="form-row">
            <label for="orden">Orden</label>
            <input type="number" id="orden" name="orden" min="1"
                   value="<?php echo (int)$ordenPasoForm; ?>" required>
          </div>

          <div class="form-row">
            <label for="titulo_paso">Título del paso</label>
            <input type="text" id="titulo_paso" name="titulo_paso"
                   value="<?php echo htmlspecialchars($tituloPasoForm); ?>" required>
          </div>

          <div class="form-row">
            <label for="texto">Texto / instrucciones</label>
            <textarea id="texto" name="texto" rows="3" required><?php
                echo htmlspecialchars($textoPasoForm);
            ?></textarea>
          </div>

          <div class="form-row">
            <label for="imagen_paso">Imagen del paso (opcional)</label>
            <input type="file" id="imagen_paso" name="imagen_paso" accept="image/*">
            <?php if ($imagenActualForm): ?>
              <p class="hint-imagen">
                Imagen actual: <?php echo htmlspecialchars($imagenActualForm); ?>
              </p>
            <?php endif; ?>
          </div>

          <div class="form-actions">
            <button type="submit" name="accion_paso" value="crear" class="btn btn-primary">
              + Crear nuevo paso
            </button>

            <button type="submit" name="accion_paso" value="actualizar" class="btn btn-secondary">
              ^ Actualizar paso
            </button>

            <button type="submit" name="accion_paso" value="eliminar" class="btn btn-danger"
                    onclick="return confirm('¿Seguro que deseas eliminar este paso?');">
              - Eliminar paso
            </button>
          </div>
        </form>
      </section>

      <!-- Tabla de pasos -->
      <section class="panel-card" aria-label="Listado de pasos">
        <h2>Pasos de esta guía</h2>

        <div class="crud-table-wrapper">
          <table class="crud-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Orden</th>
                <th>Título</th>
                <th>Texto</th>
                <th>Imagen</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($pasos) === 0): ?>
                <tr class="empty-row">
                  <td colspan="6">
                    Aún no hay pasos para esta guía. Usa el formulario para crear el primero.
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($pasos as $index => $p): ?>
                  <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo (int)$p['orden']; ?></td>
                    <td><?php echo htmlspecialchars($p['titulo_paso']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($p['texto'])); ?></td>
                    <td>
                      <?php if (!empty($p['imagen'])): ?>
                        <span style="font-size:12px;"><?php echo htmlspecialchars($p['imagen']); ?></span>
                      <?php else: ?>
                        <span style="font-size:12px; color:#A8B2D1;">(Sin imagen)</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <a class="btn btn-small btn-secondary" 
                         href="editarPasos.php?guia_id=<?php echo (int)$guiaId; ?>&paso_edit=<?php echo (int)$p['id']; ?>">
                        Editar
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <p style="margin-top:18px;">
          <a href="editar.php" class="btn btn-secondary">← Volver al panel de guías</a>
        </p>
      </section>
    </div>
  </main>

  <footer class="pids-footer">
    <div class="pids-wrap pids-footer-copy">
      <p>&copy; <span id="copyYear"></span> PIDS — Inclusión digital para personas mayores.</p>
    </div>
  </footer>

  <script src="js/index.js"></script>
</body>
</html>

