<?php
function d($var = null)
{
  $d = debug_backtrace();
  if ($d[0]['args'] === []) {
    return debug_backtrace(true);
  }
  return Charon\Dump::dump($var);
}
function generate_token()
{
  return md5(uniqid(mt_rand(), true));
}
function xss_clean($input, $double_encode = true)
{
  return htmlspecialchars($input, 3, 'UTF-8', $double_encode);
}
function array_random($array)
{
  shuffle($array);
  return array_pop($array);
}
function is_windows()
{
  return strtolower(substr(PHP_OS, 0, 3)) === 'win';
}
function bool_to_string($bool, $true, $false)
{
  return ($bool == true) ? $true : $false;
}
function glob_recursive($pattern, $flags = 0)
{
  $files = glob($pattern, $flags);
  foreach (glob(dirname($pattern).DS.'*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
  {
    $files = array_merge($files, glob_recursive($dir.DS.basename($pattern), $flags));
  }
  return $files;
}
function rmdir_recursive($dirname)
{
  if (file_exists($dirname) === false)
  {
    return false;
  }
  foreach(scandir($dirname) as $file) {
    if ('.' === $file || '..' === $file)
    {
      continue;
    }
    if (is_dir($dirname.DS.$file))
    {
      rmdir_recursive($dirname.DS.$file);
    }
    else
    {
      unlink($dirname.DS.$file);
    }
  }
  rmdir($dirname);
  return true;
}
function copy_recursive($source, $destination, $permissions = 0755)
{
  if (is_link($source))
  {
    return symlink(readlink($source), $destination);
  }
  if (is_file($source))
  {
    return copy($source, $destination);
  }
  if (!is_dir($destination))
  {
    mkdir($destination, $permissions);
  }
  $dir = dir($source);
  while (false !== $entry = $dir->read()) {
    if ($entry == '.' || $entry == '..') {
      continue;
    }
    copy_recursive($source.DS.$entry, $destination.DS.$entry, $permissions);
  }

  $dir->close();
  return true;
}
function round_to($number, $to)
{
  return round($number / $to) * $to;
}
function ceil_to($number, $to)
{
  return ceil($number / $to) * $to;
}
function floor_to($number, $to)
{
  return floor($number / $to) * $to;
}
function slug($text)
{
  $text = preg_replace('~[^\pL\d]+~u', '-', $text);
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
  $text = preg_replace('~[^-\w]+~', '', $text);
  $text = trim($text, '-');
  $text = preg_replace('~-+~', '-', $text);
  $text = strtolower($text);
  return $text;
}
