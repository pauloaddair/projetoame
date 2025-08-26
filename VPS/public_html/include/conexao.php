<?php 
// session_start(); // Inicia a sessão
define('HOST', '127.0.0.1');
define('USUARIO', 'projetoame');
define('SENHA', 'teste');
define('DB', 'projetoame');
$conexao = mysqli_connect(HOST, USUARIO, SENHA, DB) or die ('Não foi possível conectar');
?>