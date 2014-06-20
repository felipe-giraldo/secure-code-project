<?php
/*
 * El siguiente código utiliza librerias del proyecto PHPMailer 
 * que puede ser descargado libremente desde el repositorio de GitHub
 * https://github.com/PHPMailer/PHPMailer
 */
require_once("class.phpmailer.php");
require_once("class.smtp.php");

global $error;
$mail = new PHPMailer();
$mail->IsSMTP();
$mail->SMTPDebug = 2;       				// 1 = Mostrar errores y mensajes, 2 = Sólo mostrar mensajes
$mail->SMTPAuth = true;   					// Se determina que el servidor SMTP requiere autenticación
$mail->SMTPSecure = "ssl"; 					// Gmail requiere una conexión segura por lo que se especifica ssl
$mail->Host = "smtp.gmail.com";         	// Dirección donde se encuentra el servidor de correo SMTP
$mail->Port = 465;                      	// Puesto al que se deberá conectar el cliente en el servidor
$mail->Username = 'pruebasuniandes0@gmail.com'; // Usuario con el que se realizará la autenticación
$mail->Password = "Uniandes2014"; 				// Clave para realizar la autenticación en el servidor

// Configuración propia del mensaje de correo que se desea enviar 
$mail->From = 'secureCoding@gmail.com'; 
$mail->FromName = "Nombre del Remitente"; 
$mail->Subject = "Prueba Envio de Correos";
$mail->AltBody = "Este es un mensaje de prueba."; 
$mail->MsgHTML("Cuerpo del mensaje<br><b>...........</b>."); 
$mail->IsHTML(true);
$mail->AddAddress("tm.hernan10@uniandes.edu.co", "Destinatario");
// Se tiene la posibilidad de enviar tambien un archivo adjunto
//$mail->AddAttachment("archivos/phpmailer0.9.zip");      

if(!$mail->Send()) {
	echo "Error: " . $mail->ErrorInfo;
}else {
	echo "Mensaje enviado correctamente";
}
?>