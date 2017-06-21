<?php
namespace Charon;
$root = realpath('.');
define('CLI_LEFT', 0);
define('CLI_CENTER', 1);
define('CLI_RIGHT', 2);
class CLI
{
  private static $argv;
  private static $cli = [];
  private static $base = '';
  private static $width = 100;
  private static $undefined;
  private static $fore_color = [
    'black' => '0;30',
    'dark_gray' => '1;30',
    'blue' => '0;34',
    'light_blue' => '1;34',
    'green' => '0;32',
    'light_green' => '1;32',
    'cyan' => '0;36',
    'light_cyan' => '1;36',
    'red' => '0;31',
    'light_red' => '1;31',
    'purple' => '0;35',
    'light_purple' => '1;35',
    'brown' => '0;33',
    'yellow' => '1;33',
    'light_gray' => '0;37',
    'white' => '1;37'
  ];
  private static $bg_color = [
    'black' => '40',
    'red' => '41',
    'green' => '42',
    'yellow' => '43',
    'blue' => '44',
    'magenta' => '45',
    'cyan' => '46',
    'light_gray' => '47'
  ];
  public static function check()
  {
    return PHP_SAPI === 'cli' || defined('STDOUT');
  }
  public static function in($string = '')
  {
    if (empty($string) === false)
    {
      self::out($string);
    }
    self::out('>> ', CLI_LEFT, false);
    return trim(fgets(STDIN));
  }
  public static function width($int)
  {
    self::$width = $int;
  }
  public static function eol($times = 1)
  {
    for ($i=0; $i < $times; $i++)
    {
      echo PHP_EOL;
    }
  }
  public static function colorify($string, $fore_color = null, $background_color = null)
  {
    if (self::has_color_support())
    {
      $o = "";
      if (isset(self::$fore_color[$fore_color])) {
        $o .= "\033[".self::$fore_color[$fore_color]."m";
      }
      if (isset(self::$bg_color[$background_color])) {
        $o .= "\033[".self::$bg_color[$background_color]."m";
      }
      $o .= $string."\033[0m";
      return $o;
    }
    return $string;
  }
  public static function out($string, $align = CLI_LEFT, $fore_color = null, $background_color = null, $eol = true)
  {
    $cstring = self::colorify($string, $fore_color, $background_color);
    if ($align === CLI_LEFT)
    {
      echo $cstring;
    }
    else if ($align === CLI_CENTER)
    {
      $space = (self::$width - strlen($string)) / 2;
      $space = ceil($space);
      for ($i=0; $i < $space; $i++)
      {
        echo ' ';
      }
      echo $cstring;
    }
    else if ($align === CLI_RIGHT)
    {
      $space = self::$width - strlen($string);
      $space = ceil($space);
      for ($i=0; $i < $space; $i++)
      {
        echo ' ';
      }
      echo $cstring;
    }
    if ($eol)
    {
      echo PHP_EOL;
    }
  }
  public static function line($eol = true)
  {
    echo PHP_EOL;
    for ($i=0; $i < self::$width; $i++)
    {
      echo '-';
    }
    echo PHP_EOL;
    if ($eol == true)
    {
      echo PHP_EOL;
    }
  }
  public static function has_color_support()
  {
    if (DIRECTORY_SEPARATOR === '\\')
    {
      return getenv('ANSICON') || getenv('ConEmuANSI') || getenv('TERM');
    }
    if (!defined('STDOUT')) {
      return false;
    }
    return function_exists('posix_isatty') && @posix_isatty(STDOUT);
  }
  public static function run($vpattern = null)
  {
    $pass = false;

    global $argv;
    $vargv = $argv;
    array_shift($vargv);
    if ($vpattern !== null)
    {
      self::eol(50);
      self::line();
    }
    foreach((array)self::$cli as $pattern => $callback)
    {
      if ($vpattern === null)
      {
        $cline = implode(' ', $vargv);
      } else{
        $cline = $vpattern;
      }
      $params_key = [];
      preg_match_all("/\[([^\]]*)\]/", $pattern, $cline_brackets);
      foreach($cline_brackets[1] as $cline_bracket)
      {
        $e = explode(':', $cline_bracket, 2);
        if (isset($e[1]))
        {
          if ($e[1] === 'i')
          {
            $e[1] = '(\d+)';
          }
        }
        else
        {
          $e[1] = '(\w+)';
        }
        $pattern = str_replace('['.$cline_bracket.']', $e[1], $pattern);
        if (in_array($e[0], $params_key))
        {
          die('param replication');
        }
        array_push($params_key, $e[0]);
      }
      if (preg_match_all('#^'.$pattern.'$#', $cline, $params_value))
      {
        $pass = true;
        array_shift($params_value);
        if (isset($params_value[0]) === false)
        {
          $params_value[0] = [];
        }
        \Charon::call($callback, $params_value[0]);
      }
    }
    if ($pass === false && isset(self::$undefined))
    {
      \Charon::call(self::$undefined, [$cline]);
    }
  }
  public static function call($pattern)
  {
    self::run($pattern);
  }
  public static function group($base, \Closure $fn)
  {
    self::$base = $base.' ';
    call_user_func($fn);
    self::$base = '';
  }
  public static function command($pattern, $callback)
  {
    self::$cli[self::$base.$pattern] = $callback;
  }
  public static function undefined($callback)
  {
    self::$undefined = $callback;
  }
}
