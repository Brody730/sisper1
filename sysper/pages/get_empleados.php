<?php
header('Content-Type: application/json');

// Configuración de la base de datos
$servername = "localhost";
$username = "skyper";
$password = "ctpalm2113";
$dbname = "estadiaunid";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Obtener el término de búsqueda
    $busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $per_page = 30;
    $offset = ($page - 1) * $per_page;
    
    // Consulta para obtener empleados (búsqueda por RPE o nombre completo)
    $sql = "SELECT 
                rpe,
                nombre,
                a_paterno,
                a_materno,
                categ
            FROM empleados 
            WHERE rpe LIKE :busqueda 
            OR CONCAT(nombre, ' ', a_paterno, ' ', a_materno) LIKE :busqueda
            ORDER BY CASE 
                WHEN rpe LIKE :busqueda THEN 1
                WHEN CONCAT(nombre, ' ', a_paterno, ' ', a_materno) LIKE :busqueda THEN 2
                ELSE 3
            END
            LIMIT :offset, :per_page";
            
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':busqueda', '%' . $busqueda . '%', PDO::PARAM_STR);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':per_page', $per_page, PDO::PARAM_INT);
    $stmt->execute();
    
    // Formatear resultados para Select2
    $items = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $nombre_completo = $row['nombre'] . ' ' . $row['a_paterno'] . ' ' . $row['a_materno'];
        $items[] = [
            'id' => $row['rpe'],
            'rpe' => $row['rpe'],
            'nombre' => $nombre_completo,
            'categoria' => $row['categ'],
            'text' => $row['categ'] . ' - ' . $nombre_completo . ' - ' . $row['rpe']
        ];
    }
    
    // Contar total de resultados para paginación
    $sql_count = "SELECT COUNT(*) 
                  FROM empleados 
                  WHERE rpe LIKE :busqueda 
                  OR CONCAT(nombre, ' ', a_paterno, ' ', a_materno) LIKE :busqueda";
    $stmt_count = $conn->prepare($sql_count);
    $stmt_count->bindValue(':busqueda', '%' . $busqueda . '%', PDO::PARAM_STR);
    $stmt_count->execute();
    $total_count = $stmt_count->fetchColumn();
    
    // Devolver resultados
    echo json_encode([
        'items' => $items,
        'total_count' => $total_count
    ]);
    
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

$conn = null;
?>