<?php
session_start();
include('conexao.php');
include('funcoes.php');
$ref = "./";
if (isset($_SERVER['HTTP_REFERER'])){
	$ref = $_SERVER['HTTP_REFERER'];	
}
if (isset($_POST['ref'])){
    $ref = $_POST['ref'];
}
/*
echo "<pre>";
print_r($_POST);
echo "</pre>";
echo $ref;
echo "<pre>";
print_r($_COOKIE);
echo "</pre>";
// $ref = "/";
 exit;
*/

if(empty($_POST['nome']) || empty($_POST['senha'])) {
	$_SESSION['nao_autenticado']==true;
	header('Location: '.$ref);
	exit();
}
	$usuario = mysqli_real_escape_string($conexao, $_POST['nome']);
	$senha = mysqli_real_escape_string($conexao, $_POST['senha']);
	$telefone = formataWA($usuario);
	$query = "select usuario_ID, login, nome, nivel, imagens.url 
	from usuarios 
	LEFT JOIN imagens
	ON usuarios.imagem_id = imagens.imagem_id
	WHERE ((login LIKE '{$usuario}' OR email LIKE '{$usuario}' OR telefone LIKE '{$usuario}') and (senha = md5('{$senha}')))";
/*
echo $query;
exit;


*/
$result = mysqli_query($conexao, $query);

$row = mysqli_num_rows($result);
// echo "Usu&aacute;rios: " . $row . "<br>";
 // output data of each row
/*
	echo $ref . "<br>";
    while($row1 = mysqli_fetch_assoc($result)) {
        echo "Name: " . $row1["nome"]. "<br> Nível:" . $row1["nivel"]. "<br>";
    }
exit;
*/

if($row == 1) {
    $row1 = mysqli_fetch_assoc($result);
/*
	echo "<pre>";
	print_r($row1);
	echo "</pre>";
	echo $ref . "<br>";
	exit;
	$usuario = $row1["login"];
*/

   if (isset($_POST['lembrar'])){
        setcookie("usuario_id", $row1["usuario_ID"], time()+14*24*60*60);
    } else {
        setcookie("usuario_id", "", time()-3600);
    }
	$_SESSION['id'] = $row1["usuario_ID"];
	$_SESSION['usuario'] = $usuario;
	$_SESSION['nome'] = $row1["nome"];
	$_SESSION['nivel'] = $row1["nivel"];
	$_SESSION['perfil'] = $row1["url"];
	$host  = $_SERVER['HTTP_HOST'];
	$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
/*
		echo "<pre>";
		print_r($_SESSION);
		echo "</pre>";			  
*/
//	exit();
	//echo $ref;
//	header('Location: $ref');
	header("Location: $ref");
	exit();
} else {
	$_SESSION['nao_autenticado'] = true;
	header('Location: /home');
	exit();
}

