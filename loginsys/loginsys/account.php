<?php
  
  define("_VALID_PHP", true);
  require_once("init.php");
  
  if (!$user->logged_in)
      redirect_to("index.php");
	  
  $row = $user->getUserData();
?>
<?php include("header.php");?>
<p class="bluetip"><i class="icon-lightbulb icon-3x pull-left"></i> Here you can update your user info<br>
  Fields marked <i class="icon-append icon-asterisk"></i> are required.</p>
<form class="xform" id="admin_form" method="post">
  <header>Manage Your Account<span>User Account Edit <i class="icon-double-angle-right"></i> <?php echo $row->username;?></span></header>
  <div class="row">
    <section class="col col-6">
      <label class="input state-disabled"> <i class="icon-prepend icon-user"></i> <i class="icon-append icon-asterisk"></i>
        <input type="text" disabled="disabled" name="username" readonly="readonly" value="<?php echo $row->username;?>" placeholder="Username">
      </label>
      <div class="note note-error">Username</div>
    </section>
    <section class="col col-6">
      <label class="input"> <i class="icon-prepend icon-lock"></i> <i class="icon-append icon-asterisk"></i>
        <input type="password" name="password" placeholder="********">
      </label>
      <div class="note note-info">Leave it empty unless changing the password</div>
    </section>
  </div>
  <div class="row">
    <section class="col col-4">
      <label class="input"> <i class="icon-prepend icon-envelope-alt"></i> <i class="icon-append icon-asterisk"></i>
        <input type="text" name="email" value="<?php echo $row->email;?>" placeholder="Email">
      </label>
      <div class="note note-error">Email</div>
    </section>
    <section class="col col-4">
      <label class="input"> <i class="icon-prepend icon-user"></i>
        <input type="text" name="fname" value="<?php echo $row->fname;?>" placeholder="First Name">
      </label>
      <div class="note note-error">First Name</div>
    </section>
    <section class="col col-4">
      <label class="input"> <i class="icon-prepend icon-user"></i>
        <input type="text" name="lname" value="<?php echo $row->lname;?>" placeholder="Last Name">
      </label>
      <div class="note note-error">Last Name</div>
    </section>
  </div>
  <div class="row">
    <section class="col col-4">
      <label class="radio">
        <input type="radio" name="newsletter" value="1" <?php getChecked($row->newsletter, 1); ?>>
        <i></i>Yes</label>
      <label class="radio">
        <input type="radio" name="newsletter" value="0" <?php getChecked($row->newsletter, 0); ?>>
        <i></i>No</label>
      <div class="note">Newsletter Subscriber</div>
    </section>
    <section class="col col-5">
      <label class="input">
        <input name="avatar" type="file" class="fileinput"/>
      </label>
      <div class="note">User Avatar</div>
    </section>
    <section class="col col-3"> <img src="thumbmaker.php?src=<?php echo UPLOADURL;?><?php echo ($row->avatar) ? $row->avatar : "blank.png";?>&amp;w=<?php echo $core->thumb_w;?>&amp;h=<?php echo $core->thumb_h;?>&amp;s=1&amp;a=t1" alt="" title="" class="avatar" /> </section>
  </div>
  <div class="row">
    <section class="col col-6">
      <label class="input state-disabled"> <i class="icon-prepend icon-calendar"></i>
        <input type="text" name="created" disabled="disabled" readonly="readonly" value="<?php echo $row->cdate;?>" placeholder="Email">
      </label>
      <div class="note">Registration Date:</div>
    </section>
    <section class="col col-6">
      <label class="input state-disabled"> <i class="icon-prepend icon-calendar"></i>
        <input type="text" name="lastlogin" disabled="disabled" readonly="readonly" value="<?php echo $row->ldate;?>" placeholder="First Name">
      </label>
      <div class="note">Last Login</div>
    </section>
  </div>
  <footer>
    <button class="button" name="doupdate" type="submit">Update Profile<span><i class="icon-ok"></i></span></button>
  </footer>
</form>
<?php echo Core::doForm("processUser","ajax/controller.php");?> 
<?php include("footer.php");?>