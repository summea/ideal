<?php
class Entry extends Model
{
  public $table = 'entries';
  public $related = array(
    'has_many' => array(
      'comments' => 'entry_id',
      'crankies' => 'entry_id'
    )
  );

  public function getAllEntries()
  {
    return $this->find('all', array(
      'fields' => '*',
      'order' => 'id',
      'desc' => 'z-a',
      #'conditions' => array('id' => '2'),
      'keep' => array()
    ));
    //return $this->query('select * from entries where id = :id', array(':id' => '1'));
  }

  public function getentry($id)
  {
    return $this->find('first', array(
      'fields' => '*',
      'conditions' => array('entries.id' => digits($id)),
      #'keep' => array('comments','crankies')
      #'keep' => array('comments')
      #'keep' => array('crankies')
      #'keep' => array()
      ));
  }
}
?>
