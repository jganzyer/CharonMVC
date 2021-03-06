<?php
class Config implements \ArrayAccess
{
  private static $config = [];
  public static function load($file)
  {
    $loc = CONFIG_DIR.$file;
    if (file_exists($loc))
    {
      switch(pathinfo($loc, PATHINFO_EXTENSION))
      {
        case 'php':
          require_once($loc);
          if (isset($config)) {
            self::$config = array_merge(self::$config, $config);
          }
          else
          {
            \oops::push('$config variable doesn\'t exists');
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
      return true;
    } else {
      return false;
    }
  }

  public static function &get($key = null)
  {
    return self::$config[$key];
  }

  public function offsetSet($offset, $value)
  {
    if ($offset === null)
    {
      self::$config[] = $value;
    }
    else
    {
      self::$config[$offset] = $value;
    }
  }

  public function offsetExists($offset)
  {
    return isset(self::$config[$offset]);
  }

  public function offsetUnset($offset)
  {
    unset(self::$config[$offset]);
  }

  public function &offsetGet($offset)
  {
    return self::get($offset);
  }
}
