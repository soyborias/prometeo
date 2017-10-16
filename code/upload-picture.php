<?php
include_once('../config.php');
$folder   = (isset($_GET['f']))   ? $_GET['f']    : 'profile';

function json($data){
  if(is_array($data)){
    return json_encode($data);
  }
}

if (isset($_SESSION['username'])){
  include_once('../jupiter/code/fxModelo.php');
  include_once('../jupiter/code/fxVista.php');
  include_once('../jupiter/code/db_procter.php');

  // Сheck file
  if((!empty($_FILES["fileUpload"])) && ($_FILES['fileUpload']['error'] == 0)) {
    $filename = basename($_FILES['fileUpload']['name']);
    $ext = strtolower( substr($filename, strrpos($filename, '.') + 1) );

    $arrExt = array('jpg', 'jpeg', 'png');
    if (in_array($ext, $arrExt)) {
      $type    = $_FILES["fileUpload"]["type"];
      $arrType = array('image/jpeg', 'image/jpg', 'image/png');
      if (in_array($type, $arrType)) {
        if ($_FILES["fileUpload"]["size"] < 500000){
          $newFilename = uniqid(). '_'. time(). '.'. $ext;

          // Upload File
          $targetPath  = dirname(__FILE__). '/../temp/'. $newFilename;
          move_uploaded_file($_FILES['fileUpload']['tmp_name'], $targetPath);

          // falta resize by tipo for better size (small)

          // Move to s3
          $rpta_s3  = 'null';
          $bucket   = S3_BUCKET_STATIC;
          $keyFull  = $folder. '/'. $newFilename;
          $filepath = $targetPath;
          include_once('upload-s3.php');

          if ($folder == 'profile') {
            updateUserPicture($_SESSION['userID'], $newFilename, $db);
            $_SESSION['picture']  = $rpta_s3;
          }

          print json(array('rpta' => 'ok', 'msg' => basename($newFilename), 'server' => $rpta_s3 ));
        } else { print json(array('rpta' => 'error', 'msg' => 'Tamaño maximo es de 500 KB')); }
      } else   { print json(array('rpta' => 'error', 'msg' => 'Solo esta permitido subir imagenes')); }
    } else     { print json(array('rpta' => 'error', 'msg' => 'Solo permitido: jgp, jpeg, png')); }

  } else {
    print json(array('rpta' => 'error', 'msg' => "No hay archivo para subir"));
  }
}
?>
