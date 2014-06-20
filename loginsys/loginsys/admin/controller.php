<?php
  
  define("_VALID_PHP", true);
  
  require_once("../init.php");
  if (!$user->is_Admin())
   redirect_to("../login.php");
?>
<?php
  /* Proccess Configuration */
  if (isset($_POST['processConfig'])):
      $core->processConfig();
  endif;

  /* Proccess Newsletter */
  if (isset($_POST['processNewsletter'])):
      $core->processNewsletter();
  endif;

  /* == Proccess Email Template == */
  if (isset($_POST['processEmailTemplate'])):
      $core->processEmailTemplate();
  endif;

  /* == Proccess News == */
  if (isset($_POST['processNews'])):
      $core->processNews();
  endif;

  /* == Delete News == */
  if (isset($_POST['deleteNews'])):
      $id = intval($_POST['deleteNews']);
      $db->delete(Core::nTable, "id='" . $id . "'");
      $title = sanitize($_POST['newstitle']);

      print ($db->affected()) ? Filter::msgOk('News <strong>' . $title . '</strong> deleted successfully!') : Filter::msgAlert('<span>Alert!</span>Nothing to process.');
  endif;

  /* == Proccess User == */
  if (isset($_POST['processUser'])):
      $user->processUser();
  endif;

  /* == Acctivate User == */
  if (isset($_POST['activateAccount'])):
      $user->activateAccount();
  endif;
  
  /* == Delete User == */
  if (isset($_POST['deleteUser'])):
      $id = intval($_POST['deleteUser']);
      if ($id == 1):
          Filter::msgError("<span>Error!</span>You cannot delete main Super Admin account!");
      else:
          $db->delete("users", "id='" . $id . "'");
          $username = sanitize($_POST['title']);
          print ($db->affected()) ? Filter::msgOk('User <strong>' . $username . '</strong> deleted successfully!') : Filter::msgAlert('<span>Alert!</span>Nothing to process.');
      endif;
  endif;

  /* == User Search == */
  if (isset($_POST['userSearch'])):
      $string = sanitize($_POST['userSearch'], 15);

      if (strlen($string) > 3):
          $sql = "SELECT id, username, email, CONCAT(fname,' ',lname) as name" 
		  . "\n FROM users" 
		  . "\n WHERE MATCH (username) AGAINST ('" . $db->escape($string) . "*' IN BOOLEAN MODE)" 
		  . "\n ORDER BY username LIMIT 10";
          $display = '';
          if ($result = $db->fetch_all($sql)):
              $display .= '<ul id="searchresults">';
              foreach ($result as $row):
                  $link = 'index.php?do=users&amp;action=edit&amp;id=' . (int)$row->id;
                  $display .= '<li> <a href="' . $link . '"><p><i class="icon-chevron-sign-right"></i> ' . $row->username . ' - ' . $row->name . '</p>' . $row->email . '</a></li>';
              endforeach;
              $display .= '</ul>';
              print $display;
          endif;
      endif;
  endif;

  /* == Site Maintenance == */
  if (isset($_POST['processMaintenance'])):
      if (isset($_POST['inactive'])):
          $now = date('Y-m-d H:i:s');
          $diff = intval($_POST['days']);
          $expire = date("Y-m-d H:i:s", strtotime($now . -$diff . " days"));
          $db->delete("users", "lastlogin < '" . $expire . "' AND active = 'y' AND userlevel !=9");

          print ($db->affected()) ? Filter::msgOk('All (' . $db->affected() . ') inactive user(s) deleted successfully!') : Filter::msgAlert('<span>Alert!</span>Nothing to process.');

      elseif (isset($_POST['banned'])):
          $db->delete("users", "active = 'b'");
          print ($db->affected()) ? Filter::msgOk('All banned users deleted successfully!') : Filter::msgAlert('<span>Alert!</span>Nothing to process.');
      endif;
  endif;
  
  /* == Delete SQL Backup == */
  if (isset($_POST['deleteBackup'])):
      $action = @unlink(BASEPATH . 'admin/backups/' . sanitize($_POST['deleteBackup']));

      print ($action) ? Filter::msgOk('<span>Success!</span>Backup deleted successfully!') : Filter::msgAlert('<span>Alert!</span>Nothing to process.');
  endif;

  /* == Latest Sales Stats == */
  if (isset($_GET['getSaleStats'])):
      if (intval($_GET['getSaleStats']) == 0 || empty($_GET['getSaleStats'])):
          die();
      endif;

      $range = (isset($_GET['timerange'])) ? sanitize($_GET['timerange']) : 'month';
      $data = array();
      $data['order'] = array();
      $data['xaxis'] = array();
      $data['order']['label'] = '&nbsp;&nbsp;User Statistics';

      switch ($range) {
          case 'day':
              $date = date('Y-m-d');
              for ($i = 0; $i < 24; $i++) {
                  $query = $db->first("SELECT COUNT(*) AS total FROM users" 
				  . "\n WHERE DATE(created) = '" . $db->escape($date) . "'" 
				  . "\n AND HOUR(created) = '" . (int)$i . "'" 
				  . "\n GROUP BY HOUR(created) ORDER BY created ASC");

                  ($query) ? $data['order']['data'][] = array($i, (int)$query->total) : $data['order']['data'][] = array($i, 0);
                  $data['xaxis'][] = array($i, date('H', mktime($i, 0, 0, date('n'), date('j'), date('Y'))));
              }
              break;
          case 'week':
              $date_start = strtotime('-' . date('w') . ' days');

              for ($i = 0; $i < 7; $i++) {
                  $date = date('Y-m-d', $date_start + ($i * 86400));
                  $query = $db->first("SELECT COUNT(*) AS total FROM users" 
				  . "\n WHERE DATE(created) = '" . $db->escape($date) . "'" 
				  . "\n GROUP BY DATE(created)");

                  ($query) ? $data['order']['data'][] = array($i, (int)$query->total) : $data['order']['data'][] = array($i, 0);
                  $data['xaxis'][] = array($i, date('D', strtotime($date)));
              }

              break;
          default:
          case 'month':
              for ($i = 1; $i <= date('t'); $i++) {
                  $date = date('Y') . '-' . date('m') . '-' . $i;
                  $query = $db->first("SELECT COUNT(*) AS total FROM users" 
				  . "\n WHERE (DATE(created) = '" . $db->escape($date) . "')" 
				  . "\n GROUP BY DAY(created)");

                  ($query) ? $data['order']['data'][] = array($i, (int)$query->total) : $data['order']['data'][] = array($i, 0);
                  $data['xaxis'][] = array($i, date('j', strtotime($date)));
              }
              break;
          case 'year':
              for ($i = 1; $i <= 12; $i++) {
                  $query = $db->first("SELECT COUNT(*) AS total FROM users" 
				  . "\n WHERE YEAR(created) = '" . date('Y') . "'" 
				  . "\n AND MONTH(created) = '" . $i . "'" 
				  . "\n GROUP BY MONTH(created)");

                  ($query) ? $data['order']['data'][] = array($i, (int)$query->total) : $data['order']['data'][] = array($i, 0);
                  $data['xaxis'][] = array($i, date('M', mktime(0, 0, 0, $i, 1, date('Y'))));
              }
              break;
      }

      print json_encode($data);
  endif;
?>