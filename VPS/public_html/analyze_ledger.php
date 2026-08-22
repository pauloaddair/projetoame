<?php
require_once __DIR__ . '/database/conexao.php';

if (!$conexao) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "=== TABLE STRUCTURE ===\n";
$res = mysqli_query($conexao, "DESCRIBE `contabil_movimento`");
while ($row = mysqli_fetch_assoc($res)) {
    echo "{$row['Field']} - {$row['Type']}\n";
}

echo "\n=== MONTHLY ANALYSIS ===\n";
$query = "
    SELECT 
        YEAR(data_prevista) AS ano,
        MONTH(data_prevista) AS mes,
        COUNT(*) AS total_transacoes,
        SUM(CASE WHEN valor_previsto > 0 THEN valor_previsto ELSE 0 END) AS total_entradas,
        SUM(CASE WHEN valor_previsto < 0 THEN valor_previsto ELSE 0 END) AS total_saidas,
        SUM(valor_previsto) AS saldo_previsto,
        SUM(CASE WHEN valor_realizado > 0 THEN valor_realizado ELSE 0 END) AS total_entradas_realizadas,
        SUM(CASE WHEN valor_realizado < 0 THEN valor_realizado ELSE 0 END) AS total_saidas_realizadas,
        SUM(valor_realizado) AS saldo_realizado
    FROM `contabil_movimento`
    GROUP BY YEAR(data_prevista), MONTH(data_prevista)
    ORDER BY ano ASC, mes ASC
";

$res = mysqli_query($conexao, $query);
$meses = [];
$saldo_acumulado_previsto = 0;
$saldo_acumulado_realizado = 0;

echo str_pad("Ano/Mês", 10) . " | " . 
     str_pad("Qtd", 5) . " | " . 
     str_pad("Entradas (Prev)", 15) . " | " . 
     str_pad("Saídas (Prev)", 15) . " | " . 
     str_pad("Saldo (Prev)", 15) . " | " . 
     str_pad("Acumulado (Prev)", 18) . " | " .
     str_pad("Entradas (Real)", 15) . " | " .
     str_pad("Saídas (Real)", 15) . " | " .
     str_pad("Saldo (Real)", 15) . " | " .
     str_pad("Acumulado (Real)", 18) . "\n";
echo str_repeat("-", 160) . "\n";

while ($row = mysqli_fetch_assoc($res)) {
    $ano_mes = sprintf("%04d-%02d", $row['ano'], $row['mes']);
    $saldo_previsto = $row['saldo_previsto'] ?? 0;
    $saldo_realizado = $row['saldo_realizado'] ?? 0;
    $saldo_acumulado_previsto += $saldo_previsto;
    $saldo_acumulado_realizado += $saldo_realizado;
    
    echo str_pad($ano_mes, 10) . " | " . 
         str_pad($row['total_transacoes'], 5, " ", STR_PAD_LEFT) . " | " . 
         str_pad(number_format($row['total_entradas'], 2, ",", "."), 15, " ", STR_PAD_LEFT) . " | " . 
         str_pad(number_format($row['total_saidas'], 2, ",", "."), 15, " ", STR_PAD_LEFT) . " | " . 
         str_pad(number_format($saldo_previsto, 2, ",", "."), 15, " ", STR_PAD_LEFT) . " | " . 
         str_pad(number_format($saldo_acumulado_previsto, 2, ",", "."), 18, " ", STR_PAD_LEFT) . " | " .
         str_pad(number_format($row['total_entradas_realizadas'], 2, ",", "."), 15, " ", STR_PAD_LEFT) . " | " . 
         str_pad(number_format($row['total_saidas_realizadas'], 2, ",", "."), 15, " ", STR_PAD_LEFT) . " | " . 
         str_pad(number_format($saldo_realizado, 2, ",", "."), 15, " ", STR_PAD_LEFT) . " | " . 
         str_pad(number_format($saldo_acumulado_realizado, 2, ",", "."), 18, " ", STR_PAD_LEFT) . "\n";
}
