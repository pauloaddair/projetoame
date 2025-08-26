<?php
header('Content-Type: application/json');
include_once('./conexao.php');
include_once('./processar_rodizio.php'); // Inclui a função que processa o rodízio

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos.']);
    exit;
}

// Ação: Processar o rodízio para uma data
if (isset($data['action']) && $data['action'] === 'processar_rodizio') {
    if (isset($data['data_evento'])) {
        $resultado = processar_rodizio_por_data($data['data_evento'], $conexao);
        echo json_encode(['success' => true, 'message' => $resultado]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Data do evento não fornecida.']);
    }
    exit;
}

// Ação: Atualizar o status de um único evento
if (isset($data['id'], $data['escala_fechada'], $data['status_evento'])) {
    $evento_id = intval($data['id']);
    $escala_fechada = intval($data['escala_fechada']);
    $status_evento = mysqli_real_escape_string($conexao, $data['status_evento']);

    $query = "UPDATE eventos_marcados SET escala_fechada = {$escala_fechada}, status_evento = '{$status_evento}' WHERE id = {$evento_id}";

    if (mysqli_query($conexao, $query)) {
        echo json_encode(['success' => true, 'message' => 'Status do evento atualizado com sucesso.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Falha ao atualizar o status do evento: ' . mysqli_error($conexao)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos para atualizar status.']);
}

?>