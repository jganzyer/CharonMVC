<?php
class Home extends Controller
{
  public function index($app)
  {
    echo $this->request->post('lol');
  }
}
