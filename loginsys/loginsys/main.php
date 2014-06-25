<?php
 
  define("_VALID_PHP", true);
  require_once("init.php");


  
  //if (!$user->is_Admin())
  //    redirect_to("login.php");
?>
<?php
  if (!$user->logged_in)
      redirect_to("index.php");
?>
<?php include("header.php");?>
  <!-- Start Content-->
<div class="wrap clearfix">
  <div id="content-wrap">
    <div id="content">
     <div id="msgholder"></div>
	   <?php (Filter::$do && file_exists(Filter::$do.".php")) ? include(Filter::$do.".php") : include("main.php");?>
    </div>
  </div>
</div>
  <!-- End Content/-->
<?php include("footer.php");?>