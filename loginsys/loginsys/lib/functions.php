<?php
 
  if (!defined("_VALID_PHP"))
      die('Direct access to this location is not allowed.');
  
  /**
   * redirect_to()
   * 
   * @param mixed $location
   * @return
   */
  function redirect_to($location)
  {
      if (!headers_sent()) {
          header('Location: ' . $location);
		  exit;
	  } else
          echo '<script type="text/javascript">';
          echo 'window.location.href="' . $location . '";';
          echo '</script>';
          echo '<noscript>';
          echo '<meta http-equiv="refresh" content="0;url=' . $location . '" />';
          echo '</noscript>';
  }
  
  /**
   * countEntries()
   * 
   * @param mixed $table
   * @param string $where
   * @param string $what
   * @return
   */
  function countEntries($table, $where = '', $what = '')
  {
      if (!empty($where) && isset($what)) {
          $q = "SELECT COUNT(*) FROM " . $table . "  WHERE " . $where . " = '" . $what . "' LIMIT 1";
      } else
          $q = "SELECT COUNT(*) FROM " . $table . " LIMIT 1";
      
      $record = Registry::get("Database")->query($q);
      $total = Registry::get("Database")->fetchrow($record);
      return $total[0];
  }
  
  /**
   * getChecked()
   * 
   * @param mixed $row
   * @param mixed $status
   * @return
   */
  function getChecked($row, $status)
  {
      if ($row == $status) {
          echo "checked=\"checked\"";
      }
  }
  
  /**
   * post()
   * 
   * @param mixed $var
   * @return
   */
  function post($var)
  {
      if (isset($_POST[$var]))
          return $_POST[$var];
  }
  
  /**
   * get()
   * 
   * @param mixed $var
   * @return
   */
  function get($var)
  {
      if (isset($_GET[$var]))
          return $_GET[$var];
  }
  
  /**
   * sanitize()
   * 
   * @param mixed $string
   * @param bool $trim
   * @return
   */
  function sanitize($string, $trim = false, $int = false, $str = false)
  {
      $string = filter_var($string, FILTER_SANITIZE_STRING);
      $string = trim($string);
      $string = stripslashes($string);
      $string = strip_tags($string);
      $string = str_replace(array('‘', '’', '“', '”'), array("'", "'", '"', '"'), $string);
      
	  if ($trim)
          $string = substr($string, 0, $trim);
      if ($int)
		  $string = preg_replace("/[^0-9\s]/", "", $string);
      if ($str)
		  $string = preg_replace("/[^a-zA-Z\s]/", "", $string);
		  
      return $string;
  }

  /**
   * cleanSanitize()
   * 
   * @param mixed $string
   * @param bool $trim
   * @return
   */
  function cleanSanitize($string, $trim = false,  $end_char = '&#8230;')
  {
	  $string = cleanOut($string);
      $string = filter_var($string, FILTER_SANITIZE_STRING);
      $string = trim($string);
      $string = stripslashes($string);
      $string = strip_tags($string);
      $string = str_replace(array('‘', '’', '“', '”'), array("'", "'", '"', '"'), $string);
      
	  if ($trim) {
        if (strlen($string) < $trim)
        {
            return $string;
        }

        $string = preg_replace("/\s+/", ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $string));

        if (strlen($string) <= $trim)
        {
            return $string;
        }

        $out = "";
        foreach (explode(' ', trim($string)) as $val)
        {
            $out .= $val.' ';

            if (strlen($out) >= $trim)
            {
                $out = trim($out);
                return (strlen($out) == strlen($string)) ? $out : $out.$end_char;
            }       
        }
	  }
      return $string;
  }

  /**
   * truncate()
   * 
   * @param mixed $string
   * @param mixed $length
   * @param bool $ellipsis
   * @return
   */
  function truncate($string, $length, $ellipsis = true)
  {
      $wide = strlen(preg_replace('/[^A-Z0-9_@#%$&]/', '', $string));
      $length = round($length - $wide * 0.2);
      $clean_string = preg_replace('/&[^;]+;/', '-', $string);
      if (strlen($clean_string) <= $length)
          return $string;
      $difference = $length - strlen($clean_string);
      $result = substr($string, 0, $difference);
      if ($result != $string and $ellipsis) {
          $result = add_ellipsis($result);
      }
      return $result;
  }
  
  /**
   * getValue()
   * 
   * @param mixed $stwhatring
   * @param mixed $table
   * @param mixed $where
   * @return
   */
  function getValue($what, $table, $where)
  {
      $sql = "SELECT $what FROM $table WHERE $where";
      $row = Registry::get("Database")->first($sql);
      return ($row) ? $row->$what : '';
  }  

  /**
   * getValueById()
   * 
   * @param mixed $what
   * @param mixed $table
   * @param mixed $id
   * @return
   */
  function getValueById($what, $table, $id)
  {
      $sql = "SELECT $what FROM $table WHERE id = $id";
      $row = Registry::get("Database")->first($sql);
      return ($row) ? $row->$what : '';
  } 
  
  /**
   * tooltip()
   * 
   * @param mixed $tip
   * @return
   */
  function tooltip($tip)
  {
      return '<img src="'.SITEURL.'/images/tooltip.png" alt="Tip" class="tooltip" title="' . $tip . '" />';
  }
  
  /**
   * required()
   * 
   * @return
   */
  function required()
  {
      return '<img src="' . SITEURL . '/images/required.png" alt="Required Field" class="tooltip" title="Required Field" />';
  }

  /**
   * cleanOut()
   * 
   * @param mixed $text
   * @return
   */
  function cleanOut($text) {
	 $text =  strtr($text, array('\r\n' => "", '\r' => "", '\n' => ""));
	 $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
	 $text = str_replace('<br>', '<br />', $text);
	 return stripslashes($text);
  }
    

  /**
   * isAdmin()
   * 
   * @param mixed $userlevel
   * @return
   */
  function isAdmin($userlevel)
  {
	  switch ($userlevel) {
		  case 9:
			 $display = '<i class="icon-user tooltip text-red" data-title="Super Admin"></i>';
			 break;

		  case 7:
		     $display = '<i class="icon-user tooltip text-green" data-title="User Level 7"></i>';
			 break;

		  case 6:
		     $$display = '<i class="icon-user tooltip text-orange" data-title="User Level 6"></i>';
			 break;

		  case 5:
		     $display = '<i class="icon-user tooltip text-blue" data-title="User Level 5"></i>';
			 break;
			 
		  case 4:
		     $$display = '<i class="icon-user tooltip text-green" data-title="User Level 4"></i>';
			 break;		  

		  case 3:
		     $display = '<i class="icon-user tooltip text-orange" data-title="User Level 3"></i>';
			 break;

		  case 2:
		     $display = '<i class="icon-user tooltip text-blue" data-title="User Level 2"></i>';
			 break;
			 
		  case 1:
		     $display = '<i class="icon-user tooltip text-green" data-title="User Level 1"></i>';
			 break;			  
	  }

      return $display;;
  }

  /**
   * getSize()
   * 
   * @param mixed $size
   * @param integer $precision
   * @param bool $long_name
   * @param bool $real_size
   * @return
   */
  function getSize($size, $precision = 2, $long_name = false, $real_size = true)
  {
      if ($size == 0) {
          return '-/-';
      } else {
          $base = $real_size ? 1024 : 1000;
          $pos = 0;
          while ($size > $base) {
              $size /= $base;
              $pos++;
          }
          $prefix = _getSizePrefix($pos);
          $size_name = $long_name ? $prefix . "bytes" : $prefix[0] . 'B';
          return round($size, $precision) . ' ' . ucfirst($size_name);


      }
  }

  /**
   * _getSizePrefix()
   * 
   * @param mixed $pos
   * @return
   */  
  function _getSizePrefix($pos)
  {
      switch ($pos) {
          case 00:
              return "";
          case 01:
              return "kilo";

          case 02:
              return "mega";
          case 03:
              return "giga";
          default:
              return "?-";
      }
  }
  
  /**
   * userStatus()
   * 
   * @param mixed $id
   * @return
   */
  function userStatus($status, $id)
  {
	  switch ($status) {
		  case "y":
			  $display = '<i class="icon-ok-sign text-green"></i> Active';
			  break;
			  
		  case "n":
			  $display = '<a class="activate" id="act_' . $id . '"><i class="icon-adjust text-orange"></i> Inactive</a>';
			  break;
			  
		  case "t":
			  $display = '<i class="icon-time text-blue"></i> Pending';
			  break;
			  
		  case "b":
			  $display = '<i class="icon-ban-circle text-red"></i> Banned';
			  break;
	  }
	  
      return $display;;
  }

  /**
   * isActive()
   * 
   * @param mixed $id
   * @return
   */
  function isActive($id)
  {
	  if ($id == 1) {
		  $display = '<span class="tbicon"><a class="tooltip" data-title="Yes"><i class="icon-check"></i></a></span>';
	  } else {
		  $display = '<span class="tbicon"><a class="tooltip" data-title="No"><i class="icon-time"></i></a></span>';
	  }

      return $display;
  }

  
  /**
   * randName()
   * 
   * @return
   */ 
  function randName() {
	  $code = '';
	  for($x = 0; $x<6; $x++) {
		  $code .= '-'.substr(strtoupper(sha1(rand(0,999999999999999))),2,6);
	  }
	  $code = substr($code,1);
	  return $code;
  }
  
  //function generateStrongPassword($length = 9, $dashes = false, $char_set = 'luds')
