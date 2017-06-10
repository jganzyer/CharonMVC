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
    if (class_exists($class))
    {
      Controller::$_libs[($name === null) ? $class : $name] = new $class(...$params);
      var_dump(Controller::$_libs);
      return true;
    }
    else
    {
      return false;
    }
  }
}
