<?php
include 'conexao.php';

$sql = "SELECT id, nome FROM leads_eventos";
$result = $conexao->query($sql);

$options = '<option value="">Selecione um evento</option>';
while ($row = $result->fetch_assoc()) {
    $options .= "<option value='{$row['id']}'>{$row['nome']}</option>";
}
echo $options;

$conexao->close();
?>