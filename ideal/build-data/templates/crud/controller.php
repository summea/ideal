<?php
class __CONTROLLER_NAME_UPPER_CASE__ extends Ideal
{
  public $model = '__MODEL_NAME_UPPER_CASE__';
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
    $this->param('__CONTROLLER_NAME_LOWER_CASE__', $this->__MODEL_NAME__->getAll__CONTROLLER_NAME_UPPER_CASE__());
    $this->render('__CONTROLLER_NAME_LOWER_CASE__/show_index.html');
  }

  public function create___MODEL_NAME_LOWER_CASE__()
  {
    $this->param('action', $this->baseURL() . '__CONTROLLER_NAME_LOWER_CASE__/save___CONTROLLER_NAME_LOWER_CASE__');
    $this->render('__CONTROLLER_NAME_LOWER_CASE__/create___MODEL_NAME_LOWER_CASE__.html');
  }

  public function read___MODEL_NAME_LOWER_CASE__()
  {
    $id = $this->urlParam(':id');
    $this->param('__MODEL_NAME_LOWER_CASE__', $this->__MODEL_NAME__->get__MODEL_NAME__($id));
    $this->render('__CONTROLLER_NAME_LOWER_CASE__/read___MODEL_NAME_LOWER_CASE__.html');
  }

  public function update___MODEL_NAME_LOWER_CASE__()
  {
    $id = $this->urlParam(':id');
    $this->param('action', $this->baseURL() . '__CONTROLLER_NAME_LOWER_CASE__/save___CONTROLLER_NAME_LOWER_CASE__');
    $this->param('__MODEL_NAME_LOWER_CASE__', $this->__MODEL_NAME__->get__MODEL_NAME__($id));
    $this->render('__CONTROLLER_NAME_LOWER_CASE__/update___MODEL_NAME_LOWER_CASE__.html');
  }

  public function save___CONTROLLER_NAME_LOWER_CASE__()
  {
    if ($this->__MODEL_NAME__->save($_POST)) {
      $this->dialog('Success!');
      $this->redirect('__CONTROLLER_NAME_LOWER_CASE__/show_index');
    }
  }

  public function delete___MODEL_NAME_LOWER_CASE__()
  {
    $id = $this->urlParam(':id');
    if ($this->__MODEL_NAME__->delete(array('conditions' => array('id' => $id)))) {
      $this->redirect('__CONTROLLER_NAME_LOWER_CASE__/show_index');
    }
  }
}
?>
