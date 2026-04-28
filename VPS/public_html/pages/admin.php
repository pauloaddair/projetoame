<?php
//session_start();
//$titulo = "A.B.I.A.T. - Ass. Bras. Inclusão Através do Trabalho";
$titulo = "Painel";
// include_once("include/conexao.php");
include_once("include/funcoes.php");
if (array_key_exists(1, $parametros)) {
    $parametros[1] = strtok($parametros[1], '?');
}
if (array_key_exists(1,$parametros) && $parametros[1]<>""){
		if(file_exists('pages/admin'.$parametros[1].'.php')){
			include_once('pages/admin'. $parametros[1].'.php');
		} else {
			include_once('include/inexiste.php');
		} 
	} else {
  		include_once('pages/adminindex.php');
	}
?>