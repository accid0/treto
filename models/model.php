<?php
namespace Application;
use Application\Visiter;

include_once('views/view.php');

interface Visitable {

  function visit(Visiter $obj);

}

interface AspectVisitable extends Visitable {
  function visit(Visiter $obj);
  function beforeVisit(Visiter $obj);
  function afterVisit(Visiter $obj);
}

abstract class Model implements AspectVisitable {

  public function visit(Visiter $view){
    $class                                  = get_class($view);
    $method                                 = explode('\\', $class);
    $method                                 = 'visit' . end($method);
    $this->beforeVisit($view);
    $this->$method($view);
    $this->afterVisit($view);
  }

  abstract public function beforeVisit(Visiter $view);
  abstract public function afterVisit(Visiter $view);

}
