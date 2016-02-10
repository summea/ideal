<?php
class __MODEL_NAME__ extends Model
{
  public $table = '__TABLE_NAME__';

  public function getAll__CONTROLLER_NAME_UPPER_CASE__()
  {
    return $this->find('all', array(
      'fields' => '*',
      'order' => 'id',
      'desc' => ''
    ));
  }

  public function get__MODEL_NAME__($id)
  {
    return $this->find('first', array(
      'fields' => '*',
      'conditions' => array('id' => $id),
      'limit' => '1'
    ));
  }

}
?>
