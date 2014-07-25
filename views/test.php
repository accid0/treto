<?php
namespace Application;
use Application\View;

include_once('views/view.php');

class TestView extends View {

  public function do_render(){
?>
<!DOCTYPE HTML>
<html lang="ru">
  <head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
  body {
    margin: 10px;
    padding: 0;
  }
  .wrapper {
    margin: 0;
    padding: 0;
    border: 0;
  }
  .content .inner-row {
    height: 150px;
  }
  .content .inner-row .entry {
    height: 100%;
    display: inline-block;
    padding: 2px;
    box-sizing: border-box;
  }
  .content .inner-row .entry img {
    height: 100%;
  }
  .modal {
    display: none;
    z-index: 100;
    width: 100%;
    height: 100%;
    background-image: url(assets/img/loader.gif);
    position: fixed;
    top: 0;
    background-repeat: no-repeat;
    background-position: 50%;
    background-color: rgba(29, 10, 10, 0.61);
  }
  </style>
  <script type="text/javascript">
  window.onload                     = function(){
    var $                             = document.querySelectorAll.bind(document),
        $content                      = $('#content')[0],
        $submit                       = $('#submit')[0],
        $form                         = $('#upload')[0],
        $modal                        = $('.modal')[0],
        list;

    $content.onchange                   = function(event){
      console.log(event)
    }
    $content.onload                   = function(event){
      console.log(event)
    }
    $submit.onclick                   = function(event){
      var data,
          req                         = new XMLHttpRequest();
      event.preventDefault();
      data                            = new FormData($form);
      data.append('action', 'ajax');
      req.open('POST', '/', true);
      req.send(data);
      $modal.style.display            = 'block';
      $content.innerHTML              = "";
      req.onreadystatechange          = function(){
      if(4 === req.readyState && 200 === req.status){
          $content.innerHTML          = req.responseText;
          list.parse();
          $modal.style.display        = 'none';
        }
      };
    };

    List.prototype.parse              = function(){
      var $row                        = this.$el.querySelectorAll('.inner-row'),
          _this                       = this;
      $row                            = Array.prototype.slice.call($row);
        $row.map(function(item){
        if(!item.querySelectorAll) return;
        var $img                      = item.querySelectorAll('.entry'),
          width, last;
        $img                          = Array.prototype.slice.call($img);
        width                         = $img.reduce(function(acc, item){
          if(!item.offsetWidth) return acc;
          return acc + item.offsetWidth;
        }, 0);
        last                          = 100;
        $img.map(function(item, index){
          if(!item.offsetWidth) return;
          var w                       = 100 * item.offsetWidth / width;
          if(index === ($img.length -1)){
            item.style.width          = last + '%';
          }
          else{
            item.style.width          = w + '%';
          }
          item.childNodes[0].style.width = '100%';
          last                        -= w;
        });
      });
    };

    list                              = new List($content);

    function List(el){
      this.$el                        = el;
      this.row_width                  = window.innerWidth;
      this.parse();
    }
    
  };
  </script>
  </head>
  <body>
    <form id="upload" class="upload" action="" method="POST" enctype='multipart/form-data'>
      <div class="form-row">
        <label for="list">Файл для загруки</label>
        <input name="list" type="file" id="list" />
      </div>
      <div class="form-row">
        <label for="label">Введите текст:</label>
        <input type="text" name="label" id="label" />
      </div>
      <div class="form-row">
        <button class="submit" id="submit">Загрузить</button>
      </div>
    </form>
      <div class="wrapper">
      <div class="content" id="content">
        <?php echo $this->data?>
      </div>
      <div class="modal"></div>
    </div>
  </body>
</html>
<?php
  }

}
