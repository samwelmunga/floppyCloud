<?php

function request( $param ) {
  $result = null;
  if(isset($_GET[$param])) $result = $_GET[$param];
  else if(isset($_POST[$param])) $result = $_POST[$param];
  return $result;
}

?>
