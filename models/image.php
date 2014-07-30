<?php
namespace Application;
use Application\Model;
use Application\View;

include_once('models/model.php');
include_once('views/view.php');

class ImageModel extends Model {
  const ATTR_IMG_DIR                  = '/img/';
  const ATTR_INDENT                   = ':path';
  const ATTR_WIDTH                    = ':width';
  const ATTR_HEIGHT                   = ':height';
  const ATTR_TEMPLATE                 = '<div class="entry" img-width=":width" img-height=":height"><img src=":path"/></div>';
  const ATTR_ROW                      = ':row';
  const ATTR_CONTAINER                = '<div class="inner-row">:row</div>';

  const ATTR_FONT                     = 4;
  const ATTR_OFFSET_X                 = 0;
  const ATTR_OFFSET_Y                 = 0;
  const ATTR_RGB_RED                  = 127;
  const ATTR_RGB_GREEN                = 127;
  const ATTR_RGB_BLUE                 = 127;
  const ATTR_QUALITY                  = 100;
  const ATTR_EXT                      = '.jpg';
  /**
   * path
   *
   * @var string
   */
  public $path                        = '';

  public function __construct(){
    $this->init();
  }

  public function init(){
    $this->path                       = dirname(__DIR__) . self::ATTR_IMG_DIR;
    if(!is_dir($this->path)){
      mkdir($this->path, 0750);
    }
  }

  public function beforeVisit(Visiter $view){

  }

  public function afterVisit(Visiter $view){
    $dir                              = opendir($this->path);
    $template                         = [];
    $index                            = 0;
    $current                          = [];
    while($path = readdir($dir)){
      if('.' === $path || '..' === $path){
        continue;
      }
      $id                             = $index++ % 3;
      if(0 === $id){
        $template[]                   = [];
        $current                      = &$template[count($template) - 1];
      }
      list($width, $height)           = getimagesize($this->path . $path);
      $current[]                      = str_ireplace(
        [
          self::ATTR_INDENT,
          self::ATTR_WIDTH,
          self::ATTR_HEIGHT,
        ],
        [ 
          self::ATTR_IMG_DIR . $path,
          $width,
          $height,
        ],
        self::ATTR_TEMPLATE);
    }
    $row                              = [];
    foreach($template as &$item)
      $row[]                          = str_ireplace(self::ATTR_ROW, implode('', $item), self::ATTR_CONTAINER);
    $view->data                       = implode('', $row);
  }

  public function visitTestView(Visiter $view){

  }

  public function visitAjaxView(Visiter $view){
    if(!isset($_FILES['list']) || !isset($_FILES['list']['tmp_name'])){
      return;
    }
    $data                             = file($_FILES['list']['tmp_name']);
    $label                            = $_REQUEST['label'];
    foreach($data as $url){
      $url                            = trim($url);
      $name                           = $this->path . md5($url) . self::ATTR_EXT;
      $raw                            = @file_get_contents($url);
      if(empty($raw)){
        continue;
      }
      $src                            = @imagecreatefromstring($raw);
      if(!$src){
        continue;
      }
      $color                          = imagecolorallocate($src,
        self::ATTR_RGB_RED,
        self::ATTR_RGB_GREEN,
        self::ATTR_RGB_BLUE
      );
      imagestring($src, self::ATTR_FONT, self::ATTR_OFFSET_X, self::ATTR_OFFSET_Y, $label, $color);
      imagejpeg($src, $name, self::ATTR_QUALITY);
      imagedestroy($src);
    }
  }

}
