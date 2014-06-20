<?php
  error_reporting(E_ALL);
  define("_VALID_PHP", true);

  $BASEPATH = str_replace("upgrade.php", "", realpath(__file__));
  define("BASEPATH", $BASEPATH);

  $configFile = BASEPATH . "lib/config.ini.php";
  if (file_exists($configFile)) {
      require_once ($configFile);
  } else {
      exit("Configuration file is missing. Upgrade can not continue");
  }

  require_once (BASEPATH . "lib/class_db.php");
  $db = new Database(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
  $db->connect();
  $ver = $db->first("SELECT version FROM settings");


  /**
   * redirect_to()
   * 
   * @param mixed $location
   * @return
   */
  function redirect_to($location)
  {
      if (!headers_sent()) {
          header('Location: ' . $location);
          exit;
      } else {
          echo '<script type="text/javascript">';
          echo 'window.location.href="' . $location . '";';
          echo '</script>';
          echo '<noscript>';
          echo '<meta http-equiv="refresh" content="0;url=' . $location . '" />';
          echo '</noscript>';
      }
  }
  
  if (isset($_POST['submit'])) {
	  $db->query("  
		  ALTER TABLE `settings` 
			  ADD COLUMN `logo` varchar(40)  COLLATE utf8_general_ci NULL after `backup`,
			  ADD COLUMN `mailer` enum('PHP','SMTP')  COLLATE utf8_general_ci NOT NULL DEFAULT 'PHP' after `backup`,
			  ADD COLUMN `smtp_host` varchar(100)  COLLATE utf8_general_ci NULL after `mailer`,
			  ADD COLUMN `smtp_user` varchar(50)  COLLATE utf8_general_ci NULL after `smtp_host`,
			  ADD COLUMN `smtp_pass` varchar(50)  COLLATE utf8_general_ci NULL after `smtp_user`,
			  ADD COLUMN `smtp_port` varchar(6)  COLLATE utf8_general_ci NULL after `smtp_pass`,
			  ADD COLUMN `is_ssl` tinyint(1)   NOT NULL DEFAULT '0' after `smtp_port`;
	  ");

	  $db->query("  
		  ALTER TABLE `users` 
			  ADD COLUMN `notes` text  COLLATE utf8_general_ci NULL after `userlevel`;
	  ");

	  $db->query("  
		  INSERT INTO `email_templates` (`id`, `name`, `subject`, `help`, `body`) VALUES
		  (16, 'Account Activation', 'Your account have been activated', 'This template is used to notify user when manual account activation is completed', '&lt;div align=&quot;center&quot;&gt;\n&lt;table style=&quot;background: none repeat scroll 0% 0% rgb(244, 244, 244); border: 1px solid rgb(102, 102, 102);&quot; border=&quot;0&quot; cellpadding=&quot;5&quot; cellspacing=&quot;5&quot; width=&quot;600&quot;&gt;\n    &lt;tbody&gt;\n        &lt;tr&gt;\n            &lt;th style=&quot;background-color: rgb(204, 204, 204);&quot;&gt;Hello, [NAME]! &lt;br&gt;&lt;/th&gt;\n        &lt;/tr&gt;\n        &lt;tr&gt;\n          &lt;td style=&quot;text-align: left;&quot; valign=&quot;top&quot;&gt;Hello,&lt;br&gt;\n            &lt;br&gt;\n            You&#039;re now a member of [SITE_NAME].&lt;br&gt;\n            &lt;br&gt;\n            Your account is now fully activated\n            , and you may login at \n            &lt;a href=&quot;[URL]&quot;&gt;[URL]&lt;/a&gt;\n            &lt;hr&gt;&lt;/td&gt;\n        &lt;/tr&gt;\n        &lt;tr&gt;\n            &lt;td style=&quot;text-align: left;&quot;&gt;&lt;em&gt;Thanks,&lt;br&gt;\n            [SITE_NAME] Team&lt;br&gt;\n            &lt;a href=&quot;[URL]&quot;&gt;[URL]&lt;/a&gt;&lt;/em&gt;&lt;/td&gt;\n        &lt;/tr&gt;\n    &lt;/tbody&gt;\n&lt;/table&gt;\n&lt;/div&gt;');
	  ");
	  
	  $sdata['version'] = "2.50";
	  $db->update("settings", $sdata);
	  
      redirect_to("upgrade.php?install=done");
  }
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>ALS Upgrade Wizard</title>
<style type="text/css">
@import url(http://fonts.googleapis.com/css?family=Roboto:400,100,300,700);
body{font-family:Roboto, Arial, Helvetica, sans-serif;font-size:14px;line-height:1.3em;color:#FFF;background-color:#222;font-weight:300;margin:0;padding:0}
#wrap{width:700px;margin-top:150px;margin-right:auto;margin-left:auto;background-color:#208ed3;box-shadow:2px 2px 2px 2px rgba(0,0,0,0.1);border:2px solid #111;border-radius:3px}
header{background-color:#145983;font-size:26px;font-weight:200;padding:35px}
.line{height:2px;background:linear-gradient(to right, rgba(255,255,255,0) 0%, rgba(255,255,255,1) 47%, rgba(255,255,255,0) 100%)}
.line2{position:absolute;left:200px;height:360px;width:2px;background:linear-gradient(to bottom, rgba(255,255,255,0) 0%, rgba(255,255,255,1) 47%, rgba(255,255,255,0) 100%); display: block}
#content{position:relative;padding:45px 20px}
#content .left{float:left;width:200px;height:400px;background-image:url(assets/images/installer.png);background-repeat:no-repeat;background-position:10px center}
#content .right{margin-left:200px}
h4{font-size:18px;font-weight:300;margin:0 0 40px;padding:0}
p.info{background-color:#383838;border-radius:3px;box-shadow:1px 1px 1px 1px rgba(0,0,0,0.1);padding:10px}
p.info span{ display: block; float: left;padding:10px;background:rgba(255,255,255,0.1);margin-left:-10px;margin-top:-10px;border-radius:3px 0 0 3px;margin-right:5px;border-right:1px solid rgba(255,255,255,0.05) }
footer{background-color:#383838;padding:20px}
form{display:inline-block;float:right;margin:0;padding:0}
.button{border:2px solid #222;font-family:Roboto, Arial, Helvetica, sans-serif;font-size:14px;color:#FFF;background-color:#208ED3;text-align:center;cursor:pointer;font-weight:400;-webkit-transition:all .35s ease;-moz-transition:all .35s ease;-o-transition:all .35s ease;transition:all .35s ease;outline:none;margin:0;padding:5px 20px}
.button:hover{background-color:#222;-webkit-transition:all .55s ease;-moz-transition:all .55s ease;-o-transition:all .35s ease;transition:all .55s ease;outline:none}
.clear{font-size:0;line-height:0;clear:both;height:0}
a{text-decoration:none;float:right}
</style>
</head>
<body>
<div id="wrap">
  <header>Welcome to Advanced Login System Upgrade</header>
  <div class="line"></div>
  <div id="content">
    <div class="left">
      <div class="line2"></div>
    </div>
    <div class="right">
      <h4>Advanced Login System Upgrade Wizard</h4>
      <?php if(isset($_GET['install']) && $_GET['install'] == "done"):?>
      <p class="info"><span>Success!</span>Installation Completed. Please delete upgrade.php</p>
      <?php else:?>
      <p class="info"><span>Warning!</span>Please make sure you have performed database backup!!!</p>
      <p style="margin-top:60px">When ready click Install button.</p>
      <p><span>Please be patient, and<strong> DO NOT</strong> Refresh your browser.<br>
        This process might take a while</span>.</p>
      <?php endif;?>
    </div>
  </div>
  <div class="clear"></div>
  <footer> <small>als v.2.50 | als <?php echo $ver->version;?></small>
    <?php if(isset($_GET['install']) && $_GET['install'] == "done"):?>
    <a href="admin/index.php" class="button">Back to admin panel</a>
    <?php else:?>
    <form action="#" method="post" name="upgrade_form">
      <input name="submit" type="submit" class="button" value="Upgrade als" id="submit" />
    </form>
    <?php endif;?>
    <div class="clear"></div>
  </footer>
</div>
</body>
</html>