<?php
session_start(); // Inicia a sessão

include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $data = $_POST['data'];
    $local = $_POST['local'];
    $site = $_POST['site'];
//	echo $_SESSION['id']."<br>";
	$u_id = $_SESSION['id'] ?? 1;

    $sql = "INSERT INTO leads_eventos (nome, data, local, site, u_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ssssi", $nome, $data, $local, $site, $u_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Evento cadastrado com sucesso!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar evento.']);
    }
    $stmt->close();
}
$conexao->close();
?>