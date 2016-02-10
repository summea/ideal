<?php
/**
 * Core Ideal class
 * @package ideal
 */
class Ideal
{
  public $urlParam = array();
  public $param = array();
  public $dialog = '';

  /**
   * Constructor gathers $urlParam from request URL.
   * Then, constructor creates corresponding model,
   * and checks for dialog messages.
   * @param array $urlParam
   */
  public function __construct($urlParam=array())
  {
    $this->urlParam = $urlParam;
    $model = $this->model();
    require_once('app/models/' . strtolower($model) . '.php');
    $this->$model = new $model;
    if (isset($_SESSION['dialog']) && $_SESSION['dialog']) {
      $this->dialog = $_SESSION['dialog'];
      unset($_SESSION['dialog']);
    }
  }

  /**
   * Get base URL from request URL.
   * @return string
   */
  public function baseURL()
  {
    return preg_replace("/^(.+{$_SERVER['HTTP_HOST']})\/.*/", 
      "$1", $_SERVER['SCRIPT_URI']) . '/' . $this->baseURLSubdirectory();
  }

  /**
   * Get subdirectory URL from request URL (if any.)
   * @return string
   */
  public function baseURLSubdirectory()
  {
    return trim(str_replace("index.php", "", $_SERVER['PHP_SELF']), "/") . '/';

  }

  /**
   * Set dialog message.
   * @param string $message
   * @return string
   */
  public function dialog($message='')
  {
    if ($message)
      $_SESSION['dialog'] = $message;
    return $this->dialog;
  }

  /**
   * Get associated model.
   * @return string
   */
  public function model()
  {
    return $this->model;
  }

  /**
   * Set param variable for view.
   * @param string $key
   * @param array $value
   */
  public function param($key, $value)
  {
    array_push($this->param, array($key => $value));
  }

  /**
   * Redirect to another page.
   * @param string $to
   */
  public function redirect($to)
  {
    Header('Location:' . $this->baseURL() . $to);
    exit;
  }

  /**
   * Render view.
   * @param string $source
   */
  public function render($source)
  {
    $layout = ($this->layout) ? 'app/views/layouts/' . $this->layout . '.html' : '';
    if ($layout) {
      try {
        if (!file_exists($layout))
          throw new Exception('Layout could not be found.');
      } catch (Exception $e) {
        echo $e->getMessage();
      }
    }
    
    $source = 'app/views/' . $source;
    $BASEURL = $this->baseURL();
    foreach($this->param as $param) {
      extract($param); # import params into symbol table
    }
    $dialog = $this->dialog();

    if (file_exists($source)) {
      $content_for_cache = '<!--CREATED:' . time() . ';//-->';
      ob_start();
      include_once($source);
      $content_for_layout .= ob_get_contents();
      if ($layout) {
        ob_end_clean();
        ob_start();
        include_once($layout);
        $content_for_cache .= ob_get_contents();
        ob_end_flush();
      } else {
        $content_for_cache .= ob_get_contents();
        ob_end_flush();
      }
      # create cache page
      $cache_view_a = ($this->cacheView) ? $this->cacheView : array();
      if (in_array($this->urlParam(':action'), $cache_view_a)) {
        $file = 'cache/' . implode("-", $this->urlParam());
        file_put_contents($file, $content_for_cache);
      }
    } else {
      die('View could not be found.');
    }
  }

  /**
   * Get URL parameters (or individual value of $key).
   * @param string $key
   * @return string
   */
  public function urlParam($key='')
  {
    return ($key) ? $this->urlParam[$key] : $this->urlParam;
  }

}

?>
