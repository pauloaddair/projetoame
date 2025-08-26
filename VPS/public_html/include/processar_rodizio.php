<?php
// processar_rodizio.php

function processar_rodizio_por_data($data_evento, $conexao) {
    // Garante que a data está no formato correto para o banco
    $data_formatada = date('Y-m-d', strtotime($data_evento));

    // 1. Pega o valor máximo de rodízio ANTES de qualquer alteração
    $query_max = "SELECT MAX(rodizio) as max_rodizio FROM candidatos";
    $resp_max = mysqli_query($conexao, $query_max);
    $max_data = mysqli_fetch_assoc($resp_max);
    $max_rodizio_geral = $max_data['max_rodizio'];

    // 2. Busca todos os atendentes de eventos de 'atendimento' na data especificada,
    // que ainda não tiveram o rodízio processado, ordenados pelo rodízio atual.
    $query_atendentes = "SELECT DISTINCT
            d.candidato_id, c.rodizio
        FROM disponibilidade d
        JOIN candidatos c ON d.candidato_id = c.candidato_id
        JOIN horarios h ON d.atividade_id = h.horario_id
        JOIN eventos_marcados em ON h.evento_id = em.id
        WHERE 
            d.escalado = 1 AND
            em.tipo_evento = 'atendimento' AND
            em.status_evento = 'realizado' AND
            em.rodizio_processado = 0 AND
            DATE(h.data_inicio) = '" . $data_formatada . "'
        ORDER BY c.rodizio ASC";

    $resp_atendentes = mysqli_query($conexao, $query_atendentes);
    
    $atendentes_para_processar = [];
    while ($row = mysqli_fetch_assoc($resp_atendentes)) {
        $atendentes_para_processar[] = $row['candidato_id'];
    }

    if (empty($atendentes_para_processar)) {
        return "Nenhum atendente de eventos de 'atendimento' realizados em " . date('d/m/Y', strtotime($data_formatada)) . " para processar.";
    }

    // 3. Inicia o processamento em lote
    $rank = 1;
    $erros = [];
    foreach ($atendentes_para_processar as $candidato_id) {
        $novo_rodizio = $max_rodizio_geral + $rank;
        $query_update = "UPDATE candidatos SET rodizio = " . $novo_rodizio . " WHERE candidato_id = " . $candidato_id;
        
        if (!mysqli_query($conexao, $query_update)) {
            $erros[] = "Falha ao atualizar rodízio para o candidato ID: " . $candidato_id;
        }
        $rank++;
    }

    // 4. Marca os eventos como processados para não serem incluídos novamente
    $query_marcar_eventos = "UPDATE eventos_marcados SET rodizio_processado = 1 
                             WHERE tipo_evento = 'atendimento' AND 
                                   status_evento = 'realizado' AND 
                                   rodizio_processado = 0 AND 
                                   DATE(data_inicio) = '" . $data_formatada . "'";
    mysqli_query($conexao, $query_marcar_eventos);

    if (empty($erros)) {
        return (count($atendentes_para_processar)) . " atendente(s) tiveram seu rodízio atualizado com sucesso para a data " . date('d/m/Y', strtotime($data_formatada)) . ".";
    } else {
        return "Processamento concluído com erros: <br>" . implode("<br>", $erros);
    }
}
?>