function generateTransactionCodes($length, $dashes, $char_set)
{
	$sets = array();
	if(strpos($char_set, 'l') !== false)
		$sets[] = 'abcdefghjkmnpqrstuvwxyz';
	if(strpos($char_set, 'u') !== false)
		$sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
	if(strpos($char_set, 'd') !== false)
		$sets[] = '23456789';
	if(strpos($char_set, 's') !== false)
		$sets[] = '!@#$%&*?';
 
	$password = '';
    $all = '';
	foreach($sets as $set)
	{
		$password .= $set[array_rand(str_split($set))];
		$all .= $set;
	}
 
	$all = str_split($all);
	for($i = 0; $i < $length - count($sets); $i++)
		$password .= $all[array_rand($all)];
 
	$password = str_shuffle($password);
 
	if(!$dashes)
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

function sendPHPMail($email, $array)
{
    require_once("class.phpmailer.php");
    require_once("class.smtp.php");

    global $error;
    $mail = new PHPMailer();
    $mail->IsSMTP();
    //$mail->SMTPDebug = 2;       				// 1 = Mostrar errores y mensajes, 2 = Sólo mostrar mensajes
    $mail->SMTPAuth = true;   					// Se determina que el servidor SMTP requiere autenticación
    $mail->SMTPSecure = "ssl"; 					// Gmail requiere una conexión segura por lo que se especifica ssl
    $mail->Host = "smtp.gmail.com";         	// Dirección donde se encuentra el servidor de correo SMTP
    $mail->Port = 465;                      	// Puesto al que se deberá conectar el cliente en el servidor
    $mail->Username = 'pruebasuniandes0@gmail.com'; // Usuario con el que se realizará la autenticación
    $mail->Password = "Uniandes2014"; 				// Clave para realizar la autenticación en el servidor

    // Configuración propia del mensaje de correo que se desea enviar 
    $mail->From = 'secureCoding@gmail.com'; 
    $mail->FromName = "Banco FivePlus SC"; 
    $mail->Subject = "Confirmacion de activacion de cuenta";
    $mail->AltBody = "Mensaje de anuncio de activacion de cuenta bancaria."; 
    $mail->MsgHTML($array); 
    $mail->IsHTML(true);
    $mail->AddAddress($email, "Destinatario");
    // Se tiene la posibilidad de enviar tambien un archivo adjunto
    //$mail->AddAttachment("archivos/phpmailer0.9.zip");      

    
    if(!$mail->Send()) {
        //echo "Error: " . $mail->ErrorInfo;
        Filter::msgError('<span>Error!</span>There was an error while sending email.' . $mail->ErrorInfo);
    }else {
        //echo "Mensaje enviado correctamente";
    }
     
     
}

//function generateStrongPassword($length = 9, $add_dashes = false, $available_sets = 'luds')
function generateStrongPassword($length, $add_dashes = false, $available_sets)
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

?>