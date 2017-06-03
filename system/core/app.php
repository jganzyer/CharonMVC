<?php
class Charon
{
  const VERSION = '1.0.0';

  public $base;
  public $uri;

  private $route_base;
  private $route_main;
  private $route_current;
  private $route_nf;

  public function __construct()
  {
    $this->base = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1));
    $this->uri = $this->uri();
  }

  public static function call($var, $params = [], $delimeter = '.')
  {
    $type = gettype($var);
    if ($type === 'object') {
      return call_user_func_array($var, $params);
    }
    else if ($type === 'string')
    {
      $e = explode($delimeter, $var);
      return call_user_func_array([new $e[0](), $e[1]], $params);
    }
  }

  public function uri()
  {
    $uri = substr($_SERVER['REQUEST_URI'], strlen($this->base));
    if (strstr($uri, '?'))
    {
      $uri = substr($uri, 0, strpos($uri, '?'));
    }
    return '/'.trim($uri, '/');
  }

  public function get($pattern,$callback)
  {
    return $this->map('get',$pattern,$callback);
  }

  public function post($pattern,$callback)
  {
    return $this->map('post',$pattern,$callback);
  }

  public function put($pattern,$callback)
  {
    return $this->map('put',$pattern,$callback);
  }

  public function delete($pattern,$callback)
  {
    return $this->map('delete',$pattern,$callback);
  }

  public function any($pattern, $callback)
  {
    return $this->map('any',$pattern, $callback);
  }

  public function map($methods, $pattern, $callback)
  {
    $name = $this->base.$pattern;
    $methods = explode(',', $methods);
    foreach ($methods as $method)
    {
      $this->route_main[$name]['cb'][strtolower($method)] = $callback;
    }
    $this->route_current = $name;
    return $this;
  }

  public function middleware($callback)
  {
    $this->route_main[$this->route_current]['mws'][] = $callback;
    return $this;
  }

  public function group($base, Closure $fn)
  {
    $this->route_base = $base;
    call_user_func($fn, $this);
    $this->route_base = '';
  }

  public function set404($callback)
  {
    $this->route_nf['404'] = $callback;
  }

  public function run()
  {
    $nf = true;
    $params_key = [];
    $req = new stdClass();
    $rep;
    foreach((array)$this->route_main as $pattern => $route)
    {
      preg_match_all("/\[([^\]]*)\]/", $pattern, $pattern_brackets);
      foreach ($pattern_brackets[1] as $pbs)
      {
        $pb = explode(':',$pbs,2);
        $pb[1] = isset($pb[1]) ? $pb[1] : '(\w+)';
        switch($pb[1])
        {
          case 'i':
            $pb[1] = '(\d+)';
            break;
        }
        $pattern = str_replace('['.$pbs.']', $pb[1], $pattern);
        if (in_array($pb[0], $params_key))
        {
          die('param replacition');
        }
        array_push($params_key, $pb[0]);
      }
      if (preg_match_all('#^'.$pattern.'$#', $this->base.$this->uri, $params_value))
      {
        array_shift($params_value);
        $params = array_combine($params_key, $params_value);
        // $req::config($params, Charon\Libs\IP::get());
        if (!empty($route['mws']))
        {
          foreach ($route['mws'] as $mw)
          {
            if ($this->call($mw,[$this]) !== true)
            {
              return false;
            }
          }
        }
        if (isset($route['cb'][strtolower($_SERVER['REQUEST_METHOD'])]))
        {
          $nf = false;
          $this->call($route['cb'][strtolower($_SERVER['REQUEST_METHOD'])], [$this]);
        }
        else if (isset($route['cb']['any']))
        {
          $nf = false;
          $this->call($route['cb']['any'], [$this]);
        }
      }
    }
    if ($nf === true)
    {
      // $req::config([], Charon\Libs\IP::get());
      if (!empty($this->route_nf['404']))
      {
        $this->call($this->route_nf['404']);
      }
    }
    return true;
  }
}
