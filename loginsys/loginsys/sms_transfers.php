<?php
  if (!$user->logged_in)
      redirect_to("index.php");
  
  if (!defined("_VALID_PHP"))
      die('Direct access to this location is not allowed.');
?>

<?php 
    $row = $user->getUserData();
    $useraccounts = $core->getAccounts($row->id);
?>
<?php 
    if (isset($_POST['btnTransfer'])):
        $row = $user->getUserData();
        $core->smcTransfer($row->id);
    endif;
    if (isset($_POST['btnCancel'])):
        redirect_to("account.php");
    endif;
    
    /*
    $string = '39695959' . '|' . '86423334' . '|' .  '1000' . '|' . '7584';
    $string = hash('SHA256', $string);
    $sub_string = substr($string, 0, 6);
    
    echo $sub_string;
     * 
     */
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
            <select name="origin_account" id="userfilter" class="validate[required]">
              <option value="NA">--- Origin Account ---</option>
              <?php foreach($useraccounts as $accounts): ?>
              <option value='<?php echo $accounts->id_account; ?>'><?php echo $accounts->id_account; ?></option>
              <?php endforeach; ?>
            </select>
          </section>
            <section class="col col-4">
            <label class="input"> <i class="icon-prepend icon-search"></i>
              <input type="text" name="destination_account"  id="search-input2" class="validate[required, custom[integer], min[0]]" placeholder="Destination Account">
            </label>
            <div id="suggestions"></div>
          </section>
            <section class="col col-4">
            <label class="input"> <i class="icon-prepend icon-search"></i>
              <input type="text" name="ammount"  id="search-input2" class="validate[required, custom[integer], min[0]]" placeholder="Ammount to transfer">
            </label>
            <div id="suggestions"></div>
          </section>
            <section class="col col-4">
            <label class="input"> <i class="icon-prepend icon-search"></i>
              <input type="text" name="t_token" class="validate[required, minSize[15], maxSize[15] ]" id="search-input" placeholder="PIN for transaction">
            </label>
            <div id="suggestions"></div>
          </section>
            <section class="col col-4">
            <label class="input"> <i class="icon-prepend icon-search"></i>
              <input type="text" name="v_token" class="validate[required, minSize[15], maxSize[15] ]" id="search-input" placeholder="Token for transaction">
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
