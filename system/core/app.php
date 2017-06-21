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
  public $route_name;
  public function __construct()
  {
    $this->base = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1));
    $uri = substr($_SERVER['REQUEST_URI'], strlen($this->base));
    $uri = '/'.trim((strstr($uri, '?')) ? substr($uri, 0, strpos($uri, '?')) : $uri, '/');
    $this->uri = $uri;
  }

  public static function call($var, $params = [], $vthis = null, $delimeter = '.')
  {
    $type = gettype($var);
    if ($type === 'object')
    {
      return call_user_func_array(Closure::bind($var, $vthis), $params);
    }
    else if ($type === 'string')
    {
      $e = explode($delimeter, $var);
      if (isset($e[1]) === true)
      {
        $callable = [new $e[0](),$e[1]];
        if (method_exists('Closure', 'fromCallable'))
        {
          $callable = Closure::fromCallable($callable);
          $callable = Closure::bind($callable, $vthis);
        }
        return call_user_func_array($callable, $params);
      }
      $callable = $e[0];
      if (method_exists('Closure', 'fromCallable'))
      {
        $callable = Closure::fromCallable($callable);
        $callable = Closure::bind($callable, $vthis);
      }
      return call_user_func_array($callable, $params);
    }
  }

  public function get($pattern, $callback)
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

  public function name($name)
  {
    $this->route_name[strtolower($name)] = $this->route_current;
    return $this;
  }

  public function uri($name, $params = [])
  {
    $pattern = $this->route_name[strtolower($name)];
    preg_match_all("/\[([^\]]*)\]/", $pattern, $pattern_brackets);
    foreach($pattern_brackets[1] as $bracket)
    {
      $brackete = explode(':', $bracket,2);
      if (isset($brackete[1]))
      {
        if ($brackete[1] === 'i')
        {
          $brackete[1] = '(\d+)';
        }
      }
      else
      {
        $brackete[1] = '(\w+)';
      }
      if (isset($params[$brackete[0]]))
      {
        if (preg_match('#^'.$brackete[1].'$#', $params[$brackete[0]]))
        {
          $pattern = str_replace('['.$bracket.']', $params[$brackete[0]], $pattern);
        }
        else
        {
          return false;
        }
      }
      else
      {
        return false;
      }
    }
    $is = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
    $link = (($is === true) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'];
    $link .= $pattern;
    return $link;
  }

  public function redirect($name, $params = [], $timeout = 0, $statusCode = 302)
  {
    Response::redirect($this->uri($name, $params), $timeout, $statusCode);
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
    foreach((array)$this->route_main as $pattern => $route)
    {
      $params_key = [];
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
          \oops::push('param replication');
        }
        array_push($params_key, $pb[0]);
      }
      if (preg_match_all('#^'.$pattern.'$#', $this->base.$this->uri, $params_value))
      {
        array_shift($params_value);
        $params_value = array_map(function($v){return $v[0];}, $params_value);
        $params = array_combine($params_key, $params_value);
        Request::__init($params);
        if (!empty($route['mws']))
        {
          foreach ($route['mws'] as $mw)
          {
            if ($this->call($mw,[$this], new Controller()) !== true)
            {
              return false;
            }
          }
        }
        if (isset($route['cb'][strtolower($_SERVER['REQUEST_METHOD'])]))
        {
          $nf = false;
          $this->call($route['cb'][strtolower($_SERVER['REQUEST_METHOD'])], [$this], new Controller());
        }
        else if (isset($route['cb']['any']))
        {
          $nf = false;
          $this->call($route['cb']['any'], [$this], new Controller());
        }
      }
    }
    if ($nf === true)
    {
      if (!empty($this->route_nf['404']))
      {
        $this->call($this->route_nf['404'], [], new Controller());
      }
    }
    return true;
  }
}
