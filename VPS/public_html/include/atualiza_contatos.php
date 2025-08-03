<?php
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $contato_id = $_POST['contato_id'];
    $status = $_POST['status'];
	$u_id = $_SESSION['id'];
    $sql = "UPDATE leads_contatos SET status = ?,u_id=? WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("sii", $status, $u_id, $contato_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Status atualizado com sucesso!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar status.']);
    }
    $stmt->close();
}
$conexao->close();
?>