<?php

/**
 * Public class FileController
 *
 * Class that supervise navigation and transfers on the filesystem
 */
class FileController {

  const DOWNLOAD_FAILED = 'Download failed';

  const SHARE_FAILED    = 'Failed to change the files share-status';

  const DELETE_FAILED   = 'Failed to delete file';

  /*
   * Private properties
   */

  private $data;

  private $files;

  private $view;
  
  private $baseURL;

  private $updateApplicationState;


    /*
     * Constructor
     */
    public function __construct(FileModel $files, ViewHandler $view = null) {

      $this->files   = $files;
      $this->view    = $view;

      if($this->view) {
        $this->updateApplicationState = 'updateApplicationView';
      } else {
        $this->updateApplicationState = 'returnAPIData';
      }

    }


    /*
     * Functions
     */
    function updateApplicationView() {

      $this->files->getFolderContent();
      $this->view->addNote($this->files->status);

      for($i = 0; $i < count($this->files->filesData); $i++) {
        $file = $this->updateData($this->files->filesData[$i]);
        $this->view->addContent($file);
      }

      $this->view->setView($this->view->main());

    }
    
    function returnAPIData() {

      if($this->data === null) {
        die(json_encode(array('error' => $this->files->status)));
      } else {
        die(json_encode(array('data' => $this->data)));
      }

    }


   /*
    * Public methods
    */
   public function checkForUpdates() {

      $this->files->status = '+ Upload new file';

      if(request('category')) {
        $this->view->currentMenu = str_replace('_',' ',request('category'));
      } else {
        $this->view->currentMenu = 'all';
      }

      if(request('download')) {
        $this->downloadHandler();
      } else if(request('load')) {
        $this->loadHandler();
      } else if(request('share')) {
        $this->shareHandler();
      } else if(request('delete')) {
        $this->deleteHandler();
      } else if(isset($_POST['upload_file'])) {
        $this->files->uploadFile($_FILES['file_upload']);
      } else if(request('category')) {
        $this->data = $this->files->getFolderContent(request('category'));
      } else  $this->data = $this->files->getFolderContent();

       $this->{$this->updateApplicationState}();

   }

    public function updateData($file) {
    
      $file['category'] = explode('/', $file['mime_type'])[0];
      $file['category'] = $this->checkCategory($file['category']);
      $file['size_converted'] = $this->convertFileSize($file['file_size']);
      $file['icon'] = ($file['category'] != 'image') ? '../assets/icons/'.$file['category'].'.jpg' : 'data:'.$file['mime_type'].';base64,'.base64_encode($this->files->getBLOB($file['file_name']));
      $file['html_tag'] = $this->getHTMLTag($file['category'], explode('/', $file['mime_type'])[1]);
      return $file;
      
    }


   /*
    * Private methods
    */

    private function downloadHandler() {
      $this->data = $this->files->setDownloadLink(request('download'));
      if($this->data == false) {
        $this->files->status = self::DOWNLOAD_FAILED;
        $this->data = null;
      }
    }

    private function loadHandler() {
      require_once('../action/load.php');
    }

    private function shareHandler() {

      $status = $this->files->setSharedLink(request('share'));
      
      if($this->files->affected == false) {
        $this->files->status = self::SHARE_FAILED;
        $this->data = null;
      } else if($status == true) {
        $this->data = $_SERVER['PHP_SELF'] . '?id=' . $this->files->id . '&download=' . request('share');
      } else $this->data = boolval($status);
    
    }

    private function deleteHandler() {
      $this->data = $this->files->setDeleteLink(request('delete'));
      if($this->data == false) {
        $this->files->status = self::DELETE_FAILED;
        $this->data = null;
      }
    }

    private function convertFileSize($size) {
      return ((int)$size < 1000000) ? round((int)$size / 1000).'kB' : round((int)$size / 1000000, 2).'MB';
    }

    private function getHTMLTag($type) {

      switch ($type) {

        case 'image': return 'img';

        case 'audio': return 'audio';

        case 'video': return 'video';

        default: return 'embed';
      }

    }

    private function checkCategory($type) {

      switch ($type) {

        case 'image': return $type;

        case 'audio': return $type;

        case 'video': return $type;

        default: return 'other';

      }

    }

}


 ?>
