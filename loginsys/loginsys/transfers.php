<?php
  
  if (!defined("_VALID_PHP"))
      die('Direct access to this location is not allowed.');
?>

<?php //$userrow = $user->getUsers();
    $row = $user->getUserData();
    $useraccounts = $core->getAccounts();
?>
<?php //echo Core::doForm("processTransfer","ajax/controller_tr.php");
    if (isset($_POST['btnTransfer'])):
        $row = $user->getUserData();
        $core->makeTransfer($row->id, 1);
        //haceTransfer($row->id, $_POST['t_token'], $_POST['origin_account'], $_POST['destination_account'], $_POST['ammount']);
        //redirect_to("main.php?do=transactions");
    endif;
    if (isset($_POST['btnCancel'])):
        redirect_to("account.php");
    endif;
?> 
<p class="bluetip"><i class="icon-lightbulb icon-3x pull-left"></i>Here you can transfer money to other accounts <br />
  The destination account must be linked to your main account in order to make the transfer.</p>
<section class="widget">
  <header>
    <div class="row">
      <h1><i class="icon-reorder"></i> Funds transfer</h1>
      
    </div>
  </header>
  <div class="content2">
    <div class="row">
      <div class="ptop30">
        <form class="xform" id="dForm" method="post" style="padding:0;">
          <section class="col col-6">
            <select name="origin_account" id="userfilter">
              <option value="NA">--- Origin Account ---</option>
              <?php foreach($useraccounts as $accounts): ?>
              <option value='<?php echo $accounts->id_account; ?>'><?php echo $accounts->id_account; ?></option>
              <?php endforeach; ?>
            </select>
          </section>
            <section class="col col-6">
            <select name="destination_account" id="userfilter">
              <option value="NA">--- Destination Account ---</option>
              <?php foreach($useraccounts as $accounts): ?>
              <option value='<?php echo $accounts->id_account; ?>'><?php echo $accounts->id_account; ?></option>
              <?php endforeach; ?>
            </select>
          </section>
            <section class="col col-4">
            <label class="input"> <i class="icon-prepend icon-search"></i>
              <input type="text" name="ammount"  id="search-input" placeholder="Ammount to transfer">
            </label>
            <div id="suggestions"></div>
          </section>
            <section class="col col-4">
            <label class="input"> <i class="icon-prepend icon-search"></i>
              <input type="text" name="t_token"  id="search-input" placeholder="Token for transaction">
            </label>
            <div id="suggestions"></div>
          </section>
            <section class="col col-4">
            <button class="button inline" name="btnTransfer" type="submit">Transfer<span><i class="icon-chevron-right"></i></span></button>
            <button class="button button-red inline" name="btnCancel" type="submit">Cancel<span><i class="icon-chevron-right"></i></span></button>
          </section>
          <div class="hr2"></div>
          
        </form>
      </div>
    </div>
    <?php echo $pager->display_pages();?> </div>
</section>

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