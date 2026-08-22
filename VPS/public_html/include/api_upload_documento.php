<?php
// F:/01_Projetos/Ativos/PROJETO_AME/VPS/public_html/include/api_upload_documento.php
// Endpoint para upload seguro de documentos e registro no banco de dados

ini_set('display_errors', 0);
error_reporting(E_ALL);
header('Content-Type: application/json');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Verifica autenticação do usuário
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado.']);
    exit;
}

$usuario_id = (int)$_SESSION['id'];
$nivel_usuario = (int)$_SESSION['nivel'];
$is_admin = ($nivel_usuario >= 4);

// Conexão com o Banco de Dados
$base_path = dirname(__DIR__) . '/';
include_once($base_path . 'database/conexao.php');

if (!$conexao) {
    echo json_encode(['success' => false, 'message' => 'Falha na conexão com o banco de dados.']);
    exit;
}

// 2. Recebe e sanitiza parâmetros
$candidato_id = isset($_POST['candidato_id']) ? (int)$_POST['candidato_id'] : 0;
$tipo_doc = isset($_POST['tipo_doc']) ? trim($_POST['tipo_doc']) : '';
$descritivo_custom = isset($_POST['descritivo_custom']) ? trim($_POST['descritivo_custom']) : '';

if ($candidato_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Associado inválido informado.']);
    exit;
}

// Define o texto descritivo
$descritivo = ($tipo_doc === 'Outros') ? $descritivo_custom : $tipo_doc;
if (empty($descritivo)) {
    $descritivo = 'Documento';
}

// 3. Valida permissão do usuário sobre o candidato
if (!$is_admin) {
    $q_perm = "SELECT 1 FROM candidatos_usuarios WHERE usuario_id = $usuario_id AND candidato_id = $candidato_id LIMIT 1";
    $res_perm = mysqli_query($conexao, $q_perm);
    if (!$res_perm || mysqli_num_rows($res_perm) == 0) {
        echo json_encode(['success' => false, 'message' => 'Você não tem permissão para gerenciar documentos deste associado.']);
        exit;
    }
}

// Busca o nome do candidato para o retorno
$q_cand = "SELECT nome FROM candidatos WHERE candidato_id = $candidato_id LIMIT 1";
$res_cand = mysqli_query($conexao, $q_cand);
$candidato_nome = "Associado";
if ($res_cand && $row_cand = mysqli_fetch_assoc($res_cand)) {
    $candidato_nome = $row_cand['nome'];
}

// 4. Valida se o arquivo foi enviado
if (!isset($_FILES['documento_file']) || $_FILES['documento_file']['error'] !== UPLOAD_ERR_OK) {
    $error_code = isset($_FILES['documento_file']['error']) ? $_FILES['documento_file']['error'] : UPLOAD_ERR_NO_FILE;
    $err_msg = 'Erro no envio do arquivo.';
    if ($error_code === UPLOAD_ERR_INI_SIZE || $error_code === UPLOAD_ERR_FORM_SIZE) {
        $err_msg = 'O arquivo enviado excede o limite de tamanho do servidor.';
    } elseif ($error_code === UPLOAD_ERR_NO_FILE) {
        $err_msg = 'Nenhum arquivo foi selecionado.';
    }
    echo json_encode(['success' => false, 'message' => $err_msg]);
    exit;
}

$file = $_FILES['documento_file'];
$file_size = $file['size'];
$file_tmp = $file['tmp_name'];
$file_name = basename($file['name']);

// Limite de tamanho: 10MB
$max_size = 10 * 1024 * 1024;
if ($file_size > $max_size) {
    echo json_encode(['success' => false, 'message' => 'O arquivo excede o limite máximo permitido de 10MB.']);
    exit;
}

// Valida extensões permitidas
$allowed_exts = ['pdf', 'jpg', 'jpeg', 'png'];
$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

if (!in_array($file_ext, $allowed_exts)) {
    echo json_encode(['success' => false, 'message' => 'Formato de arquivo inválido. Apenas PDF, JPG e PNG são aceitos.']);
    exit;
}

// 5. Configuração do diretório de upload: docs/{candidato_id}/
$relative_dir = 'docs/' . $candidato_id . '/';
$upload_dir = $base_path . $relative_dir;

if (!file_exists($upload_dir)) {
    if (!mkdir($upload_dir, 0755, true)) {
        echo json_encode(['success' => false, 'message' => 'Erro interno ao criar diretório para documentos no servidor.']);
        exit;
    }
}

// Higieniza nome do arquivo e gera um nome único
$sanitized_desc = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower(remove_accents($descritivo)));
$new_filename = $sanitized_desc . '_' . time() . '.' . $file_ext;
$target_path = $upload_dir . $new_filename;
$relative_file_path = $relative_dir . $new_filename;

// Função auxiliar simples para remover acentos na higienização
function remove_accents($string) {
    return preg_replace(
        '~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i',
        '$1',
        htmlentities($string, ENT_QUOTES, 'UTF-8')
    );
}

// Move o arquivo temporário
if (move_uploaded_file($file_tmp, $target_path)) {
    // 6. Insere na tabela documentos do banco
    $descritivo_esc = mysqli_real_escape_string($conexao, $descritivo);
    $url_esc = mysqli_real_escape_string($conexao, $relative_file_path);
    
    $query_insert = "INSERT INTO documentos (candidato_id, url, descritivo, data) 
                     VALUES ($candidato_id, '$url_esc', '$descritivo_esc', NOW())";
                     
    if (mysqli_query($conexao, $query_insert)) {
        $app_web_root = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
        $app_web_root = str_replace('include/', '', $app_web_root);
        
        echo json_encode([
            'success' => true,
            'message' => 'Documento enviado e registrado com sucesso!',
            'candidato_nome' => htmlspecialchars($candidato_nome),
            'url' => $app_web_root . $relative_file_path,
            'descritivo' => htmlspecialchars($descritivo),
            'data' => date('d/m/Y H:i')
        ]);
    } else {
        // Se falhou ao inserir no banco, remove o arquivo físico para não deixar lixo
        @unlink($target_path);
        echo json_encode(['success' => false, 'message' => 'Erro ao registrar o documento no banco de dados.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Erro interno ao mover arquivo para a pasta definitiva no servidor.']);
}
exit;
