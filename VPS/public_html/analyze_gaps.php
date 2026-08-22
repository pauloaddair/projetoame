<?php
require_once __DIR__ . '/database/conexao.php';

if (!$conexao) {
    die("Connection failed: " . mysqli_connect_error());
}

// 1. Get monthly stats
$query = "
    SELECT 
        YEAR(data_prevista) AS ano,
        MONTH(data_prevista) AS mes,
        COUNT(*) AS total_transacoes,
        SUM(CASE WHEN valor_realizado > 0 THEN valor_realizado ELSE 0 END) AS total_entradas,
        SUM(CASE WHEN valor_realizado < 0 THEN valor_realizado ELSE 0 END) AS total_saidas,
        SUM(valor_realizado) AS saldo_realizado
    FROM `contabil_movimento`
    GROUP BY YEAR(data_prevista), MONTH(data_prevista)
    ORDER BY ano ASC, mes ASC
";

$res = mysqli_query($conexao, $query);
$monthly_data = [];
$min_year = 9999;
$max_year = 0;
$min_month = 12;
$max_month = 1;

while ($row = mysqli_fetch_assoc($res)) {
    $y = intval($row['ano']);
    $m = intval($row['mes']);
    
    if ($y < $min_year || ($y == $min_year && $m < $min_month)) {
        $min_year = $y;
        $min_month = $m;
    }
    if ($y > $max_year || ($y == $max_year && $m > $max_month)) {
        $max_year = $y;
        $max_month = $m;
    }
    
    $key = sprintf("%04d-%02d", $y, $m);
    $monthly_data[$key] = [
        'count' => intval($row['total_transacoes']),
        'entradas' => floatval($row['total_entradas']),
        'saidas' => floatval($row['total_saidas']),
        'saldo' => floatval($row['saldo_realizado'])
    ];
}

// 2. Generate all months range and detect missing months
$start_time = strtotime("$min_year-$min_month-01");
$end_time = strtotime("$max_year-$max_month-01");

$missing_months = [];
$no_outflows = [];
$suspicious_months = [];

$current = $start_time;
$saldo_acumulado = 0;

echo "=== FULL MONTHLY ANALYSIS & GAPS ===\n";
echo str_pad("Mês", 8) . " | " . 
     str_pad("Transações", 11) . " | " . 
     str_pad("Entradas (R$)", 15) . " | " . 
     str_pad("Saídas (R$)", 15) . " | " . 
     str_pad("Saldo (R$)", 15) . " | " . 
     str_pad("Acumulado (R$)", 18) . "\n";
echo str_repeat("-", 90) . "\n";

while ($current <= $end_time) {
    $key = date('Y-m', $current);
    if (isset($monthly_data[$key])) {
        $data = $monthly_data[$key];
        $saldo_acumulado += $data['saldo'];
        
        echo str_pad($key, 8) . " | " . 
             str_pad($data['count'], 11, " ", STR_PAD_LEFT) . " | " . 
             str_pad(number_format($data['entradas'], 2, ",", "."), 15, " ", STR_PAD_LEFT) . " | " . 
             str_pad(number_format($data['saidas'], 2, ",", "."), 15, " ", STR_PAD_LEFT) . " | " . 
             str_pad(number_format($data['saldo'], 2, ",", "."), 15, " ", STR_PAD_LEFT) . " | " . 
             str_pad(number_format($saldo_acumulado, 2, ",", "."), 18, " ", STR_PAD_LEFT) . "\n";
             
        // Analysis metrics
        if ($data['saidas'] == 0 && $data['entradas'] > 0) {
            $no_outflows[] = [
                'mes' => $key,
                'entradas' => $data['entradas']
            ];
        }
        
        // Suspicious: high inflows (> R$ 1000) and extremely low outflows (< 5% of inflows or < R$ 100)
        $abs_saidas = abs($data['saidas']);
        if ($data['entradas'] > 1000 && ($abs_saidas < 100 || $abs_saidas < 0.05 * $data['entradas'])) {
            $suspicious_months[] = [
                'mes' => $key,
                'entradas' => $data['entradas'],
                'saidas' => $data['saidas']
            ];
        }
    } else {
        // Missing month (0 transactions)
        $missing_months[] = $key;
        echo str_pad($key, 8) . " | " . 
             str_pad("0", 11, " ", STR_PAD_LEFT) . " | " . 
             str_pad("0,00", 15, " ", STR_PAD_LEFT) . " | " . 
             str_pad("0,00", 15, " ", STR_PAD_LEFT) . " | " . 
             str_pad("0,00", 15, " ", STR_PAD_LEFT) . " | " . 
             str_pad(number_format($saldo_acumulado, 2, ",", "."), 18, " ", STR_PAD_LEFT) . " [GAP/Vazio]\n";
    }
    
    // Move to next month
    $current = strtotime("+1 month", $current);
}

echo "\n=== SUMMARY OF IRREGULARITIES ===\n";

echo "\n1. Meses totalmente vazios (GAPs com zero transações):\n";
if (empty($missing_months)) {
    echo "Nenhum mês vazio encontrado no intervalo.\n";
} else {
    foreach ($missing_months as $m) {
        echo " - $m\n";
    }
}

echo "\n2. Meses com Entradas mas ZERO Saídas/Despesas:\n";
if (empty($no_outflows)) {
    echo "Nenhum.\n";
} else {
    foreach ($no_outflows as $item) {
        echo " - {$item['mes']}: Entradas de R$ " . number_format($item['entradas'], 2, ",", ".") . " e nenhuma despesa registrada.\n";
    }
}

echo "\n3. Meses suspeitos (Altas Entradas e Baixíssimas Despesas):\n";
if (empty($suspicious_months)) {
    echo "Nenhum.\n";
} else {
    foreach ($suspicious_months as $item) {
        echo " - {$item['mes']}: Entradas de R$ " . number_format($item['entradas'], 2, ",", ".") . " | Despesas de R$ " . number_format($item['saidas'], 2, ",", ".") . " (menos de 5% das entradas).\n";
    }
}
