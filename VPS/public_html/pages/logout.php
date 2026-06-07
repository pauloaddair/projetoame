<?php
session_start();
$ref = $GLOBALS['app_web_root'] ?? './';
if(isset($_SESSION['ref'])){
	$ref = $_SESSION['ref'];
}
setcookie("usuario_id", "", time()-3600, "/");
session_destroy();
header ('Location: '.$ref);
exit();
?>