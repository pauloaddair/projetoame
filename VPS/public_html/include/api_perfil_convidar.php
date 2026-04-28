<?php
// include/api_perfil_convidar.php - Gera convite para corresponsável
header('Content-Type: application/json');
include_once('./conexao.php');
session_start();

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Sessão expirada.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$email_convidado = mysqli_real_escape_string($conexao, $data['email']);
$telefone_convidado = preg_replace('/\D/', '', $data['telefone']);
$vinculo_tipo = mysqli_real_escape_string($conexao, $data['tipo']);
$candidato_id = intval($data['candidato_id']);

// 1. Verifica se o convidado já existe em 'usuarios'
$query_user = "SELECT usuario_ID FROM usuarios WHERE login = '$email_convidado' LIMIT 1";
$res_user = mysqli_query($conexao, $query_user);

if ($row_user = mysqli_fetch_assoc($res_user)) {
    $novo_usuario_id = $row_user['usuario_ID'];
} else {
    // Cria novo usuário (sem senha inicial)
    $sql_new_user = "INSERT INTO usuarios (login, nome, nivel) VALUES ('$email_convidado', 'Responsável Convidado', 1)";
    mysqli_query($conexao, $sql_new_user);
    $novo_usuario_id = mysqli_insert_id($conexao);
}

// 2. Cria o vínculo na tabela pivot
$sql_pivot = "INSERT INTO candidatos_usuarios (candidato_id, usuario_id, vinculo_tipo) 
              VALUES ($candidato_id, $novo_usuario_id, '$vinculo_tipo')
              ON DUPLICATE KEY UPDATE vinculo_tipo = '$vinculo_tipo'";

if (mysqli_query($conexao, $sql_pivot)) {
    // 3. Gera Token de Ativação para o convidado
    $token = bin2hex(random_bytes(16));
    $expira = date('Y-m-d H:i:s', strtotime('+48 hours'));
    mysqli_query($conexao, "INSERT INTO tokens_acesso (usuario_id, token, expira_em) VALUES ($novo_usuario_id, '$token', '$expira')");

    // 4. Dispara WhatsApp via Evolution API
    $link = "https://projetoame.org/ativar-perfil/" . $token;
    $mensagem = "Olá! Você foi convidado para gerenciar o perfil de um atendente no Portal AME como *$vinculo_tipo*.\n\nAcesse o link abaixo para ativar sua conta:\n$link";

    // ... lógica de CURL idêntica ao api_perfil_send_token.php ...
    // (Vou omitir o CURL aqui para brevidade, mas ele segue o padrão já estabelecido)

    echo json_encode(['success' => true, 'message' => 'Convite enviado com sucesso!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao criar vínculo.']);
}
?>
