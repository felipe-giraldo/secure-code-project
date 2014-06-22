<?php
  
  
  if (!defined("_VALID_PHP"))
      die('Direct access to this location is not allowed.');

  class Users
  {
	  const uTable = "users";
      const acTable = 'account';
      const toTable = "user_token";
      
	  public $logged_in = null;
	  public $uid = 0;
	  public $userid = 0;
      public $username;
	  public $email;
	  public $name;
      public $userlevel;
	  public $last;
	  private $lastlogin = "NOW()";
	  private static $db;
      

      /**
       * Users::__construct()
       * 
       * @return
       */
      function __construct()
      {
		  self::$db = Registry::get("Database");
		  $this->startSession();
      }
 

      /**
       * Users::startSession()
       * 
       * @return
       */
      private function startSession()
      {
          if (strlen(session_id()) < 1)
              session_start();

          $this->logged_in = $this->loginCheck();

          if (!$this->logged_in) {
              $this->username = $_SESSION['username'] = "Guest";
              $this->sesid = md5(session_id());
              $this->userlevel = 0;
          }
      }

	  /**
	   * Users::loginCheck()
	   * 
	   * @return
	   */
      private function loginCheck()
      {
          if (isset($_SESSION['username']) && $_SESSION['username'] != "Guest") {
			  
              $row = $this->getUserInfo($_SESSION['username']);
              $this->uid = $row->id;
              $this->username = $row->username;
              $this->email = $row->email;
              $this->name = $row->fname . ' ' . $row->lname;
              $this->userlevel = $row->userlevel;
			  $this->last = $row->lastlogin;
              return true;
          } else {
              return false;
          }
      }

	  /**
	   * Users::is_Admin()
	   * 
	   * @return
	   */
	  public function is_Admin()
	  {
		  return($this->userlevel == 9);
	  
	  }	

	  /**
	   * Users::login()
	   * 
	   * @param mixed $username
	   * @param mixed $pass
	   * @return
	   */
	  public function login($username, $pass)
	  {

		  if ($username == "" && $pass == "") {
			  Filter::$msgs['username'] = 'Please enter valid username and password.';
		  } else {
			  $status = $this->checkStatus($username, $pass);
			  
              switch ($status) {
                  case 0:
                      Filter::$msgs['username'] = 'Login and/or password did not match to the database.';
                      break;

                  case 1:
                      Filter::$msgs['username'] = 'Your account has been banned.';
                      break;

                  case 2:
                      Filter::$msgs['username'] = 'Your account it\'s not activated.';
                      break;

                  case 3:
                      Filter::$msgs['username'] = 'You need to verify your email address.';
                      break;
              }
		  }
          if (empty(Filter::$msgs) && $status == 5) {
              $row = $this->getUserInfo($username);
              $this->uid = $_SESSION['userid'] = $row->id;
              $this->username = $_SESSION['username'] = $row->username;
              $this->email = $_SESSION['email'] = $row->email;
              $this->name = $_SESSION['name'] = $row->fname . ' ' . $row->lname;
              $this->userlevel = $_SESSION['userlevel'] = $row->userlevel;
			  $this->last = $_SESSION['last'] = $row->lastlogin;

			  $data = array(
					'lastlogin' => $this->lastlogin, 
					'lastip' => sanitize($_SERVER['REMOTE_ADDR'])
			  );

			  self::$db->update(self::uTable, $data, "username='" . $this->username . "'");
				  
			  return true;
		  } else
			  Filter::msgStatus();
	  }

      /**
       * Users::logout()
       * 
       * @return
       */
      public function logout()
      {
          unset($_SESSION['username']);
		  unset($_SESSION['email']);
		  unset($_SESSION['name']);
          unset($_SESSION['userid']);
          session_destroy();
		  session_regenerate_id();
          
          $this->logged_in = false;
          $this->username = "Guest";
          $this->userlevel = 0;
      }

	  /**
	   * Users::getUserInfo()
	   * 
	   * @param mixed $username
	   * @return
	   */
      private function getUserInfo($username)
      {
          $username = sanitize($username);
          $username = self::$db->escape($username);

          $sql = "SELECT * FROM " . self::uTable . " WHERE username = '" . $username . "'";
          $row = self::$db->first($sql);
          if (!$username)
              return false;

          return ($row) ? $row : 0;
      }

	  /**
	   * Users::checkStatus()
	   * 
	   * @param mixed $username
	   * @param mixed $pass
	   * @return
	   */
      public function checkStatus($username, $pass)
      {

          $username = sanitize($username);
          $username = self::$db->escape($username);
          $pass = sanitize($pass);

          $sql = "SELECT password, active FROM " . self::uTable 
		  . "\n WHERE username = '" . $username . "'";
          $result = self::$db->query($sql);

          if (self::$db->numrows($result) == 0)
              return 0;

          $row = self::$db->fetch($result);
          $entered_pass = md5($pass);

          switch ($row->active) {
              case "b":
                  return 1;
                  break;

              case "n":
                  return 2;
                  break;

              case "t":
                  return 3;
                  break;

              case "y" && $entered_pass == $row->password:
                  return 5;
                  break;
          }
      }

	  /**
	   * Users::getUsers()
	   * 
	   * @param bool $from
	   * @return
	   */
	  public function getUsers($from = false)
	  {
		  
          $pager = Paginator::instance();
          $pager->items_total = countEntries(self::uTable);
          $pager->default_ipp = Registry::get("Core")->perpage;
          $pager->paginate();

		  $clause = (isset($clause)) ? $clause : null;

          if (isset($_GET['sort'])) {
              list($sort, $order) = explode("-", $_GET['sort']);
              $sort = sanitize($sort);
              $order = sanitize($order);
              if (in_array($sort, array(
                  "username",
                  "fname",
                  "lname",
                  "email"))
				  ) {
                  $ord = ($order == 'DESC') ? " DESC" : " ASC";
                  $sorting = $sort . $ord;
              } else {
                  $sorting = " created DESC";
              }
          } else {
              $sorting = " created DESC";
          }
		  
          if (isset($_POST['fromdate']) && $_POST['fromdate'] <> "" || isset($from) && $from != '') {
              $enddate = date("Y-m-d");
              $fromdate = (empty($from)) ? $_POST['fromdate'] : $from;
              if (isset($_POST['enddate']) && $_POST['enddate'] <> "") {
                  $enddate = $_POST['enddate'];
              }
              $clause .= " WHERE created BETWEEN '" . trim($fromdate) . "' AND '" . trim($enddate) . " 23:59:59'";
          } 
		  
          $sql = "SELECT *, CONCAT(fname,' ',lname) as name,"
		  . "\n DATE_FORMAT(created, '%d. %b. %Y %H:%i') as cdate,"
		  . "\n DATE_FORMAT(lastlogin, '%d. %b. %Y %H:%i') as adate"
		  . "\n FROM " . self::uTable
		  . "\n " . $clause
		  . "\n ORDER BY " . $sorting . $pager->limit;
          $row = self::$db->fetch_all($sql);
          
		  return ($row) ? $row : 0;
	  }
      
      

	  /**
	   * Users::processUser()
	   * 
	   * @return
	   */
	  public function processUser()
	  {

		  if (!Filter::$id) {
			  if (empty($_POST['username']))
				  Filter::$msgs['username'] = 'Please Enter Valid Username';
			  
			  if ($value = $this->usernameExists($_POST['username'])) {
				  if ($value == 1)
					  Filter::$msgs['username'] = 'Username Is Too Short (less Than 4 Characters Long).';
				  if ($value == 2)
					  Filter::$msgs['username'] = 'Invalid Characters Found In Username.';
				  if ($value == 3)
					  Filter::$msgs['username'] = 'Sorry, This Username Is Already Taken';
			  }
		  }

		  if (empty($_POST['fname']))
			  Filter::$msgs['fname'] = 'Please Enter First Name';
			  
		  if (empty($_POST['lname']))
			  Filter::$msgs['lname'] = 'Please Enter Last Name';
			  
		  if (!Filter::$id) {
			  if (empty($_POST['password']))
				  Filter::$msgs['password'] = 'Please Enter Valid Password.';
		  }

		  if (empty($_POST['email']))
			  Filter::$msgs['email'] = 'Please Enter Valid Email Address';
		  if (!Filter::$id) {
			  if ($this->emailExists($_POST['email']))
				  Filter::$msgs['email'] = 'Entered Email Address Is Already In Use.';
		  }
		  if (!$this->isValidEmail($_POST['email']))
			  Filter::$msgs['email'] = 'Entered Email Address Is Not Valid.';

          if (!empty($_FILES['avatar']['name'])) {
              if (!preg_match("/(\.jpg|\.png)$/i", $_FILES['avatar']['name'])) {
                  Filter::$msgs['avatar'] = "Illegal file type. Only jpg and png file types allowed.";
              }
              $file_info = getimagesize($_FILES['avatar']['tmp_name']);
              if (empty($file_info))
                  Filter::$msgs['avatar'] = "Illegal file type. Only jpg and png file types allowed.";
          }
		  
		  if (empty(Filter::$msgs)) {
			  
			  $data = array(
				  'username' => sanitize($_POST['username']), 
				  'email' => sanitize($_POST['email']), 
				  'lname' => sanitize($_POST['lname']), 
				  'fname' => sanitize($_POST['fname']), 
				  'newsletter' => intval($_POST['newsletter']),
				  'notes' => sanitize($_POST['notes']),
				  'userlevel' => intval($_POST['userlevel']), 
				  'active' => sanitize($_POST['active'])
			  );

              if (!Filter::$id)
                  $data['created'] = "NOW()";
				   
              if (Filter::$id)
                  $userrow = Registry::get("Core")->getRowById(self::uTable, Filter::$id);
			  
			  if ($_POST['password'] != "") {
				  $data['password'] = md5($_POST['password']);
			  } else {
				  $data['password'] = $userrow->password;
			  }

              // Procces Avatar
              if (!empty($_FILES['avatar']['name'])) {
                  $thumbdir = UPLOADS;
                  $tName = "AVT_" . randName();
                  $text = substr($_FILES['avatar']['name'], strrpos($_FILES['avatar']['name'], '.') + 1);
                  $thumbName = $thumbdir . $tName . "." . strtolower($text);
                  if (Filter::$id && $thumb = getValueById("avatar", self::uTable, Filter::$id)) {
                      @unlink($thumbdir . $thumb);
                  }
                  move_uploaded_file($_FILES['avatar']['tmp_name'], $thumbName);
                  $data['avatar'] = $tName . "." . strtolower($text);
              }
			  
				  
              (Filter::$id) ? self::$db->update(self::uTable, $data, "id='" . Filter::$id . "'") : self::$db->insert(self::uTable, $data);
              $message = (Filter::$id) ? '<span>Success!</span>User updated successfully!' : '<span>Success!</span>User added successfully!';

              if (self::$db->affected()) {
                  Filter::msgOk($message);
				  
                  if (isset($_POST['notify']) && intval($_POST['notify']) == 1) {
                      require_once (BASEPATH . "lib/class_mailer.php");
                      $mailer = $mail->sendMail();

                      $row = Registry::get("Core")->getRowById("email_templates", 3);

                      $body = str_replace(array(
                          '[USERNAME]',
                          '[PASSWORD]',
                          '[NAME]',
                          '[SITE_NAME]',
                          '[URL]'), array(
                          $data['username'],
                          $_POST['password'],
                          $data['fname'] . ' ' . $data['lname'],
                          Registry::get("Core")->site_name,
                          Registry::get("Core")->site_url), $row->body);

                      $msg = Swift_Message::newInstance()
								->setSubject($row->subject)
								->setTo(array($data['email'] => $data['fname'] . ' ' . $data['lname']))
								->setFrom(array(Registry::get("Core")->site_email => Registry::get("Core")->site_name))
								->setBody(cleanOut($body), 'text/html');

                      $numSent = $mailer->send($msg);
                  }
              } else
                  Filter::msgAlert('<span>Alert!</span>Nothing to process.');
          } else
              print Filter::msgStatus();
      }

	  /**
	   * Users::updateProfile()
	   * 
	   * @return
	   */
	  public function updateProfile()
	  {

		  if (empty($_POST['fname']))
			  Filter::$msgs['fname'] = 'Please Enter First Name';
			  
		  if (empty($_POST['lname']))
			  Filter::$msgs['lname'] = 'Please Enter Last Name';

		  if (empty($_POST['email']))
			  Filter::$msgs['email'] = 'Please Enter Valid Email Address';

		  if (!$this->isValidEmail($_POST['email']))
			  Filter::$msgs['email'] = 'Entered Email Address Is Not Valid.';

          if (!empty($_FILES['avatar']['name'])) {
              if (!preg_match("/(\.jpg|\.png)$/i", $_FILES['avatar']['name'])) {
                  Filter::$msgs['avatar'] = "Illegal file type. Only jpg and png file types allowed.";
              }
              $file_info = getimagesize($_FILES['avatar']['tmp_name']);
              if (empty($file_info))
                  Filter::$msgs['avatar'] = "Illegal file type. Only jpg and png file types allowed.";
          }
		  
		  if (empty(Filter::$msgs)) {
			  
			  $data = array(
				  'email' => sanitize($_POST['email']), 
				  'lname' => sanitize($_POST['lname']), 
				  'fname' => sanitize($_POST['fname']), 
				  'newsletter' => intval($_POST['newsletter'])
			  );

              // Procces Avatar
              if (!empty($_FILES['avatar']['name'])) {
                  $thumbdir = UPLOADS;
                  $tName = "AVT_" . randName();
                  $text = substr($_FILES['avatar']['name'], strrpos($_FILES['avatar']['name'], '.') + 1);
                  $thumbName = $thumbdir . $tName . "." . strtolower($text);
                  if (Filter::$id && $thumb = getValueById("avatar", self::uTable, Filter::$id)) {
                      @unlink($thumbdir . $thumb);
                  }
                  move_uploaded_file($_FILES['avatar']['tmp_name'], $thumbName);
                  $data['avatar'] = $tName . "." . strtolower($text);
              }
			     
			  $userpass = getValueById("password", self::uTable, $this->uid);
			  
			  if ($_POST['password'] != "") {
				  $data['password'] = md5($_POST['password']);
			  } else
				  $data['password'] = $userpass;
			  
              self::$db->update(self::uTable, $data, "id='" . $this->uid . "'");

              (self::$db->affected()) ? Filter::msgOk('<span>Success!</span> You have successfully updated your profile.') : Filter::msgAlert('<span>Alert!</span>Nothing to process.');
          } else
              print Filter::msgStatus();
      }

      /**
       * User::register()
       * 
       * @return
       */
	  public function register()
	  {		  
		  if (empty($_POST['username']))
			  Filter::$msgs['username'] = 'Please Enter Valid Username';
		  
		  if ($value = $this->usernameExists($_POST['username'])) {
			  if ($value == 1)
				  Filter::$msgs['username'] = 'Username Is Too Short (less Than 4 Characters Long).';
			  if ($value == 2)
				  Filter::$msgs['username'] = 'Invalid Characters Found In Username.';
			  if ($value == 3)
				  Filter::$msgs['username'] = 'Sorry, This Username Is Already Taken';
		  }

		  if (empty($_POST['fname']))
			  Filter::$msgs['fname'] = 'Please Enter First Name';
			  
		  if (empty($_POST['lname']))
			  Filter::$msgs['lname'] = 'Please Enter Last Name';
			  
		  if (empty($_POST['pass']))
			  $this->msgs['pass'] = 'Please Enter Valid Password.';
		  
		  if (strlen($_POST['pass']) < 6)
			  Filter::$msgs['pass'] = 'Password is too short (less than 6 characters long)';
		  elseif (!preg_match("/^[a-z0-9_-]{6,15}$/", ($_POST['pass'] = trim($_POST['pass']))))
			  Filter::$msgs['pass'] = 'Password entered is not alphanumeric.';
		  elseif ($_POST['pass'] != $_POST['pass2'])
			  Filter::$msgs['pass'] = 'Your password did not match the confirmed password!.';
		  
		  if (empty($_POST['email']))
			  Filter::$msgs['email'] = 'Please Enter Valid Email Address';
		  
		  if ($this->emailExists($_POST['email']))
			  Filter::$msgs['email'] = 'Entered Email Address Is Already In Use.';
		  
		  if (!$this->isValidEmail($_POST['email']))
			  Filter::$msgs['email'] = 'Entered Email Address Is Not Valid.';
			  		  
		  if (empty($_POST['captcha']))
			  Filter::$msgs['captcha'] = 'Please enter captcha code!';
		  
		  if ($_SESSION['captchacode'] != $_POST['captcha'])
			  Filter::$msgs['captcha'] = "Entered captcha code is incorrect";
		  
		  if (empty(Filter::$msgs)) {

			  $token = (Registry::get("Core")->reg_verify == 1) ? $this->generateRandID() : 0;
			  $pass = sanitize($_POST['pass']);
			  
              if (Registry::get("Core")->reg_verify == 1) {
                  $active = "t";
              } elseif (Registry::get("Core")->auto_verify == 0) {
                  $active = "n";
              } else {
                  $active = "n";        //this one should be Y in regular cases.
              }
				  
			  $data = array(
					  'username' => sanitize($_POST['username']), 
					  'password' => md5($_POST['pass']),
					  'email' => sanitize($_POST['email']), 
					  'fname' => sanitize($_POST['fname']),
					  'lname' => sanitize($_POST['lname']),
					  'token' => $token,
					  'active' => $active, 
					  'created' => "NOW()"
			  );
			  
			  self::$db->insert(self::uTable, $data);
              /**
               * new modification
               */
              $user = $this->getUserInfo($_POST['username']);
              
              $account = $this->generateStrongPassword(10, 'd');
              $array = array(
                  'id_account' => $account,
                  'id_user' => $user->id,
                  'money' => 0,
                  'active' => 1
              );
                  
              self::$db->insert(self::acTable, $array);
              
              //self::$db->insert(self::toTable, $account);
              for($i = 0; $i< 100; $i++){
                  $tokens = $this->generateStrongPassword(15, 'lud');
                  $this->setTokens($tokens, $account); 
                  $temp_message .= '<b>' . $tokens . '</b><br>';
              }
              
              $message = 'Estimado ' . sanitize($_POST['fname']) .' '.sanitize($_POST['lname']) . ' Usted ha sido registrado adecuadamente.';
              $message .= '<br>Su nombre de usuario: ' . sanitize($_POST['username']) . ' Su numero de cuenta: ' . $account . '<br><br>';
              $message .= 'Estos son los TOKENS asignados a su cuenta: <br><br><br>';
              $message .= $temp_message;
              
              
              
              sendMail('kaspalone@gmail.com', $message);
              /*
               * end new modification
               */
              
              /*
			  require_once(BASEPATH . "lib/class_mailer.php");
			  
              if (Registry::get("Core")->reg_verify == 1) {
                  $actlink = Registry::get("Core")->site_url . "/activate.php";
                  $row = Registry::get("Core")->getRowById("email_templates", 1);
				  
                  $body = str_replace(array(
                      '[NAME]',
                      '[USERNAME]',
                      '[PASSWORD]',
                      '[TOKEN]',
                      '[EMAIL]',
                      '[URL]',
                      '[LINK]',
                      '[SITE_NAME]'), array(
                      $data['fname'] . ' ' . $data['lname'],
                      $data['username'],
                      $_POST['pass'],
                      $token,
                      $data['email'],
                      Registry::get("Core")->site_url,
                      $actlink,
                      Registry::get("Core")->site_name), $row->body);
						
				 $newbody = cleanOut($body);	
					 
				  $mailer = $mail->sendMail();
                  $message = Swift_Message::newInstance()
							->setSubject($row->subject)
							->setTo(array($data['email'] => $data['username']))
							->setFrom(array(Registry::get("Core")->site_email => Registry::get("Core")->site_name))
							->setBody($newbody, 'text/html');

                  $mailer->send($message);
				 
              } elseif (Registry::get("Core")->auto_verify == 0) {
                  $row = Registry::get("Core")->getRowById("email_templates", 14);
				  
                  $body = str_replace(array(
                      '[NAME]',
                      '[USERNAME]',
                      '[PASSWORD]',
                      '[URL]',
                      '[SITE_NAME]'), array(
                      $data['fname'] . ' ' . $data['lname'],
                      $data['username'],
                      $_POST['pass'],
                      Registry::get("Core")->site_url,
                      Registry::get("Core") > site_name), $row->body);
						
				 $newbody = cleanOut($body);	

				  $mailer = $mail->sendMail();
                  $message = Swift_Message::newInstance()
							->setSubject($row->subject)
							->setTo(array($data['email'] => $data['username']))
							->setFrom(array(Registry::get("Core")->site_email => Registry::get("Core")->site_name))
							->setBody($newbody, 'text/html');
							
				 $mailer->send($message); 
				  
			  } else {
				  $row = Registry::get("Core")->getRowById("email_templates", 7);
				  
                  $body = str_replace(array(
                      '[NAME]',
                      '[USERNAME]',
                      '[PASSWORD]',
                      '[URL]',
                      '[SITE_NAME]'), array(
                      $data['fname'] . ' ' . $data['lname'],
                      $data['username'],
                      $_POST['pass'],
                      Registry::get("Core")->site_url,
                      Registry::get("Core")->site_name), $row->body);
						
				 $newbody = cleanOut($body);	

				  $mailer = $mail->sendMail();
                  $message = Swift_Message::newInstance()
							->setSubject($row->subject)
							->setTo(array($data['email'] => $data['username']))
							->setFrom(array(Registry::get("Core")->site_email => Registry::get("Core")->site_name))
							->setBody($newbody, 'text/html');
							
				 $mailer->send($message);

			  }
              if (Registry::get("Core")->notify_admin) {
                  $arow = Registry::get("Core")->getRowById("email_templates", 13);
  
                  $abody = str_replace(array(
                      '[USERNAME]',
                      '[EMAIL]',
                      '[NAME]',
                      '[IP]'), array(
                      $data['username'],
                      $data['email'],
                      $data['fname'] . ' ' . $data['lname'],
                      $_SERVER['REMOTE_ADDR']), $arow->body);
						  
				   $anewbody = cleanOut($abody);	
  
				  $amailer = $mail->sendMail();
                  $amessage = Swift_Message::newInstance()
							->setSubject($arow->subject)
							->setTo(array(Registry::get("Core")->site_email => Registry::get("Core")->site_name))
							->setFrom(array(Registry::get("Core")->site_email => Registry::get("Core")->site_name))
							->setBody($anewbody, 'text/html');
							  
				   $amailer->send($amessage);
			  }
			  
              (self::$db->affected() && $mailer) ? print "OK" : Filter::msgError('<span>Error!</span>There was an error during registration process. Please contact the administrator...', false);
               * 
               */
          } else
              print Filter::msgStatus();
      }
      
      public function setTokens($token, $user)
      {
          $time = time();
          $exp = $time + 2592000;
          
          //$sql = "INSER INTO user_token (token_id, user_id, date_creation, date_expiration, used) VALUES (" . $token . "," . $user . "," . $time . "," . $exp .",0)";
          $data = array(
					  'token_id' => $token, 
					  'user_id' => $user,
					  'date_creation' => $time, 
					  'date_expiration' => $exp,
					  'used' => 0
			  );
			  
			  self::$db->insert(self::toTable, $data);
      }
      
      //function generateStrongPassword($length = 9, $add_dashes = false, $available_sets = 'luds')
private function generateStrongPassword($length, $available_sets)
{
	$sets = array();
	if(strpos($available_sets, 'l') !== false)
		$sets[] = 'abcdefghjkmnpqrstuvwxyz';
	if(strpos($available_sets, 'u') !== false)
		$sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
	if(strpos($available_sets, 'd') !== false)
		$sets[] = '23456789';
	if(strpos($available_sets, 's') !== false)
		$sets[] = '!@#$%&*?';
 
	$all = '';
	$password = '';
	foreach($sets as $set)
	{
		$password .= $set[array_rand(str_split($set))];
		$all .= $set;
	}
 
	$all = str_split($all);
	for($i = 0; $i < $length - count($sets); $i++)
		$password .= $all[array_rand($all)];
 
	$password = str_shuffle($password);
 
	if(!$add_dashes)
		return $password;
 
	$dash_len = floor(sqrt($length));
	$dash_str = '';
	while(strlen($password) > $dash_len)
	{
		$dash_str .= substr($password, 0, $dash_len) . '-';
		$password = substr($password, $dash_len);
	}
	$dash_str .= $password;
	return $dash_str;
}
	  
      /**
       * User::passReset()
       * 
       * @return
       */
	  public function passReset()
	  {
		  
		  if (empty($_POST['uname']))
			  Filter::$msgs['uname'] = 'Please Enter Valid Username';
		  
		  $uname = $this->usernameExists($_POST['uname']);
		  if (strlen($_POST['uname']) < 4 || strlen($_POST['uname']) > 30 || !preg_match("/^([0-9a-z])+$/i", $_POST['uname']) || $uname != 3)
			  Filter::$msgs['uname'] = 'We are sorry, selected username does not exist in our database';

		  if (empty($_POST['email']))
			  Filter::$msgs['email'] = 'Please Enter Valid Email Address';

		  if (!$this->emailExists($_POST['email']))
			  Filter::$msgs['uname'] = 'Entered Email Address Does Not Exists.';
			    
		  if (empty($_POST['captcha']))
			  Filter::$msgs['captcha'] = 'Please enter captcha code!';
		  
		  if ($_SESSION['captchacode'] != $_POST['captcha'])
			  Filter::$msgs['captcha'] = "Entered captcha code is incorrect";
		  
		  if (empty(Filter::$msgs)) {
			  
              $user = $this->getUserInfo($_POST['uname']);
			  $randpass = $this->getUniqueCode(12);
			  $newpass = md5($randpass);
			  
			  $data['password'] = $newpass;
			  
			  self::$db->update(self::uTable, $data, "username = '" . $user->username . "'");
		  
			  require_once(BASEPATH . "lib/class_mailer.php");
			  $row = Registry::get("Core")->getRowById("email_templates", 2);
			  
              $body = str_replace(array(
                  '[USERNAME]',
                  '[PASSWORD]',
                  '[URL]',
                  '[LINK]',
                  '[IP]',
                  '[SITE_NAME]'), array(
                  $user->username,
                  $randpass,
                  Registry::get("Core")->site_url,
                  Registry::get("Core")->site_url,
                  $_SERVER['REMOTE_ADDR'],
                  Registry::get("Core")->site_name), $row->body);
					
			  $newbody = cleanOut($body);

			  $mailer = $mail->sendMail();
              $message = Swift_Message::newInstance()
						->setSubject($row->subject)
						->setTo(array($user->email => $user->username))
						->setFrom(array(Registry::get("Core")->site_email => Registry::get("Core")->site_name))
						->setBody($newbody, 'text/html');
						
              (self::$db->affected() && $mailer->send($message)) ? Filter::msgOk('<span>Success!</span>You have successfully changed your password. Please check your email for further info!', false) : Filter::msgError('<span>Error!</span>There was an error during the process. Please contact the administrator.', false);

          } else
              print Filter::msgStatus();
      }

      /**
       * User::activateAccount()
       * 
       * @return
       */
      public function activateAccount()
      {

          $data['active'] = "y";
		  self::$db->update(self::uTable, $data, "id = '" . Filter::$id . "'");
		  
		  require_once (BASEPATH . "lib/class_mailer.php");
		  $row = Registry::get("Core")->getRowById("email_templates", 15);
		  $usr = Registry::get("Core")->getRowById(self::uTable, Filter::$id);

		  $body = str_replace(array(
			  '[NAME]',
			  '[URL]',
			  '[SITE_NAME]'), array(
			  $usr->fname . ' ' .$usr->lname,
			  Registry::get("Core")->site_url,
			  Registry::get("Core")->site_name), $row->body);

		  $newbody = cleanOut($body);

		  $mailer = $mail->sendMail();
		  $message = Swift_Message::newInstance()
					->setSubject($row->subject)
					->setTo(array($usr->email => $usr->username))
					->setFrom(array(Registry::get("Core")->site_email => Registry::get("Core")->site_name))
					->setBody($newbody, 'text/html');

		  (self::$db->affected() && $mailer->send($message)) ? Filter::msgOk('<span>Success!</span>User have been successfully activated and email has been sent.', false) : Filter::msgError('<span>Error!</span>There was an error while sending email.');

      }
	  
      /**
       * User::activateUser()
       * 
       * @return
       */
	  public function activateUser()
	  {		  
		  if (empty($_POST['email']))
			  Filter::$msgs['email'] = 'Please Enter Valid Email Address';
		  
		  if (!$this->emailExists($_POST['email']))
			  Filter::$msgs['email'] = 'Entered Email Address Does Not Exists.';
		  
		  if (empty($_POST['token']))
			  Filter::$msgs['token'] = 'The token code is not valid';
		  
		  if (!$this->validateToken($_POST['token']))
			  Filter::$msgs['token'] = 'This account has been already activated!';
		  
		  if (empty(Filter::$msgs)) {
			  $email = sanitize($_POST['email']);
			  $token = sanitize($_POST['token']);
              $message = (Registry::get("Core")->auto_verify == 1) ? '<span>Success!</span>You have successfully activated your account!' : '<span>Success!</span>Your account is now active. However you still need to wait for administrative approval.';

              $data = array('token' => 0, 'active' => (Registry::get("Core")->auto_verify) ? "y" : "n");
			  
              self::$db->update(self::uTable, $data, "email = '" . $email . "' AND token = '" . $token . "'");
              (self::$db->affected()) ? Filter::msgOk($message, false) : Filter::msgError('<span>Error!</span>There was an error during the activation process. Please contact the administrator.', false);
          } else
              print Filter::msgStatus();
      }

	  /**
	   * Users::getUserData()
	   * 
	   * @return
	   */
	  public function getUserData()
	  {
		  
          $sql = "SELECT *, DATE_FORMAT(created, '%a. %d, %M %Y') as cdate," 
		  . "\n DATE_FORMAT(lastlogin, '%a. %d, %M %Y') as ldate" 
		  . "\n FROM " . self::uTable 
		  . "\n WHERE id = " . $this->uid;
          $row = self::$db->first($sql);

		  return ($row) ? $row : 0;
	  }
	  	  	  	  
	  /**
	   * Users::usernameExists()
	   * 
	   * @param mixed $username
	   * @return
	   */
	  private function usernameExists($username)
	  {
          $username = sanitize($username);
          if (strlen(self::$db->escape($username)) < 4)
              return 1;

          //Username should contain only alphabets, numbers, underscores or hyphens.Should be between 4 to 15 characters long
		  $valid_uname = "/^[a-z0-9_-]{4,15}$/"; 
          if (!preg_match($valid_uname, $username))
              return 2;

          $sql = self::$db->query("SELECT username" 
		  . "\n FROM " . self::uTable 
		  . "\n WHERE username = '" . $username . "'" 
		  . "\n LIMIT 1");

          $count = self::$db->numrows($sql);

          return ($count > 0) ? 3 : false;
	  }  	
	  
	  /**
	   * User::emailExists()
	   * 
	   * @param mixed $email
	   * @return
	   */
	  private function emailExists($email)
	  {
		  
          $sql = self::$db->query("SELECT email" 
		  . "\n FROM " . self::uTable 
		  . "\n WHERE email = '" . sanitize($email) . "'" 
		  . "\n LIMIT 1");

          if (self::$db->numrows($sql) == 1) {
              return true;
          } else
              return false;
	  }
	  
	  /**
	   * User::isValidEmail()
	   * 
	   * @param mixed $email
	   * @return
	   */
	  private function isValidEmail($email)
	  {
		  if (function_exists('filter_var')) {
			  if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
				  return true;
			  } else
				  return false;
		  } else
			  return preg_match('/^[a-zA-Z0-9._+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/', $email);
	  } 	

      /**
       * User::validateToken()
       * 
       * @param mixed $token
       * @return
       */
     private function validateToken($token)
      {
          $token = sanitize($token, 40);
          $sql = "SELECT token" 
		  . "\n FROM " . self::uTable 
		  . "\n WHERE token ='" . self::$db->escape($token) . "'" 
		  . "\n LIMIT 1";
          $result = self::$db->query($sql);

          if (self::$db->numrows($result))
              return true;
      }
	  
	  /**
	   * Users::getUniqueCode()
	   * 
	   * @param string $length
	   * @return
	   */
	  private function getUniqueCode($length = "")
	  {
		  $code = md5(uniqid(rand(), true));
		  if ($length != "") {
			  return substr($code, 0, $length);
		  } else
			  return $code;
	  }

	  /**
	   * Users::generateRandID()
	   * 
	   * @return
	   */
	  private function generateRandID()
	  {
		  return md5($this->getUniqueCode(24));
	  }

	  /**
	   * Users::levelCheck()
	   * 
	   * @param string $levels
	   * @return
	   */
	  public function levelCheck($levels)
	  {
		  $m_arr = explode(",", $levels);
		  reset($m_arr);
		  
		  if ($this->logged_in and in_array($this->userlevel, $m_arr))
		  return true;
	  }
	  
      /**
       * Users::getUserLevels()
       * 
       * @return
       */
      public function getUserLevels($level = false)
	  {
		  $arr = array(
				 9 => 'Super Admin',
				 1 => 'Registered User'
		  );
		  
		  $list = '';
		  foreach ($arr as $key => $val) {
				  if ($key == $level) {
					  $list .= "<option selected=\"selected\" value=\"$key\">$val</option>\n";
				  } else
					  $list .= "<option value=\"$key\">$val</option>\n";
		  }
		  unset($val);
		  return $list;
	  } 
	  	  	  
      /**
       * Users::getUserFilter()
       * 
       * @return
       */
      public static function getUserFilter()
	  {
		  $arr = array(
				 'username-ASC' => 'Username &uarr;',
				 'username-DESC' => 'Username &darr;',
				 'fname-ASC' => 'First Name &uarr;',
				 'fname-DESC' => 'First Name &darr;',
				 'lname-ASC' => 'Last Name &uarr;',
				 'lname-DESC' => 'Last Name &darr;',
				 'email-ASC' => 'Email Address &uarr;',
				 'email-DESC' => 'Email Address &darr;',
				 'created-ASC' => 'Registered &uarr;',
				 'created-DESC' => 'Registered &darr;',
		  );
		  
		  $filter = '';
		  foreach ($arr as $key => $val) {
				  if ($key == get('sort')) {
					  $filter .= "<option selected=\"selected\" value=\"$key\">$val</option>\n";
				  } else
					  $filter .= "<option value=\"$key\">$val</option>\n";
		  }
		  unset($val);
		  return $filter;
	  } 	  	  	  	   
  }
?>