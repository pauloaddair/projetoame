<?php
require_once __DIR__ . '/conexao.php';
global $conexao;

echo "--- Auditoria de Duplicidade ---\n";

$resEmail = mysqli_query($conexao, "SELECT email, COUNT(*) as qtd FROM usuarios WHERE email IS NOT NULL AND email != '' GROUP BY email HAVING qtd > 1");
echo "E-mails duplicados encontrados: " . mysqli_num_rows($resEmail) . "\n";
while($row = mysqli_fetch_assoc($resEmail)) echo "- " . $row['email'] . " (" . $row['qtd'] . ")\n";

$resTel = mysqli_query($conexao, "SELECT telefone, COUNT(*) as qtd FROM usuarios WHERE telefone IS NOT NULL AND telefone != '' GROUP BY telefone HAVING qtd > 1");
echo "Telefones duplicados encontrados: " . mysqli_num_rows($resTel) . "\n";
while($row = mysqli_fetch_assoc($resTel)) echo "- " . $row['telefone'] . " (" . $row['qtd'] . ")\n";
?>