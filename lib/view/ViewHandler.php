<?php
/**
 * Public class ViewHandler
 *
 * Class that manages the view of the application.
 */
class ViewHandler {

  /*
   * Public constants
   */
  const LOGIN_TEMP   = 'login_temp.html';

  const DOWNLOAD_TEMP   = 'download_temp.html';

  const PREVIEW_TEMP = 'preview_temp.html';

  const FILE_BROWSER_TEMP = 'file_browser_temp.html';

  const FILE_CONTENT_TEMP = 'file_content_temp.html';

  /*
   * Public properties
   */
  public $currentView = '';

  public $currentMenu = '';

  public $stylesheet  = '';

  public $title       = '';

  /*
   * Private properties
   */
  private $message    = '';

  private $content    = '';

  private $options = array('all','audio','image','video','other');

  /*
   * Constructor
   */
  public function __construct(){

  }

  /*
   * Public methods
   */
  public function addNote($msg) {
    $this->message = '<h2 class="message-box">'.$msg.'</h2>';
  }

  public function setView($vw) {
    $this->currentView = $vw;
  }

  public function login() {
    $this->setCSS(str_replace('html','css',self::LOGIN_TEMP));
    $this->setTitle('FloppyCloud Login');

    $temp = $this->getTemplate(self::LOGIN_TEMP);
    $temp = str_replace('$message_box', $this->message, $temp);
    $temp = str_replace('$login_post_url', $_SERVER['PHP_SELF'], $temp);

    return $temp;
  }

  public function main() {
    $this->setCSS(str_replace('html','css',self::FILE_BROWSER_TEMP));
    $this->setTitle( ucfirst($_SESSION['sessionUser']) . '\'s FloppyCloud');

    $temp = $this->getTemplate(self::FILE_BROWSER_TEMP);
    $temp = str_replace('$server_self', $_SERVER['PHP_SELF'], $temp);
    $temp = str_replace('$file_browser_menu', $this->addMenuButtons(), $temp);
    $temp = str_replace('$message_box', $this->message, $temp);
    $temp = str_replace('$main_content', $this->content, $temp);

    return $temp;
  }

  public function download() {
    $this->setCSS(str_replace('html','css',self::DOWNLOAD_TEMP));
    $this->setTitle('FloppyCloud Download');

    $temp = $this->getTemplate(self::DOWNLOAD_TEMP);
    $temp = str_replace('$message_box', $this->message, $temp);

    return $temp;
  }

  public function addContent($file) {

    if(!isset($file['file_name']) || strlen($file['file_name']) == 0) return;
    if($this->currentMenu != 'all' && $file['category'] != str_replace(' files','',$this->currentMenu)) return;

    $filter = ($this->currentMenu != '') ? 'category='.str_replace(' ','_',$this->currentMenu).'&' : '';
    $link   = (boolval($file['shared'])) ? '<p class="share-link">http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?id=' . $file['owner_id'] . '&download=' . $file['file_name'] . '</p>' : ''; 

    $temp = $this->getTemplate(self::FILE_CONTENT_TEMP);
    $temp = str_replace('$file_name', $file['file_name'], $temp);
    $temp = str_replace('$file_icon', $file['icon'], $temp);
    $temp = str_replace('$file_size', $file['size_converted'], $temp);
    $temp = str_replace('$share_status', $file['shared'], $temp);
    $temp = str_replace('$share_link', $link, $temp);
    $temp = str_replace('$server_self', $_SERVER['PHP_SELF'], $temp);
    $temp = str_replace('$filter', $filter, $temp);

    $this->content .= $temp;

  }

  public function preview($file) {
    $temp = $this->getTemplate(self::PREVIEW_TEMP);
    $temp = str_replace('$html_tag', $file['html_tag'], $temp);
    $temp = str_replace('$size', $file['css_size'], $temp);
    $temp = str_replace('$mime_type', $file['mime_type'], $temp);

    return $temp;
  }

  public function setLoginState($msg) {
    $this->addNote($msg);
    $this->setView($this->login());
  }

  public function setDownloadState($msg) {
    $this->addNote($msg);
    $this->setView($this->download());
  }

  public function getTemplate($temp) {
    return $temp = file_get_contents('../assets/templates/'.$temp);
  }

  public function setCSS($sheet, $keep = false) {
    if($keep == false) $this->stylesheet = '';
    $this->stylesheet .= '<link rel="stylesheet" href="../assets/css/'.$sheet.'">';
  }

  public function setTitle($title) {
    $this->title = $title;
  }

  public function resetContent() {
    $this->content = '';
  }

  public function resetNote() {
    $this->message = '';
  }

  /*
   * Private methods
   */
  private function addMenuButtons() {
    $menu    = '';
    for ($i=0; $i < count($this->options); $i++) {
      $disabled = ($this->options[$i] == str_replace(' files','',$this->currentMenu)) ? ' disabled' : '';
      $menu .= '<input type="submit" name="category" value="' . $this->options[$i] . '"'.$disabled.'>';
    }
    $menu .= '<input type="submit" name="logout" value="logout">';
    return $menu;
  }

}

?>
