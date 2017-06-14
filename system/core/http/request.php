<?php
class Request
{
  private static $ip;
  private static $server;
  private static $headers;
  private static $post;
  private static $files;

  private static $vars = [];

  public function __construct($ip, $server, $headers, $post, $files)
  {
    self::$ip = $ip;
    self::$server = $server;
    self::$headers = $headers;
    self::$post = $post;
    self::$files = $files;
  }

  public static function __init($vars)
  {
    self::$vars = $vars;
  }

  public static function isSecure()
  {
    return (!empty(self::$server['HTTPS']) && self::$server['HTTPS'] !== 'off') || self::$server['SERVER_PORT'] == 443;
  }

  public static function isAjax()
  {
    return (isset(self::$server['HTTP_X_REQUESTED_WITH']) && (strtolower(self::$server('HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest'));
  }

  public static function request($method, $url, $params = [], $options = [], &$info = null)
  {
    if ($options === null) {
      $options = [];
    }
    $ch = curl_init();
    switch(strtolower($method))
    {
      case 'get':
        curl_setopt_array($ch, $options + [
          CURLOPT_URL => $url,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_RETURNTRANSFER => true
        ]);
        break;
      case 'post':
        curl_setopt_array($ch, $options + [
          CURLOPT_URL => $url,
          CURLOPT_POST => true,
          CURLOPT_POSTFIELDS => $params,
          CURLOPT_RETURNTRANSFER => true
        ]);
        break;
      default:
        curl_setopt_array($ch, $options + [
          CURLOPT_URL => $url,
          CURLOPT_CUSTOMREQUEST => $method,
          CURLOPT_RETURNTRANSFER => true
        ]);
        break;
    }
    $r = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    return $r;
  }

  public static function referer()
  {
    return (isset(self::$server['HTTP_REFERER'])) ? self::$server['HTTP_REFERER'] : false;
  }

  public function uri($full = false)
  {
    if ($full == true)
    {
      return ((self::isSecure() === true) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    }
    return self::$server['REQUEST_URI'];
  }

  public static function language()
  {
    return isset(self::$server["HTTP_ACCEPT_LANGUAGE"]) ? substr(self::$server["HTTP_ACCEPT_LANGUAGE"],0,2) : '';
  }

  public static function ip()
  {
    return self::$ip;
  }

  public static function method($type = null)
  {
    return ($type === null) ? self::$server['REQUEST_METHOD'] : (strtolower($type) === strtolower(self::$server['REQUEST_METHOD']));
  }

  public static function variable($data = null)
  {
    return ($data === null) ? self::$vars : self::$vars[$data];
  }

  public static function file($data = null)
  {
    if ($data === null)
    {
      return self::$files;
    }
    return (isset(self::$files[$data])) ? self::$files[$data] : false;
  }

  public static function post($data = null)
  {
    if ($data === null)
    {
      return self::$post;
    }
    return (isset(self::$post[$data])) ? self::$post[$data] : false;
  }

  public static function server($data = null)
  {
    if ($data === null)
    {
      return self::$server;
    }
    return (isset(self::$server[$data])) ? self::$server[$data] : false;
  }

  public static function header($data = null)
  {
    if ($data === null)
    {
      return self::$headers;
    }
    return (isset(self::$headers[$data])) ? self::$headers[$data] : false;
  }
}
