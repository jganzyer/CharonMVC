<?php
class Puck
{
  private static $cf = [];
  private static $loads = [];
  public static function init($mode)
  {
    if ($mode !== 1 && file_exists(__DIR__.'\cache.json'))
    {
      self::$cf = json_decode(file_get_contents(__DIR__.'\cache.json') , true);
    }
    else
    {
      self::dump();
      file_put_contents(__DIR__.'\cache.json', json_encode(self::$cf, JSON_PRETTY_PRINT));
    }
    spl_autoload_register('Puck::__autoload');
  }
  public static function dump()
  {
    foreach(self::rglob(realpath('.')."\*.php") as $file)
    {
      self::init_file($file);
    }
  }
  public static function autoload(Callable $fn)
  {
    array_push(self::$loads, $fn);
  }
  public static function __autoload($cn)
  {
    if (isset(self::$cf[$cn])) {
      require_once(self::$cf[$cn]);
    }
    foreach((array)self::$loads as $load)
    {
      call_user_func($load, $cn);
    }
  }
  private static function rglob($pattern, $flags = 0)
  {
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern).DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
    {
      $files = array_merge($files, self::rglob($dir.DIRECTORY_SEPARATOR.basename($pattern), $flags));
    }
    return $files;
  }
  private static function init_file($f)
  {
    $contents = file_get_contents($f);
    $tokens = token_get_all($contents);
    $count = count($tokens);
    $ns = self::token2ns($tokens);
    $ns = ($ns === '') ? "" : $ns.DIRECTORY_SEPARATOR;
    for ($i = 2; $i < $count; $i++)
    {
      if ($tokens[$i - 2][0] === T_CLASS && $tokens[$i - 1][0] === T_WHITESPACE || $tokens[$i - 2][0] === T_TRAIT || $tokens[$i - 2][0] === T_INTERFACE || $tokens[$i - 2][0] === T_ABSTRACT)
      {
        $cn = $tokens[$i][1];
        self::$cf[$ns.$cn] = $f;
      }
    }
  }
	private static function token2ns($tokens)
  {
    $count = count($tokens);
    $i = 0;
    $namespace = '';
    $namespace_ok = false;
    while ($i < $count) {
      $token = $tokens[$i];
      if (is_array($token) && $token[0] === T_NAMESPACE) {
        while (++$i < $count) {
          if ($tokens[$i] === ';') {
            $namespace_ok = true;
    				$namespace = trim($namespace);
    				break;
    			}
    			$namespace .= is_array($tokens[$i]) ? $tokens[$i][1] : $tokens[$i];
    		}
    		break;
    	}
    	$i++;
    }
    if (!$namespace_ok) {
      return '';
    } else {
      return $namespace;
    }
  }
}
