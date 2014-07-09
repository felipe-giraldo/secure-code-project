<?php
  if (!$user->logged_in)
      redirect_to("index.php");
  
  if (!defined("_VALID_PHP"))
      die('Direct access to this location is not allowed.');
?>
<?php
    //$string = '12345678|87654321|100|9876';
    //$string = hash('SHA256', $string);
    //$sub_string = substr($string, 0, 6);
    
    //echo 'Hash: ' . $string . ' <br>Sub String: ' . $sub_string;
    ?>

<p class="bluetip"><i class="icon-lightbulb icon-3x pull-left"></i>Here you can grab a copy of the SmartCard application.<br />
  Click on the DOWNLOAD button and save the application to your computer in order to use it. <br /></p>
<section class="widget">
  <header>
    <div class="row">
      <h1><i class="icon-reorder"></i> Viewing Downloads <?php //echo BASEPATH . 'downloads/'; ?></h1>
      
      
    </div>
  </header>
  <div class="content2">
    <?php
    $dir = BASEPATH . 'downloads';
    $uri=  str_replace("main.php?do=download", "", $_SERVER["REQUEST_URI"]);
    //echo $dir;
    if (is_dir($dir)):
        $getDir = dir($dir);
        while (false !== ($file = $getDir->read())):
            if ($file != "." && $file != ".." && $file != "index.php"):
                  echo '<div class="db-backup' . $latest . '" id="item_' . $file . '"><i class="icon-hdd pull-left icon-4x icon-white"></i>';
                  echo '<span>' . getSize(filesize(BASEPATH . 'downloads/' . $file)) . '</span>';
                  
                  echo '<a href="' . $uri . 'downloads/' . $file . '">
                  <small class="sdown tooltip" data-title="Download"><i class="icon-download-alt icon-white"></i></small></a>';
                  
                  
                  echo '</div>';
            endif;
        endwhile;
        echo BASEPATH;
        $getDir->close();
    endif;
  ?>
  </div>
</section>
<script type="text/javascript"> 
</script>