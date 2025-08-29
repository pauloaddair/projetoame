<?php
date_default_timezone_set('America/Sao_Paulo');
include_once('./conexao.php'); // Assuming conexao.php is in the same directory
include_once('./funcoes.php'); // Assuming funcoes.php is in the same directory

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['action']) && $data['action'] === 'get_escala_details') {
    $evento_id = intval($data['evento_id']);

    // Fetch event image URL
    $event_image_url = '';
    $query_event_image = "SELECT i.url FROM eventos_marcados em JOIN imagens i ON em.imagem_id = i.imagem_id WHERE em.id = {$evento_id}";
    $result_event_image = mysqli_query($conexao, $query_event_image);
    if ($result_event_image && mysqli_num_rows($result_event_image) > 0) {
        $row_image = mysqli_fetch_assoc($result_event_image);
        $event_image_url = $row_image['url'];
    }
    
    // 1. Fetch all horarios for the event
    $query_horarios = "SELECT horario_id, data_inicio, data_final, vagas FROM horarios WHERE evento_id = {$evento_id} ORDER BY data_inicio ASC";
    $result_horarios = mysqli_query($conexao, $query_horarios);
    if (!$result_horarios) {
        echo json_encode(['success' => false, 'message' => 'Erro na consulta de horários: ' . mysqli_error($conexao)]);
        exit;
    }
    $horarios_data = [];
    $horario_ids_in_event = []; // To store all horario_ids for the event
    while ($horario = mysqli_fetch_assoc($result_horarios)) {
        $horarios_data[] = $horario;
        $horario_ids_in_event[] = $horario['horario_id'];
    }

    // 2. Fetch all candidates (active and inactive)
    $query_candidatos = "SELECT c.candidato_id, c.nome, c.rodizio, i.url, c.ativo
                         FROM candidatos c
                         LEFT JOIN imagens i ON c.imagem_id = i.imagem_id
                         WHERE c.ativo != -1 AND c.rodizio > 0
                         ORDER BY c.ativo DESC, c.rodizio ASC"; // Order by rodizio for consistent display
    $result_candidatos = mysqli_query($conexao, $query_candidatos);
    if (!$result_candidatos) {
        echo json_encode(['success' => false, 'message' => 'Erro na consulta de candidatos: ' . mysqli_error($conexao)]);
        exit;
    }
    $candidatos_data = [];
    $candidatos_map = []; // Map for quick lookup by candidato_id
    while ($candidato = mysqli_fetch_assoc($result_candidatos)) {
        $candidato['horarios_status'] = []; // Initialize status for each horario
        $candidatos_data[] = $candidato;
        $candidatos_map[$candidato['candidato_id']] = &$candidatos_data[count($candidatos_data) - 1]; // Reference to the last added candidate
    }

    // 3. Fetch all disponibilidade records for the event's horarios
    if (!empty($horario_ids_in_event)) {
        $horario_ids_string = implode(',', $horario_ids_in_event);
        $query_disponibilidade = "SELECT candidato_id, atividade_id, escalado
                                  FROM disponibilidade
                                  WHERE atividade_id IN ({$horario_ids_string})";
        $result_disponibilidade = mysqli_query($conexao, $query_disponibilidade);
        if (!$result_disponibilidade) {
            echo json_encode(['success' => false, 'message' => 'Erro na consulta de disponibilidade: ' . mysqli_error($conexao)]);
            exit;
        }
        while ($disp = mysqli_fetch_assoc($result_disponibilidade)) {
            $candidato_id = $disp['candidato_id'];
            $horario_id = $disp['atividade_id'];
            if (isset($candidatos_map[$candidato_id])) {
                $candidatos_map[$candidato_id]['horarios_status'][$horario_id] = [
                    'is_disponivel' => 1, // If a record exists, they are available
                    'is_escalado' => $disp['escalado']
                ];
            }
        }
    }

    // For candidates not found in disponibilidade for a specific horario, set default status
    foreach ($candidatos_data as &$candidato) {
        foreach ($horarios_data as $horario) {
            $horario_id = $horario['horario_id'];
            if (!isset($candidato['horarios_status'][$horario_id])) {
                $candidato['horarios_status'][$horario_id] = [
                    'is_disponivel' => 0,
                    'is_escalado' => 0
                ];
            }
        }
    }
    unset($candidato); // Break the reference

    echo json_encode([
        'success' => true,
        'horarios' => $horarios_data,
        'candidatos' => $candidatos_data,
        'event_image_url' => $event_image_url // Add image URL to response
    ]);
    exit;
}

if (isset($data['action']) && $data['action'] === 'save_escala') {
    $evento_id = intval($data['evento_id']);
    $escalados_por_horario = $data['escalados'] ?? [];
    $query_get_horarios = "SELECT horario_id FROM horarios WHERE evento_id = {$evento_id}";
    $result_horarios = mysqli_query($conexao, $query_get_horarios);
    $horario_ids = [];
    while($row = mysqli_fetch_assoc($result_horarios)) {
        $horario_ids[] = $row['horario_id'];
    }
    if (!empty($horario_ids)) {
        $ids_string = implode(',', $horario_ids);
        $query_limpar = "UPDATE disponibilidade SET escalado = 0 WHERE atividade_id IN ({$ids_string})";
        mysqli_query($conexao, $query_limpar);
    }
    foreach ($escalados_por_horario as $horario_id => $candidatos_ids) {
        if (!empty($candidatos_ids)) {
            $candidatos_ids_string = implode(',', array_map('intval', $candidatos_ids));
            $query_escalar = "UPDATE disponibilidade SET escalado = 1 WHERE atividade_id = ".intval($horario_id)." AND candidato_id IN ({$candidatos_ids_string})";
            mysqli_query($conexao, $query_escalar);
            $query_max_rodizio = "SELECT MAX(rodizio) as max_rodizio FROM candidatos";
            $result_max = mysqli_query($conexao, $query_max_rodizio);
            $max_rodizio = mysqli_fetch_assoc($result_max)['max_rodizio'];
            foreach ($candidatos_ids as $candidato_id) {
                $max_rodizio++;
                $query_update_rodizio = "UPDATE candidatos SET rodizio = {$max_rodizio} WHERE candidato_id = ".intval($candidato_id);
                mysqli_query($conexao, $query_update_rodizio);
            }
        }
    }
    echo json_encode(['success' => true, 'message' => 'Escala salva com sucesso!']);
    exit;
}
