<?php
include 'conexao.php';

header('Content-Type: application/json');

$expositor_id = $_GET['expositor_id'] ?? null;

if (!$expositor_id) {
    echo json_encode(['error' => 'Expositor não especificado']);
    exit;
}

$sql_expositor = "SELECT nome, contato FROM leads_expositores WHERE id = ?";
$stmt_expositor = $conexao->prepare($sql_expositor);
$stmt_expositor->bind_param("i", $expositor_id);
$stmt_expositor->execute();
$result_expositor = $stmt_expositor->get_result();
$expositor = $result_expositor->fetch_assoc();

if (!$expositor) {
    echo json_encode(['error' => 'Expositor não encontrado']);
    exit;
}

$sql = "SELECT tipo_contato, data_contato, observacao, data_followup, status
        FROM leads_contatos
        WHERE expositor_id = ?
        ORDER BY data_contato DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $expositor_id);
$stmt->execute();
$result = $stmt->get_result();

$contatos = [];
while ($row = $result->fetch_assoc()) {
    $contatos[] = $row;
}

echo json_encode([
    'expositor' => $expositor['nome'],
    'contato' => $expositor['contato'],
    'contatos' => $contatos
]);

$stmt->close();
$stmt_expositor->close();
$conexao->close();
?>