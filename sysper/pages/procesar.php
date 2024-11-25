<?php
//-- Tabla para registros principales
// CREATE TABLE IF NOT EXISTS registros (
//////id INT AUTO_INCREMENT PRIMARY KEY,
//////fecha_registro DATE NOT NULL,
//////hora_inicio TIME NOT NULL,
//////hora_termino TIME NOT NULL,
//////horas_extra INT NOT NULL,
//////actividades TEXT NOT NULL,
//////justificacion TEXT NOT NULL,
//////fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
////);

////-- Tabla para detalles de órdenes
////CREATE TABLE IF NOT EXISTS detalles_orden (
//////id INT AUTO_INCREMENT PRIMARY KEY,
//////id_registro INT NOT NULL,
//////id_departamento INT NOT NULL,
//////numero_orden VARCHAR(50) NOT NULL,080904

//////om VARCHAR(50) NOT NULL,
//////FOREIGN KEY (id_registro) REFERENCES registros(id)
////);

////-- Tabla para empleados asignados
////CREATE TABLE IF NOT EXISTS empleados_asignados (
//////id INT AUTO_INCREMENT PRIMARY KEY,
//////id_registro INT NOT NULL,
//////rpe INT NOT NULL,
//////FOREIGN KEY (id_registro) REFERENCES registros(id)
//    );

// Activar todos los errores

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Función para escribir logs
function writeLog($message, $type = 'INFO') {
    $logFile = 'debug_' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp][$type] " . print_r($message, true) . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Iniciar el buffer para evitar salidas accidentales
ob_start();

try {
    // Headers necesarios
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');

    // Configuración de la base de datos
    $servidor = "localhost";
    $usuario = "skyper";
    $password = "ctpalm2113";
    $bd = "estadiaunid";

    // Log de datos recibidos
    writeLog($_POST, 'POST_DATA');

    // Respuesta inicial
    $response = [
        'success' => false,
        'message' => '',
        'errors' => [],
        'debug' => ['post' => $_POST]
    ];

    // Crear conexión con PDO
    $pdo = new PDO("mysql:host=$servidor;dbname=$bd;charset=utf8", $usuario, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Iniciar transacción
    $pdo->beginTransaction();

    // 1. Insertar en la tabla `registros`
    $sql_registros = "INSERT INTO registros (fecha_registro, hora_inicio, hora_termino, horas_extra, actividades, justificacion) 
                      VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql_registros);
    $stmt->execute([
        $_POST['fecha_act'],
        $_POST['hora_inicio'],
        $_POST['hora_termino'],
        $_POST['horas_extra'],
        $_POST['actividades'],
        $_POST['justificacion']
    ]);

    $id_registro = $pdo->lastInsertId();
    writeLog("Registro insertado con ID: $id_registro");

    // 2. Insertar en la tabla `detalles_orden`
    if (!empty($_POST['numero_orden']) && is_array($_POST['numero_orden'])) {
        $sql_detalles = "INSERT INTO detalles_orden (id_registro, id_departamento, numero_orden, om) 
                         VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql_detalles);
        foreach ($_POST['numero_orden'] as $key => $numero) {
            $stmt->execute([
                $id_registro,
                $_POST['departamento'],
                $numero,
                $_POST['om'][$key] ?? ''
            ]);
            writeLog("Detalle de orden insertado - Número: $numero");
        }
    }

    // 3. Insertar en la tabla `empleados_asignados`
    if (!empty($_POST['empleados']) && is_array($_POST['empleados'])) {
        $sql_empleados = "INSERT INTO empleados_asignados (id_registro, rpe) 
                          VALUES (?, ?)";
        $stmt = $pdo->prepare($sql_empleados);
        foreach ($_POST['empleados'] as $rpe) {
            $stmt->execute([$id_registro, $rpe]);
            writeLog("Empleado asignado - RPE: $rpe");
        }
    }

    // Confirmar transacción
    $pdo->commit();
    writeLog("Transacción completada con éxito");

    // Actualizar respuesta de éxito
    $response['success'] = true;
    $response['message'] = 'Registro guardado exitosamente';
    $response['id_registro'] = $id_registro;

} catch (PDOException $e) {
    writeLog($e->getMessage(), 'ERROR_PDO');
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $response['message'] = 'Error en la base de datos';
    $response['errors'][] = $e->getMessage();
} catch (Exception $e) {
    writeLog($e->getMessage(), 'ERROR');
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $response['message'] = 'Error al procesar la solicitud';
    $response['errors'][] = $e->getMessage();
}

// Limpiar cualquier salida previa
ob_clean();

// Validar y enviar respuesta JSON
$json_response = json_encode($response);
if (json_last_error() === JSON_ERROR_NONE) {
    echo $json_response;
} else {
    writeLog('Error generando JSON: ' . json_last_error_msg(), 'ERROR_JSON');
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error interno al generar respuesta JSON']);
}

?>
