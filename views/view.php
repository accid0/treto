<?php
namespace Application;
use Application\Model;
use Application\Visitable;

include_once('models/model.php');

interface Visiter {

  public function attach(Visitable $obj);
  public function detach(Visitable $obj);
  public function update();

}

abstract class View implements Visiter {
  
  public $model                            = [];

  public $data                             = [];

  public function attach(Visitable $model){
    $this->model[spl_object_hash($model)]  = $model;
  }

  public function detach(Visitable $model){
    unset($this->model[spl_object_hash($model)]);
  }

  public function update(){
    foreach($this->model as $item){
      $item->visit($this);
    }
  }

  public function render(){
    $this->update();
    $this->do_render();
  }

  abstract function do_render();

}
