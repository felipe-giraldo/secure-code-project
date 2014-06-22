<?php
  
  define("_VALID_PHP", true);
  require_once("../init.php");

  if (!$user->logged_in)
      redirect_to("../index.php");
?>
<?php
  /* Proccess Transfers */
  if (isset($_POST['processTransfer']))
      : if (intval($_POST['processTransfer']) == 0 || empty($_POST['processTransfer']))
      : redirect_to("../transfers.php");
  endif;
  $core->makeTransfer();
  endif;
?>
<?php
  /* Proccess Pruchases */
  if (isset($_POST['processPurchase']))
      : if (intval($_POST['processPurchase']) == 0 || empty($_POST['processPurchase']))
      : redirect_to("../purchase.php");
  endif;
  $core->makeTransfer();
  endif;
?>