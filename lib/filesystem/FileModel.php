<?php
/**
 * Public class FileModel
 *
 * Class that manages the users files and uploads.
 */
class FileModel extends DatabaseConnection {

  /*
   * Constants 
   */
  const FILE = 'file';

  const PATH = 'path';

  const MAX_FILE_SIZE = 30000000;

  /*
   * Public properties
   */
  public $filesData;

  public $status;

  public $baseURL;

  public $id;


  /*
   * Constructor
   */
  public function __construct( $sessID ){
    parent::__construct();
    $this->id = $sessID;
    $this->baseURL = $_SERVER['PHP_SELF'] . '/../../';
  }


  /*
   * Public methods
   */
  public function getFileMeta( $fname ) {
      $result = $this->db_send_assoc(array('select' => 'file_name,file_type,file_size,category,mime_type,shared,upload_date,owner_id', 'from' => 'floppyFiles', 'where' => 'owner_id', 'equals' => $this->str($this->id), 'and' => 'file_name', 'is' => $this->str($fname)));
      return $result;
  }

  public function getFolderContent( $filter = null ) {
    $query = array('select' => 'file_name,file_type,file_size,category,mime_type,shared,upload_date,owner_id', 'from' => 'floppyFiles', 'where' => 'owner_id', 'equals' => $this->str($this->id));
    if($filter) { $query['and'] = 'category'; $query['is'] = $this->str($filter); }
    $this->filesData = $this->db_send_assoc($query);
    if(empty($this->filesData)) return;
    if($this->is_assoc($this->filesData)) $this->filesData = array($this->filesData);
    return $this->filesData;
  }

  public function uploadFile( $file ) {
    $file['name'] = str_replace(' ', '_', $file['name']);

    if($file['size'] > self::MAX_FILE_SIZE) {
      $this->status = 'The file was to big to be uploaded';
      return;
    }

    $result = $this->getFileMeta( $file['name'] );

    if(isset($result['file_name'])) {
      $this->status = 'The file already exist in this folder';
      return;
    }

    $data = $this->setFilesData( $file );
    if($data == false) return;

    $result = $this->db_send("INSERT INTO `floppyFiles` VALUES ('".$this->id."', '".$data['file_name']."', '".$data['file_type']."', '".$data['file_size']."', '".$data['category']."', '".$data['mime_type']."', ".$data['file_data'].", 0, NOW())");
    if($result == false) $this->status = 'WOPS! Something went wrong when trying to save the uploaded file';
      else return true;

  }

  public function setDownloadLink( $fname ) {
    if($fname == '') return null;
    $_SESSION['download_file_name'] = $fname;
    $url = str_replace('&download='.$fname,'',$_SERVER['QUERY_STRING']);
    $name = (strlen($fname) > 12) ? substr($fname,0,12).'...' : $fname.'';
    $this->status  = '<a href="' . $this->baseURL . 'action/download.php" class="action_URL" target="_blank" onclick=removeBtn(' . $this->str($this->baseURL) . ',' . $this->str($url) . ');>Download: '.$name.'</a>';
    $this->status .= '<button class="cancel_action" onclick=removeBtn(' . $this->str($this->baseURL) . ',' . $this->str($url) . ');>Cancel</button><div class="cover"></div>';
    return $this->status;
  }

  public function checkFileStatus( $fname ) {
    $result = $this->db_send("SELECT `shared` FROM `floppyFiles` WHERE `file_name`='$fname' AND `owner_id`='".$this->id."' LIMIT 1");
    return boolval($result['shared']);
  }

  public function downloadFile( $fname ) {
    $result = $this->db_send("SELECT * FROM `floppyFiles` WHERE `file_name`='$fname' AND `owner_id`='".$this->id."' LIMIT 1");
    $this->status = '';
    $_SESSION['download_file_name'] = null;
    header('Content-length: '.$result['file_size']);
    header('Content-type: '.$result['mime_type']);
    header('Content-Disposition: attachment; filename='.$result['file_name']);
    echo $result['data'];
  }

  public function getBLOB( $fname ) {
    $result = $this->db_send("SELECT `data` FROM `floppyFiles` WHERE `file_name`='$fname' AND `owner_id`='".$this->id."' LIMIT 1");
    return $result['data'];
  }

  public function setSharedLink( $fname ) {
    $result = $this->db_send("SELECT `shared` FROM `floppyFiles` WHERE `file_name`='$fname' AND `owner_id`='".$this->id."' LIMIT 1");
    $sharable = ($result['shared'] == 0) ? 1 : 0;
    $result = $this->db_send("UPDATE `floppyFiles` SET `shared`='$sharable' WHERE `file_name`='$fname' AND `owner_id`='".$this->id."' LIMIT 1");
    return $sharable;
  }

  public function setDeleteLink( $fname ) {
    if($fname == '') return null;
    $_SESSION['delete_file_name'] = $fname;
    $url = str_replace('&delete='.$fname,'',$_SERVER['QUERY_STRING']);
    $name = (strlen($fname) > 12) ? substr($fname,0,12).'...' : $fname.'';
    $this->status  = '<a href="' . $this->baseURL . 'action/delete.php" class="action_URL" target="_blank" onclick=removeBtn(' . $this->str($this->baseURL) . ',' . $this->str($url) . ');>Delete: '.$name.'</a>';
    $this->status .= '<button class="cancel_action" onclick=removeBtn(' . $this->str($this->baseURL) . ',' . $this->str($url) . ');>Cancel</button><div class="cover"></div>';
    return $this->status;
  }

  public function deleteFile( $fname ) {
    $result = $this->db_send("DELETE FROM `floppyFiles` WHERE `file_name`='$fname' AND `owner_id`='".$this->id."' LIMIT 1");
    $_SESSION['delete_file_name'] = null;
    echo "<script>window.close();</script>";
  }


  /*
   * Private methods
   */
  private function setFilesData( $file ) {

    $status = $this->getUploadStatus($file);
    if($status === false) return false;

    $info = pathinfo($file['name']);
    $data = array();

    $data['file_name'] = $file['name'];
    $data['file_type'] = $info['extension'];
    $data['file_size'] = $file['size'];
    $data['mime_type'] = mime_content_type($file['tmp_name']);
    $data['category'] = explode('/', $data['mime_type'])[0];
    $data['file_data'] = $this->db_xcape(file_get_contents($file['tmp_name']));

    return $data;
  }

  private function getUploadStatus( $file ) {

      switch ($file['error']) {
        case 0:
          $this->status = 'The file has been uploaded successfully';
          return true;
        case 1:
          $this->status = 'The file was to big to be uploaded. The max-size for a file is '.(self::MAX_FILE_SIZE / 1000000).'MB';
          return false;
        case 2:
          $this->status = 'The file was to big to be uploaded. The max-size for a file is '.(self::MAX_FILE_SIZE / 1000000).'MB';
          return false;
        case 3:
          $this->status = 'The file was not fully uploaded';
          return false;
        case 4:
          $this->status = 'No file found for upload';
          return false;
        case 6:
          $this->status = 'Temporary folder missing';
          return false;
        case 7:
          $this->status = 'Failed to write file to disk';
          return false;
        case 8:
          $this->status = 'A unknown error stopped the file upload';
          return false;
      }

  }
  
  private function checkCategory( $type ) {

    switch ($type) {

      case 'image': return $type;

      case 'audio': return $type;

      case 'video': return $type;

      default: return 'other';

    }

  }


}

?>
