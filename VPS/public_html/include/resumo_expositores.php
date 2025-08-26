<?php
include 'conexao.php';

$sql = "SELECT ev.nome AS evento, COUNT(e.id) AS total 
        FROM leads_expositores e 
        JOIN leads_eventos ev ON e.evento_id = ev.id 
        GROUP BY ev.id, ev.nome";
$result = $conexao->query($sql);

$data = [
    'labels' => [],
    'values' => []
];
while ($row = $result->fetch_assoc()) {
    $data['labels'][] = $row['evento'];
    $data['values'][] = (int)$row['total'];
}

header('Content-Type: application/json');
echo json_encode($data);

$conexao->close();
?>