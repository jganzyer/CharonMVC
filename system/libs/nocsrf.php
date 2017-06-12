<?php
namespace Charon;
define('NOCSRF_VALID', 0);
define('NOCSRF_INVALID', 1);
define('NOCSRF_MISSING', 2);
define('NOCSRF_EXPIRED', 3);
define('NOCSRF_FAKEIP', 4);
class NoCSRF
{
  protected static $prefix = 'nocsrf_';
  protected static $ip_check = true;
  protected static function token()
  {
    return md5(uniqid(mt_rand(), true));
  }
  protected static function session_on(){
    if (session_status() !== PHP_SESSION_NONE) {
      session_start();
    }
  }
  protected static function session_in($name, $value)
  {
    self::session_on();
    if ($value === null)
    {
      unset($_SESSION[self::$prefix.$name]);
    }
    $_SESSION[self::$prefix.$name] = $value;
  }
  protected static function session_out($name)
  {
    self::session_on();
    if (isset($_SESSION[self::$prefix.$name]) === true)
    {
      return $_SESSION[self::$prefix.$name];
    }
    else
    {
      return false;
    }
  }
  public static function ip_check($bool)
  {
    self::$ip_check = $bool;
  }
  public static function prefix($prefix)
  {
    self::$prefix = $prefix;
  }
  public static function make($name)
  {
    $name = preg_replace('/[^a-zA-Z0-9]+/', '', $name);
    $token = base64_encode(time().((self::$ip_check == true) ? \Charon\IP::get() : '').self::token());
    self::session_in($name, $token);
    return $token;
  }
  public static function check($name, $token, $lifetime = null)
  {
    $hash = self::session_out($name);
    if ($hash === false)
    {
      return NOCSRF_MISSING;
    }
    if (strlen($token) !== strlen($hash))
    {
      return NOCSRF_INVALID;
    }
    if (self::$ip_check == true && substr(base64_decode($token), 10, -32) !== \Charon\IP::get())
    {
      return NOCSRF_FAKEIP;
    }
    if ($token !== $hash)
    {
      return NOCSRF_INVALID;
    }
    if (is_int($lifetime) === true && intval(substr(base64_decode($hash),0,10)) + $lifetime < time())
    {
      return NOCSRF_EXPIRED;
    }
    return NOCSRF_VALID;
  }
}
