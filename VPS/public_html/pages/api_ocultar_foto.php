<?php
// pages/api_ocultar_foto.php - Oculta/Desativa uma foto reconhecida no portfólio
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Conexão com o banco de dados
if (!isset($conexao)) {
    require_once __DIR__ . '/../database/conexao.php';
}

// 1. Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado.']);
    exit;
}

$usuario_id = (int)$_SESSION['id'];
$nivel_usuario = (int)$_SESSION['nivel'];
$is_admin = ($nivel_usuario >= 4);

// 2. Recebe e valida parâmetros
$foto_id = isset($_POST['foto_id']) ? (int)$_POST['foto_id'] : 0;
if ($foto_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID da foto inválido.']);
    exit;
}

// 3. Busca candidato associado à foto
$q_foto = "SELECT candidato_id FROM fotos_reconhecidas WHERE id = $foto_id LIMIT 1";
$res_foto = mysqli_query($conexao, $q_foto);
if (!$res_foto || mysqli_num_rows($res_foto) == 0) {
    echo json_encode(['success' => false, 'message' => 'Foto não encontrada.']);
    exit;
}
$foto_data = mysqli_fetch_assoc($res_foto);
$candidato_id = (int)$foto_data['candidato_id'];

// 4. Valida permissão do usuário
if (!$is_admin) {
    $q_perm = "SELECT 1 FROM candidatos_usuarios WHERE usuario_id = $usuario_id AND candidato_id = $candidato_id LIMIT 1";
    $res_perm = mysqli_query($conexao, $q_perm);
    if (!$res_perm || mysqli_num_rows($res_perm) == 0) {
        echo json_encode(['success' => false, 'message' => 'Você não tem permissão para remover esta foto.']);
        exit;
    }
}

// 5. Oculta a foto
$q_update = "UPDATE fotos_reconhecidas SET oculta = 1 WHERE id = $foto_id";
if (mysqli_query($conexao, $q_update)) {
    echo json_encode(['success' => true, 'message' => 'Foto removida do portfólio com sucesso.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao remover a foto no banco de dados.']);
}
exit;
