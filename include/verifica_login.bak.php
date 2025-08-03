<?php
//session_start();
/*
if (isset($_SESSION['usuario'])){
	if(!array_key_exists('usuario',$_SESSION)) {
		$_SESSION['ref'] = $_SERVER["REQUEST_URI"];
		header('Location: /login');
		exit();
	}
}
*/
session_start();
if(!$_SESSION['usuario']) {
	header('Location: login.php?ref=' . $_SERVER["PHP_SELF"]);
	exit();
}

if(isset($_COOKIE[$usuario_id])){
    $usuario_id = $_COOKIE['user_id'];
$query = "select imagens.url, usuario_ID, login, nome, nivel from imagens, usuarios where usuario_id = ". $usuario_id." AND imagens.imagem_id = usuarios.imagem_id";

$result = mysqli_query($conexao, $query);

$row = mysqli_num_rows($result);
// echo "Usu&atilde;rios: " . $row . "<br>";
// exit;
// output data of each row
    //while($row1 = mysqli_fetch_assoc($result)) {
     //   echo "Name: " . $row1["nome"]. " N�vel:" . $row1["nivel"]. "<br>";
    //}

if($row == 1) {
    $row1 = mysqli_fetch_assoc($result);
/*
	echo "<pre>";
	print_r($row1);
	echo "</pre>";
	echo $ref . "<br>";
	exit;
*/
    setcookie("user_id", $row1["usuario_ID"], time()+14*24*60*60);

	$_SESSION['id'] = $row1["usuario_ID"];
	$_SESSION['usuario'] = $usuario;
	$_SESSION['nome'] = $row1["nome"];
	$_SESSION['nivel'] = $row1["nivel"];
	$_SESSION['perfil'] = $row1["url"];
	$host  = $_SERVER['HTTP_HOST'];
	$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	//echo $ref;
//	header('Location: $ref');
	header("Location: $ref");
	exit();
} else {
	$_SESSION['nao_autenticado'] = true;
	header('Location: /login');
	exit();
}
}
?>