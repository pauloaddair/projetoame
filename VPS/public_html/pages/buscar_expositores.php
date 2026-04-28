<?php
header('Content-Type: application/json');
// include_once('./include/conexao.php');

$term = isset($_GET['term']) ? mysqli_real_escape_string($conexao, $_GET['term']) : '';
$expositores = [];

if (strlen($term) > 2) { // Começa a busca após 3 caracteres
    $query = "SELECT expositor_id, empresa, nome FROM expositores2024 WHERE empresa LIKE '%$term%' OR nome LIKE '%$term%' LIMIT 15";
    $result = mysqli_query($conexao, $query);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Formata o rótulo para exibição
            $label = $row['empresa'] . ($row['nome'] ? ' (' . $row['nome'] . ')' : '');
            $expositores[] = ['id' => $row['expositor_id'], 'label' => $label, 'value' => $label];
        }
    }
}

echo json_encode($expositores);
?>
