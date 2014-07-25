<?php
namespace Application;
use Application\View;

include_once('views/view.php');

class AjaxView extends View {

  public function do_render(){
    die($this->data);
  }

}
