<?php
include_once('./include/conexao.php');
// Consulta para obter as colunas dinâmicas (eventos futuros)
$sqlColumns = "
    SELECT GROUP_CONCAT(DISTINCT
        CONCAT(
            'MAX(CASE WHEN horarios.horario_id = ', horarios.horario_id, 
            ' THEN \"X\" ELSE \"\" END) AS `',
            eventos_marcados.nome, ' (', 
            DATE_FORMAT(horarios.data_inicio, '%Y-%m-%d'), ') ')
        ORDER BY horarios.data_inicio ASC
    ) AS dynamic_columns
    FROM horarios
    LEFT JOIN eventos_marcados ON horarios.evento_id = eventos_marcados.id
    LEFT JOIN imagens ON eventos_marcados.imagem_id = imagens.imagem_id
    WHERE horarios.data_inicio >= CURDATE()
";

$resultColumns = $conexao->query($sqlColumns);
if (!$resultColumns) {
    die("Erro ao buscar colunas dinâmicas: " . $mysqli->error);
}

$row = $resultColumns->fetch_assoc();
$dynamicColumns = $row['dynamic_columns'];

// Consulta principal para montar a tabela
$sqlMain = "
    SELECT candidatos.candidato_id, candidatos.nome, $dynamicColumns
    FROM candidatos
    LEFT JOIN disponibilidade ON candidatos.candidato_id = disponibilidade.candidato_id
    LEFT JOIN horarios ON disponibilidade.atividade_id = horarios.horario_id
    LEFT JOIN eventos_marcados ON horarios.evento_id = eventos_marcados.id
    LEFT JOIN imagens ON eventos_marcados.imagem_id = imagens.imagem_id
    WHERE horarios.data_inicio >= CURDATE()
    GROUP BY candidatos.candidato_id, candidatos.nome
    ORDER BY candidatos.nome
";
echo $sqlMain."<br>";
exit;
$resultMain = $conexao->query($sqlMain);
if (!$resultMain) {
    die("Erro ao buscar dados: " . $mysqli->error);
}

// Montagem da tabela em HTML
echo "<table border='1' cellspacing='0' cellpadding='5'>";
echo "<thead><tr>";

// Adiciona cabeçalhos das colunas (nome dos candidatos + colunas dinâmicas)
echo "<th>ID</th><th>Nome</th>";
foreach ($resultMain->fetch_fields() as $field) {
    echo "<th>{$field->name}</th>";
}
echo "</tr></thead><tbody>";

// Adiciona os dados da tabela
while ($row = $resultMain->fetch_assoc()) {
    echo "<tr>";
    foreach ($row as $value) {
        echo "<td>$value</td>";
    }
    echo "</tr>";
}

echo "</tbody></table>";

// Fecha a conexão com o banco de dados
$mysqli->close();
?>

