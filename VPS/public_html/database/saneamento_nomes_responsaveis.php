<?php
require_once __DIR__ . '/conexao.php';
global $conexao;

echo "--- Iniciando correção de nomes dos usuários (responsáveis) ---\n";

// A ideia é: para cada usuario_id vinculado a um candidato, 
// o nome desse usuário deve ser o nome do responsável registrado no cadastro do candidato.
$query = "SELECT u.usuario_id, c.responsavel 
          FROM usuarios u 
          JOIN candidatos_usuarios cu ON u.usuario_id = cu.usuario_id 
          JOIN candidatos c ON cu.candidato_id = c.candidato_id 
          WHERE c.responsavel IS NOT NULL AND c.responsavel != '' 
          AND u.nome LIKE 'Responsável Legado%'"; // Foca nos que criamos como placeholders

$result = mysqli_query($conexao, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $uid = $row['usuario_id'];
    $novoNome = mysqli_real_escape_string($conexao, $row['responsavel']);
    
    $update = "UPDATE usuarios SET nome = '$novoNome' WHERE usuario_id = $uid";
    if (mysqli_query($conexao, $update)) {
        echo "Usuário $uid renomeado para: $novoNome<br>";
    } else {
        echo "Erro ao renomear usuário $uid: " . mysqli_error($conexao) . "<br>";
    }
}
echo "--- Correção concluída ---\n";
?>