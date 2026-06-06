<?php
// session_start();
include_once(dirname(__DIR__) . '/database/conexao.php');
include_once(__DIR__ . '/funcoes.php');
$ref = "../";
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
	$_SESSION['nao_autenticado'] = true;
	header('Location: '.$ref);
	exit();
}
$usuario = mysqli_real_escape_string($conexao, $_POST['nome']);
$senha_input = $_POST['senha']; // Mantemos sem escape para password_verify

// Buscamos apenas pelo usuário, a senha será verificada no PHP
$query = "SELECT usuario_ID, login, nome, nivel, imagens.url, senha 
          FROM usuarios 
          LEFT JOIN imagens ON usuarios.imagem_id = imagens.imagem_id
          WHERE login LIKE '{$usuario}' OR email LIKE '{$usuario}' OR telefone LIKE '{$usuario}'";

$result = mysqli_query($conexao, $query);

if ($result && mysqli_num_rows($result) == 1) {
    $row1 = mysqli_fetch_assoc($result);
    $senha_hash = $row1['senha'];
    $autenticado = false;

    // 1. Verifica se é hash Bcrypt (novo padrão)
    if (password_verify($senha_input, $senha_hash)) {
        $autenticado = true;
    } 
    // 2. Verifica se é MD5 (legado)
    elseif (md5($senha_input) === $senha_hash) {
        $autenticado = true;
        // Aqui podemos futuramente adicionar uma flag para forçar a troca
    }

    if ($autenticado) {
        if (isset($_POST['lembrar'])){
            setcookie("usuario_id", $row1["usuario_ID"], time()+14*24*60*60, "/");
        } else {
            setcookie("usuario_id", "", time()-3600, "/");
        }
        
        session_start();
        $_SESSION['id'] = $row1["usuario_ID"];
        $_SESSION['usuario'] = $usuario;
        $_SESSION['nome'] = $row1["nome"];
        $_SESSION['nivel'] = $row1["nivel"];
        $_SESSION['perfil'] = $row1["url"];
        
        // Verifica se a senha está vazia (Primeiro acesso)
        if (empty($row1['senha'])) {
            header("Location: " . $GLOBALS['app_web_root'] . "pages/novasenha.php");
        } else {
            header("Location: $ref");
        }
        exit();
    }
}

// Se chegou aqui, falhou
$_SESSION['nao_autenticado'] = true;
header('Location: ' . $ref);
exit();

