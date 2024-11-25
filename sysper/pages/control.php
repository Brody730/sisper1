<?php
require_once("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $password = $_POST['clave'];

    // Consulta para obtener el usuario y la contraseña de la base de datos
    $sql = "SELECT rpe, clave FROM usuarios WHERE rpe = '$usuario'";
    $resultado = mysqli_query($conecta, $sql);
    $usuario = mysqli_fetch_assoc($resultado);

    if ($usuario) {
        // Verificar si la contraseña almacenada está cifrada o en texto plano
        if (password_verify($password, $usuario['clave'])) {
            // La contraseña está cifrada correctamente, iniciar sesión
            session_start();    
            $_SESSION['autentica'] = "SIP";
            $_SESSION['usuarioactual'] = $usuario['rpe'];
            $_SESSION['rpe'] = $usuario['rpe'];


            // Hacer consulta a la tabla empleados para obtener el nombre y la categoría
            $sql_empleado = "SELECT nombre, a_paterno, a_materno, categ FROM empleados WHERE rpe = '$usuario[rpe]'";
            $resultado_empleado = mysqli_query($conecta, $sql_empleado);
            $empleado = mysqli_fetch_assoc($resultado_empleado);

            if ($empleado) {
                // Guardar los datos del empleado en la sesión
                $_SESSION['nombre_completo'] = $empleado['nombre'] . " " . $empleado['a_paterno'] . " " . $empleado['a_materno'];
                $_SESSION['categoria'] = $empleado['categ'];

                header("Location: index.php");
            } else {
                echo "<script>alert('No se encontraron datos del empleado'); window.location.href = 'sign-in.php';</script>";
            }
        } elseif ($password == $usuario['clave']) {
            // La contraseña está en texto plano, cifrarla ahora
            $password_cifrada = password_hash($password, PASSWORD_DEFAULT);
            $sql_update = "UPDATE usuarios SET clave = '$password_cifrada' WHERE rpe = '$usuario[rpe]'";
            mysqli_query($conecta, $sql_update);

            // Iniciar sesión después de actualizar la contraseña
            session_start();
            $_SESSION['autentica'] = "SIP";
            $_SESSION['usuarioactual'] = $usuario['rpe'];

            // Hacer consulta a la tabla empleados para obtener el nombre y la categoría
            $sql_empleado = "SELECT nombre, a_paterno, a_materno, categ FROM empleados WHERE rpe = '$usuario[rpe]'";
            $resultado_empleado = mysqli_query($conecta, $sql_empleado);
            $empleado = mysqli_fetch_assoc($resultado_empleado);

            if ($empleado) {
                // Guardar los datos del empleado en la sesión
                $_SESSION['nombre_completo'] = $empleado['nombre'] . " " . $empleado['a_paterno'] . " " . $empleado['a_materno'];
                $_SESSION['categoria'] = $empleado['categ'];

                header("Location: index.php");
            } else {
                echo "<script>alert('No se encontraron datos del empleado'); window.location.href = 'sign-in.php';</script>";
            }
        } else {
            echo "<script>alert('La contraseña es incorrecta'); window.location.href = 'sign-in.php';</script>";
        }
    } else {
        echo "<script>alert('El usuario no existe'); window.location.href = 'sign-in.php';</script>";
    }

    mysqli_close($conecta);
}
?>
