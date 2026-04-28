<?php
// include/api_perfil_check.php - Verifica status de acesso do responsável
header('Content-Type: application/json');
include_once('./conexao.php');

$data = json_decode(file_get_contents('php://input'), true);
$email = mysqli_real_escape_string($conexao, $data['email']);

if (empty($email)) {
    echo json_encode(['success' => false, 'message' => 'E-mail não fornecido']);
    exit;
}

// 1. Busca candidato pelo e-mail para achar o usuario_id
$query = "SELECT c.candidato_id, c.usuario_id, c.nome as nome_candidato, u.login, u.senha, u.nome as nome_usuario 
          FROM candidatos c
          LEFT JOIN usuarios u ON c.usuario_id = u.usuario_ID
          WHERE c.Email = '$email' LIMIT 1";

$result = mysqli_query($conexao, $query);

if ($row = mysqli_fetch_assoc($result)) {
    $has_password = !empty($row['senha']);
    
    echo json_encode([
        'success' => true,
        'exists' => true,
        'has_password' => $has_password,
        'nome_candidato' => $row['nome_candidato'],
        'usuario_id' => $row['usuario_id']
    ]);
} else {
    echo json_encode([
        'success' => true,
        'exists' => false,
        'message' => 'E-mail não encontrado em nossa base de atendentes.'
    ]);
}
?>
