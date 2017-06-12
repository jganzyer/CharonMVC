<?php
namespace Charon;
class Captcha
{
  protected static $prefix = 'captcha_';
  protected static $ip_check = true;
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
  public static function prefix($prefix)
  {
    self::$prefix = $prefix;
  }
  public static function make($name, $numchars = 4, $case_sensitive = false, $height = 130, $difficulty = \SVGCaptcha::EASY)
  {
    $name = preg_replace('/[^a-zA-Z0-9]+/', '', $name);
    $obj = \SVGCaptcha::getInstance($numchars, $height, $height, $difficulty);
    $obj = $obj->generate();
    $obj[2] = $case_sensitive;
    self::session_in($name, $obj);
    return $obj;
  }
  public static function check($name, $value)
  {
    $system = self::session_out($name);
    if ($system === false)
    {
      return false;
    }
    if ($system[2] == true)
    {
      return ($system[0] === $value) ? true : false;
    }
    return (strtolower($system[0]) === strtolower($value));
  }
}
