<?php
  
  if (!defined("_VALID_PHP"))
      die('Direct access to this location is not allowed.');
?>
<p class="bluetip"><i class="icon-lightbulb icon-3x pull-left"></i><strong>Protecting pages based on user level</strong><br />
Here you can find instructions on how to protect your pages based on user level, login state etc..</p>

<section class="widget">
  <header>
    <div class="row">
      <h1><i class="icon-reorder"></i> Help Section <i class="icon-double-angle-right"></i> Level Protection</h1>
    </div>
  </header>
  <div class="content2">
    <section class="content">
    
    

<div class="pagetip">This is the most versatile way of protecting your pages. We can assign multiple user levels to a single page. <br />
What it means is that users with different user levels can access the same page. You have 6 user levels available plus registered user level. <br />
<em>Please note that there is no difference between registered user and any other user level when it comes to permissions, except in order to access page protected with user level that particular user must be a part of assigned user level 2-7.</em></div>


<div class="pagetip">First start by creating a new php page that you want to give access to all of your users with user level 3 and 4.<br />
  Let's call this page for the purpose of this tutorial <strong>level_3-4only.php</strong><br />
</div>

<div class="pagetip"> <strong>1</strong>. At the very beginning of the page start by adding following php code:
  <?php
highlight_string('
<?php
  define("_VALID_PHP", true);
  require_once("init.php");
?>');
?>
  <br />
  Make sure that <strong>init.php</strong> point to correct directory. For example, If your <strong><em>level_3-4only.php</em></strong> page is in the same directory as main script, than no changes are necessary, otherwise you need to enter correct path to your init.php page. Depending where you placed <strong><em>level_3-4only.php</em></strong> page, below or above root directory init.php becomes <strong>../init.php</strong> if below the root or <strong>otherdir/init.php</strong> if above the root.</div>

<div class="pagetip"> <strong>2</strong>. Now let's add some protection:
  <?php
highlight_string('
<?php
  define("_VALID_PHP", true);
  require_once("init.php");
  
  if (!$user->levelCheck("3,4"))
      redirect_to("index.php");
?>');
?>
  <br />
  The two new lines of code do level verification. First line checks if user belongs to levels <strong>3 or 4</strong>, the second one redirects user to login page <strong>index.php</strong> </div>

<div class="pagetip"> <strong>3</strong>. Now you can continue to add the usual html or php code to your page.</div>

<div class="pagetip"> <em>In this example we protected single page using user level verification process. If user does not have valid user level in this case 3 or 4 we redirect to login page, otherwise, we let the user view the page.</em> </div><br />
<div class="pagetip"> <strong>4</strong>. Another example would be to show customized error message if user does not have valid user level.
  <?php
highlight_string('
<?php
  define("_VALID_PHP", true);
  require_once("init.php");
?>
  <?php if (!$user->levelCheck("3,4")) : ?>
  
      Your custom error message goes here, such as Sorry you do not have valid access!!!
	 
  <?php else: ?>
  
      In this section here you would place your content that users with calid access level will be able to see.
	  
  <?php endif;?>
');
?>

</div>

<div class="pagetip">We only used two levels in this case 3 and 4. You can add multiple levels separated by coma such as <strong>$user->levelCheck("2,3,4,5")</strong>. 
Or just single user level <strong>$user->levelCheck("3")</strong></div>
<div class="pagetip">In order to include administrator in level verification you need to add <strong>9</strong>, or for registered users <strong>1</strong></div>

    </section>
  </div>
</section>