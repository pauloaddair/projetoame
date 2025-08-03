<?php
include 'conexao.php';

$sql = "SELECT id, nome, contato FROM leads_expositores";
$result = $conexao->query($sql);

$options = '<option value="">Selecione um expositor</option>';
while ($row = $result->fetch_assoc()) {
	$display = $row['nome'] . ($row['contato'] ? " (" . $row['contato'] . ")" : "");
    $options .= "<option value='{$row['id']}'>{$display}</option>";}
echo $options;

$conexao->close();
?>