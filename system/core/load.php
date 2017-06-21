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
  public static function view($name, $params = [])
  {
    $f = VIEW_DIR.$name.'.layout.html';
    if (file_exists($f) === false)
    {
      \oops::push('File **'.str_replace(ROOT,'..\\',$f).'** doesn\'t exists');
    }
    $c = file_get_contents($f);
    foreach ((array)$params as $key => $value)
    {
      $c = str_replace('{!!'.$key.'!!}', $value, $c);
      $c = str_replace('{{'.$key.'}}', xss_clean($value), $c);
      $c = str_replace('{{--'.$key.'--}}', '<!--'.xss_clean($value).'-->', $c);
    }
    return $c;
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
      Controller::$_libs[strtolower(($name === null) ? end($e) : $name)] = new stdClass();
      return false;
    }
  }
}
