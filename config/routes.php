<?php
$routes = array(
  #'example' => ':name/:action/:controller/:id',
  'favorite' => array('controller' => 'entries', 'action' => 'show_index'),
  'default' => ':controller/:action/:id'
);
?>
