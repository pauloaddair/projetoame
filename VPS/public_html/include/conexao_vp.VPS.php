<?php 
	define('HOST', 'localhost');
	define('DB', 'virtualb_ag');
	define('USUARIO', 'virtualb_admin');
	define('SENHA', 'Pitt@3802');
	//$conexao = mysqli_connect(HOST, USUARIO, SENHA, DB) or die ('Não foi possível conectar<br>'.HOST.'<br>'.USUARIO.'<br>'.SENHA.'<br>'.DB);
	$conexao = mysqli_connect(HOST, USUARIO, SENHA, DB) or die ('Não foi possível conectar.Base de dados indisponível<br>');
?>