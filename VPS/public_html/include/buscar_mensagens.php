<?php
include 'conexao.php';

$sql = "SELECT id, titulo, mensagem, criado_em FROM mensagens ORDER BY criado_em DESC";
$result = $conexao->query($sql);

if ($result->num_rows > 0) {
    echo "<ul class='list-group'>";
    while ($row = $result->fetch_assoc()) {
        echo "<li class='list-group-item'>";
        echo "<strong>" . htmlspecialchars($row['titulo']) . "</strong><br>";
        echo htmlspecialchars($row['mensagem']) . "<br>";
        echo "<small class='text-muted'>Enviado em: " . $row['criado_em'] . "</small>";
        echo "</li>";
    }
    echo "</ul>";
} else {
    echo "<p class='text-muted'>Nenhuma mensagem encontrada.</p>";
}

$conexao->close();
?>
