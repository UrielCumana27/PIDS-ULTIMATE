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

// ========================
//  VALIDAR GUIA
// ========================
$guiaId = isset($_GET['guia_id']) ? (int)$_GET['guia_id'] : 0;

if ($guiaId <= 0) {
    header("Location: editar.php");
    exit;
}

$stmt = $conn->prepare("SELECT id, titulo FROM guia WHERE id = ?");
$stmt->bind_param("i", $guiaId);
$stmt->execute();
$res = $stmt->get_result();
$guia = $res->fetch_assoc();
$stmt->close();

if (!$guia) {
    die("La guía indicada no existe.");
}

// ========================
//  VALORES DEL FORM
// ========================
$modo          = "crear";
$mensaje       = "";
$actividadId   = 0;

$ordenForm     = 1;
$preguntaForm  = "";
$opAForm       = "";
$opBForm       = "";
$opCForm       = "";
$opDForm       = "";
$correctaForm  = "A";
$feedbackForm  = "";   // nuevo: texto explicativo

// ========================
//  EDITAR
// ========================
if (isset($_GET['actividad_edit'])) {
    $actividadId = (int)$_GET['actividad_edit'];

    $stmt = $conn->prepare("
        SELECT id, orden, pregunta, opcionA, opcionB, opcionC, opcionD, correcta, feedback
        FROM guia_actividad
        WHERE id = ? AND guia_id = ?
    ");
    $stmt->bind_param("ii", $actividadId, $guiaId);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        $modo         = "actualizar";

        $ordenForm    = $row['orden'];
        $preguntaForm = $row['pregunta'];
        $opAForm      = $row['opcionA'];
        $opBForm      = $row['opcionB'];
        $opCForm      = $row['opcionC'];
        $opDForm      = $row['opcionD'];
        $correctaForm = $row['correcta'];
        $feedbackForm = $row['feedback'] ?? "";
    }
    $stmt->close();
}

