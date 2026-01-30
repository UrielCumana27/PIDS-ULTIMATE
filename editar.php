<?php
session_start();

// ===============================
//  BLOQUEAR ACCESO SIN SESIÓN
// ===============================
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require_once "config.php";

// Forzar charset seguro
if (method_exists($conn, 'set_charset')) {
    $conn->set_charset("utf8mb4");
}

// ============================================================
// FUNCIONES SEGURAS
// ============================================================
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function limpiar($str, $max = 255) {
    $str = trim($str);
    return mb_substr($str, 0, $max);
}

function int_seguro($v) {
    return filter_var($v, FILTER_VALIDATE_INT) ?: 0;
}

// ============================================================
// SUBIDA SEGURA DE IMÁGENES
// ============================================================
function subir_imagen_segura($campo, $directorio = "img/") {

    if (!isset($_FILES[$campo]) || $_FILES[$campo]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    // Detectar MIME real
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($_FILES[$campo]['tmp_name']);

    $permitidos = [
        "image/jpeg" => "jpg",
        "image/png"  => "png",
        "image/webp" => "webp"
    ];

    if (!isset($permitidos[$mime])) {
        return false; // MIME NO PERMITIDO
    }

    // Crear nombre aleatorio seguro
    $ext = $permitidos[$mime];
    $nombreSeguro = "guia_" . bin2hex(random_bytes(8)) . "." . $ext;

    if (!is_dir($directorio)) {
        mkdir($directorio, 0777, true);
    }

    $ruta = $directorio . $nombreSeguro;

    if (move_uploaded_file($_FILES[$campo]['tmp_name'], $ruta)) {
        return $ruta;
    }

    return false;
}

// ============================================================
// VARIABLES BASE
// ============================================================
$modo            = "crear";
$edit_id         = 0;
$mensaje         = "";
$tituloForm      = "";
$descripcionForm = "";
$informacionForm = "";
$tipoIdForm      = 1;
$activoForm      = 1;
$imagenForm      = "";
$adminId         = $_SESSION['admin_id'] ?? null;

// ============================================================
// CARGAR TIPOS
// ============================================================
$tipos = [];
$stmtT = $conn->prepare("SELECT id, nombre FROM tipo_guia ORDER BY id ASC");
$stmtT->execute();
$resTipos = $stmtT->get_result();

while ($row = $resTipos->fetch_assoc()) {
    $tipos[] = [
        "id"     => (int)$row['id'],
        "nombre" => h($row['nombre'])
    ];
}
$stmtT->close();

// ============================================================
// CARGAR MODO EDICIÓN
// ============================================================
if (isset($_GET['edit'])) {
    $edit_id = int_seguro($_GET['edit']);

    $stmt = $conn->prepare("
        SELECT id, tipo_id, titulo, descripcion_corta, informacion, imagen, activo
        FROM guia
        WHERE id = ?
        LIMIT 1
    ");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($fila = $res->fetch_assoc()) {
        $modo            = "actualizar";
        $edit_id         = (int)$fila['id'];
        $tipoIdForm      = (int)$fila['tipo_id'];
        $tituloForm      = limpiar($fila['titulo'], 255);
        $descripcionForm = limpiar($fila['descripcion_corta'], 500);
        $informacionForm = limpiar($fila['informacion'], 2000);
        $activoForm      = (int)$fila['activo'];
        $imagenForm      = limpiar($fila['imagen'], 255);
    }

    $stmt->close();
}

// ============================================================
// CRUD SEGURO
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $accion = $_POST['accion'] ?? '';
    $id     = int_seguro($_POST['id'] ?? 0);

    // -------------------------------------
    // ELIMINAR
    // -------------------------------------
    if ($accion === 'eliminar' && $id > 0) {

        $stmt = $conn->prepare("DELETE FROM guia WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $mensaje = "Guía eliminada correctamente.";
        header("Location: editar.php?msg=" . urlencode($mensaje));
        exit;
    }

    // -------------------------------------
    // CREAR / ACTUALIZAR
    // -------------------------------------
    $titulo       = limpiar($_POST['nombre']);
    $descripcion  = limpiar($_POST['descripcion'], 500);
    $informacion  = limpiar($_POST['informacion'], 2000);
    $tipoId       = int_seguro($_POST['tipo_id']);
    $activo       = isset($_POST['activo']) ? 1 : 0;
    $imagenActual = limpiar($_POST['imagen_actual']);

    if ($titulo === '' || $descripcion === '' || $informacion === '') {
        $mensaje = "Por favor completa todos los campos obligatorios.";
    } else {

        $nuevaImagen = subir_imagen_segura("imagen");

        if ($nuevaImagen === false) {
            $mensaje = "Formato de imagen no permitido.";
        } elseif ($nuevaImagen !== null) {
            $rutaImagen = $nuevaImagen;
        } else {
            $rutaImagen = $imagenActual;
        }

        if ($mensaje === "") {

            // CREAR
            if ($accion === 'crear') {

                $stmt = $conn->prepare("
                    INSERT INTO guia (tipo_id, admin_id, titulo, descripcion_corta, informacion, imagen, activo)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->bind_param("iissssi",
                    $tipoId, $adminId, $titulo, $descripcion, $informacion, $rutaImagen, $activo
                );
                $stmt->execute();

                $mensaje = "Guía creada correctamente.";
            }

            // ACTUALIZAR
            if ($accion === 'actualizar' && $id > 0) {

                $stmt = $conn->prepare("
                    UPDATE guia
                    SET tipo_id=?, titulo=?, descripcion_corta=?, informacion=?, imagen=?, activo=?, fecha_actualizacion=NOW()
                    WHERE id=?
                ");
                $stmt->bind_param("issssii",
                    $tipoId, $titulo, $descripcion, $informacion,
                    $rutaImagen, $activo, $id
                );
                $stmt->execute();

                $mensaje = "Guía actualizada correctamente.";
            }
        }
    }

    header("Location: editar.php?msg=" . urlencode($mensaje));
    exit;
}

// ============================================================
// BUSQUEDA SEGURA
// ============================================================
$busqueda = limpiar($_GET['busqueda'] ?? '');

$guias = [];

$sql = "
    SELECT g.id, g.titulo AS nombre, g.descripcion_corta AS descripcion,
           g.informacion, g.imagen, g.activo, tg.nombre AS tipo_nombre
    FROM guia g
    LEFT JOIN tipo_guia tg ON g.tipo_id = tg.id
    WHERE (? = '' OR g.titulo LIKE ? OR g.descripcion_corta LIKE ?)
    ORDER BY g.id DESC
";

$stmt = $conn->prepare($sql);
$like = "%" . $busqueda . "%";
$stmt->bind_param("sss", $busqueda, $like, $like);
$stmt->execute();

$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $guias[] = [
        "id"          => (int)$row['id'],
        "nombre"      => h($row['nombre']),
        "descripcion" => h($row['descripcion']),
        "tipo_nombre" => h($row['tipo_nombre']),
        "activo"      => (int)$row['activo']
    ];
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PIDS – Panel de administración</title>
  <link rel="stylesheet" href="css/editar.css">
  <link rel="icon" type="image/x-icon" href="img/logo.png">
</head>

<body>

  <!-- HEADER -->
  <header class="pids-header" role="banner">
    <div class="pids-wrap header-flex">

        <a href="index.php" class="pids-brand">
            <img src="img/logo2.png" alt="PIDS" class="pids-logo">
            <span class="pids-brand-text pids-brand-text-big">
                Plataforma de Inclusión Digital Senior
            </span>
        </a>

        <button class="menu-toggle" id="menuToggle">☰</button>

        <nav class="pids-nav" id="pidsNav">
            <a href="index.php">Inicio</a>
            <a href="guias.php" class="active">Guías</a>
            <a href="#contacto">Contacto</a>

            <?php if (isset($_SESSION['admin_id'])): ?>
                <a href="logout.php" class="pids-admin-btn admin-mobile">
                    Cerrar
                </a>
            <?php endif; ?>
        </nav>

        <button class="pids-a11y-btn" id="btnA11y">A+</button>
    </div>
  </header>

  <main class="panel-main">
    <div class="pids-wrap">

      <section class="panel-header">
        <h1>Panel de administración</h1>
        <p>Gestiona las guías de la plataforma y sus pasos.</p>
      </section>

      <?php if (!empty($_GET['msg'])): ?>
        <div class="alert">
          <?= h($_GET['msg']) ?>
        </div>
      <?php endif; ?>

      <!-- FORMULARIO -->
      <section class="panel-card">
        <h2><?= $modo === 'actualizar' ? "Editar guía" : "Agregar nueva guía" ?></h2>

        <form method="post" enctype="multipart/form-data" class="crud-form">
          <input type="hidden" name="id" value="<?= (int)$edit_id ?>">
          <input type="hidden" name="imagen_actual" value="<?= h($imagenForm) ?>">

          <div class="form-row">
            <label>Nombre / Título</label>
            <input type="text" name="nombre" value="<?= h($tituloForm) ?>" required>
          </div>

          <div class="form-row">
            <label>Tipo de guía</label>
            <select name="tipo_id" required>
              <?php foreach ($tipos as $t): ?>
                <option value="<?= $t['id'] ?>" <?= $tipoIdForm == $t['id'] ? 'selected' : '' ?>>
                  <?= $t['nombre'] ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-row">
            <label>Imagen de portada</label>
            <input type="file" name="imagen" accept="image/*">
            <?php if ($imagenForm): ?>
              <p class="hint-imagen">Imagen actual: <?= h($imagenForm) ?></p>
            <?php endif; ?>
          </div>

          <div class="form-row">
            <label>Descripción corta</label>
            <textarea name="descripcion" rows="2" required><?= h($descripcionForm) ?></textarea>
          </div>

          <div class="form-row">
            <label>Información</label>
            <textarea name="informacion" rows="3" required><?= h($informacionForm) ?></textarea>
          </div>

          <div class="visible-wrapper">
              <label class="switch">
                  <input type="checkbox" name="activo" value="1" <?= $activoForm ? 'checked' : '' ?>>
                  <span class="slider"></span>
              </label>
              <span>Guía visible para usuarios</span>
          </div>

          <div class="form-actions">
            <button type="submit" name="accion" value="crear" class="btn btn-primary">+ Crear nueva</button>
            <button type="submit" name="accion" value="actualizar" class="btn btn-secondary">^ Actualizar</button>
            <button type="submit" name="accion" value="eliminar" class="btn btn-danger"
                    onclick="return confirm('¿Seguro que deseas eliminar esta guía?');">- Eliminar</button>
          </div>
        </form>
      </section>

      <!-- TABLA DE GUÍAS -->
      <section class="panel-card">
        <h2>Guías registradas</h2>

        <form method="get" class="search-bar">
          <input type="text" name="busqueda" placeholder="Buscar guías..." 
                 value="<?= h($busqueda) ?>"
                 style="padding: 10px; width: 60%; max-width: 350px;">

          <button type="submit" class="btn btn-primary">Buscar</button>

          <?php if (!empty($busqueda)): ?>
            <a href="editar.php" class="btn btn-secondary">Limpiar</a>
          <?php endif; ?>
        </form>

        <div class="crud-table-wrapper">
          <table class="crud-table">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>

            <tbody>
              <?php if (count($guias) === 0): ?>
                <tr>
                  <td colspan="5">No se encontraron guías.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($guias as $g): ?>
                  <tr>
                    <td><?= $g['nombre'] ?></td>
                    <td><?= $g['tipo_nombre'] ?></td>
                    <td><?= nl2br($g['descripcion']) ?></td>
                    <td><?= $g['activo'] ? "Activa" : "Oculta" ?></td>
                    <td>
                      <section class="acciones">
                        <a class="btn btn-small btn-secondary" href="editar.php?edit=<?= $g['id'] ?>">Editar</a>
                        <a class="btn btn-small btn-steps" href="editarPasos.php?guia_id=<?= $g['id'] ?>">Pasos</a>
                        <a class="btn btn-small btn-steps" href="editarActividades.php?guia_id=<?= $g['id'] ?>">Actividades</a>
                      </section>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>

          </table>
        </div>
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
