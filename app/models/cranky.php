<?php
class Example extends Model
{
  public $table = 'example';

  public function getAllExamples()
  {
    return $this->find('all', array(
      'fields' => '*',
      'order' => 'id',
      'desc' => ''
    ));
  }

  public function getExample($id)
  {
    return $this->find('first', array(
      'fields' => '*',
      'conditions' => array('id' => $id),
      'limit' => '1'
    ));
  }

}
?>
