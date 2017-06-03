<?php
class Controller
{
  public $request;
  public $response;
  public $service;

  public function __construct()
  {
    $this->request = new Request(null, $_SERVER, getallheaders(), $_POST, $_FILES);
    $this->response = new Response();
    $this->service = new Service();
  }
}
