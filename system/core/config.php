<?php
class Config
{
  private static $config = [];
  public static function load($file)
  {
    $loc = '.\app\config\\'.$file;
    if (file_exists($loc)) {
      switch(pathinfo($loc, PATHINFO_EXTENSION))
      {
        case 'php':
          require_once($loc);
          if (isset($config)) {
            self::$config = array_merge(self::$config, $config);
          } else {
            die('config not set');
          }
          break;
        case 'xml':
          self::$config = array_merge(self::$config,json_decode(json_encode((array)simplexml_load_string(file_get_contents($loc))),1));
          break;
        case 'json':
          self::$config = array_merge(self::$config,json_decode(file_get_contents($loc), true));
          break;
        case 'ini':
          self::$config = array_merge(self::$config,parse_ini_file($loc,true));
          break;
      }
    } else {
      die("dosya yoh");
    }
  }
  public static function get($key = null)
  {
    return self::$config[$key];
  }
}
