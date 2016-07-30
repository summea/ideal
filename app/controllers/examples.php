<?php
class Examples extends Ideal
{
  public $model = 'Example';
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
    $this->param('examples', $this->Example->getAllExamples());
    $this->render('examples/show_index.html');
  }

  public function create_example()
  {
    $this->param('action', $this->baseURL() . 'examples/save_examples');
    $this->render('examples/create_example.html');
  }

  public function read_example()
  {
    $id = $this->urlParam(':id');
    $this->param('example', $this->Example->getExample($id));
    $this->render('examples/read_example.html');
  }

  public function update_example()
  {
    $id = $this->urlParam(':id');
    $this->param('action', $this->baseURL() . 'examples/save_examples');
    $this->param('example', $this->Example->getExample($id));
    $this->render('examples/update_example.html');
  }

  public function save_examples()
  {
    if ($this->Example->save($_POST)) {
      $this->dialog('Success!');
      $this->redirect('examples/show_index');
    }
  }

  public function delete_example()
  {
    $id = $this->urlParam(':id');
    if ($this->Example->delete(array('conditions' => array('id' => $id)))) {
      $this->redirect('examples/show_index');
    }
  }
}
?>
