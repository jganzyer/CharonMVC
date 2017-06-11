<?php
class Load
{
  public static function helper($name)
  {
    $path = HELPERS_DIR.$name.'.php';
    if (file_exists($path) === true) {
      require_once($path);
      return true;
    } else {
      return false;
    }
  }
  public static function view($name)
  {

  }
  public static function model($name)
  {

  }
  public static function config($file)
  {
    return \Config::load($file);
  }
  public static function library($class, $params = null, $name = null)
  {
    $e = explode('.', $class);
    $r = implode(DS,$e);
    if (Puck::exists($r, false, DS, $ro))
    {
      Controller::$_libs[strtolower(($name === null) ? end($e) : $name)] = new $ro(...$params);
      return true;
    }
    else
    {
      return false;
    }
  }
}
