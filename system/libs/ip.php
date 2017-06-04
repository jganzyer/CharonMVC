<?php
namespace Charon;
class IP
{
  public static function random()
  {
    return mt_rand(0,255).".".mt_rand(0,255).".".mt_rand(0,255).".".mt_rand(0,255);
  }
  public static function get()
  {
    return getenv('HTTP_CLIENT_IP')?: getenv('HTTP_X_FORWARDED_FOR')?: getenv('HTTP_X_FORWARDED')?: getenv('HTTP_FORWARDED_FOR')?: getenv('HTTP_FORWARDED')?: getenv('REMOTE_ADDR')?: 'UNKNOWN';
  }
  public static function check($ip)
  {
    return filter_var($ip, 275);
  }
  public static function check_white_list(array $ips, $ip = null)
  {
    if (!isset($ip))
    {
      $ip = self::get();
    }
    foreach ($ips as $ipp)
    {
      if ($ipp === $ip)
      {
        return true;
      }
    }
    return false;
  }
  public static function check_black_list(array $ips, $ip = null)
  {
    if (!isset($ip))
    {
      $ip = self::get();
    }
    foreach ($ips as $ipp)
    {
      if ($ipp === $ip)
      {
        return false;
      }
    }
    return true;
  }
}
