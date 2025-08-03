<?php
include 'conexao.php';
$sql = "SELECT c.id, e.id AS expositor_id, e.nome AS expositor, e.contato, ev.nome AS evento, c.tipo_contato, c.data_contato, 
        c.observacao, c.data_followup, c.status
        FROM leads_contatos c
        JOIN leads_expositores e ON c.expositor_id = e.id
        JOIN leads_eventos ev ON e.evento_id = ev.id
        WHERE c.id = (
            SELECT MAX(c2.id)
            FROM leads_contatos c2
            WHERE c2.expositor_id = c.expositor_id
        )
        ORDER BY c.data_followup ASC;";
/*
$sql = "SELECT c.id, e.id AS expositor_id, e.nome AS expositor, ev.nome AS evento, c.tipo_contato, c.data_contato, 
        c.observacao, c.data_followup, c.status
        FROM leads_contatos c
        JOIN leads_expositores e ON c.expositor_id = e.id
        JOIN leads_eventos ev ON e.evento_id = ev.id
        ORDER BY c.data_followup ASC";
*/
$result = $conexao->query($sql);

$contatos = [];
while ($row = $result->fetch_assoc()) {
    $contatos[] = $row;
}

header('Content-Type: application/json');
echo json_encode($contatos);

$conexao->close();
?>