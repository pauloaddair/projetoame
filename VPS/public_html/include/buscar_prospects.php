<?php
include 'conexao.php';

$sql = "SELECT p.id, e.Evento AS evento, ex.nome AS expositor, p.status, p.data_registro 
        FROM prospects p
        JOIN eventos e ON p.evento_id = e.evento_id
        JOIN expositores2024 ex ON p.expositor_id = ex.id
        ORDER BY p.data_registro DESC";

$result = $conexao->query($sql);

if ($result->num_rows > 0) {
    echo "<table class='table table-striped'>";
    echo "<thead><tr>
            <th>Evento</th>
            <th>Expositor</th>
            <th>Status</th>
            <th>Data Registro</th>
            <th>Ações</th>
          </tr></thead><tbody>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row["evento"]) . "</td>
                <td>" . htmlspecialchars($row["expositor"]) . "</td>
                <td>" . htmlspecialchars($row["status"]) . "</td>
                <td>" . $row["data_registro"] . "</td>
                <td>
                    <button class='btn btn-sm btn-primary' onclick='editarProspect(" . $row["id"] . ")'>Editar</button>
                    <button class='btn btn-sm btn-danger' onclick='excluirProspect(" . $row["id"] . ")'>Excluir</button>
                </td>
              </tr>";
    }
    echo "</tbody></table>";
} else {
    echo "<p class='text-muted'>Nenhum prospect encontrado.</p>";
}
?>
