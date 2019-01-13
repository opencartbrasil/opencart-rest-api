<?php
if ( basename($_SERVER["PHP_SELF"]) == basename(__FILE__) ) {
  header('Content-Type', true, 401);
  exit();
}

// Config
define('RESTRICT_IP', false);
define('SESSION_LOG', false);

if (ini_get('magic_quotes_gpc')) {
  function clean($data) {
    if (is_array($data)) {
      foreach ($data as $key => $value) {
        $data[clean($key)] = clean($value);
      }
    } else {
      $data = stripslashes($data);
    }
    return $data;
  }
  $_GET = clean($_GET);
  $_POST = clean($_POST);
  $_COOKIE = clean($_COOKIE);
}

if (is_file('config.php')) {
  require_once('config.php');
}

class Database {
  protected static $db;

  private function __construct() {
    try {
      self::$db = new PDO('mysql:host='.DB_HOSTNAME.';dbname='.DB_DATABASE.'', DB_USERNAME, DB_PASSWORD);
      self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      self::$db->exec('SET NAMES utf8');
    } catch (PDOException $e) {
        print "Error Database: " . $e->getMessage();
        die();
    }
  }

  public static function connection() {
    if (!self::$db) 
      new Database();
      return self::$db;
  }

  public static function getHeaders() {
    if(function_exists('apache_request_headers')) {
      return apache_request_headers();
    }
    $headers = array();
    $keys = preg_grep('{^HTTP_}i', array_keys($_SERVER));
    foreach($keys as $val) {
      $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($val, 5)))));
      $headers[$key] = $_SERVER[$val];
    }
    return $headers;
  }
}

class Session {
  public $data = array();

  public function __construct($handler = '') {
    if ($handler) {
      session_set_save_handler(
        array($handler, 'open'),
        array($handler, 'close'),
        array($handler, 'read'),
        array($handler, 'write'),
        array($handler, 'destroy'),
        array($handler, 'gc')
      );
    }
  }

  public function getId() {
    return session_id();
  }

  public function start($session_id = '', $key = 'default') {
    if (!session_id()) {
      ini_set('session.use_only_cookies', 'Off');
      ini_set('session.use_cookies', 'On');
      ini_set('session.use_trans_sid', 'Off');
      ini_set('session.cookie_httponly', 'On');
      if ($session_id) {
        session_id($session_id);
      }
      if (isset($_COOKIE[session_name()]) && !preg_match('/^[a-zA-Z0-9,\-]{22,52}$/', $_COOKIE[session_name()])) {
        exit('Error: Invalid session ID!');
      }
      session_set_cookie_params(0, '/');
      session_start();
    }
    if (!isset($_SESSION[$key])) {
      $_SESSION[$key] = array();
    }
    $this->data =& $_SESSION[$key];
    return true;
  }
}

$headers = Database::getHeaders();
foreach ($headers as $name => $value) {
  $name_lower = strtolower($name);
  if ($name_lower == 'key') {
    $key = $value;
  }
}

if (isset($key) && ctype_alnum($key)) {
  $conn = Database::connection();
  $sql = $conn->prepare("SELECT api_id FROM `" . DB_PREFIX . "api` WHERE `key` = :key AND `status` = '1'");
  $sql->bindParam(':key', $key, PDO::PARAM_STR);
  $sql->execute();
  $api_info = $sql->fetch(PDO::FETCH_OBJ);
  if (isset($api_info->api_id) && ctype_digit($api_info->api_id)) {
    if (RESTRICT_IP) {
      $sql = $conn->prepare("SELECT ip FROM `" . DB_PREFIX . "api_ip` WHERE `api_id` = :api_id AND `ip` = :ip");
      $sql->bindParam(':api_id', $api_info->api_id, PDO::PARAM_INT);
      $sql->bindParam(':ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
      $sql->execute();
      $ip_info = $sql->fetch(PDO::FETCH_OBJ);
      if (!isset($ip_info->ip)) {
        header('Content-Type: application/json; charset=utf-8', true, 401);
        exit(json_encode(array('Conection Error' => 'Your IP '. $_SERVER['REMOTE_ADDR'] .' is not allowed to access this API!')));
      }
    }
    if (SESSION_LOG) {
      $session_name = 'temp_session_' . uniqid();
      $session = new Session();
      $session->start($session->getId(), $session_name);
      $session_id = $session->getId();
      require_once(DIR_SYSTEM . 'helper/general.php');
      $token = token(32);
      $sql = $conn->prepare("INSERT INTO `" . DB_PREFIX . "api_session` SET `api_id` = :api_id, `token` = :token, `session_name` = :session_name, `session_id` = :session_id, `ip` = :ip, `date_added` = NOW(), `date_modified` = NOW()");
      $sql->bindParam(':api_id', $api_info->api_id, PDO::PARAM_INT);
      $sql->bindParam(':token', $token, PDO::PARAM_STR);
      $sql->bindParam(':session_name', $session_name, PDO::PARAM_STR);
      $sql->bindParam(':session_id', $session_id, PDO::PARAM_STR);
      $sql->bindParam(':ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
      $sql->execute();
    }
    $api = new PHP_CRUD_API(array(
                'dbengine'  =>  'MySQL',
                'hostname'  =>  DB_HOSTNAME,
                'username'  =>  DB_USERNAME,
                'password'  =>  DB_PASSWORD,
                'database'  =>  DB_DATABASE,
                'charset'   =>  'utf8'
              ));
    $api->executeCommand();
  } else {
    header('Content-Type: application/json; charset=utf-8', true, 401);
    exit(json_encode(array('API Error' => 'Incorrect Key!')));
  }
} else {
  header('Content-Type: application/json; charset=utf-8', true, 401);
  exit(json_encode(array('API Error' => 'Key not found!')));
}
