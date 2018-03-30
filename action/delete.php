<?php

  require_once("../lib/init/autoloader.php");
  $user = new UserModel();
  $file = new FileModel($user->getSession());
  $file->deleteFile($_SESSION['delete_file_name']);

?>
