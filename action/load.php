<?php

  if(empty($_GET['load'])  && empty($_GET['download'])) {
    require_once("../lib/init/autoloader.php");
  }

  $user  = new UserModel();

  if(isset($_GET['id'])) {
    $owner = $_GET['id'];
  } else {
    $owner = $user->getSession();
  }

  if(isset($_GET['load'])) {
    $name  = $_GET['load'];
  } else {
    $name  = $_GET['file_name'];
  }

  $view = new ViewHandler();
  $file = new FileModel($owner);
  $cont = new FileController($file);
  $meta = $cont->updateData($file->getFileMeta($name));
  $meta['css_size'] = ($meta['category'] == 'image') ? 'auto' : '100%';

  $temp = $view->preview($meta);

  $temp = str_replace('$file_blob', base64_encode($file->getBLOB($name)), $temp);

  if(isset($_GET['api'])) {
    die(json_encode(array('data' => $temp)));
  } else echo $temp;

?>
