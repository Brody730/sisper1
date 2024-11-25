<?php
session_start();
   date_default_timezone_set('America/Mexico_City');
     $conecta =  mysqli_connect('localhost', 'skyper', 'ctpalm2113', 'estadiaunid');
     if(!$conecta){
         die('no pudo conectarse:' . mysqli_connect_error());
      }
   if (!mysqli_set_charset($conecta,'utf8')) {
    die('No pudo conectarse: ' . mysqli_error($conecta));
    }
?> 