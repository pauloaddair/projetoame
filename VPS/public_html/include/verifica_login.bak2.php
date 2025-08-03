<?php
//session_start();
$ref = "/login";
if(isset($_SESSION['usuario'])){
	if(isset($_SESSION['ref'])){
		$ref = $_SESSION['ref'];
	}
}
session_start();
//if(!isset($_SESSION['usuario'])) {
echo $ref."<br>".$_SESSION['ref'];
exit;

	header('Location: '.$ref);
	exit();
//}
