<!-- END -->
<?php
if ($ativ==""){
	$ativ = "entrou";
}
$ip= $_SERVER['REMOTE_ADDR'];
$usuario_id= 0;
$u_id = 0;
if (isset($_SESSION['id'])){
$u_id = $_SESSION['id'];	
}
$url = "";
if (isset($_GET['url'])){
	$url = $_GET['url'];	
}
/*
if ($ativ<>"inserido"){
	$query = "INSERT INTO `acessos`(`IP`, `parametros`, `usuario_id`, `acao`) 
	VALUES ('".$ip."','".$url."',".$usuario_id.",'".$ativ."')";
	$r = mysqli_query($conexao,$query);
	// Fecha a conexão
	// mysqli_close($conexao);

}
*/
?>
</body>
</html>