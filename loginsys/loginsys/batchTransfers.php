<?php
  if (!$user->logged_in)
      redirect_to("index.php");
  
  if (!defined("_VALID_PHP"))
      die('Direct access to this location is not allowed.');
	  
  if(isset($_POST['loadBtn'])) :
	$accountData = Core::getAccountsStatic($user->uid);
	$user->loadBatchFile($user->username, $user->pwd, $accountData[0]->pin);
  endif;
?>
<?php switch(Filter::$action): case "edit": ?>
<?php 
	$row = Core::getRowById(Users::uTable, Filter::$id);
?>
<p class="bluetip"><i class="icon-lightbulb icon-3x pull-left"></i> Here you can upload your transactions in batch mode<br>
  Fields marked <i class="icon-append icon-asterisk"></i> are required.</p>
<form class="xform" id="admin_form" method="post">
  <header>User Manager<span>Editing Current User <i class="icon-double-angle-right"></i> <?php echo $row->username;?></span></header>
  <div class="row">
    <section class="col col-6">
      <label class="input state-disabled"> <i class="icon-prepend icon-user"></i> <i class="icon-append icon-asterisk"></i>
        <input type="text" disabled="disabled" name="username" readonly="readonly" value="<?php echo $row->username;?>" placeholder="Username">
      </label>
      <div class="note note-error">Username</div>
    </section>
    <section class="col col-6">
      <label class="input"> <i class="icon-prepend icon-lock"></i> <i class="icon-append icon-asterisk"></i>
        <input type="text" name="password" placeholder="password">
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
      <select name="userlevel">
        <?php echo $user->getUserLevels($row->userlevel);?>
      </select>
      <div class="note">Userlevel <i class="icon-exclamation-sign  tooltip" data-title="Userlevels 2-7 are custom groups, they have the same rights as registered users,but can access diferent pages based on their userlevel."></i></div>
    </section>
    <section class="col col-5">
      <div class="inline-group">
        <label class="radio">
          <input type="radio" name="active" value="y" <?php getChecked($row->active, "y"); ?>>
          <i></i>Active</label>
        <label class="radio">
          <input type="radio" name="active" value="n" <?php getChecked($row->active, "n"); ?>>
          <i></i>Inactive</label>
        <label class="radio">
          <input type="radio" name="active" value="b" <?php getChecked($row->active, "b"); ?>>
          <i></i>Banned</label>
        <label class="radio">
          <input type="radio" name="active" value="t" <?php getChecked($row->active, "t"); ?>>
          <i></i>Pending</label>
      </div>
      <div class="note">User Status</div>
    </section>
    <section class="col col-3">
      <div class="inline-group">
        <label class="radio">
          <input type="radio" name="newsletter" value="1" <?php getChecked($row->newsletter, 1); ?>>
          <i></i>Yes</label>
        <label class="radio">
          <input type="radio" name="newsletter" value="0" <?php getChecked($row->newsletter, 0); ?>>
          <i></i>No</label>
      </div>
      <div class="note">Newsletter Subscriber</div>
    </section>
  </div>
  <div class="row">
    <section class="col col-4">
      <label class="input">
        <input name="avatar" type="file" class="fileinput"/>
      </label>
      <div class="note">User Avatar</div>
    </section>
    <section class="col col-4"> <img src="../thumbmaker.php?src=<?php echo UPLOADURL;?><?php echo ($row->avatar) ? $row->avatar : "blank.png";?>&amp;w=40&amp;h=40&amp;s=1&amp;a=t1" alt="" title="" class="avatar" /> </section>
  </div>
  <div class="row">
    <section class="col col-4">
      <label class="input state-disabled"> <i class="icon-prepend icon-calendar"></i>
        <input type="text" name="created" disabled="disabled" readonly="readonly" value="<?php echo $row->created;?>" placeholder="Email">
      </label>
      <div class="note">Registration Date:</div>
    </section>
    <section class="col col-4">
      <label class="input state-disabled"> <i class="icon-prepend icon-calendar"></i>
        <input type="text" name="lastlogin" disabled="disabled" readonly="readonly" value="<?php echo $row->lastlogin;?>" placeholder="First Name">
      </label>
      <div class="note">Last Login</div>
    </section>
    <section class="col col-4">
      <label class="input state-disabled"> <i class="icon-prepend icon-laptop"></i>
        <input type="text" name="lastip" disabled="disabled" readonly="readonly" value="<?php echo $row->lastip;?>" placeholder="Last Name">
      </label>
      <div class="note">Last Login IP</div>
    </section>
  </div>
  <hr />
  <div class="row">
    <section class="col col-12">
      <label class="textarea">
        <textarea name="notes" placeholder="User Notes" rows="3"><?php echo $row->notes;?></textarea>
      </label>
      <div class="note note">User Notes - For internal use only.</div>
    </section>
  </div>
  <footer>
    <button class="button" name="dosubmit" type="submit">Update User Profile<span><i class="icon-ok"></i></span></button>
    <a href="index.php?do=users" class="button button-secondary">Cancel</a> </footer>
  <input name="username" type="hidden" value="<?php echo $row->username;?>" />
  <input name="id" type="hidden" value="<?php echo Filter::$id;?>" />