// ========================
//  PROCESAR POST
// ========================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $accion        = $_POST["accion"] ?? "";
    $actividadPost = (int)($_POST["actividad_id"] ?? 0);

    $ordenPost    = (int)($_POST["orden"] ?? 1);
    $preguntaPost = trim($_POST["pregunta"] ?? "");
    $opAPost      = trim($_POST["opA"] ?? "");
    $opBPost      = trim($_POST["opB"] ?? "");
    $opCPost      = trim($_POST["opC"] ?? "");
    $opDPost      = trim($_POST["opD"] ?? "");
    $correctaPost = $_POST["correcta"] ?? "A";
    $feedbackPost = trim($_POST["feedback"] ?? ""); // nuevo

    // ELIMINAR
    if ($accion === "eliminar" && $actividadPost > 0) {

        $stmt = $conn->prepare("DELETE FROM guia_actividad WHERE id = ? AND guia_id = ?");
        $stmt->bind_param("ii", $actividadPost, $guiaId);

        if ($stmt->execute()) {
            $mensaje = "Actividad eliminada correctamente.";
        } else {
            $mensaje = "Error al eliminar la actividad.";
        }
        $stmt->close();

    } else {

        // Validación básica
        if (
            $preguntaPost === "" || 
            $opAPost === "" || 
            $opBPost === "" || 
            $opCPost === "" || 
            $opDPost === ""
        ) {
            $mensaje = "Todos los campos son obligatorios.";
        } else {

            // CREAR
            if ($accion === "crear") {

                $stmt = $conn->prepare("
                    INSERT INTO guia_actividad 
                    (guia_id, orden, pregunta, opcionA, opcionB, opcionC, opcionD, correcta, feedback)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");

                $stmt->bind_param("iisssssss",
                    $guiaId,
                    $ordenPost,
                    $preguntaPost,
                    $opAPost, $opBPost, $opCPost, $opDPost,
                    $correctaPost,
                    $feedbackPost
                );

                if ($stmt->execute()) {
                    $mensaje = "Actividad creada correctamente.";
                } else {
                    $mensaje = "Error al crear la actividad.";
                }

                $stmt->close();

            }

            // ACTUALIZAR
            elseif ($accion === "actualizar" && $actividadPost > 0) {

                $stmt = $conn->prepare("
                    UPDATE guia_actividad
                    SET orden = ?, pregunta = ?, opcionA = ?, opcionB = ?, opcionC = ?, opcionD = ?, correcta = ?, feedback = ?
                    WHERE id = ? AND guia_id = ?
                ");

                $stmt->bind_param("isssssssii",
                    $ordenPost,
                    $preguntaPost,
                    $opAPost, $opBPost, $opCPost, $opDPost,
                    $correctaPost,
                    $feedbackPost,
                    $actividadPost,
                    $guiaId
                );

                if ($stmt->execute()) {
                    $mensaje = "Actividad actualizada correctamente.";
                } else {
                    $mensaje = "Error al actualizar la actividad.";
                }
                $stmt->close();
            }
        }
    }
}

// ========================
//  LISTADO DE ACTIVIDADES
// ========================
$actividades = [];
$stmt = $conn->prepare("
    SELECT id, orden, pregunta, correcta
    FROM guia_actividad
    WHERE guia_id = ?
    ORDER BY orden ASC, id ASC
");
$stmt->bind_param("i", $guiaId);
$stmt->execute();

$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $actividades[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>PIDS – Actividades de la Guía</title>
  <link rel="stylesheet" href="css/editarActividades.css">
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

<div class="pids-wrap">

  <section class="panel-header">
    <h1>Actividades de la guía</h1>
    <p>Editando: <strong><?php echo htmlspecialchars($guia['titulo']); ?></strong></p>
  </section>

  <?php if ($mensaje): ?>
  <div class="alert"><?php echo htmlspecialchars($mensaje); ?></div>
  <?php endif; ?>

  <!-- FORMULARIO -->
  <section class="panel-card">
    <h2><?php echo ($modo === "actualizar") ? "Editar actividad" : "Agregar actividad"; ?></h2>

    <form method="post" class="crud-form">

      <input type="hidden" name="actividad_id" value="<?php echo $actividadId; ?>">

      <div class="form-row">
        <label>Orden:</label>
        <input type="number" name="orden" min="1" value="<?php echo $ordenForm; ?>" required>
      </div>
      
      <div class="form-row">
        <label>Pregunta:</label>
        <textarea name="pregunta" rows="3" required><?php echo htmlspecialchars($preguntaForm); ?></textarea>
      </div>
      
      <div class="form-row">
        <label>Opción A:</label>
        <input type="text" name="opA" value="<?php echo htmlspecialchars($opAForm); ?>" required>
      </div>

      <div class="form-row">
        <label>Opción B:</label>
        <input type="text" name="opB" value="<?php echo htmlspecialchars($opBForm); ?>" required>
      </div>
      
      <div class="form-row">
        <label>Opción C:</label>
        <input type="text" name="opC" value="<?php echo htmlspecialchars($opCForm); ?>" required>
      </div>
      
      <div class="form-row">
        <label>Opción D:</label>
        <input type="text" name="opD" value="<?php echo htmlspecialchars($opDForm); ?>" required>
      </div>
      
      <div class="form-row">
        <label>Respuesta correcta:</label>
        <select name="correcta">
          <option value="A" <?php if($correctaForm=="A") echo "selected"; ?>>A</option>
          <option value="B" <?php if($correctaForm=="B") echo "selected"; ?>>B</option>
          <option value="C" <?php if($correctaForm=="C") echo "selected"; ?>>C</option>
          <option value="D" <?php if($correctaForm=="D") echo "selected"; ?>>D</option>
        </select>
      </div>

      <div class="form-row">
        <label>Feedback / explicación para el usuario:</label>
        <textarea name="feedback" rows="3" placeholder="Ej: Recuerda que la ClaveÚnica es personal y no debe compartirse."><?php echo htmlspecialchars($feedbackForm); ?></textarea>
      </div>

      <div class="form-actions">
        <button type="submit" name="accion" value="crear" class="btn btn-primary">Crear</button>
        <button type="submit" name="accion" value="actualizar" class="btn btn-secondary">Actualizar</button>
        <button type="submit" name="accion" value="eliminar" class="btn btn-danger"
          onclick="return confirm('¿Deseas eliminar esta actividad?');">Eliminar</button>
      </div>
    </form>
  </section>


  <!-- LISTADO -->
  <section class="panel-card">
    <h2>Actividades creadas</h2>

    <table class="crud-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Orden</th>
          <th>Pregunta</th>
          <th>Correcta</th>
          <th>Acción</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($actividades)): ?>
        <tr><td colspan="5">Aún no hay actividades.</td></tr>
        <?php else: ?>
        <?php foreach ($actividades as $i => $a): ?>
        <tr>
          <td><?php echo $i + 1; ?></td>
          <td><?php echo $a['orden']; ?></td>
          <td><?php echo htmlspecialchars($a['pregunta']); ?></td>
          <td><strong><?php echo $a['correcta']; ?></strong></td>
          <td>
            <a class="btn btn-small btn-secondary"
               href="editarActividades.php?guia_id=<?php echo $guiaId; ?>&actividad_edit=<?php echo $a['id']; ?>">
              Editar
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>

    <p style="margin-top:18px;">
      <a href="editar.php" class="btn btn-secondary">← Volver al panel de guias.</a>
    </p>
  </section>

</div>

<footer class="pids-footer">
    <div class="pids-wrap pids-footer-copy">
      <p>&copy; <span id="copyYear"></span> PIDS — Inclusión digital para personas mayores.</p>
    </div>
  </footer>

</body>
</html>
