<?php
namespace Charon;
define('AUTH_CANCEL', 0);
define('AUTH_OK', 1);
define('AUTH_WRONG', 2);
class HTTP_DIGEST_AUTH
{
  private $users = [];
  private $realm = '';
  private $user = null;
  private $condition;
  public function __construct(array $users, $realm)
  {
    $this->users = $users;
    $this->realm = $realm;
  }
  public function authenticate()
  {
    if (empty($_SERVER['PHP_AUTH_DIGEST']))
    {
      header('HTTP/1.1 401 Unauthorized');
      header('WWW-Authenticate: Digest realm="'.$this->realm.'",qop="auth",nonce="'.generate_token().'",opaque="'.generate_token().'"');
      $this->condition = AUTH_CANCEL;
      return AUTH_CANCEL;
    }
    if (($data = $this->http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])) === false || isset($this->users[$data['username']]) === false)
    {
      $this->condition = AUTH_WRONG;
      return AUTH_WRONG;
    }
    $a1 = md5($data['username'].':'.$this->realm.':'.$this->users[$data['username']]);
    $a2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
    $valid = md5($a1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$a2);
    if ($data['response'] != $valid)
    {
      $this->condition = AUTH_WRONG;
      return AUTH_WRONG;
    }
    $this->user = $data['username'];
    $this->condition = AUTH_OK;
    return AUTH_OK;
  }
  public function passed()
  {
    if ($this->condition === AUTH_OK)
    {
      return true;
    }
    return false;
  }
  public function user()
  {
    return $this->user;
  }
  private function http_digest_parse($txt)
  {
    $needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
    $data = array();
    $keys = implode('|', array_keys($needed_parts));
    preg_match_all('@('.$keys.')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);
    foreach ($matches as $m)
    {
      $data[$m[1]] = $m[3] ? $m[3] : $m[4];
      unset($needed_parts[$m[1]]);
    }
    return $needed_parts ? false : $data;
  }
}
