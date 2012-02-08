<?php

class Letter extends Ideal
{
  public function showIndex()
  {
    $this->param('name', 'Sammy');
    $this->render('showIndex.html');
  }
}

?>
