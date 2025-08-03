<?php 
// session_start(); // Inicia a sessão
define('HOST', 'localhost');
define('USUARIO', 'projetoame');
define('SENHA', 'vp3imJizMOgWxbM');
define('DB', 'projetoame');
$conexao = mysqli_connect(HOST, USUARIO, SENHA, DB) or die ('Não foi possível conectar');
?>