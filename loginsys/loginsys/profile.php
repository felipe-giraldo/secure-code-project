<?php
  
  define("_VALID_PHP", true);
  require_once("init.php");
  
  if(isset($_GET['username'])):
      $username = sanitize($_GET['username'],15);
      $row = $db->first("SELECT * FROM users WHERE username  = '" . $db->escape($username) . "'");
  endif;
?>
<?php include("header.php");?>
<?php if(!$row):?>
<div class="msgError"><span>Error!</span>You have selected invalid username.</div>
<?php else:?>
    <h1>Viewing Profile For &rsaquo; <?php echo $row['username'];?></h1>
      <div class="box">
      <div><span style="float:right"><?php echo ($row['avatar']) ? '<img src="'.UPLOADURL . $row['avatar'].'" alt=""/>' : '<img src="'.UPLOADURL.'blank.png" alt=""/>';?></span>
      <?php echo $row['fname'];?> <?php echo $row['lname'];?>
      <br clear="all" />
    </div>
    </div>
<?php endif;?>     
<?php include("footer.php");?>

<?php
/*
To pull the rest of the user information dump the array for selected user like:

$db->pre($row);
 and just start showing additional fields you need
*/
?>