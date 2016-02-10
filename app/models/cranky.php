<?php
class Cranky extends Model
{
  public $table = 'crankies';

  public function getAllCrankies()
  {
    return $this->find('all', array(
      'fields' => '*',
      'order' => 'id',
      'desc' => ''
    ));
  }

  public function getCranky($id)
  {
    return $this->find('first', array(
      'fields' => '*',
      'conditions' => array('id' => $id),
      'limit' => '1'
    ));
  }

}
?>
