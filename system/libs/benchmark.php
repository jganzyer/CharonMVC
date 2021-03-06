<?php
namespace Charon;
class Benchmark
{
  private static $steps = [];
  private static $cs;
  public static function step($name)
  {
    self::$steps[$name] = microtime(true);
  }
  public static function report($start, $end)
  {
    if (isset(self::$steps[$start]) && isset(self::$steps[$end]))
    {
      return self::$steps[$end] - self::$steps[$start];
    }
    else
    {
      \oops::push('step doesn\'t exists');
    }
  }
  public static function time()
  {
    return microtime(true);
  }
  public static function memory_usage()
  {
    return memory_get_usage();
  }
  public static function run($fn, array $params = [])
  {
    $s = microtime(true);
    \Charon::call($fn, $params);
    $e = microtime(true);
    return $e - $s;
  }
  public static function first($fn, array $params = [])
  {
    self::$cs['f'] = self::run($fn, $params);
    return new static;
  }
  public static function second($fn, array $params = [])
  {
    $f = self::$cs['f'];
    $s = self::run($fn, $params);
    if ($f > $s) {
      $p = ($f - $s)/$f * 100;
      return 'Second function is %'.$p.' faster then first function';
    } else {
      $p = ($s - $f)/$s * 100;
      return 'First function is %'.$p.' faster then second function';
    }
  }
}
