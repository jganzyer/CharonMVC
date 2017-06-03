<?php
// error_reporting(0);
$root = realpath('.');
function cline()
{
  echo PHP_EOL.'----------------------------------------------------------------------'.PHP_EOL.PHP_EOL;
}
function cout($string, $eol = true)
{
  echo '| '.$string;
  if ($eol)
  {
    echo PHP_EOL;
  }
}
function cin($string)
{
  cout($string);
  cout('>> ', false);
  return trim(fgets(STDIN));
}
