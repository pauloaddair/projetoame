<?php
//INDEX
session_start();
$ativ = "entrou";
$titulo = "Projeto A.M.E. Web";
$parametros = array("home");
if (isset($_GET['url'])){
	$url = $_GET['url'];	
	$parametros = explode("/",$url);
	$hostName = $_GET['hostName'];
}
if(file_exists('pages/'.$parametros[0].'.php')){
  include_once('pages/'. $parametros[0].'.php');
} else {
	include_once('home/index.php');		  
}
?>