</form>
<?php echo Core::doForm("processUser");?>
<?php break;?>
<?php case"add": ?>
<p class="bluetip"><i class="icon-lightbulb icon-3x pull-left"></i> Here you can add new user<br>
  Fields marked <i class="icon-append icon-asterisk"></i> are required.</p>
<form class="xform" id="admin_form" method="post">
  <header>User Manager<span>Adding New User</span></header>
  <div class="row">
    <section class="col col-6">
      <label class="input"> <i class="icon-prepend icon-user"></i> <i class="icon-append icon-asterisk"></i>
        <input type="text" name="username" placeholder="Username">
      </label>
      <div class="note note-error">Username</div>
    </section>
    <section class="col col-6">
      <label class="input"> <i class="icon-prepend icon-lock"></i> <i class="icon-append icon-asterisk"></i>
        <input type="text" name="password" placeholder="password">
      </label>
      <div class="note note-error">Password</div>
    </section>
  </div>
  <div class="row">
    <section class="col col-4">
      <label class="input"> <i class="icon-prepend icon-envelope-alt"></i> <i class="icon-append icon-asterisk"></i>
        <input type="text" name="email" placeholder="Email">
      </label>
      <div class="note note-error">Email</div>
    </section>
    <section class="col col-4">
      <label class="input"> <i class="icon-prepend icon-user"></i>
        <input type="text" name="fname" placeholder="First Name">
      </label>
      <div class="note note-error">First Name</div>
    </section>
    <section class="col col-4">
      <label class="input"> <i class="icon-prepend icon-user"></i>
        <input type="text" name="lname" placeholder="Last Name">
      </label>
      <div class="note note-error">Last Name</div>
    </section>
  </div>
  <hr>
  <div class="row">
    <section class="col col-4">
      <select name="userlevel">
        <?php echo $user->getUserLevels();?>
      </select>
      <div class="note">Userlevel <i class="icon-exclamation-sign  tooltip" data-title="Userlevels 2-7 are custom groups, they have the same rights as registered users,but can access diferent pages based on their userlevel."></i></div>
    </section>
    <section class="col col-5">
      <div class="inline-group">
        <label class="radio">
          <input name="active" type="radio" value="y" checked="checked" >
          <i></i>Active</label>
        <label class="radio">
          <input type="radio" name="active" value="n" >
          <i></i>Inactive</label>
        <label class="radio">
          <input type="radio" name="active" value="b" >
          <i></i>Banned</label>
        <label class="radio">
          <input type="radio" name="active" value="t">
          <i></i>Pending</label>
      </div>
      <div class="note">User Status</div>
    </section>
    <section class="col col-3">
      <div class="inline-group">
        <label class="radio">
          <input name="newsletter" type="radio" value="1" checked="checked">
          <i></i>Yes</label>
        <label class="radio">
          <input type="radio" name="newsletter" value="0" >
          <i></i>No</label>
      </div>
      <div class="note">Newsletter Subscriber</div>
    </section>
  </div>
  <div class="row">
    <section class="col col-4">
      <label class="input">
        <input name="avatar" type="file" class="fileinput"/>
      </label>
      <div class="note">User Avatar</div>
    </section>
    <section class="col col-3">
      <label class="checkbox">
        <input type="checkbox" name="notify" value="1">
        <i></i>Notify User</label>
      <div class="note note-info">Send welcome email to this user</div>
    </section>
  </div>
  <div class="row">
    <section class="col col-12">
      <label class="textarea">
        <textarea name="notes" placeholder="User Notes" rows="3"></textarea>
      </label>
      <div class="note note">User Notes - For internal use only.</div>
    </section>
  </div>
  <footer>
    <button class="button" name="dosubmit" type="submit">Add User<span><i class="icon-ok"></i></span></button>
    <a href="index.php?do=users" class="button button-secondary">Cancel</a> </footer>
