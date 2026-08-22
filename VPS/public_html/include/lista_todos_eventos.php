<?php
header('Content-Type: application/json');
include_once(dirname(__DIR__) . '/database/conexao.php');

$query = "SELECT id, nome, inicio, final, status_evento, uuid FROM eventos_marcados ORDER BY inicio DESC";
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