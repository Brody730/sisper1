<?php
// Conexión a la base de datos
$con = new mysqli('localhost', 'skyper', 'ctpalm2113', 'estadiaunid');
if ($con->connect_error) {
    die("Error en la conexión: " . $con->connect_error);
}

// Consulta para obtener departamentos y sus jefes relacionados
$query = "
    SELECT 
        d.id_departamento AS id, 
        d.departamento AS nombre, 
        j.nombre_jefe AS jefe 
    FROM 
        departamentos d
    LEFT JOIN 
        jefes_dpto j 
    ON 
        d.id_departamento = j.id_departamento
";
$result = $con->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . $row['id'] . '" data-jefe="' . htmlspecialchars($row['jefe'] ?? 'Sin jefe asignado', ENT_QUOTES) . '">';
        echo $row['nombre'];
        echo '</option>';
    }
} else {
    echo '<option value="">No hay departamentos disponibles</option>';
}

$con->close();
?>
