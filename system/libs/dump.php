<?php
namespace Charon;
class Dump
{
  private static $h = '';
  private static $f = true;
  public static function dump($var)
  {
    if (self::$f === true) {
      echo '<style>.cdump{color:#e6e9ed!important;background-color:#2F3640!important;padding:16px 20px 0!important;font-size:12px!important;line-height:17px!important;border-radius:2px!important}.cdump-type{color:#4fc1e9!important}.cdump-key{color:#ffce54!important}.cdump-value{color:#a0d468!important}</style>';
      self::$f = false;
    }
    echo '<pre class="cdump">';
    self::type2function($var);
    self::$h .= PHP_EOL.'</pre>';
    echo self::$h;
  }
  private static function type2function($var)
  {
    $type = gettype($var);
    if ($type === "array")
    {
      self::array2html($var);
    }
    else if ($type === "string")
    {
      self::string2html($var);
    }
    else if ($type === "object")
    {
      self::object2html($var);
    }
    else
    {
      self::all2html($var, $type);
    }
  }
  private static function var2key($var)
  {
  }
  private static $tab = 0;
  private static function tab()
  {
    $o = '';
    for ($x = 1; $x <= self::$tab; $x++) {
      $o .= '  ';
    }
    return $o;
  }
  private static function array2html($var)
  {
    self::$tab += 1;
    self::$h .= '<span class="cdump-type">array('.count($var).')</span> {'.PHP_EOL;
    foreach ((array)$var as $vkey => $vvalue)
    {
      if (gettype($vkey) === "string")
      {
        $vkey = '"'.$vkey.'"';
      }
      self::$h .= self::tab().'<span class="cdump-key">['.$vkey.']</span> => ';
      self::type2function($vvalue, $vkey);
    }
    self::$tab -= 1;
    self::$h .= self::tab().'}'.PHP_EOL;
  }
  private static function string2html($var)
  {
    self::$h .= '<span class="cdump-type">string('.strlen($var).')</span> <span class="cdump-value">"'.$var.'"</span>'.PHP_EOL;
  }
  private static function object2html($var)
  {
    self::$h .= '<span class="cdump-type">object(Closure)##</span> <span class="cdump-value">"'.'$var'.'"</span>'.PHP_EOL;
  }
  private static function all2html($var, $type)
  {
    self::$h .= '<span class="cdump-type">'.$type.'(<span class="cdump-value">'.$var.'</span>)</span>'.PHP_EOL;
  }
}
