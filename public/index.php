<?php

  require('../lib/init/applicationController.php');
  
?>

<!DOCTYPE html>

<html>

  <head>

    <meta charset="utf-8">
    <link rel="stylesheet" href=<?php echo setUrl('../../assets/css/style.css'); ?>>
    <link rel="preload" href=<?php echo setUrl('../../assets/js/redirect.js'); ?> as="script">
    <script type="text/javascript" src=<?php echo setUrl('../../assets/js/redirect.js'); ?>></script>
    <?php echo $view->stylesheet ?>
    <title><?php echo $view->title ?></title>

  </head>

  <body>

    <h1 class="main-page-title"><?php echo $view->title ?></h1>

    <?php
      echo $view->currentView;
    ?>

    <footer>
      <strong>FloppyWare inc.</strong><br>
      <a href='http://www.freepik.com/free-vector/multimedia-simple-icons-set_713131.htm' target="_blank">Icons designed by Freepik</a>
    </footer>

  </body>

</html>
