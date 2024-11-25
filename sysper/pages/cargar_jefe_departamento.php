<?php
// Conexión a la base de datos
$con = new mysqli('localhost', 'skyper', 'ctpalm2113', 'estadiaunid');
if ($con->connect_error) {
    die("Error en la conexión: " . $con->connect_error);
}

// Obtener el ID del departamento desde el parámetro GET
$departamentoId = intval($_GET['departamento_id']);

// Consulta para obtener el jefe relacionado con el departamento
$query = "
    SELECT 
        j.nombre_jefe AS jefe 
    FROM 
        jefes_dpto j
    INNER JOIN 
        departamentos d 
    ON 
        j.id_departamento = d.id_departamento
    WHERE 
        d.id_departamento = ?
";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $departamentoId);
$stmt->execute();
$stmt->bind_result($jefe);
$stmt->fetch();

if ($jefe) {
    echo '<option value="' . htmlspecialchars($jefe, ENT_QUOTES) . '">' . htmlspecialchars($jefe, ENT_QUOTES) . '</option>';
} else {
    echo '<option value="">Jefe no encontrado</option>';
}

$stmt->close();
$con->close();
?>

