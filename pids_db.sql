-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-12-2025 a las 03:05:30
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `pids_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin`
--

CREATE TABLE `admin` (
  `id` int(10) UNSIGNED NOT NULL,
  `usuario` varchar(60) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `creado_en` datetime DEFAULT current_timestamp(),
  `intentos_fallidos` int(11) DEFAULT 0,
  `bloqueo_hasta` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `admin`
--

INSERT INTO `admin` (`id`, `usuario`, `password`, `nombre`, `creado_en`, `intentos_fallidos`, `bloqueo_hasta`) VALUES
(1, 'admin', 'admin', 'Administrador General', '2025-11-24 00:03:53', 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `guia`
--

CREATE TABLE `guia` (
  `id` int(10) UNSIGNED NOT NULL,
  `tipo_id` int(10) UNSIGNED NOT NULL,
  `admin_id` int(10) UNSIGNED DEFAULT NULL,
  `titulo` varchar(150) NOT NULL,
  `descripcion_corta` varchar(255) NOT NULL,
  `informacion` text DEFAULT NULL,
  `actividades` text DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `portada_img` varchar(255) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `guia`
--

INSERT INTO `guia` (`id`, `tipo_id`, `admin_id`, `titulo`, `descripcion_corta`, `informacion`, `actividades`, `imagen`, `portada_img`, `fecha_creacion`, `fecha_actualizacion`, `activo`) VALUES
(9, 3, 1, 'Seguridad en internet', 'Esta guía te ayudará a entender cómo proteger tu información personal mientras navegas por Internet, evitando fraudes y peligros en línea.', 'Internet es una herramienta poderosa, pero también puede ser riesgosa si no tomamos las precauciones necesarias. Es importante aprender a proteger tu información personal y estar alerta ante posibles amenazas.', NULL, 'img/guia_c61f6464a7a989e1.jpg', NULL, '2025-12-08 22:42:19', '2025-12-08 22:47:50', 1),
(10, 1, 1, 'Guía de Ofimática Básica', 'Aprende a usar herramientas básicas de ofimática como Word, para mejorar tu productividad en el computador.', 'Las herramientas de ofimática son esenciales para hacer documentos, hojas de cálculo y presentaciones. Con esta guía aprenderás lo básico para empezar a utilizar estos programas de manera sencilla.', NULL, 'img/guia_4298115ca7701efd.png', NULL, '2025-12-08 22:49:38', '2025-12-08 22:55:00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `guia_actividad`
--

CREATE TABLE `guia_actividad` (
  `id` int(10) UNSIGNED NOT NULL,
  `guia_id` int(10) UNSIGNED NOT NULL,
  `pregunta` text NOT NULL,
  `opcionA` varchar(255) NOT NULL,
  `opcionB` varchar(255) NOT NULL,
  `opcionC` varchar(255) NOT NULL,
  `opcionD` varchar(255) NOT NULL,
  `correcta` enum('A','B','C','D') NOT NULL,
  `feedback` text DEFAULT NULL,
  `orden` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `guia_actividad`
--

INSERT INTO `guia_actividad` (`id`, `guia_id`, `pregunta`, `opcionA`, `opcionB`, `opcionC`, `opcionD`, `correcta`, `feedback`, `orden`) VALUES
(7, 9, '¿Cuál de las siguientes opciones es una contraseña segura?', '123456', 'admin', 'Juan123!', 'password', 'C', 'Es incorrecta porque no cumple con las condiciones para ser una contraseña segura según lo visto en la guía.', 1),
(8, 9, '¿Cuál es la mejor forma de protegerte de fraudes en correos electrónicos?', 'Hacer clic en todos los enlaces para ver qué sucede.', 'No compartir tus datos personales con desconocidos.', 'Compartir tu contraseña si el correo parece ser de una entidad confiable.', 'Descargar archivos adjuntos de correos no solicitados.', 'B', 'Esta opción arriesga tu información personal al provenir de un sitio no oficial.', 2),
(9, 10, '¿Cuál es el primer paso para crear un documento en Word?', 'Hacer clic en \"Nuevo\" y seleccionar \"Documento en blanco\".', 'Escribir directamente en la pantalla de inicio.', 'Seleccionar \"Guardar\" antes de escribir.', 'Iniciar sesión en tu cuenta de Microsoft.', 'A', 'La opción no es correcta porque es uno de los pasos posteriores a (Hacer clic en \"Nuevo\" y seleccionar \"Documento en blanco\").', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `guia_paso`
--

CREATE TABLE `guia_paso` (
  `id` int(10) UNSIGNED NOT NULL,
  `guia_id` int(10) UNSIGNED NOT NULL,
  `orden` int(10) UNSIGNED NOT NULL,
  `titulo_paso` varchar(150) NOT NULL,
  `texto` text DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `guia_paso`
--

INSERT INTO `guia_paso` (`id`, `guia_id`, `orden`, `titulo_paso`, `texto`, `imagen`) VALUES
(10, 9, 1, 'Usa contraseñas seguras', 'Crea contraseñas con al menos 8 caracteres, mezclando letras, números y símbolos. No uses la misma contraseña para todas tus cuentas.\r\n\r\nEjemplo de contraseña segura: PatitoFeo23$', 'img/contraseña.webp'),
(11, 9, 2, 'Evita hacer clic en enlaces desconocidos', 'No hagas clic en enlaces de correos electrónicos o mensajes de desconocidos, ya que pueden ser intentos de fraude.', ''),
(12, 9, 3, 'Mantén tu antivirus actualizado', 'Instala un antivirus confiable y actualízalo regularmente para proteger tu dispositivo de virus y malware.', ''),
(13, 9, 4, 'No compartas información personal', 'No compartas tus datos personales (como tu número de cuenta o dirección) en sitios web o correos electrónicos que no sean seguros.', ''),
(14, 9, 5, 'Cierra sesión después de usar cuentas en línea', 'Asegúrate de cerrar sesión en cuentas bancarias, redes sociales o correos cuando termines de usarlas.', ''),
(15, 10, 1, 'Word - Crear un documento', 'Abre Microsoft Word.', 'img/Captura de pantalla 2025-12-08 225051.png'),
(16, 10, 2, 'Word - Crear un documento', 'Haz clic en \"Nuevo\" y selecciona \"Documento en blanco\".', 'img/Captura de pantalla 2025-12-08 225205.png'),
(17, 10, 3, 'Word - Crear un documento:', 'Empieza a escribir tu texto mediante el uso del teclado.', ''),
(18, 10, 4, 'Word - Crear un documento', 'Guarda el documento en \"Archivo\" > \"Guardar como\", eligiendo un lugar en tu computadora.', 'img/Captura de pantalla 2025-12-08 225337.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_guia`
--

CREATE TABLE `tipo_guia` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_guia`
--

INSERT INTO `tipo_guia` (`id`, `nombre`, `slug`, `descripcion`) VALUES
(1, 'Guía de Ofimática', 'ofimatica', 'Word, Excel, PowerPoint y otras herramientas.'),
(2, 'Guía de Trámites', 'tramites', 'Trámites en línea y servicios públicos.'),
(3, 'Guía de Seguridad', 'seguridad', 'Ciberseguridad básica y prevención de fraudes.');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Indices de la tabla `guia`
--
ALTER TABLE `guia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_guia_tipo` (`tipo_id`),
  ADD KEY `fk_guia_admin` (`admin_id`);

--
-- Indices de la tabla `guia_actividad`
--
ALTER TABLE `guia_actividad`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guia_id` (`guia_id`);

--
-- Indices de la tabla `guia_paso`
--
ALTER TABLE `guia_paso`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guia_id` (`guia_id`);

--
-- Indices de la tabla `tipo_guia`
--
ALTER TABLE `tipo_guia`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `guia`
--
ALTER TABLE `guia`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `guia_actividad`
--
ALTER TABLE `guia_actividad`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `guia_paso`
--
ALTER TABLE `guia_paso`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `tipo_guia`
--
ALTER TABLE `tipo_guia`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `guia`
--
ALTER TABLE `guia`
  ADD CONSTRAINT `fk_guia_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_guia_tipo` FOREIGN KEY (`tipo_id`) REFERENCES `tipo_guia` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `guia_actividad`
--
ALTER TABLE `guia_actividad`
  ADD CONSTRAINT `guia_actividad_ibfk_1` FOREIGN KEY (`guia_id`) REFERENCES `guia` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `guia_paso`
--
ALTER TABLE `guia_paso`
  ADD CONSTRAINT `fk_guia_paso_guia` FOREIGN KEY (`guia_id`) REFERENCES `guia` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
