<?php 
session_start(); // Inicia a sessão
define('HOST', 'localhost');
define('USUARIO', 'projetoa_admin');
define('SENHA', 'ProjetoAME#3802');
define('DB', 'projetoa_AME');
$conexao = mysqli_connect(HOST, USUARIO, SENHA, DB) or die ('Não foi possível conectar');
?>