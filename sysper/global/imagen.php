<?php
session_start();
date_default_timezone_set('America/Mexico_City');
  $conecta =  mysqli_connect('localhost', 'skyper', 'ctpalm2113', 'rescatar');
  if(!$conecta){
      die('no pudo conectarse:' . mysqli_connect_error());
   }
if (!mysqli_set_charset($conecta,'utf8')) {
 die('No pudo conectarse: ' . mysqli_error($conecta));
 }

$idImagen = $_SESSION['rpe'];
$resultado = mysqli_query($conecta, "SELECT imagen FROM imagenes WHERE idImagen = '$idImagen'");

if ($resultado) {
    $fila = mysqli_fetch_assoc($resultado);
    $foto = $fila['imagen'];

    if ($foto) {
        // https://www.php.net/manual/en/function.imagejpeg.php
        // https://www.superprof.es/blog/tecnica-codigo-foto-programacion/
        // https://www.php.net/manual/es/function.header.php 
        header("Content-Type: image/jpeg");
        echo $foto;
    } else {
        echo "No se encontro la imagen.";
    }
} else {
    echo "error chavo" . mysqli_error($conecta);
}
mysqli_close($conecta);
?>
