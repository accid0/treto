<?php
namespace Application;
use Application\TestView;
use Application\AjaxView;
use Application\ImageModel;

include_once('views/ajax.php');
include_once('views/test.php');
include_once('models/image.php');

class FrontController {
  /**
   * index
   * 
   * @since 0.1 Start version
   * @author andrew scherbakov <kontakt.asch@gmail.com>
   * @copyright © 2014 andrew scherbakov
   * @license MIT http://opensource.org/licenses/MIT
   *
   * @return void
   */
  public function index(){
    $view                             = new TestView();
    $model                            = new ImageModel();
    $view->attach($model);
    return $view->render();
  }

  /**
   * ajax
   * 
   * @since 0.1 Start version
   * @author andrew scherbakov <kontakt.asch@gmail.com>
   * @copyright © 2014 andrew scherbakov
   * @license MIT http://opensource.org/licenses/MIT
   *
   * @return void
   */
  public function ajax(){
    $view                             = new AjaxView();
    $model                            = new ImageModel();
    $view->attach($model);
    return $view->render();
  }
}
