<?php
include 'conexao.php';

$id = $_GET['id'];

$query = "SELECT telefone, email FROM contatos WHERE id = ?";
$stmt = $conexao->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$contato = $result->fetch_assoc();

echo json_encode($contato);
?>
