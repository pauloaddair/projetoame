<?php
// session_start();
if(!isset($_SESSION['usuario'])) {
//	header('Location: login.php?ref=' . $_SERVER["PHP_SELF"]);
	header('Location: /login?ref='.$url);
	exit();
}
?>