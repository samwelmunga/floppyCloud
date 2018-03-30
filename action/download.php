<?php

  require_once("../lib/init/autoloader.php");

  if($_SESSION['download_file_owner']) {
    $owner = $_SESSION['download_file_owner'];
    $_SESSION['download_file_owner'] = null;
  } else {
    $user  = new UserModel();
    $owner = $user->getSession();
  }
  
  $file = new FileModel($owner);
  $file->downloadFile($_SESSION['download_file_name']);

?>
