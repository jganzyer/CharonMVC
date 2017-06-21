<?php
namespace Charon;
class Dump
{
  private static $h = '';
  private static $f = true;
  private static $tab = 0;

  public static function dump($var)
  {
    if (self::$f === true)
    {
      echo '<style>.cdump{overflow:auto!important;color:#e6e9ed!important;background-color:#2F3640!important;padding:16px 20px 0!important;font-size:12px!important;line-height:17px!important;border-radius:2px!important}.cdump-type{color:#4fc1e9!important}.cdump-key{color:#ffce54!important}.cdump-value{color:#a0d468!important}.cdump-space{display:inline-block;width:18px;color:#363e49;-webkit-touch-callout:none;-webkit-user-select:none;-khtml-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none}</style>';
      self::$f = false;
    }
    echo '<pre class="cdump">';
    self::type2function($var);
    self::$h .= '<br/></pre>';
    echo self::$h;
    self::$h = '';
  }
  private static function type2function($var)
  {
    $type = gettype($var);
    if ($type === 'array')
    {
      self::array2html($var);
    }
    else if ($type === 'string')
    {
      self::string2html(xss_clean($var));
    }
    else if ($type === 'object')
    {
      self::object2html($var);
    }
    else if ($type === 'boolean')
    {
      self::boolean2html($var);
    }
    else if ($type === 'NULL')
    {
      self::null2html($var);
    }
    else
    {
      self::all2html($var, $type);
    }
  }
  private static function tab()
  {
    $o = '';
    for ($x = 1; $x <= self::$tab; $x++) {
      $o .= '<span class="cdump-space">&#x7C;</span>';
    }
    return $o;
  }
  private static function array2html($var)
  {
    self::$tab += 1;
    self::$h .= '<span class="cdump-type">array(<span class="cdump-value">'.count($var).'</span>)</span> [<br/>';
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
    self::$h .= self::tab().']<br/>';
  }
  private static function null2html($var)
  {
    self::$h .= '<span class="cdump-value">null</span><br/>';
  }
  private static function string2html($var)
  {
    self::$h .= '<span class="cdump-type">string(<span class="cdump-value">'.strlen($var).'</span>)</span> <span class="cdump-value">"'.$var.'"</span><br/>';
  }
  private static function object2html($var)
  {
    self::$h .= '<span class="cdump-type">object(<span class="cdump-value">'.get_class($var).'</span>)</span><br/>';
  }
  private static function boolean2html($var)
  {
    self::$h .= '<span class="cdump-type">boolean(<span class="cdump-value">'.(($var === true) ? 'true' : 'false').'</span>)</span><br/>';
  }
  private static function all2html($var, $type)
  {
    self::$h .= '<span class="cdump-type">'.$type.'(<span class="cdump-value">'.$var.'</span>)</span><br/>';
  }
}
