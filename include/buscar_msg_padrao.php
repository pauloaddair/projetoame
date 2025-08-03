<?php
include 'conexao.php';

$query = "SELECT * FROM mensagens";
$result = $conexao->query($query);

$mensagens = [];
while ($row = $result->fetch_assoc()) {
    $mensagens[] = $row;
}

echo json_encode($mensagens);
?>
