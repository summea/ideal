<?php

  session_start();

  require_once('config/conf.php');
  require_once('ideal/ideal.php');
  require_once('ideal/model.php');
  require_once('ideal/clean.php');
  require_once('config/routes.php');

  // request
  $request = $_SERVER['REQUEST_URI'];
  $sub_dir = str_replace("index.php", "", $_SERVER['PHP_SELF']);
  $request = str_replace($sub_dir, "", $request);
  $request_origin_a = explode("/", $request);

  # check for cache file
  $cache_file = 'cache/' . str_replace("/", "-", $request);
  if (file_exists($cache_file)) {
    $file = file_get_contents($cache_file);
    preg_match_all("/[A-Z]+\:.+\;/", $file, $metadata_a);
    $keys_a = array();
    $values_a = array();
    $metadata_a = explode(";", rtrim($metadata_a[0][0], ";"));
    foreach ($metadata_a as $k => $v) {
      $v = rtrim($v, ";");
      $v_a = explode(":", $v);
      array_push($keys_a, $v_a[0]);
      array_push($values_a, $v_a[1]);
    }
    $metadata_a = array_combine($keys_a, $values_a);
    ob_start();
    echo $file;
    ob_end_flush();
    if (isset($metadata_a['CREATED']))
      if (time() > $metadata_a['CREATED'] + ($CACHE_VIEW_EXPIRE))
        unlink($cache_file);  # clear old cache file
    exit;
  }

  $route_found = false;
  $route_is_array = false;
  foreach ($routes as $k => $v) {
    if (preg_match("/$k/", $request)) {
      $route_found = true;
      if (is_array($routes[$k])) {
        $route_is_array = true;
        foreach ($routes[$k] as $k => $v) {
          $request_a[":$k"] = $v;
        }
      } else {
        $route_a = explode("/", $routes[$k]);
      }
    }
  }

  if (!$route_found) {
    $route_a = explode("/", $routes['default']);
  }

  if (!$route_is_array) {
    $request_a = array();
    for ($i=0; $i<count($request_origin_a); $i++) {
      $request_a = $request_a + array($route_a[$i] => $request_origin_a[$i]);
    }
  }

  # debug
  if ($DEBUG_MODE) {
    echo 'Request Array: <br /><pre>' . print_r($request_a, true) . '</pre>';
    echo 'Server Array: <br /><pre>' . print_r($_SERVER, true) . '</pre>';
  }

  // response
  try {
    if (!file_exists('app/controllers/' . $request_a[':controller'] . '.php')) {
      throw new Exception('Controller could not be found.');
    }
    require_once('app/controllers/' . $request_a[':controller'] . '.php');
    $response = new $request_a[':controller']($request_a);
    try {
      if (!method_exists($request_a[':controller'], $request_a[':action'])) {
        throw new Exception('Action could not be found.');
      }
      if (method_exists($request_a[':controller'], 'before_response'))
        $response->before_response($request_a[':action']);

      $response->$request_a[':action']();

      if (method_exists($request_a[':controller'], 'after_response'))
        $response->after_response($request_a[':action']);
    } catch (Exception $e) {
      throw $e;
    }
  } catch (Exception $e) { 
    echo $e->getMessage();
    die;
  }

?>
