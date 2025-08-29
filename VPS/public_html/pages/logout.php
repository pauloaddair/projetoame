<?php
//session_start();
$ref = "./";
if(isset($_SESSION['ref'])){
	$ref = $_SESSION['ref'];
}
setcookie("user_id", "", time()-3600);
session_destroy();
header ('Location: '.$ref);
//header('Location: /');
exit();
?>