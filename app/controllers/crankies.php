<?php
class Crankies extends Ideal
{
  public $model = 'Cranky';
  public $layout = '';
  public $cacheView = array();

  public function before_response($action='')
  {
    if (!in_array($action, array())) {
      // Before response happens... do this.
    }
  }

  public function after_response($action='')
  {
    if (!in_array($action, array())) {
      // After response happens... do this.
    }
  }

  public function show_index()
  {
    $this->param('crankies', $this->Cranky->getAllCrankies());
    $this->render('crankies/show_index.html');
  }

  public function create_cranky()
  {
    $this->param('action', $this->baseURL() . 'crankies/save_crankies');
    $this->render('crankies/create_cranky.html');
  }

  public function read_cranky()
  {
    $id = $this->urlParam(':id');
    $this->param('cranky', $this->Cranky->getCranky($id));
    $this->render('crankies/read_cranky.html');
  }

  public function update_cranky()
  {
    $id = $this->urlParam(':id');
    $this->param('action', $this->baseURL() . 'crankies/save_crankies');
    $this->param('cranky', $this->Cranky->getCranky($id));
    $this->render('crankies/update_cranky.html');
  }

  public function save_crankies()
  {
    if ($this->Cranky->save($_POST)) {
      $this->dialog('Success!');
      $this->redirect('crankies/show_index');
    }
  }

  public function delete_cranky()
  {
    $id = $this->urlParam(':id');
    if ($this->Cranky->delete(array('conditions' => array('id' => $id)))) {
      $this->redirect('crankies/show_index');
    }
  }
}
?>
