<?php
require_once __DIR__ . '/conexao.php';
global $conexao;

$sql = "UPDATE usuarios u 
        JOIN candidatos_usuarios cu ON u.usuario_id = cu.usuario_id 
        JOIN candidatos c ON cu.candidato_id = c.candidato_id 
        SET u.nome = c.responsavel 
        WHERE c.responsavel IS NOT NULL AND c.responsavel != ''";

if (mysqli_query($conexao, $sql)) {
    echo "Sucesso: Nomes dos usuários sincronizados com o campo 'responsavel' dos candidatos vinculados.";
} else {
    echo "Erro: " . mysqli_error($conexao);
}
?>