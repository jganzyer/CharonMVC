<?php
function d($var = null)
{
  if ($var === null) {
    return debug_backtrace(true);
  }
  return \Charon\Dump::dump($var) ;
}
function generate_token()
{
  return md5(uniqid(mt_rand(), true));
}
function xss_clean($input)
{
  return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}
