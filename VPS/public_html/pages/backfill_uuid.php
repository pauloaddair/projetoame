<?php
// backfill_uuid.php - RODE ESTE SCRIPT APENAS UMA VEZ

include_once('./include/conexao.php');

// Função para gerar um UUID v4 (padrão para IDs aleatórias)
function gen_uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
        mt_rand( 0, 0xffff ),
        mt_rand( 0, 0x0fff ) | 0x4000,
        mt_rand( 0, 0x3fff ) | 0x8000,
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}

$query = "SELECT id FROM eventos_marcados WHERE uuid IS NULL";
$result = mysqli_query($conexao, $query);

if (!$result) {
    die("Erro ao buscar eventos: " . mysqli_error($conexao));
}

$count = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['id'];
    $uuid = gen_uuid();
    
    $update_query = "UPDATE eventos_marcados SET uuid = '{$uuid}' WHERE id = {$id}";
    
    if (mysqli_query($conexao, $update_query)) {
        echo "Evento ID {$id} atualizado com UUID: {$uuid}<br>";
        $count++;
    } else {
        echo "<strong style='color:red;'>Falha ao atualizar evento ID {$id}: " . mysqli_error($conexao) . "</strong><br>";
    }
}

echo "<hr><h2>Processo concluído. {$count} evento(s) foram atualizados.</h2>";
echo "<p style='color:red; font-weight:bold;'>Por favor, apague este arquivo (backfill_uuid.php) do servidor agora.</p>";

?>