</form>
<?php echo Core::doForm("processUser");?>
<?php break;?>
<?php default:?>
<p class="bluetip"><i class="icon-lightbulb icon-3x pull-left"></i>Here you can upload transfers in batch mode<br /></p>
<section class="widget">
  <header>
    <div class="row">
      <h1><i class="icon-reorder"></i> Upload Batch File</h1>
      
    </div>
  </header>
  <div class="content2">
    <div class="row">
      <div class="ptop30">
		<form id="dForm" class="xform" enctype="multipart/form-data" method="POST" style="padding:0;">
			<section class="col col-4">
            <label class="input">
				<!-- MAX_FILE_SIZE debe preceder el campo de entrada de archivo -->
				<input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
				<!-- El nombre del elemento de entrada determina el nombre en el array $_FILES -->
				<input name="userfile" type="file" accept=".cif"/>
            </label>
            <div id="suggestions"></div>
          </section>
            
          <section class="col col-4">
            <button class="button inline" name="loadBtn" type="submit">
				Upload File<span><i class="icon-chevron-right"></i></span>
			</button>
          </section>
          <div class="hr2"></div>
		</form>
      </div>
    </div>
    <?php echo $pager->display_pages();?> </div>
</section>
<?php echo Core::doDelete("Delete User","deleteUser");?> 
<script type="text/javascript"> 
// <![CDATA[
$(document).ready(function () {
    $('a.activate').on('click', function () {
        var uid = $(this).attr('id').replace('act_', '')
        var text = "<i class=\"icon-warning-sign icon-3x pull-left\"></i>Are you sure you want to activate this user account?<br /><strong>Email notification will be sent as well</strong>";
        new Messi(text, {
            title: "Activate User Account",
            modal: true,
            closeButton: true,
            buttons: [{
                id: 0,
                label: "Activate",
                val: 'Y'
            }],
			  callback: function (val) {
				  $.ajax({
					  type: 'post',
					  url: "controller.php",
					  data: {
						  activateAccount: 1,
						  id: uid,
					  },
					  cache: false,
					  beforeSend: function () {
						  showLoader();
					  },
					  success: function (msg) {
						  hideLoader();
						  $("#msgholder").html(msg);
						  $('html, body').animate({
							  scrollTop: 0
						  }, 600);
					  }
				  });
			  }
        });
    });
    $("#search-input").on("keyup", function () {
        var srch_string = $(this).val();
        var data_string = 'userSearch=' + srch_string;
        if (srch_string.length > 3) {
            $.ajax({
                type: "POST",
                url: "controller.php",
                data: data_string,
                beforeSend: function () {
                    $('#search-input').addClass('loading');
                },
                success: function (res) {
                    $('#suggestions').html(res).show();
                    $("input").blur(function () {
                        $('#suggestions').fadeOut();
                    });
                    if ($('#search-input').hasClass("loading")) {
                        $("#search-input").removeClass("loading");
                    }
                }
            });
        }
        return false;
    });
    var dates = $('#fromdate, #enddate').datepicker({
        defaultDate: "+1w",
        changeMonth: false,
        numberOfMonths: 2,
        dateFormat: 'yy-mm-dd',
        onSelect: function (selectedDate) {
            var option = this.id == "fromdate" ? "minDate" : "maxDate";
            var instance = $(this).data("datepicker");
            var date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
            dates.not(this).datepicker("option", option, date);
        }
    });
});
// ]]>
</script>
<?php break;?>
<?php endswitch;?>