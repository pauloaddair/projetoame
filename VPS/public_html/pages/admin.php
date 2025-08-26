<?php
// session_start();
//$titulo = "A.B.I.A.T. - Ass. Bras. Inclusão Através do Trabalho";
$titulo = "Painel";
include_once("include/conexao.php");
include_once("include/funcoes.php");
if (isset($_SESSION['nivel']) && $_SESSION['nivel'] >= 3) {
	if (array_key_exists(1,$parametros) && $parametros[1]<>""){
		if(file_exists('pages/admin'.$parametros[1].'.php')){
			include_once("include/head-table.php");
			include_once('pages/admin'. $parametros[1].'.php');
		} else {
			include_once('include/head.php');
			include_once('include/inexiste.php');
			include_once('include/scripts.php');
		} 
	} else {
		include_once('include/head.php');
  		include_once('pages/adminindex.php');
	}
} else {
// Usuário não tem permissão, redirecione ou exiba uma mensagem de erro
	include_once('include/head.php');
	include_once('include/restrito.php');
	include_once('include/scripts.php');
}
include_once('include/end.php');
?>