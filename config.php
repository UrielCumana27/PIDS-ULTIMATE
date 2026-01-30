<?php

// Nombre del servidor donde está la base de datos (en XAMPP casi siempre es localhost)
$servername = "localhost";

// Nombre de usuario para acceder a la base de datos (por defecto en XAMPP es 'root')
$username = "root";

// Contraseña del usuario de la base de datos (vacía por defecto en XAMPP)
$password = "";

// Nombre de la base de datos a la que te quieres conectar
$dbname = "pids_db";

// Crea una nueva conexión a MySQL usando los parámetros anteriores
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica si ocurrió algún error al intentar conectarse.
// Si existe un error, detiene el script y muestra el mensaje.
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Configura el conjunto de caracteres para que soporte emojis y caracteres especiales.
// utf8mb4 es el estándar recomendado.
$conn->set_charset("utf8mb4");
