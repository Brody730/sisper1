<?php
session_start();

// Definir la duración de la cookie de sesión (24 horas)
define('SESSION_COOKIE_EXPIRY', 86400);

// Asegurarse de que la cookie de sesión esté activa por 24 horas
setcookie(session_name(), session_id(), time() + SESSION_COOKIE_EXPIRY, "/", "", true, true);

// Verificar si la sesión está autenticada
if (!isset($_SESSION["autentica"]) || $_SESSION["autentica"] !== "SIP") {
    // Redirigir al usuario a la página de inicio de sesión si no está autenticado
    header("Location: sign-in.php");
    exit();
}
?>