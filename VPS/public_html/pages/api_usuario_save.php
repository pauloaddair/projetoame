<?php
// pages/api_usuario_save.php - Salva dados do perfil do próprio usuário
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// 1. Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado.']);
    exit;
}

$usuario_id = (int)$_SESSION['id'];

// Inclui conexão se ainda não estiver definida (o roteador central já inclui, mas é uma boa prática)
$base_path = dirname(__DIR__) . '/';
include_once($base_path . 'database/conexao.php');

// 2. Recebe e sanitiza parâmetros obrigatórios
$nome = trim(mysqli_real_escape_string($conexao, $_POST['nome'] ?? ''));
$sobrenome = trim(mysqli_real_escape_string($conexao, $_POST['sobrenome'] ?? ''));
$email = trim(mysqli_real_escape_string($conexao, $_POST['email'] ?? ''));
$telefone = trim(mysqli_real_escape_string($conexao, $_POST['telefone'] ?? ''));
$senha = $_POST['senha'] ?? '';

// Validações básicas
if (empty($nome) || empty($sobrenome) || empty($email) || empty($telefone)) {
    echo json_encode(['success' => false, 'message' => 'Todos os campos (Nome, Sobrenome, E-mail e Telefone) são obrigatórios.']);
    exit;
}

// Verifica se o email já está em uso por outro usuário
$q_email = "SELECT usuario_id FROM usuarios WHERE email = '$email' AND usuario_id != $usuario_id LIMIT 1";
$res_email = mysqli_query($conexao, $q_email);
if ($res_email && mysqli_num_rows($res_email) > 0) {
    echo json_encode(['success' => false, 'message' => 'Este e-mail já está sendo utilizado por outra conta.']);
    exit;
}

// Prepara SQL de atualização
$sql_senha = "";
if (!empty($senha)) {
    if (strlen($senha) < 6) {
        echo json_encode(['success' => false, 'message' => 'A nova senha deve conter pelo menos 6 caracteres.']);
        exit;
    }
    // Criptografa senha com Bcrypt conforme a Regra Crítica de Segurança
    $hash_senha = password_hash($senha, PASSWORD_BCRYPT);
    $sql_senha = ", senha = '$hash_senha'";
}

$q_update = "UPDATE usuarios SET 
                nome = '$nome',
                sobrenome = '$sobrenome',
                email = '$email',
                telefone = '$telefone'
                $sql_senha
             WHERE usuario_id = $usuario_id";

if (mysqli_query($conexao, $q_update)) {
    // Atualiza a sessão para refletir as mudanças na barra de navegação imediatamente
    $_SESSION['nome'] = $nome . ' ' . $sobrenome;
    
    echo json_encode(['success' => true, 'message' => 'Seus dados foram atualizados com sucesso!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar dados: ' . mysqli_error($conexao)]);
}
exit;
?>
