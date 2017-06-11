<?php
class Home extends Controller
{
  public function index($app)
  {
    $this->load->library('Charon.license');
    d($this->license->generate('####-####-####-####'));
    d([
      0 => [
        "lol",
        "in",
        2017,
        function(){
          return "yeah";
        }
      ]
    ]);
  }
}
