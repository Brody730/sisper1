<?php
// Iniciar sesión
session_start();

// Establecer la zona horaria
date_default_timezone_set('America/Mexico_City');

// Conectar a la base de datos
$conecta = mysqli_connect('localhost', 'skyper', 'ctpalm2113', 'estadiaunid');

// Verificar la conexión
if (!$conecta) {
    die('Error de conexión: ' . mysqli_connect_error());
}

// Establecer el conjunto de caracteres a UTF-8
if (!mysqli_set_charset($conecta, 'utf8')) {
    die('Error al establecer el conjunto de caracteres UTF-8: ' . mysqli_error($conecta));
}
?>
