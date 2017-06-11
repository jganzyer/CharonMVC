<?php
namespace Charon;
class License
{
  public static function random($lenght, $chars)
  {
    $cl = strlen($chars);
    $o = '';
    for ($i=0; $i < $lenght; $i++)
    {
      $o .= $chars[rand(0, $cl -1)];
    }
    return $o;
  }
  public static function generate($pattern, $delimeter = '#', $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ')
  {
    foreach (explode($delimeter, $pattern) as $n)
    {
      $pattern = preg_replace('/'.preg_quote($delimeter,'/').'/', self::random(1, $chars), $pattern, 1);
    }
    return $pattern;
  }
  public static function check($pattern, $license, $delimeter = '#')
  {
    return (bool)preg_match_all('#^'.str_replace($delimeter, '(\w)', $pattern).'$#', $license);
  }
}
