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
  public static function check()
  {
    return PHP_SAPI === 'cli' || defined('STDOUT');
  }
  public static function in($string)
  {
    cout($string);
    cout('>> ', false);
    return trim(fgets(STDIN));
  }
  public static function width($int)
  {
    self::$width = $int;
  }
  public static function eol()
  {
    echo PHP_EOL;
  }
  public static function out($string, $align = CLI_LEFT, $eol = true)
  {
    $string = trim($string);
    if ($align === CLI_LEFT)
    {
      echo '| '.$string;
    }
    else if ($align === CLI_CENTER)
    {
      $space = (self::$width - strlen($string)) / 2;
      $space = ceil($space);
      for ($i=0; $i < $space; $i++)
      {
        echo ' ';
      }
      echo $string;
    }
    else if ($align === CLI_RIGHT)
    {
      $space = self::$width - strlen($string);
      $space = ceil($space);
      for ($i=0; $i < $space; $i++)
      {
        echo ' ';
      }
      echo $string;
    }
    if ($eol)
    {
      echo PHP_EOL;
    }
  }
  public static function line()
  {
    echo PHP_EOL;
    for ($i=0; $i < self::$width; $i++)
    {
      echo '-';
    }
    echo PHP_EOL.PHP_EOL;
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
  public static function init(\Closure $fn, $args)
  {
    array_shift($args);
    self::$argv = $args;
    call_user_func($fn);
    foreach((array)self::$cli as $pattern => $callback)
    {
      $cline = implode(' ', self::$argv);
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
        array_shift($params_value);
        if (isset($params_value[0]) === false)
        {
          $params_value[0] = [];
        }
        \Charon::call($callback, $params_value[0]);
      }
    }
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
}
