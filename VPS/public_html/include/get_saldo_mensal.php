<?php
header('Content-Type: application/json');
include_once(dirname(__DIR__) . '/database/conexao.php');
include_once(__DIR__ . '/funcoes.php');

/*
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die(json_encode(['error' => 'Falha na conexão: ' . $conn->connect_error]));
}

*/
// Query SQL
$sql = "SELECT 
            DATE_FORMAT(data_prevista, '%Y') AS ano,
            DATE_FORMAT(data_prevista, '%m') AS mes,
            SUM(valor_realizado) AS total_mes,
            (SELECT SUM(valor_realizado) FROM contabil_movimento m2 WHERE m2.data_prevista <= MAX(m1.data_prevista)) AS saldo_acumulado
        FROM contabil_movimento m1
        GROUP BY ano,mes
        ORDER BY ano,mes";

$result = $conexao->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'mes_ano' => $row['ano']."-".mes(intval($row['mes'])),
        'total_mes' => number_format($row['total_mes'], 2, '.', ''),
        'saldo_acumulado' => number_format($row['saldo_acumulado'], 2, '.', '')
    ];
}

$conexao->close();

echo json_encode($data);
?>
