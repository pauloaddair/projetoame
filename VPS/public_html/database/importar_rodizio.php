<?php
require_once __DIR__ . '/conexao.php';
global $conexao;

// CAMINHO DO ARQUIVO - Alterar conforme necessário
$arquivo = 'F:/01_Projetos/Ativos/PROJETO_AME/apoio/Rodizio em JANEIRO 2025.csv';

if (!file_exists($arquivo)) {
    die("Arquivo não encontrado: $arquivo");
}

$handle = fopen($arquivo, 'r');

// 1. Encontrar a linha de cabeçalho
$header = null;
while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
    if (in_array("Atendente", $data)) {
        $header = $data;
        break;
    }
}

if (!$header) {
    die("Cabeçalho 'Atendente' não encontrado no CSV.");
}

$idx_nome = array_search("Atendente", $header);
echo "Cabeçalho encontrado. Iniciando processamento...\n";

// 2. Processar candidatos
while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
    if (empty($data[$idx_nome])) continue;

    $nome_candidato = mysqli_real_escape_string($conexao, trim($data[$idx_nome]));
    
    // Busca candidato (pelo nome)
    $q_cand = "SELECT candidato_id FROM candidatos WHERE nome LIKE '%$nome_candidato%' LIMIT 1";
    $result_cand = mysqli_query($conexao, $q_cand);
    $c = mysqli_fetch_assoc($result_cand);
    
    if (!$c) {
        echo "Candidato não encontrado na base: $nome_candidato\n";
        continue;
    }
    $candidato_id = $c['candidato_id'];

    // 3. Cruzar colunas de eventos
    foreach ($data as $index => $valor) {
        if ($index <= $idx_nome) continue; // Pula colunas fixas (até o nome)
        
        $valor_limpo = trim(strtolower($valor));
        if ($valor_limpo == 'x' || $valor_limpo == 'pg') {
            $nome_evento = mysqli_real_escape_string($conexao, trim($header[$index]));
            
            // Busca evento (pelo nome da coluna/data)
            // Nota: Se o cabeçalho for uma data, ajustar aqui
            $q_ev = "SELECT id FROM eventos_marcados WHERE name LIKE '%$nome_evento%' LIMIT 1";
            $result_ev = mysqli_query($conexao, $q_ev);
            $e = mysqli_fetch_assoc($result_ev);
            
            if ($e) {
                $evento_id = $e['id'];
                // Insere disponibilidade
                $sql_insert = "INSERT IGNORE INTO disponibilidade (candidato_id, atividade_id) VALUES ($candidato_id, $evento_id)";
                mysqli_query($conexao, $sql_insert);
                echo "Vínculo registrado: $nome_candidato -> $nome_evento\n";
            } else {
                echo "Evento não encontrado na base: $nome_evento\n";
            }
        }
    }
}
fclose($handle);
echo "Processamento finalizado.\n";
?>