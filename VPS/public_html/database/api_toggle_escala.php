<?php
require_once __DIR__ . '/conexao.php';
global $conexao;

$action = $_POST['action']; // 'add' ou 'toggle'
$atividade_id = intval($_POST['atividade_id']);

if ($action == 'add') {
    $candidato_id = intval($_POST['candidato_id']);
    // Insere novo interessado
    $sql = "INSERT IGNORE INTO disponibilidade (candidato_id, atividade_id, escalado) VALUES ($candidato_id, $atividade_id, 0)";
    mysqli_query($conexao, $sql);
    echo json_encode(['success' => true]);
} elseif ($action == 'toggle') {
    $id = intval($_POST['id']);
    $escalado = $_POST['escalado'] == 'true' ? 1 : 0;
    // Atualiza status de escala
    $sql = "UPDATE disponibilidade SET escalado = $escalado WHERE id = $id";
    mysqli_query($conexao, $sql);
    echo json_encode(['success' => true]);
}
?>