<?php

/**
 * Public class sessionController
 *
 * Class that supervise the login, signup and session controlling.
 */
 class SessionController {

   /*
    * Constants 
    */
    const DEFAULT_LOGIN_MESSAGE = 'Please enter username and password';

    const USER_SIGNED_OUT       = 'User has been signed out';

    const INVALID_CREDENTIALS   = 'Invalid username or password';

    const USERNAME_TAKEN        = 'Username was already taken, please try again';

   /*
    * Public properties
    */
   public $errorMessage;


   /*
    * Private properties
    */
   private $user;

   private $view;

   private $deny;


   /*
    * Constructor
    */
   public function __construct(UserModel $user, ViewHandler $view = null) {
     $this->user = $user;
     $this->view = $view;
     if($view) {
       $this->view->setLoginState(self::DEFAULT_LOGIN_MESSAGE);
       $this->deny = 'deniedLoginView';
      } else {
        $this->deny = 'deniedLoginAPI';
      }
   }

   /*
    * Functions 
    */
   function deniedLoginView( $view = null ) {
    $this->view->setLoginState($this->errorMessage);
    $this->user->destroySession();
   }

   function deniedLoginAPI( $view = null ) {
    $res = array('error' => $this->errorMessage);
    if($view) {
      $view->addNote($this->errorMessage);
      $res['data'] = $view->login();
    }
    die(json_encode($res));
   }


   /*
    * Public methods
    */
   public function checkLogin() {


      if(request('logout')) {
        $this->user->destroySession();
        $this->errorMessage = self::USER_SIGNED_OUT;
        return false;
      }
     
     if($this->user->getSession() != null) return true;

       if(isset($_POST['login'])) {
         $username = $_POST['username'];
         $password = $_POST['password'];

         if($this->user->getUser($username,$password) == null) {
           $this->errorMessage = self::INVALID_CREDENTIALS;
           return false;
         }
       }

       if(isset($_POST['register'])) {
         $username = $_POST['username'];
         $password = password_hash($_POST['password'],PASSWORD_DEFAULT);

         if($this->user->addUser($username,$password) == null) {
           $this->errorMessage = self::USERNAME_TAKEN;
           return false;
         }
       }

       return true;

   }

   public function checkSession() {
     if($this->user->getSession() == null || request('logout')) {
       $this->errorMessage = self::DEFAULT_LOGIN_MESSAGE;
       return false;
     }
     return true;
   }

   public function denyLoginAction( $view = null ) {
     $this->{$this->deny}($view);
   }

 }

?>
