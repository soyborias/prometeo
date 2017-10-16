<?php
include_once('../config.php');

function genPathPictureOut($filename, $folder = '', $default = 'default.jpg'){
  $file = ( strlen($filename) > 3 ) ? $filename : $default;
  return S3_PATH. $folder. $file;
}

$fnd  = (isset($_GET['p']))  ?  $_GET['p']    : 'normal';
$img  = (isset($_GET['i']))  ?  $_GET['i']    : 'normal';
$pathPersonaje = genPathPictureOut($_SESSION['perfilImages'][$img], 'personajes/', 'default.png');

$pathFnd = 'msgOk.png';
switch ($fnd) {
    case 'ok':
        $pathFnd = 'msgOk.png';
        break;

    case 'alert':
        $pathFnd = 'msgAlert.png';
        break;

    case 'bad':
        $pathFnd = 'msgBad.png';
        break;

    case 'normal':
        $pathFnd = 'msgOk.png';
        break;
}

$baseimagen = ImageCreateTrueColor(800,600);
$base = ImageColorAllocate($baseimagen, 255, 255, 255);
//Cargamos el fondo
$fondo = ImageCreateFromPng('../static/images/feedback/'. $pathFnd);
imagecopymerge($baseimagen, $fondo, 0, 0, 0, 0, 800, 600, 100);
//Cargamos personaje
//$personaje = ImageCreateFromPng('http://staticprocter.s3.amazonaws.com/personajes/57f66e649e4f4_1475767908.png');
$personaje = ImageCreateFromPng($pathPersonaje);
//Juntamos imagenes
imagecopy($baseimagen, $personaje, 400, 100, 0, 0, 400, 400);
//Mostramos la imagen en el navegador
header("Content-Type: image/png");
ImagePng($baseimagen);
//Limpiamos la memoria utilizada con las imagenes
ImageDestroy($fondo);
ImageDestroy($personaje);
ImageDestroy($baseimagen);
