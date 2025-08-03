<?php
include 'conexao.php';

if (isset($_GET["contato_id"])) {
    $contato_id = intval($_GET["contato_id"])?? 0;

    $sql = "SELECT id, anotacao, data_criacao FROM anotacoes WHERE contato_id = ? ORDER BY data_criacao DESC";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $contato_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<ul class='list-group'>";
        while ($row = $result->fetch_assoc()) {
            echo "<li class='list-group-item'>";
            echo "<p>" . htmlspecialchars($row['anotacao']) . "</p>";
            echo "<small class='text-muted'>Criada em: " . $row['data_criacao'] . "</small>";
            echo "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p class='text-muted'>Nenhuma anotação encontrada.</p>";
    }

    $stmt->close();
}
?>
