<?php
// include_once('include/conexao.php');
$query = "DESCRIBE candidatos";
$result = mysqli_query($conexao, $query);
echo "ESTRUTURA DA TABELA:\n";
while($row = mysqli_fetch_assoc($result)) {
    print_r($row);
}

echo "\nEXEMPLO DE DADO (Nascimento e Email):\n";
$query = "SELECT Nascimento, Email FROM candidatos WHERE Email IS NOT NULL LIMIT 5";
$result = mysqli_query($conexao, $query);
while($row = mysqli_fetch_assoc($result)) {
    print_r($row);
}
?>
