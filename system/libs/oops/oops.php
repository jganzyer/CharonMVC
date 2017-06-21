<?php
class oops
{
  private static $errors = [];
  private static $handlers = [];
  private static $active = false;
  private static $ignores = [];

  public static function push($message, $type = 1, $root = false)
  {
    if (self::is_ignored($type) === false)
    {
      $db = debug_backtrace(2);
      if ($root == true)
      {
        $db = $db[0];
      } else {
        array_shift($db);
        $db = $db[0];
      }
      $file = $db['file'];
      $line = $db['line'];

      array_push(self::$errors,[
        'type' => $type,
        'message' => $message,
        'file' => $file,
        'line' => $line
      ]);
    }
    if ($type === 1 && self::is_ignored(1) === false)
    {
      self::response();
    }
  }

  public static function _push($type, $message = null, $file = null, $line = null)
  {
    if (self::is_ignored($type) === false)
    {
      array_push(self::$errors,[
        'type' => $type,
        'message' => $message,
        'file' => $file,
        'line' => $line
      ]);
    }
    if ($type === 1 && self::is_ignored(1) === false)
    {
      self::response();
    }
  }

  public static function _handle()
  {
    $error = error_get_last();
    $type = $error['type'];
    if (($type === 1 || $type === 16 || $type === 64 || $type === 256 || $type === 4096) && self::is_ignored($type) === false)
    {
      array_push(self::$errors,$error);
      self::response();
    }
  }

  public static function ignore($types)
  {
    if ($types === E_ALL)
    {
      self::$active = true;
    }
    $type = gettype($types);
    if ($type === 'array')
    {
      self::$ignores = $types;
    } else {
      self::$ignores = [$types];
    }
  }

  private static function is_ignored($type)
  {
    return in_array($type, self::$ignores);
  }

  public static function report()
  {
    return self::$errors;
  }

  public static function add_handler($handler)
  {
    self::$handlers[] = $handler;
  }

  public static function response()
  {
    if (self::$active === false && empty(self::$errors) === false && empty(self::$handlers) === false)
    {
      foreach (self::$handlers as $handler)
      {
        new $handler(self::$errors);
      }
      self::$active = true;
    }
  }

  public static function init()
  {
    error_reporting(E_PARSE);
    register_shutdown_function('\oops::_handle');
    set_error_handler('\oops::_push');
  }
}
