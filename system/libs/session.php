<?php
class Session
{
  public static function start()
  {
    if (session_status() !== PHP_SESSION_NONE) {
      session_start();
      return true;
    } else {
      return false;
    }
  }
  public static function get($name = null)
  {
    if ($name === null) {
      return $_SESSION;
    }
    return $_SESSION[$name];
  }
  public static function set($name, $value)
  {
    $_SESSION[$name] = $value;
  }
  public static function has($name)
  {
    return isset($_SESSION[$name]);
  }
  public static function push($value)
  {
    $_SESSION[] = $value;
  }
  public static function delete($name)
  {
    unset($_SESSION[$name]);
  }
  public static function destroy()
  {
    if (session_status() === PHP_SESSION_ACTIVE) {
      session_destroy();
    }
    $_SESSION = [];
  }
}
