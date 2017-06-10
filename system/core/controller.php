<?php
class Controller
{
  public $request;
  public $response;
  public $service;
  public $load;
  public $config;
  public $benchmark;
  public $ip;

  public static $_libs = [];

  public function __construct()
  {
    $this->request = new Request(\Charon\IP::get(), $_SERVER, getallheaders(), $_POST, $_FILES);
    $this->response = new Response();
    $this->service = new Service();
    $this->load = new Load();
    $this->config = new Config();
    $this->benchmark = new \Charon\Benchmark();
    $this->ip = new \Charon\IP();
  }
  public function __get($name)
  {
    return self::$_libs[$name];
  }
}
