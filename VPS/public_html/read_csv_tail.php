<?php
$csv_path = "F:/OneDrive/Projeto A.M.E/02_Administrativo_e_Operacional/Planilhas/contabil_movimento1.csv";

if (($handle = fopen($csv_path, "r")) !== FALSE) {
    $header = fgetcsv($handle, 1000, ";");
    
    // Read all rows into an array
    $rows = [];
    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
        if (count($data) < count($header)) continue;
        $rows[] = array_combine($header, array_slice($data, 0, count($header)));
    }
    fclose($handle);
    
    echo "Total rows in CSV: " . count($rows) . "\n\n";
    
    // Print last 20 rows
    $last_20 = array_slice($rows, -20);
    echo "=== LAST 20 ROWS OF CSV ===\n";
    foreach ($last_20 as $idx => $row) {
        echo "id: {$row['id']} | data_prevista: {$row['data_prevista']} | descricao: {$row['descricao']} | valor_realizado: {$row['valor_realizado']} | saldo: {$row['saldo']}\n";
    }
} else {
    echo "Failed to open CSV.\n";
}
