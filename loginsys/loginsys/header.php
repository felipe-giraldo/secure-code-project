<?php
    
  if (!defined("_VALID_PHP"))
      die('Direct access to this location is not allowed.');
	  
	  $news = $core->renderNews();
?>

<!DOCTYPE html>
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html lang="en">
<!--<![endif]-->
<head>
<meta charset="utf-8">
<title><?php echo $core->site_name;?></title>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<link href="theme/css/front.css" rel="stylesheet" type="text/css">
<link href="theme/css/admin.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="assets/jquery-ui.css" type="text/css" />
<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<script type="text/javascript" src="assets/js/jquery.js"></script>
<script type="text/javascript" src="assets/js/jquery-ui.js"></script>
<script src="assets/js/jquery.ui.touch-punch.js"></script>
<script src="assets/js/jquery.wysiwyg.js"></script>
<script src="assets/js/global.js"></script>
<script src="assets/js/custom.js"></script>
<script src="assets/js/modernizr.mq.js" type="text/javascript" ></script>
<script src="assets/js/checkbox.js"></script>

<!-- Validation Engine -->
<!--<script src="assets/jquery-1.8.2.min.js" type="text/javascript"></script>-->
<script src="assets/languages/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script>
<script src="assets/js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" href="assets/validationEngine.jquery.css" type="text/css"/>

<script type="text/javascript">
$(document).ready(function(){
   $("#dForm").validationEngine();
});
</script>
<script language="JavaScript"> 
<!--// evito que se cargue en otro frame 
if (top.location != self.location)top.location = self.location; 
//--> 
</script>
</head>
<body>
<div id="loader" style="display:none"></div>
<div class="container">
<div class="row"> 
<div class="col grid_8">
<!--<div id="logo"><a href="index.php"><?php echo ($core->logo) ? '<img src="'.SITEURL.'/uploads/'.$core->logo.'" alt="'.$core->site_name.'" class="logo"/>': $core->site_name;?></a></div>-->
</div>

    <?php
        if ($user->logged_in): ?>
<div class="col grid_21">
  <div id="usermenu" class="flright"> 
      <a href="main.php?do=transactions"><i class="icon-laptop"></i> Transactions</a> 
      <a href="main.php?do=tokens"><i class="icon-cog"></i> Tokens</a> 
      <a href="main.php?do=transfers"><i class="icon-exchange"></i> Transfers</a>
	  <a href="main.php?do=batchTransfers"><i class="icon-exchange"></i>Batch Transfers</a>	  
      <a href="main.php?do=purchase"><i class="icon-lock"></i> Purchases</a> 
      <a href="main.php?do=download"><i class="icon-hdd"></i> Downloads</a> 
      <a href="main.php?do=sms_transfers"><i class="icon-cogs"></i> SMC Transfers</a> 
    <!--<a href="contact.php"><i class="icon-envelope"></i> Contact Us</a>-->
    <?php if($user->logged_in):?>
    <a href="logout.php"><i class="icon-off"></i> Log Off</a>
    <?php endif;?>
    <?php if($user->is_Admin()):?>
    <a href="admin/index.php"><i class="icon-gear"></i> Admin Panel</a>
    <?php endif;?>
  </div>
  </div>
  <?php endif; ?>
  </div>
  <?php if($news):?>
  <div id="news-slide" class="pagetip">
    <div id="news"><i class="icon-microphone icon-3x pull-left"></i><?php echo $news->cdate.' <strong>'.$news->title.'</strong>';?> <?php echo cleanOut($news->body);?> </div>
  </div>
  <?php endif;?>
  <div id="msgholder"></div>