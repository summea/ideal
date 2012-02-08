<?php

  require_once('ideal.php');

  // request
  $request = $_SERVER['QUERY_STRING'];
  $request_a = preg_split("/&|=/", $request);
  
  // response
  require_once($request_a[1].'.php');
  $response = new $request_a[1];
  $response->$request_a[3]();

?>
