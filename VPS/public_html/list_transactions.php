<?php
require_once __DIR__ . '/database/conexao.php';

if (!$conexao) {
    die("Connection failed: " . mysqli_connect_error());
}

$query = "
    SELECT 
        SUM(CASE WHEN data_prevista < '2023-03-01' THEN 1 ELSE 0 END) AS cnt_before,
        SUM(CASE WHEN data_prevista >= '2023-03-01' THEN 1 ELSE 0 END) AS cnt_after,
        SUM(CASE WHEN data_prevista < '2023-03-01' THEN valor_realizado ELSE 0 END) AS sum_before,
        SUM(CASE WHEN data_prevista >= '2023-03-01' THEN valor_realizado ELSE 0 END) AS sum_after
    FROM `contabil_movimento`
";

$res = mysqli_query($conexao, $query);
$row = mysqli_fetch_assoc($res);
echo "=== COMPARATIVE STATISTICS ===\n";
echo "Before 2023-03-01: \n";
echo " - Count of transactions: " . $row['cnt_before'] . "\n";
echo " - Sum of realized values: R$ " . number_format($row['sum_before'], 2, ",", ".") . "\n";
echo "\nAfter 2023-03-01: \n";
echo " - Count of transactions: " . $row['cnt_after'] . "\n";
echo " - Sum of realized values: R$ " . number_format($row['sum_after'], 2, ",", ".") . "\n";
