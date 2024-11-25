<?php
session_start();

// Asegurarse de que la cookie de sesi��n est�� activa por 24 horas
setcookie(session_name(), session_id(), time() + 86400, "/");

// Verificar si la sesi��n est�� autenticada
if (!isset($_SESSION["autentica"]) || $_SESSION["autentica"] != "SIP") {
    // Redirigir al usuario a la p��gina de inicio de sesi��n si no est�� autenticado
    header("Location: sign-in.php");
    exit();
}
?>

