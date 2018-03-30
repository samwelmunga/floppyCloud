<?php

  require_once("autoloader.php");
  require_once("request.php");
  require_once("setUrl.php");

  $user = new UserModel();
  $view = (request('api')) ? null : new ViewHandler();
  $sess = new SessionController($user, $view);
  
  if($sess->checkSession() == false && request('id')) {

    if(!request('load')  && request('download')) {
      $error = 'No file was requested';
      if($view) $view-addNote($error);
      else die(json_encode(array('error' => $error)));
    }

    $files    = new FileModel(request('id'));
    $file     = (request('download')) ? request('download') : request('load');
    $download = (request('download')) ? 1 : 0;

    if($files->checkFileStatus($file) === true) {

      if(boolval($download)) {

        $_SESSION['download_file_owner'] = request('id');
        $files->setDownloadLink($file);
      
        if($view) {
          $view->setDownloadState($files->status);
        } else die(json_encode(array('data' => $files->status)));
      
      } else {
      
        require_once('../action/load.php');
        die();
      
      }

    } else die(json_encode(array('error' => 'Unauthorized request')));

  } else if($sess->checkLogin() == false || $sess->checkSession() == false) {

    if(request('login_page')) $view = new ViewHandler();
    $sess->denyLoginAction($view);

  } else {

    $files = new FileModel($user->getSession());
    $disk  = new FileController($files, $view);
    $disk->checkForUpdates();

  }

?>
