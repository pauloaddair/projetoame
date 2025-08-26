<?php
include_once('./include/conexao.php');
include_once('./include/funcoes.php');
include_once('./include/head.php');
?>
<body>
<?php
	if(array_key_exists(1,$parametros)){
		$msg = "ID=".$parametros[1];
	}
	if(array_key_exists(2,$parametros)){
		$msg .= "<Da tabela ".$parametros[2];
	}
	echo $msg;
include_once('./include/footer.php');
?>
</body>
<?php
include_once('./include/scripts.php');
?>
<?
include_once('./include/end.php');
?>
