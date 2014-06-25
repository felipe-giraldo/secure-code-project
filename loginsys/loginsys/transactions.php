<?php
  if (!$user->logged_in)
      redirect_to("index.php");
  
  if (!defined("_VALID_PHP"))
      die('Direct access to this location is not allowed.');
?>

<?php 
$row = $user->getUserData();
$userrow = $core->getTransactions($row->id);?>
<p class="bluetip"><i class="icon-lightbulb icon-3x pull-left"></i>Here you can see your transaction history <br /></p>
<section class="widget">
  <header>
    <div class="row">
      <h1><i class="icon-reorder"></i> Transactions</h1>
      
    </div>
  </header>
  <div class="content2">
    
    <table class="myTable">
      <thead>
        <tr>
          <th width="header" class="left">#</th>
          <th class="header">From account #</th>
          <th class="header">To account #</th>
          <th class="header">Ammount</th>
          <th class="header">Token used</th>
          <th class="header">Date</th>
          <th class="header">Type</th>
          <th class="header">State</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($userrow as $row):?>
        <tr>
          <th><?php echo $row->id;?>.</th>
          <td><?php echo $row->from_account;?></td>
          <td><?php echo $row->to_account;?></td>
          <td><?php echo $row->ammount;?></td>
          <td><?php echo $row->token;?></td>
          <td><?php echo gmdate("Y-m-d H:i:s", $row->transaction_date);?></td>
          <td><?php echo ($row->transaction_type == 1) ? 'Transfer' : 'Online Purchase';?></td>
          <?php if($row->transaction_state == 1):
              print('<td>Approved</td>');
          elseif($row->transaction_state == 2):
              print('<td>Rejected</td>');
          else:
              print('<td>Pending</td>');
          endif;
          ?>
          
        </tr>
        <?php endforeach;?>
        <?php unset($row);?>
    </table>
    <?php echo $pager->display_pages();?> </div>
</section>
<?php //echo Core::doDelete("Delete User","deleteUser");
    
?> 
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