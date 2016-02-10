<?php
class Comment extends Model
{
  public $table = 'comments';

  public function getAllComments()
  {
    return $this->find('all', array(
      'fields' => '*',
      'order' => 'id',
      'desc' => ''
    ));
  }

  public function getComment($id)
  {
    return $this->find('first', array(
      'fields' => '*',
      'conditions' => array('id' => $id),
      'limit' => '1'
    ));
  }

}
?>
