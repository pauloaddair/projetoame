<?php
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $expositor_id = $_POST['expositor_id'];
    $tipo_contato = $_POST['tipo_contato'];
    $data_contato = $_POST['data_contato'];
    $observacao = $_POST['observacao'];
    $data_followup = $_POST['data_followup'];

    $sql = "INSERT INTO leads_contatos (expositor_id, tipo_contato, data_contato, observacao, data_followup,u_id) 
            VALUES (?, ?, ?, ?, ?,?)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("issssi", $expositor_id, $tipo_contato, $data_contato, $observacao, $data_followup);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Contato registrado com sucesso!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao registrar contato.']);
    }
    $stmt->close();
}
$conexao->close();
?>