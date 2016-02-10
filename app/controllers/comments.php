<?php
class Comments extends Ideal
{
  public $model = 'Comment';
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
    $this->param('comments', $this->Comment->getAllComments());
    $this->render('comments/show_index.html');
  }

  public function create_comment()
  {
    $this->param('action', $this->baseURL() . 'comments/save_comments');
    $this->render('comments/create_comment.html');
  }

  public function read_comment()
  {
    $id = $this->urlParam(':id');
    $this->param('comment', $this->Comment->getComment($id));
    $this->render('comments/read_comment.html');
  }

  public function update_comment()
  {
    $id = $this->urlParam(':id');
    $this->param('action', $this->baseURL() . 'comments/save_comments');
    $this->param('comment', $this->Comment->getComment($id));
    $this->render('comments/update_comment.html');
  }

  public function save_comments()
  {
    if ($this->Comment->save($_POST)) {
      $this->dialog('Success!');
      $this->redirect('comments/show_index');
    }
  }

  public function delete_comment()
  {
    $id = $this->urlParam(':id');
    if ($this->Comment->delete(array('conditions' => array('id' => $id)))) {
      $this->redirect('comments/show_index');
    }
  }
}
?>
