<?php
	@include("lib/functions.php");
  if (!$user->logged_in)
      redirect_to("index.php");
  
  if (!defined("_VALID_PHP"))
      die('Direct access to this location is not allowed.');
?>

<?php
// En versiones de PHP anteriores a 4.1.0, $HTTP_POST_FILES debe utilizarse en lugar
// de $_FILES.  
$uploaddir = '/home/secure/parsing/movements.aes';
$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
$row = $user->getUserData();

if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
    echo "El archivo es válido y fue cargado exitosamente.\n";
	//Invocar programa en C
} else {
    echo "Se ha generado un prblema con la carga del archivo\n";
}

?>
