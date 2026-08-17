<?php
// pages/api_perfil_save.php - Salva dados do Wizard de Perfil do Associado
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
$nivel_usuario = (int)$_SESSION['nivel'];
$is_admin = ($nivel_usuario >= 4);

// 2. Recebe e valida parâmetros obrigatórios
$candidato_id = isset($_POST['candidato_id']) ? (int)$_POST['candidato_id'] : 0;
if ($candidato_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID do associado inválido.']);
    exit;
}

// 3. Valida permissão do usuário
if (!$is_admin) {
    $q_perm = "SELECT 1 FROM candidatos_usuarios WHERE usuario_id = $usuario_id AND candidato_id = $candidato_id LIMIT 1";
    $res_perm = mysqli_query($conexao, $q_perm);
    if (!$res_perm || mysqli_num_rows($res_perm) == 0) {
        echo json_encode(['success' => false, 'message' => 'Você não tem permissão para editar os dados deste associado.']);
        exit;
    }
}

// 4. Sanitiza e prepara os dados para inserção/atualização
$nome = mysqli_real_escape_string($conexao, $_POST['nome'] ?? '');
$nascimento = mysqli_real_escape_string($conexao, $_POST['nascimento'] ?? '');
$cpf = mysqli_real_escape_string($conexao, $_POST['cpf'] ?? '');
$pix = mysqli_real_escape_string($conexao, $_POST['chave_pix'] ?? '');

$restricoes_alimentares = mysqli_real_escape_string($conexao, $_POST['restricoes_alimentares'] ?? '');
$medicacao_continuada = mysqli_real_escape_string($conexao, $_POST['medicacao_continuada'] ?? '');
$medicacao_horarios = mysqli_real_escape_string($conexao, $_POST['medicacao_horarios'] ?? '');
$orientacoes_responsaveis = mysqli_real_escape_string($conexao, $_POST['orientacoes_responsaveis'] ?? '');
$cuidados_especiais = mysqli_real_escape_string($conexao, $_POST['cuidados_especiais'] ?? '');
$cursos_externos = mysqli_real_escape_string($conexao, $_POST['cursos_externos'] ?? '');

$camisa = mysqli_real_escape_string($conexao, $_POST['camisa'] ?? '');
$calca = mysqli_real_escape_string($conexao, $_POST['calca'] ?? '');
$sapato = mysqli_real_escape_string($conexao, $_POST['calcado'] ?? ''); // calcado do form vira sapato no banco

$cep = mysqli_real_escape_string($conexao, $_POST['cep'] ?? '');
$logradouro = mysqli_real_escape_string($conexao, $_POST['logradouro'] ?? '');
$numero = mysqli_real_escape_string($conexao, $_POST['numero'] ?? '');
$complemento = mysqli_real_escape_string($conexao, $_POST['complemento'] ?? '');

// Concatena endereço se número foi enviado
$endereco = $logradouro;
if (!empty($numero)) {
    $endereco .= ', ' . $numero;
}
$endereco = mysqli_real_escape_string($conexao, $endereco);

// Validação de dados essenciais
if (empty($nome)) {
    echo json_encode(['success' => false, 'message' => 'O nome do associado é obrigatório.']);
    exit;
}

// 5. Executa a query de atualização
$q_update = "UPDATE candidatos SET 
                nome = '$nome',
                Nascimento = '$nascimento',
                CPF = '$cpf',
                PIX = '$pix',
                restricoes_alimentares = '$restricoes_alimentares',
                medicacao_continuada = '$medicacao_continuada',
                medicacao_horarios = '$medicacao_horarios',
                orientacoes_responsaveis = '$orientacoes_responsaveis',
                cuidados_especiais = '$cuidados_especiais',
                cursos_externos = '$cursos_externos',
                camisa = '$camisa',
                calca = '$calca',
                sapato = '$sapato',
                CEP = '$cep',
                endereco = '$endereco',
                complemento = '$complemento'
             WHERE candidato_id = $candidato_id";

if (mysqli_query($conexao, $q_update)) {
    echo json_encode(['success' => true, 'message' => 'Perfil do associado atualizado com sucesso!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar dados no banco de dados: ' . mysqli_error($conexao)]);
}
exit;

