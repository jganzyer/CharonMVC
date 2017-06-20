<?php
class Response
{
  public static function status($code = null)
  {
    return ($code === null) ? http_response_code() : http_response_code($code);
  }

  public static function header($key, $value = null)
  {
    if ($value === null) {
      header($key);
    }
    else
    {
      header($key.': '.$value);
    }
  }

  public static function redirect($url, $timeout = 0, $statusCode = 302)
  {
    header('Refresh: '.$timeout.';URL='.$url, true, $statusCode);
    exit();
  }

  public static function back()
  {
    header("location:javascript://history.go(-1)");
    exit;
  }

  public static function file($path, $filename = null, $mimetype = null, $download = false)
  {
    ob_clean();
    if (!file_exists($path))
    {
      \oops::push('file doesn\t exists in **{file}** on line **{line}**');
    }
    if ($filename === null)
    {
      $filename = basename($path);
    }
    if ($mimetype === null)
    {
      $mimetype = finfo_file(finfo_open(16), $path);
    }
    $buffer = file_get_contents($path);
    self::header('Content-Type', $mimetype);
    self::header('Content-Length',strlen($buffer));
    self::header('Accept-Ranges','bytes');
    if ($download === true)
    {
      self::header('Content-Disposition', 'attachment; filename="'.$filename.'"');
      self::header('Pragma', 'public');
      self::header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
    }
    echo $buffer;
  }

  public static function json(array $array, $flag = null)
  {
    ob_clean();
    self::header('Content-type','application/json');
    echo json_encode($array, $flag);
  }
}
