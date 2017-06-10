<?php
function d($var = null)
{
  if ($var === null) {
    return debug_backtrace(true);
  }
  return \Charon\Dump::dump($var) ;
}
