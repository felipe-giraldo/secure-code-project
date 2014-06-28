<?php
 
  
  if (!defined("_VALID_PHP"))
      die('Direct access to this location is not allowed.');
  
  class Core
  {
      
	  const sTable = "settings";
	  const nTable = "news";
	  const eTable = "email_templates";
      
      const trTable = "transactions";
      const toTable = "user_token";
      const acTable = "account";
      
      public $year = null;
      public $month = null;
      public $day = null;
	  private static $db;
	  
	  
      /**
       * Core::__construct()
       * 
       * @return
       */
      function __construct()
      {
		  self::$db = Registry::get("Database");
		  $this->getSettings();
		  
          $this->year = (get('year')) ? get('year') : strftime('%Y');
          $this->month = (get('month')) ? get('month') : strftime('%m');
          $this->day = (get('day')) ? get('day') : strftime('%d');
          
          return mktime(0, 0, 0, $this->month, $this->day, $this->year);
      }
      
      /**
	   * Users::getTransactions()
	   * 
	   * @param bool $from
	   * @return
	   */
	  public function ____getTransactionz($from = false)
	  {
		  
          $pager = Paginator::instance();
          $pager->items_total = countEntries(self::trTable);
          $pager->default_ipp = Registry::get("Core")->perpage;
          $pager->paginate();

		  $clause = (isset($clause)) ? $clause : null;

          $sorting = " created DESC";
		  
		  
          $sql = "SELECT *, CONCAT(fname,' ',lname) as name,"
		  . "\n FROM " . self::trTable
		  . "\n " . $clause
		  . "\n ORDER BY " . $sorting . $pager->limit;
          $row = self::$db->fetch_all($sql);
          
		  return ($row) ? $row : 0;
	  }
	        
      /**
       * Core::getSettings()
       *
       * @return
       */
      private function getSettings()
      {

          $sql = "SELECT * FROM " . self::sTable;
          $row = self::$db->first($sql);
          
          $this->site_name = $row->site_name;
          $this->site_url = $row->site_url;
		  $this->site_email = $row->site_email;
		  $this->perpage = $row->user_perpage;
		  $this->logo = $row->logo;
		  $this->backup = $row->backup;
		  $this->thumb_w = $row->thumb_w;
		  $this->thumb_h = $row->thumb_h;
		  $this->reg_allowed = $row->reg_allowed;
		  $this->user_limit = $row->user_limit;
		  $this->reg_verify = $row->reg_verify;
		  $this->notify_admin = $row->notify_admin;
		  $this->auto_verify = $row->auto_verify;
          $this->mailer = $row->mailer;
          $this->smtp_host = $row->smtp_host;
          $this->smtp_user = $row->smtp_user;
          $this->smtp_pass = $row->smtp_pass;
          $this->smtp_port = $row->smtp_port;
		  $this->is_ssl = $row->is_ssl;
		  $this->version = $row->version;

      }

      /**
       * Core::processConfig()
       * 
       * @return
       */
	  public function processConfig()
	  {
		  
		  if (empty($_POST['site_name']))
			  Filter::$msgs['site_name'] = "Please enter Website Name!";
		  
		  if (empty($_POST['site_url']))
			  Filter::$msgs['site_url'] = "Please enter Website Url!";
		  
		  if (empty($_POST['site_email']))
			  Filter::$msgs['site_email'] = "Please enter valid Website Email address!";
		  
		  if (empty($_POST['thumb_w']))
			  Filter::$msgs['thumb_w'] = "Please enter Thumbnail Width!";
		  
		  if (empty($_POST['thumb_h']))
			  Filter::$msgs['thumb_h'] = "Please enter Thumbnail Height!";
			  
          if ($_POST['mailer'] == "SMTP") {
              Filter::checkPost('smtp_host', "Please enter Valid SMTP Host!");
              Filter::checkPost('smtp_user', "Please enter Valid SMTP Username!");
              Filter::checkPost('smtp_pass', "Please enter Valid SMTP Password");
              Filter::checkPost('smtp_port', "Please enter Valid SMTP Porty!");
          }
		  
		  if (empty(Filter::$msgs)) {
			  $data = array(
					  'site_name' => sanitize($_POST['site_name']), 
					  'site_url' => sanitize($_POST['site_url']),
					  'site_email' => sanitize($_POST['site_email']),
					  'reg_allowed' => intval($_POST['reg_allowed']),
					  'user_limit' => intval($_POST['user_limit']),
					  'reg_verify' => intval($_POST['reg_verify']),
					  'notify_admin' => intval($_POST['notify_admin']),
					  'auto_verify' => intval($_POST['auto_verify']),
					  'user_perpage' => intval($_POST['user_perpage']),
					  'thumb_w' => intval($_POST['thumb_w']),
					  'thumb_h' => intval($_POST['thumb_h']),
					  'mailer' => sanitize($_POST['mailer']),
					  'smtp_host' => sanitize($_POST['smtp_host']),
					  'smtp_user' => sanitize($_POST['smtp_user']),
					  'smtp_pass' => sanitize($_POST['smtp_pass']),
					  'smtp_port' => intval($_POST['smtp_port']),
					  'is_ssl' => intval($_POST['is_ssl'])

			  );
			  
			  self::$db->update(self::sTable, $data);
			  (self::$db->affected()) ? Filter::msgOk("<span>Success!</span>System Configuration updated successfully!") : Filter::msgAlert("<span>Alert!</span>Nothing to process.");
		  } else
			  print $this->msgStatus();
	  }

	  /**
	   * Core::processNewsletter()
	   * 
	   * @return
	   */
	  public function processNewsletter()
	  {
		  
		  if (empty($_POST['subject']))
			  Filter::$msgs['subject'] = "Please Enter Newsletter Subject";
		  
		  if (empty($_POST['body']))
			  Filter::$msgs['body'] = "Please Enter Email Message!";
		  
		  if (empty(Filter::$msgs)) {
				  $to = sanitize($_POST['recipient']);
				  $subject = sanitize($_POST['subject']);
				  $body = cleanOut($_POST['body']);

			  switch ($to) {
				  case "all":
					  require_once(BASEPATH . "lib/class_mailer.php");
					  $mailer = $mail->sendMail();
					  $mailer->registerPlugin(new Swift_Plugins_AntiFloodPlugin(100,30));
					  
					  $sql = "SELECT email, CONCAT(fname,' ',lname) as name FROM " . Users::uTable . " WHERE id != 1";
					  $userrow = self::$db->fetch_all($sql);
					  
					  $replacements = array();
					  foreach ($userrow as $cols) {
						  $replacements[$cols->email] = array('[NAME]' => $cols->name,'[SITE_NAME]' => $this->site_name,'[URL]' => $this->site_url);
					  }
					  
					  $decorator = new Swift_Plugins_DecoratorPlugin($replacements);
					  $mailer->registerPlugin($decorator);
					  
					  $message = Swift_Message::newInstance()
								->setSubject($subject)
								->setFrom(array($this->site_email => $this->site_name))
								->setBody($body, 'text/html');
					  
					  foreach ($userrow as $row)
						  $message->addTo($row->email, $row->name);
					  unset($row);
					  
					  $numSent = $mailer->batchSend($message);
					  break;
					  
				  case "newsletter":
					  require_once(BASEPATH . "lib/class_mailer.php");
					  $mailer = $mail->sendMail();
					  $mailer->registerPlugin(new Swift_Plugins_AntiFloodPlugin(100,30));
					  
					  $sql = "SELECT email, CONCAT(fname,' ',lname) as name FROM " . Users::uTable . " WHERE newsletter = '1' AND id != 1";
					  $userrow = self::$db->fetch_all($sql);
					  
					  $replacements = array();
					  foreach ($userrow as $cols) {
						  $replacements[$cols->email] = array('[NAME]' => $cols->name,'[SITE_NAME]' => $this->site_name,'[URL]' => $this->site_url);
					  }
					  
					  $decorator = new Swift_Plugins_DecoratorPlugin($replacements);
					  $mailer->registerPlugin($decorator);
					  
					  $message = Swift_Message::newInstance()
								->setSubject($subject)
								->setFrom(array($this->site_email => $this->site_name))
								->setBody($body, 'text/html');
					  
					  foreach ($userrow as $row)
						  $message->addTo($row->email, $row->name);
					  unset($row);
					  
					  $numSent = $mailer->batchSend($message);
					  break;
					  					  	  
				  default:
					  require_once(BASEPATH . "lib/class_mailer.php");
					  $mailer = $mail->sendMail();	
					  			  
					  $row = self::$db->first("SELECT email, CONCAT(fname,' ',lname) as name FROM " . Users::uTable . " WHERE email LIKE '%" . sanitize($to) . "%'");
					  
					  $newbody = str_replace(array('[NAME]', '[SITE_NAME]', '[URL]'), 
					  array($row->name, $this->site_name, $this->site_url), $body);

					  $message = Swift_Message::newInstance()
								->setSubject($subject)
							    ->setTo(array($to => $row->name))
								->setFrom(array($this->site_email => $this->site_name))
								->setBody($newbody, 'text/html');
					  
					  $numSent = $mailer->send($message);
					  break;
			  }

			  ($numSent) ? Filter::msgOk("<span>Success!</span>All Email(s) have been sent successfully!") :  Filter::msgAlert("<span>Error!</span>Some of the emails could not be reached!");

		  } else
			  print Filter::msgStatus();
	  }

      /**
       * Core::getEmailTemplates()
       * 
       * @return
       */
      public function getEmailTemplates()
      {
          $sql = "SELECT * FROM " . self::eTable . " ORDER BY name ASC";
          $row = self::$db->fetch_all($sql);
          
          return ($row) ? $row : 0;
      }

	  /**
	   * Core:::processEmailTemplate()
	   * 
	   * @return
	   */
	  public function processEmailTemplate()
	  {
		  
		  if (empty($_POST['name']))
			  Filter::$msgs['name'] = "Please Enter Template Title!";
		  
		  if (empty($_POST['subject']))
			  Filter::$msgs['subject'] = "Please Enter Email Subject!";

		  if (empty($_POST['body']))
			  Filter::$msgs['body'] = "Template Content is required!";
			  		  
		  if (empty(Filter::$msgs)) {
			  $data = array(
					  'name' => sanitize($_POST['name']), 
					  'subject' => sanitize($_POST['subject']),
					  'body' => $_POST['body'],
					  'help' => $_POST['help']
			  );

			  self::$db->update(self::eTable, $data, "id='" . Filter::$id . "'");
			  (self::$db->affected()) ? Filter::msgOk("<span>Success!</span>Email Template Updated Successfully") :  Filter::msgAlert("<span>Alert!</span>Nothing to process.");
		  } else
			  print Filter::msgStatus();
	  }

      /**
       * Core::getNews()
       * 
       * @return
       */
      public function getNews()
      {
          $sql = "SELECT *, DATE_FORMAT(created, '%d. %b. %Y') as cdate FROM " . self::nTable . " ORDER BY title ASC";
          $row = self::$db->fetch_all($sql);
          
          return ($row) ? $row : 0;
      }
      
      public function getTransactions($user)
      {
          $sql = "SELECT * FROM " . self::trTable . " WHERE ";
          $sql = "SELECT * 
                FROM " . self::trTable . " t INNER JOIN " . self::acTable . " a
                WHERE t.from_account = a.id_account
                AND a.id_user = " . $user;
          $row = self::$db->fetch_all($sql);
          
          return ($row) ? $row : 0;
      }
      
      public function setTokens($token, $user)
      {
          $time = NOW();
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
      
      public function getTransactionsPA()
      {
          $sql = "SELECT * FROM " . self::trTable . " WHERE ammount > 10000 AND transaction_state > 2";
          $row = self::$db->fetch_all($sql);
          
          return ($row) ? $row : 0;
      }
      
      public function checkTransaction($transaction_id)
      {
          $sql = "SELECT * FROM " . self::trTable . " WHERE id = " . $transaction_id;
          $row = self::$db->first($sql);
          
          return ($row) ? $row : 0;
      }
      
      public function rejectTransaction($transaction_id)
      {
          $data = array(
                    'transaction_state' => 2
            );
            self::$db->update(self::trTable, $data, "id = '" . $transaction_id . "'");      //we update the transaction state
      }
      
      public function getAccounts($user)
      {
          /*
          if(!empty($user)):
              $sql = "SELECT * FROM " . self::acTable . " WHERE id_user = " . $user;
          else:
              $sql = "SELECT * FROM " . self::acTable;
          endif;
           * 
           */
          
          $sql = "SELECT * FROM " . self::acTable . " WHERE id_user = " . $user;
          
          $row = self::$db->fetch_all($sql);
          
          return ($row) ? $row : 0;
      }
	  
	  public static function getAccountsStatic($user)
      {
          /*
          if(!empty($user)):
              $sql = "SELECT * FROM " . self::acTable . " WHERE id_user = " . $user;
          else:
              $sql = "SELECT * FROM " . self::acTable;
          endif;
           * 
           */
          
          $sql = "SELECT * FROM " . self::acTable . " WHERE id_user = " . $user;
          
          $row = self::$db->fetch_all($sql);
          
          return ($row) ? $row : 0;
      }
      
      public function checkValidToken($token, $user)
      {
          
          $sql = "SELECT *
            FROM " . self::toTable . " u INNER JOIN " . self::acTable . " a
            WHERE u.user_id = a.id_account
            AND a.id_user = $user
            AND u.token_id LIKE '$token'
            AND u.used = 0";
          
          $row = self::$db->first($sql);
          
          return ($row) ? $row : 0;
      }
      
      public function checkFunds($account, $ammount)
      {
          
          $sql = "SELECT *
            FROM " . self::acTable ." a
            WHERE a.id_account = " . $account . "
            -- AND a.money > " . $ammount . "
            AND a.active = 1";
          
          //$row = self::$db->fetchrow($sql);
          $row = self::$db->first($sql);
          
          //return ($row) ? $row : 0;
          //echo $sql . "<br>";
          return ($row) ? $row : 0;
          //return $sql;
      }
      
      public function updateAccount($account, $ammount, $operation)
      {
          if($operation == 1):
              $data = array ('money' => 'money + ' . $ammount);
          else:
              $data = array ('money' => 'money - ' . $ammount);
          endif;
          self::$db->update(self::acTable, $data, "id_account='" . $account . "'");
          
      }
      
      public function makeTransfer($userID, $type, $origin=false, $destination=false, $ammount=false, $token=false, $approval = false, $transaction_id = false){
          if(!$origin):
                $origin = sanitize($_POST['origin_account']);
          endif;
          if(!$destination):
                $destination = sanitize($_POST['destination_account']);
          endif;
          if(!$ammount):
                $ammount = sanitize($_POST['ammount']);
          endif;
          if(!$token):
                $token = sanitize($_POST['t_token']);
          endif;
          
          if(empty($origin)):
              Filter::$msgs['origin_account'] = 'Please select an account to get the funds from.';
          endif;
          
          if(empty($destination)):
              Filter::$msgs['destination_account'] = 'Please select an account to transfer the funds to.';
          endif;
          
          if(!is_numeric($destination)):
              Filter::$msgs['destination_account'] = 'The destination account number must contain ONLY numbers.';
          endif;
          
          if(strlen($destination) != 8):
              Filter::$msgs['destination_account'] = 'The destination account must be composed of 8 digits, check your data.';
          endif;
          
          if(strlen($origin) != 8):
              Filter::$msgs['origin_account'] = 'The origin account must be composed of 8 digits, check your data.';
          endif;
          
          if($destination == $origin):
              Filter::$msgs['destination_account'] = 'The destination account cannot be the same than the origin account';
          endif;
          
          if(empty($ammount) or !is_numeric($ammount)):
              Filter::$msgs['ammount'] = 'Please insert the ammount to transfer';
          endif;
          if($ammount <= 0):
              Filter::$msgs['ammount'] = 'Please insert a number greater than Zero for the transfering ammount.';
          endif;
          if($ammount >= 10000):
              $state = 99;
          else:
              $state = 1;
          endif;
          
          if($token != 1):
            if(!$approval):
              if(empty($token) or strlen($token) < 15):
                  Filter::$msgs['t_token'] = 'Please insert a valid token key';
              endif;

              $valid_token = $this->checkValidToken(sanitize($token), $userID);
              $v_token = $valid_token->used;
              if($v_token != 0 or !is_numeric($v_token)) :
                  Filter::$msgs['t_token'] = 'Please insert a valid and available token key';
              endif;
            endif;
          endif;
          
          $funds = self::checkFunds($origin, $ammount);
          $funds_origin = $funds->money;
          
          $funds2 = self::checkFunds($destination, $ammount);
          $funds_destination = $funds2->money;
          //Filter::$msgs['ammount'] = 'You dont have enough funds to make this transfer ' . $funds . " " . $funds2;
          
          /*
          echo "Fondos Origen: " . $funds_origin . " Fondos Destino: " . $funds_destination . 
              "<br>" . "Cuenta origen: " . $origin . " Cuenta Destino: " . $destination . 
              "<br>Cantidad: " . $ammount . "<br> Token: " . $token . " Tamanho: " . strlen($token) . 
              " Token usado: " . $v_token . " UserID: " . $userID .
              "<br> es numerico: " . is_numeric($v_token) . " Es set: " . is_null($v_token);
           * 
           */
           
           
           
          
          if($funds->money < $ammount) :
              Filter::$msgs['ammount'] = 'You dont have enough funds to make this transfer / payment';
          endif;
          if (empty(Filter::$msgs)):
  
            if(!$approval):
                $data = array(
                            'from_account' => sanitize($origin), 
                            'to_account' => sanitize($destination),
                            'ammount' => sanitize($ammount), 
                            'token' => sanitize($token),
                            'transaction_date' => "NOW()",
                            'transaction_state' => $state,
                            'transaction_type' => $type
                    );
                $data_token = array ('used' => 1);

                self::$db->insert(self::trTable, $data);
            else:
                    $data = array(
                            'transaction_state' => 1
                    );
                    self::$db->update(self::trTable, $data, "id = '" . $transaction_id . "'");      //we update the transaction state
                    $state = 1;
                    
            endif;
            if($state == 1):
                
                $funds_origin = $funds_origin - $ammount;
                $funds_destination = $funds_destination + $ammount;
                //self::updateAccount($origin, $ammount, 99);         //we debit the money
                $data = array ('money' => $funds_origin);
                self::$db->update(self::acTable, $data, "id_account='" . $origin . "'");

                $data = array ('money' => $funds_destination);
                self::$db->update(self::acTable, $data, "id_account='" . $destination . "'");
                //self::updateAccount($destination, $ammount, 1);          //we deposit the money on the destination account
                self::$db->update(self::toTable, $data_token, "token_id LIKE '" . $token . "'");      //we discard the token
                
                if($type == 1):
                    Filter::msgOk('<span>Success!</span>Money transfer successful. Thank you for using our banking services.', false);
                else:
                    Filter::msgOk('<span>Success!</span>Payment successful. Thank you for using our banking services.', false);
                endif;
                 
                 
            endif;
          else:
			  print Filter::msgStatus();
          endif;
      }
      
      public function smcTransfer($userID){
          

        $origin = sanitize($_POST['origin_account']);
        $destination = sanitize($_POST['destination_account']);
        $ammount = sanitize($_POST['ammount']);
        $token = sanitize($_POST['t_token']);
        $v_token = sanitize($_POST['v_token']);
        
        

          
          if(empty($origin)):
              Filter::$msgs['origin_account'] = 'Please select an account to get the funds from.';
          endif;
          
          if(empty($destination)):
              Filter::$msgs['destination_account'] = 'Please select an account to transfer the funds to.';
          endif;
          
          if(!is_numeric($destination)):
              Filter::$msgs['destination_account'] = 'The destination account number must contain ONLY numbers.';
          endif;
          
          if(strlen($destination) != 8):
              Filter::$msgs['destination_account'] = 'The destination account must be composed of 8 digits, check your data.';
          endif;
          
          if(strlen($origin) != 8):
              Filter::$msgs['origin_account'] = 'The origin account must be composed of 8 digits, check your data.';
          endif;
          
          if($destination == $origin):
              Filter::$msgs['destination_account'] = 'The destination account cannot be the same than the origin account';
          endif;
          
          if(empty($ammount) or !is_numeric($ammount)):
              Filter::$msgs['ammount'] = 'Please insert the ammount to transfer';
          endif;
          if($ammount <= 0):
              Filter::$msgs['ammount'] = 'Please insert a number greater than Zero for the transfering ammount.';
          endif;
          
            if(empty($token) or strlen($token) < 4):
                Filter::$msgs['t_token'] = 'Please insert a valid PIN';
            endif;
            if(empty($v_token) or strlen($v_token) < 6):
                Filter::$msgs['t_token'] = 'Please insert a valid TOKEN';
            endif;
          
          if (empty(Filter::$msgs)):
              $string = $origin . '|' . $destination . '|' .  $ammount . '|' . $token;
              $string = hash('SHA256', $string);
              $sub_string = substr($string, 0, 6);
              
              if ($sub_string == $v_token):
                  //makeTransfer($userID, $type, $origin=false, $destination=false, $ammount=false, $token=false, $approval = false, $transaction_id = false
                  $this->makeTransfer($userID, 1, $origin, $destination, $ammount, 1, 1);
              else:
                  Filter::msgError('<span>Error!</span>There was an error with the information supplied. <br>Please verify your data.');
              endif;
              
              
    
          else:
			  print Filter::msgStatus();
          endif;
      }
      
      
      
      public function getTokens($user)
      {
          $sql = "SELECT * 
            FROM user_token u INNER JOIN account a
            ON u.user_id = a.id_account
            AND a.id_user = $user ORDER BY u.used ";
          $row = self::$db->fetch_all($sql);
          
          return ($row) ? $row : 0;
      }

      /**
       * Core::renderNews()
       * 
       * @return
       */
      public function renderNews()
      {
          $sql = "SELECT *, DATE_FORMAT(created, '%d. %b. %Y') as cdate FROM " . self::nTable . " WHERE active = 1";
          $row = self::$db->first($sql);
          
          return ($row) ? $row : 0;
      }
	  
	  /**
	   * Content::processNews()
	   * 
	   * @return
	   */
	  public function processNews()
	  {
		  
		  if (empty($_POST['title']))
			  Filter::$msgs['title'] = 'Please Enter News Title';

		  if (empty($_POST['body']))
			  Filter::$msgs['body'] = 'Please Enter News Content';
			  		  
		  if (empty($_POST['created']))
			  Filter::$msgs['created'] = 'Please Enter Valid Date';
		  
		  if (empty(Filter::$msgs)) {
			  $data = array(
				  'title' => sanitize($_POST['title']), 
				  'author' => sanitize($_POST['author']), 
				  'body' => sanitize($_POST['body']),
				  'created' => sanitize($_POST['created']),
				  'active' => intval($_POST['active'])
			  );

			  if ($data['active'] == 1) {
				  $news['active'] = "DEFAULT(active)";
				  self::$db->update(self::nTable, $news);
			  }
			  
			  (Filter::$id) ? self::$db->update(self::nTable, $data, "id='" . Filter::$id . "'") : self::$db->insert(self::nTable, $data);
			  $message = (Filter::$id) ? '<span>Success!</span>News item updated successfully!' : '<span>Success!</span>News item added successfully!';
			  
			  (self::$db->affected()) ? Filter::msgOk($message) :  Filter::msgAlert('<span>Alert!</span>Nothing to process.');
		  } else
			  print Filter::msgStatus();
	  }
	  	  
      /**
       * Core::monthList()
       * 
       * @return
       */ 	  
      public function monthList()
	  {
		  $selected = is_null(get('month')) ? strftime('%m') : get('month');
		  
		  $arr = array(
				'01' => "Jan",
				'02' => "Feb",
				'03' => "Mar",
				'04' => "Apr",
				'05' => "May",
				'06' => "Jun",
				'07' => "Jul",
				'08' => "Aug",
				'09' => "Sep",
				'10' => "Oct",
				'11' => "Nov",
				'12' => "Dec"
		  );
		  
		  $monthlist = '';
		  foreach ($arr as $key => $val) {
			  $monthlist .= "<option value=\"$key\"";
			  $monthlist .= ($key == $selected) ? ' selected="selected"' : '';
			  $monthlist .= ">$val</option>\n";
          }
          unset($val);
          return $monthlist;
      }

      /**
       * Core::yearList()
	   *
       * @param mixed $start_year
       * @param mixed $end_year
       * @return
       */
	  function yearList($start_year, $end_year)
	  {
		  $selected = is_null(get('year')) ? date('Y') : get('year');
		  $r = range($start_year, $end_year);
		  
		  $select = '';
		  foreach ($r as $year) {
			  $select .= "<option value=\"$year\"";
			  $select .= ($year == $selected) ? ' selected="selected"' : '';
			  $select .= ">$year</option>\n";
		  }
		  return $select;
	  }

      /**
       * Core::yearlyStats()
       * 
       * @return
       */
      public function yearlyStats()
      {
          $sql = "SELECT *, YEAR(created) as year, MONTH(created) as month," 
		  . "\n COUNT(id) as total" 
		  . "\n FROM " . Users::uTable 
		  . "\n WHERE YEAR(created) = '" . $this->year . "'" 
		  . "\n GROUP BY year DESC, month DESC ORDER by created";

          $row = Registry::get("Database")->fetch_all($sql);

          return ($row) ? $row : 0;
      }
	  
	   
      /**
       * Core::getYearlySummary()
       * 																																																				
       * @return
       */
      public function getYearlySummary()
      {
          $sql = "SELECT YEAR(created) as year, MONTH(created) as month," 
		  . "\n COUNT(id) as total" 
		  . "\n FROM " . Users::uTable 
		  . "\n WHERE YEAR(created) = '" . $this->year . "' GROUP BY year";

          $row = Registry::get("Database")->first($sql);

          return ($row) ? $row : 0;
      }
				  
      /**
       * Core::getRowById()
       * 
       * @param mixed $table
       * @param mixed $id
       * @param bool $and
       * @param bool $is_admin
       * @return
       */
      public static function getRowById($table, $id, $and = false, $is_admin = true)
      {
          $id = sanitize($id, 8, true);
          if ($and) {
              $sql = "SELECT * FROM " . (string )$table . " WHERE id = '" . Registry::get("Database")->escape((int)$id) . "' AND " . Registry::get("Database")->escape($and) . "";
          } else
              $sql = "SELECT * FROM " . (string )$table . " WHERE id = '" . Registry::get("Database")->escape((int)$id) . "'";

          $row = Registry::get("Database")->first($sql);

          if ($row) {
              return $row;
          } else {
              if ($is_admin)
                  Filter::error("You have selected an Invalid Id - #" . $id, "Core::getRowById()");
          }
      }

      /**
       * Core::doDelete()
       * 
       * @param mixed $title
       * @param mixed $varpost
       * @param string $url
       * @param string $attr
       * @param string $id
	   * @param string $extra
       * @return
       */
      public static function doDelete($title, $varpost, $url = 'controller.php', $attr = 'item_', $id = 'a.delete', $extra = false)
      {
          $display = "
		  <script type=\"text/javascript\"> 
		  // <![CDATA[
		  $(document).ready(function () {
		      $('body').on('click', '" . $id . "', function () {
		          var id = $(this).attr('id').replace('" . $attr . "', '')
		          var parent = $(this).parent().parent().parent();
		          var name = $(this).attr('data-rel');
		          new Messi('<p class=\"messi-warning\"><i class=\"icon-warning-sign icon-3x pull-left\"></i>Are you sure you want to delete this record?<br /><strong>This action cannot be undone!!!</strong></p>', {
		              title: '" . $title . "',
		              titleClass: '',
		              modal: true,
		              closeButton: true,
		              buttons: [{
		                  id: 0,
		                  label: 'Delete',
		                  class: '',
		                  val: 'Y'
		              }],
		              callback: function (val) {
		                  if (val === 'Y') {
		                      $.ajax({
		                          type: 'post',
		                          url: '" . $url . "',
		                          data: {
									  '" . $varpost . "': id,
									  'title':encodeURIComponent(name)
									  " . $extra . "
								  },
		                          beforeSend: function () {
		                              parent.animate({
		                                  'backgroundColor': '#FFBFBF'
		                              }, 400);
		                          },
		                          success: function (msg) {
		                              parent.fadeOut(400, function () {
		                                  parent.remove();
		                              });
		                              $('html, body').animate({
		                                  scrollTop: 0
		                              }, 600);
		                              $('#msgholder').html(decodeURIComponent(msg));
		                          }
		                      });
		                  }
		              }

		          });
		      });
		  });
		  // ]]>
		  </script>";

          print $display;
      }
	  
      /**
       * Core::doForm()
       * 
       * @param mixed $data
       * @param string $url
       * @param integer $reset
       * @param integer $clear
       * @param string $form_id
       * @param string $msgholder
       * @return
       */
      public static function doForm($data, $url = "controller.php", $reset = 0, $clear = 0, $form_id = "admin_form", $msgholder = "msgholder")
      {
          $display = '
		  <script type="text/javascript">
		  // <![CDATA[
			  $(document).ready(function () {
				  var options = {
					  target: "#' . $msgholder . '",
					  beforeSubmit:  showLoader,
					  success: showResponse,
					  url: "' . $url . '",
					  resetForm : ' . $reset . ',
					  clearForm : ' . $clear . ',
					  data: {
						  ' . $data . ': 1
					  }
				  };
				  $("#' . $form_id . '").ajaxForm(options);
			  });
			  function showResponse(msg) {
				  hideLoader();
				  $(this).html(msg);
				  $("html, body").animate({
					  scrollTop: 0
				  }, 600);
			  }
			  ';
          $display .= '
		  // ]]>
		  </script>';

          print $display;
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
}}
?>