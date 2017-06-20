<?php
class Route
{
  private static $app;
  private static $base = '';
  public static function init($instance)
  {
    self::$app = $instance;
  }
  public static function call($var, $params = [], $vthis = null, $delimeter = '.')
  {
    return self::$app->call($var, $params, $vthis, $delimeter);
  }
  public static function get($pattern, $callback)
  {
    return self::map('get', $pattern, $callback);
  }
  public static function post($pattern, $callback)
  {
    return self::map('post', $pattern, $callback);
  }
  public static function put($pattern, $callback)
  {
    return self::map('put', $pattern, $callback);
  }
  public static function delete($pattern, $callback)
  {
    return self::map('delete', $pattern, $callback);
  }
  public static function any($pattern, $callback)
  {
    return self::map('any', $pattern, $callback);
  }
  public static function map($methods, $pattern, $callback)
  {
    self::$app->map($methods, self::$base.$pattern, $callback);
    return new static;
  }
  public static function uri($name, $params = [])
  {
    self::$app->uri($name, $params);
  }
  public static function name($name)
  {
    self::$app->name($name);
    return new static;
  }
  public static function redirect($name, $params = [], $timeout = 0, $statusCode = 302)
  {
    self::$app->redirect($name, $params, $timeout, $statusCode);
  }
  public static function set404($callback)
  {
    self::$app->set404($callback);
    return new static;
  }
  public static function middleware($callback)
  {
    self::$app->middleware($callback);
    return new static;
  }
  public static function group($base, $fn)
  {
    self::$base = $base;
    call_user_func($fn);
    self::$base = '';
  }
}
