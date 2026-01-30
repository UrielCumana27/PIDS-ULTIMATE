<?php

// Inicia una nueva sesión o reanuda la sesión actual
session_start();

// Elimina todas las variables de sesión almacenadas
session_unset();

// Destruye toda la sesión, eliminando los datos asociados con ella
session_destroy();

// Redirige al usuario a la página de login
header("Location: login.php");

// Finaliza la ejecución del script para asegurar que no se ejecute más código
exit();
