<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
class ImageList {
  /**
   * file
   *
   * @var File
   */
  public $file                        = null;

  /**
   * __construct
   * 
   * @since 0.1 Start version
   * @author andrew scherbakov <kontakt.asch@gmail.com>
   * @copyright © 2014 andrew scherbakov
   * @license MIT http://opensource.org/licenses/MIT
   *
   * @return void
   */
  public function __construct(){
    $this->init();
  }

  /**
   * init
   *
   * @since 0.1 Start version
   * @author andrew scherbakov <kontakt.asch@gmail.com>
   * @copyright © 2014 andrew scherbakov
   * @license MIT http://opensource.org/licenses/MIT
   *
   * @return void
   */
  public function init(){
    $this->file                       = new File($this);
  }

  public function __toString(){
    return $this->file->render();
  }

  public function upload(){
    $this->file->upload();
    return $this->file->render();
  }

}

class File {

  const ATTR_IMG_DIR                  = '/img/';
  const ATTR_INDENT                   = ':path';
  const ATTR_TEMPLATE                 = '<img src=":path" class="entry" >';

  const ATTR_FONT                     = 1;
  const ATTR_OFFSET_X                 = 0;
  const ATTR_OFFSET_Y                 = 0;
  const ATTR_RGB_RED                  = 127;
  const ATTR_RGB_GREEN                = 127;
  const ATTR_RGB_BLUE                 = 127;
  const ATTR_QUALITY                  = 100;

  /**
   * path
   *
   * @var string
   */
  public $path                        = '';

  /**
   * list
   *
   * @var ImageList
   */
  public $list                        = null;

  public function __construct(ImageList $list){
    $this->init($list);
  }

  public function init(ImageList $list){
    $this->list                       = $list;
    $this->path                       = __DIR__ . self::ATTR_IMG_DIR;
    if(!is_dir($this->path)){
      mkdir($this->path, 0750);
    }
  }

  public function upload(){
    if(!isset($_FILES['list']) || !isset($_FILES['list']['tmp_name'])){
      return;
    }
    $data                             = file($_FILES['list']['tmp_name']);
    $label                            = $_REQUEST['label'];
    foreach($data as $url){
      $name                           = $this->path . md5($url);
      $context                        = stream_context_create([
        'http'                        => [
          'method'                    => 'HEAD',
          'protocol_version'          => '1.1',
        ]  
        ]);
      if(!preg_match('@^https?://((?:www\.)?[^/]+)@i', $url, $match)){
        continue;
      }
      $host                           = $match[1];
      $url                            = str_ireplace($match[0], '', $url);
      $ip                             = gethostbyname($host);
      $sock                           = stream_socket_client('tcp://' . $ip . ":80", $err, $errno, 5);
      $req                            = <<<EOL
HEAD $url HTTP/1.1
User-Agent: curl/7.25.0 (i586-suse-linux-gnu) libcurl/7.25.0 OpenSSL/1.0.1c zlib/1.2.7 libidn/1.25 libssh2/1.4.0
Host: $host
Accept: */*

EOL;
      fwrite($sock, $req);
      $raw                            = '';
      while($data = fread($sock, 1024*64)){
        $raw                          .= $data;
      }
      var_dump($req, $raw);
      //$url                            = str_ireplace('treto.ru', '91.228.154.101', $url);
      //$raw                            = file_get_contents('http://91.228.154.101');
      //echo strlen($raw), PHP_EOL;
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

  public function render(){
    $dir                              = opendir($this->path);
    $file                             = [];
    $template                         = [];
    $match;
    while(($path = readdir($dir)) && ('.' !== $path) && ('..' !== $path)){
      $file[]                         = $path;
      $match[]                        = self::ATTR_INDENT;
      $template[]                     = self::ATTR_TEMPLATE;
    }
    return empty($file) ? '' : implode('', str_ireplace($match, $file, $template));
  }

}

$list                                 = new ImageList();

if(isset($_REQUEST['ajax'])){
  die($list->upload());
}


?>

<!DOCTYPE HTML>
<html lang="ru">
  <head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script type="text/javascript">
  window.onload                     = function(){
    var $                             = document.querySelectorAll.bind(document),
        $content                      = $('#content')[0],
        $submit                       = $('#submit')[0],
        $form                         = $('#upload')[0];

    $submit.onclick                   = function(event){
      var data,
          req                         = new XMLHttpRequest();
      event.preventDefault();
      data                            = FormData($form);
      data.append('ajax', '1');
      req.open('POST', '/', true);
      req.send(data);
    };
  };
  </script>
  </head>
  <body>
    <form id="upload" class="upload" action="" method="POST" enctype='multipart/form-data'>
      <div class="form-row">
        <label for="list">Файл для загруки</label>
        <input name="list" type="file" id="list" >
      </div>
      <div class="form-row">
        <label for="label">Введите текст:</label>
        <input type="text" name="label" id="label" >
      </div>
      <div class="form-row">
        <button class="submit" id="submit">Загрузить</button>
      </div>
    </form>
    <div class="wrapper">
      <div class="content" id="content">
        <?php echo $list?>
      </div>
    </div>
  </body>
</html>
