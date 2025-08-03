<?php
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $evento_id = $_POST['evento_id'];
    $nome = $_POST['nome'];
    $contato = $_POST['contato'] ?? null; // Novo campo
	$telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $site = $_POST['siteexp'];
    $whatsapp = $_POST['whatsapp'];
    $instagram = $_POST['instagram'];
	$u_id = $_SESSION['id'];

    $sql = "INSERT INTO leads_expositores (evento_id, nome, contato, telefone, email, whatsapp, instagram, u_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
			ON DUPLICATE KEY UPDATE nome = VALUES(nome), contato = VALUES(contato)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("issssssi", $evento_id, $nome, $contato, $telefone, $email, $whatsapp, $instagram, $u_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Expositor cadastrado com sucesso!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar expositor.']);
    }
    $stmt->close();
}
$conexao->close();
?>