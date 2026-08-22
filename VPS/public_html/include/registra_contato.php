<?php
session_start();

$base_path = dirname(__DIR__) . '/';
include_once($base_path . 'database/conexao.php');

if (!$conexao) {
    echo json_encode(['success' => false, 'message' => 'Erro de conexão com o banco de dados.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $expositor_id = intval($_POST['expositor_id'] ?? 0);
    $tipo_contato = $_POST['tipo_contato'] ?? 'outro';
    $data_contato = !empty($_POST['data_contato']) ? $_POST['data_contato'] : date('Y-m-d');
    $observacao = $_POST['observacao'] ?? '';
    $data_followup = !empty($_POST['data_followup']) ? $_POST['data_followup'] : null;
    $u_id = $_SESSION['id'] ?? 1;

    if ($expositor_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID do Expositor inválido.']);
        exit;
    }

    $sql = "INSERT INTO leads_contatos (expositor_id, tipo_contato, data_contato, observacao, data_followup, u_id) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("issssi", $expositor_id, $tipo_contato, $data_contato, $observacao, $data_followup, $u_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Contato registrado com sucesso!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao registrar contato: ' . $stmt->error]);
    }
    $stmt->close();
}
$conexao->close();
?>