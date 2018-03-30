<?php
/**
 * Public class DatabaseConnection
 *
 * Class that manages the connection and communication with the database.
 */
class DatabaseConnection {

    /*
     * Private static properties
     */
    private static $connection = null;
    
    /*
     * Public properties 
     */
    public $affected;

    /*
     * Constructor
     */
    public function __construct(){
      self::$connection = $this->db_con();
    }


    /*
     * Public methods
     */
    public function db_send_assoc($data) {

      if(isset($data['select'])) {
        if($data['select'] == '*') $query = "SELECT *";
          else {
            $section = explode(',', $data['select']);
            for ($i=0; $i < count($section); $i++) {
              $section[$i] = '`'.$section[$i].'`';
            }
            $section = implode(',', $section);
            $query = "SELECT ".$section;
          };
        $query .= " FROM `".$data['from']."`";
      } else if(isset($data['insert'])) {
          $query = "INSERT INTO `".$data['insert']."`";
          if(isset($data['keys'])) $query .= " (".$data['keys'].")";
          if(isset($data['values'])) {
            if(gettype($data['values']) == 'array') {
              $query .= " VALUES (";
              $query .= implode(', ',$data['values']);
              $query .= ')';
            }
            else $query .= " VALUES (".$data['values'].")";
          }
      } else if(isset($data['update'])) {
        $query = "UPDATE `" . $data['update'] . "`";
        $query .= "SET " . $data['set'];
      }

      $query = str_replace('NOW', 'NOW()', $query);

      if(!isset($data['where'])) return $this->db_send($query);

      if(isset($data['in'])) $query .= " WHERE EXISTS (SELECT `".$data['equals']."` FROM `".$data['in']."` WHERE `".$data['where']."` = ".$data['equals']."";

      if(isset($data['in']) && !isset($data['and'])) {
        $query .= ")";
        return $this->db_send($query);
      } else if(isset($data['where'])) {
        $query .= " WHERE `".$data['where']."` = ".$data['equals'];
      }

      if(isset($data['and'])) $query .= " AND `".$data['and']."` = ".$data['is']."";

      if(isset($data['in'])) $query .= ")";

      return $this->db_send($query);

    }

    public function db_send($query) {

        $result = $this->db_query($query);

        if($result === true) {
            return true;
        }

        if ($result === false || $result === null && mysqli_error($this->connection) != "") {
          $result = null;
          return $result;
        }

        if(mysqli_num_rows($result) < 2) {
          return  mysqli_fetch_assoc($result);
        } else {
            for ($array = array();$row = mysqli_fetch_assoc($result);$array[] = $row);
            return $array;
        }

    }

    public function db_xcape($string) {
        $con = $this->db_con();
        return "'" . mysqli_real_escape_string($con,$string) . "'";
    }

    public function str($string){
        return "'" . $string . "'";
    }

    public function is_assoc($arr){
        foreach(array_keys($arr) as $key) {if (!is_int($key)) return true;}
        return false;
    }


    /*
     * Private methods
     */
    private function db_con(){

    	if (self::$connection == null) {
            define('HOST_SERVER','localhost');
            define('HOST_ADMIN','root');
            define('HOST_PW','root');
            define('DATABASE','floppyShare');
            self::$connection = mysqli_connect(HOST_SERVER, HOST_ADMIN, HOST_PW, DATABASE);
    		    mysqli_set_charset(self::$connection,'utf8');
    	} else return self::$connection;

    	if(self::$connection === false) {
    	  die("connection failed: " . mysqli_connect_error());
    	}

    	return self::$connection;
    }

    private function db_query($query) {
        $this->affected = false;
        $con = $this->db_con();
        $result = mysqli_query($con,$query);
        $this->affected = boolval(mysqli_affected_rows($con));
        return $result;
    }

}

?>
