<?php
require_once __DIR__ . '/conexao.php';
global $conexao;

$term = mysqli_real_escape_string($conexao, $_GET['term'] ?? '');

$sql = "SELECT candidato_id as id, nome as value FROM candidatos WHERE nome LIKE '%$term%' LIMIT 10";
$result = mysqli_query($conexao, $sql);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
?>