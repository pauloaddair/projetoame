<?php
require_once __DIR__ . '/conexao.php';
global $conexao;

$sql = [
    "ALTER TABLE usuarios ADD UNIQUE (email)",
    "ALTER TABLE usuarios ADD UNIQUE (telefone)"
];

foreach ($sql as $query) {
    if (mysqli_query($conexao, $query)) {
        echo "Sucesso: $query<br>";
    } else {
        echo "Erro ao executar '$query': " . mysqli_error($conexao) . "<br>";
    }
}
?>