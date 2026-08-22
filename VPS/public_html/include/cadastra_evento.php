<?php
session_start(); // Inicia a sessão

$base_path = dirname(__DIR__) . '/';
include_once($base_path . 'database/conexao.php');

if (!$conexao) {
    echo json_encode(['success' => false, 'message' => 'Erro de conexão com o banco de dados.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'] ?? '';
    $data = !empty($_POST['data']) ? $_POST['data'] : null;
    $local = $_POST['local'] ?? '';
    $site = $_POST['site'] ?? '';

    if (empty($nome)) {
        echo json_encode(['success' => false, 'message' => 'O nome do evento é obrigatório.']);
        exit;
    }

    $sql = "INSERT INTO eventos (Evento, Inicio, Local, SITE, `Data Confirmada`) VALUES (?, ?, ?, ?, 1)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ssss", $nome, $data, $local, $site);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Evento cadastrado com sucesso!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar evento: ' . $stmt->error]);
    }
    $stmt->close();
}
$conexao->close();
?>