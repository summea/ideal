<?php

class Ideal
{
  public $param = array();

  public function __construct()
  {
  }

  public function model()
  {
    echo 'model'.'<br>';
  }

  public function controller()
  {
    echo 'controller'.'<br>';
  }

  public function view()
  {
    echo 'view'.'<br>';
  }

  public function render($source)
  {
    extract($this->param[0]); # import params into symbol table
    ob_start();
    include_once($source);
    ob_end_flush();
  }

  public function param($key, $value)
  {
    array_push($this->param, array($key => $value));
  }
}

?>
