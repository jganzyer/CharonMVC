<?php
namespace Charon;
class oops
{
  private static $errors = [];
  private static $handler = [];
  private static $active = false;

  public static function push($type, $message, $file = null, $line = null)
  {
    $db = debug_backtrace(2);
    array_pop($db);
    d(end($db));
    d($message);
    array_push(self::$errors,[
      'type' => $type,
      'message' => $message,
      'file' => $file,
      'line' => $line
    ]);
    if ($type === 1)
    {
      self::response();
    }
  }

  public static function handle()
  {
    $error = error_get_last();
    if ($error['type'] === 1)
    {
      array_push(self::$errors,$error);
      self::response();
    }
    return true;
  }

  public static function response()
  {
    d(self::$errors);
  }

  public static function init($exceptions = null)
  {
    error_reporting(0);
    register_shutdown_function('\Charon\oops::handle');
    set_error_handler('\Charon\oops::push');
  }
}
