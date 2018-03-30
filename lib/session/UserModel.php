<?php

/**
 * Public class UserModel
 *
 * Class that manages the process of login and registration process.
 */
class UserModel extends DatabaseConnection {

  /*
   * Constructor
   */
  public function __construct(){
    parent::__construct();
  }

  /*
   * Public methods
   */
  public function addUser($userNm,$userPw) {

    $result = $this->db_send_assoc(array('select' => 'username', 'from' => 'floppyUsers', 'where' => 'username', 'equals' => $this->str($userNm)));
    if($result != false || $result != null || $result != "") {
      return null;
    }

    $result = $this->db_send_assoc(array('insert' => 'floppyUsers', 'keys' => '`username`, `password`', 'values' => array($this->str($userNm),$this->str($userPw))));
    if($result == true) {
      $result = $this->db_send_assoc(array('select' => 'id,username', 'from' => 'floppyUsers', 'where' => 'username', 'equals' => $this->str($userNm)));
    } else {
      return null;
    }

    $this->setSession($result);
    return $result['id'];
  }

  public function getUser($userNm,$userPw) {
    $escape = 'db_xcape';
    $result = $this->db_send_assoc(array('select' => '*','from' => 'floppyUsers','where' => 'username','equals' => $this->str($userNm)));

    if($result === null) {
      return null;
    }

    if(password_verify($userPw,$result["password"])) {
      $this->setSession($result);
      return $result["id"];
    } else return null;

  }

  public function getSession() {
    if(empty($_SESSION['sessionID'])) return null;
    $sessID = $_SESSION['sessionID'];
    $result = $this->db_send_assoc(array('select' => 'username,id', 'from' => 'floppyUsers', 'where' => 'id', 'equals' => $this->str($sessID)));
    if($result['username'] != $_SESSION['sessionUser']) return null;
    return $result['id'];
  }

  public function destroySession() {
    $_SESSION['sessionID'] = null;
    $_SESSION['sessionUser'] = null;
    session_destroy();
  }

  
  /*
   * Private methods
   */

  private function setSession($sessData) {
    $_SESSION['sessionID']   = $sessData['id'];
    $_SESSION['sessionUser'] = $sessData['username'];
  }

}

?>
