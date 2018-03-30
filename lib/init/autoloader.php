<?php

  spl_autoload_register(function ($class) {
    if(file_exists("../lib/connection/$class.php")) {
      require_once("../lib/connection/$class.php");
    } else if(file_exists("../lib/filesystem/$class.php")) {
      require_once("../lib/filesystem/$class.php");
    } else if(file_exists("../lib/session/$class.php")) {
      require_once("../lib/session/$class.php");
    } else if(file_exists("../lib/view/$class.php")) {
      require_once("../lib/view/$class.php");
    }
  });

?>
