<?php
class Service
{

  private static $flashs = [];

  public static function json_encode(array $array, $flag = null)
  {
    return json_encode($array, $flag);
  }

  public static function json_decode(array $json, $array = true)
  {
    return json_decode($json, $array);
  }

  public static function csv_encode(array $array, $delimeter = ',')
  {
    return implode($delimeter, $array);
  }

  public static function csv_decode($csv, $delimeter = ',')
  {
    return str_getcsv($csv, $delimeter);
  }

  public static function sflash($key, $value = null)
  {
    self::$flashs[$key] = $value;
  }

  public static function session_start()
  {
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }
  }

  public static function gflash($key, $next = false)
  {
    if (isset(self::$flashs[$key]) === true)
    {
      $v = self::$flashs[$key];
      if ($next == false) {
        unset(self::$flashs[$key]);
      }
      return $v;
    }
    else
    {
      return false;
    }
  }
}
