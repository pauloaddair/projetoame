<?php
require_once __DIR__ . '/conexao.php';
global $conexao;

$candidato_id = intval($_POST['candidato_id']);
$atividade_id = intval($_POST['atividade_id']); // Vem do ID do horário criado

// Insere ou atualiza o registro na disponibilidade
// Usamos INSERT ... ON DUPLICATE KEY UPDATE para garantir que não tenhamos duplicatas
$sql = "INSERT INTO disponibilidade (candidato_id, atividade_id, escalado) 
        VALUES ($candidato_id, $atividade_id, 1)
        ON DUPLICATE KEY UPDATE escalado = 1";

if (mysqli_query($conexao, $sql)) {
    echo json_encode(['success' => true, 'message' => 'Escalado com sucesso!']);
} else {
    echo json_encode(['success' => false, 'message' => mysqli_error($conexao)]);
}
?>