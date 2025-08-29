<?php
header('Content-Type: application/json');
include_once('conexao.php');

$query = "SELECT id, nome, inicio, final, status_evento FROM eventos_marcados ORDER BY inicio DESC";
$result = mysqli_query($conexao, $query);

$eventos = [];
while ($row = mysqli_fetch_assoc($result)) {
    $eventos[] = $row;
}

// Formato esperado pelo DataTables
$output = [
    'data' => $eventos
];

echo json_encode($output);
?>