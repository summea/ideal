<?php

class Entries extends Ideal
{
  public $model = 'Entry';
  public $layout = 'backend';
  public $cacheView = array();

  public function before_response($action='')
  {
    #if (!in_array($action, array('delete_entry', 'save_entries')))
    #  echo '**Before response happens... do this.**';
  }

  public function after_response($action='')
  {
    #if (!in_array($action, array('delete_entry', 'save_entries')))
    #  echo '**After response happens... do this.**';
  }

  public function show_entry()
  {
    $id = $this->urlParam(':id');
    $this->param('entry', $this->Entry->getEntry($id));
    $this->render('entries/show_entry.html');
  }

  public function show_index()
  {
    $this->param('name', 'Sammy');
    $this->param('entries', $this->Entry->getAllEntries());
    $this->render('entries/show_index.html');
  }

  public function create_entry()
  {
    $this->layout = '';
    $this->param('action', $this->baseURL() . 'entries/save_entries');
    $this->render('entries/create_entry.html');
  }

  public function edit_entry()
  {
    $id = $this->urlParam(':id');
    $this->param('action', $this->baseURL() . 'entries/save_entries');
    $this->param('entry', $this->Entry->getEntry($id));
    $this->render('entries/edit_entry.html');
  }

  public function save_entries()
  {
    if ($this->Entry->save($_POST)) {
      $this->dialog('Success!');
      $this->redirect('entries/show_index');
    }
  }

  public function delete_entry()
  {
    $id = $this->urlParam(':id');
    if ($this->Entry->delete(array('conditions' => array('id' => $id)))) {
      $this->redirect('entries/show_index');
    }
  }
}

?>
