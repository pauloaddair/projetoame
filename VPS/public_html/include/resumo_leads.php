<?php
include 'conexao.php';

$sql = "SELECT status, COUNT(*) as total 
        FROM leads_contatos 
        GROUP BY status";
$result = $conexao->query($sql);

$data = [
    'labels' => [],
    'values' => []
];
while ($row = $result->fetch_assoc()) {
    $data['labels'][] = ucfirst($row['status']); // Capitaliza o status
    $data['values'][] = (int)$row['total'];
}

header('Content-Type: application/json');
echo json_encode($data);

$conexao->close();
?>