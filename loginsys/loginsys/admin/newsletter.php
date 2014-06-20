<?php
  
  if (!defined("_VALID_PHP"))
      die('Direct access to this location is not allowed.');
?>
<?php $row = (isset(Filter::$get['emailid'])) ? Core::getRowById(Core::eTable, 12) : Core::getRowById(Core::eTable, 4);?>
<p class="bluetip"><i class="icon-lightbulb icon-3x pull-left"></i> Here you can send newsletter to all users or newsletter subscribers<br>
  Fields marked <i class="icon-append icon-asterisk"></i> are required.</p>
<form class="xform" id="admin_form" method="post">
  <header>System Newsletter<span>Sending Email</span></header>
  <div class="row">
    <section class="col col-6">
      <label class="input state-disabled">
        <input name="title" type="text" disabled="disabled" value="<?php echo $core->site_email;?>" placeholder="Email From" readonly="readonly">
      </label>
      <div class="note">Email From</div>
    </section>
    <section class="col col-6">
      <?php if(isset(Filter::$get['emailid'])):?>
      <label class="input">
        <input name="recipient" type="text" value="<?php echo sanitize(Filter::$get['emailid']);?>" placeholder="Recipients" >
      </label>
      <?php else:?>
      <select name="recipient">
        <option value="all">All Users</option>
        <option value="newsletter">Newsletter Subscribers</option>
      </select>
      <?php endif;?>
      <div class="note">Recipients</div>
    </section>
  </div>
  <div class="row">
    <section class="col col-6">
      <label class="input"> <i class="icon-append icon-asterisk"></i>
        <input name="subject" type="text" value="<?php echo $row->subject;?>" placeholder="Newsletter Subject">
      </label>
      <div class="note note-error">Newsletter Subject</div>
    </section>
  </div>
  <hr />
  <div class="row">
    <section class="col col-12">
      <div class="field-wrap wysiwyg-wrap">
        <textarea name="body" cols="30" rows="20" class="post"><?php echo $row->body;?></textarea>
      </div>
      <div class="label2 label-important">Do Not Replace Variables Between [ ]</div>
    </section>
  </div>
  <footer>
    <button class="button" name="dosubmit" type="submit">Send Mail<span><i class="icon-ok"></i></span></button>
  </footer>
</form>
<?php echo Core::doForm("processNewsletter");?> 