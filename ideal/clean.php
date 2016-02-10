<?php
/**
* Get numbers from input.
* @return int
*/
function digits($number)
{
  return (int)preg_replace("/[^0-9]+/", "", $number);
}
?>
