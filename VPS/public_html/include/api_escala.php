<?php
header('Content-Type: application/json');
include_once(dirname(__DIR__) . '/database/conexao.php');
include_once(__DIR__ . '/funcoes.php');

if (function_exists('garantir_tabelas_suporte_escala')) {
    garantir_tabelas_suporte_escala($conexao);
}

function format_data_nascimento($date_str) {
    if (empty($date_str)) return '';
    $clean = trim($date_str);
    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $clean, $m)) {
        return "{$m[3]}/{$m[2]}/{$m[1]}";
    }
    if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})/', $clean, $m)) {
        return "{$m[1]}/{$m[2]}/{$m[3]}";
    }
    return $clean;
}

try {
    $raw_input = file_get_contents('php://input');
    $data = json_decode($raw_input, true);
    if (!is_array($data) && !empty($_POST)) {
        $data = $_POST;
    }

    if (isset($data['action']) && $data['action'] === 'get_escala_details') {
        $evento_id = intval($data['evento_id']);
        
        // Fetch event details (image, uuid, name)
        $event_image_url = null;
        $event_uuid = null;
        $event_nome = '';
        $event_tipo = 'Trabalho';
        $event_tipo_evento = 'atendimento';
        $event_linha_corte = 75;
        $event_valor_diaria_padrao = 200.00;
        $query_event_info = "SELECT e.nome, e.uuid, e.tipo, e.tipo_evento, e.linha_corte_presenca, e.valor_diaria_padrao, i.url FROM eventos_marcados e LEFT JOIN imagens i ON e.imagem_id = i.imagem_id WHERE e.id = {$evento_id}";
        $result_event_info = mysqli_query($conexao, $query_event_info);
        if ($result_event_info && $row_info = mysqli_fetch_assoc($result_event_info)) {
            $event_image_url = $row_info['url'];
            $event_uuid = $row_info['uuid'];
            $event_nome = $row_info['nome'];
            $event_tipo = !empty($row_info['tipo']) ? $row_info['tipo'] : 'Trabalho';
            $event_tipo_evento = !empty($row_info['tipo_evento']) ? $row_info['tipo_evento'] : 'atendimento';
            $event_linha_corte = !empty($row_info['linha_corte_presenca']) ? intval($row_info['linha_corte_presenca']) : 75;
            $event_valor_diaria_padrao = !empty($row_info['valor_diaria_padrao']) ? floatval($row_info['valor_diaria_padrao']) : 200.00;
        }
        
        // 1. Fetch all horarios for the event
        $query_horarios = "SELECT horario_id, data_inicio, data_final, vagas, valor_diaria FROM horarios WHERE evento_id = {$evento_id} ORDER BY data_inicio ASC";
        $result_horarios = mysqli_query($conexao, $query_horarios);
        if (!$result_horarios) {
            echo json_encode(['success' => false, 'message' => 'Erro na consulta de horários: ' . mysqli_error($conexao)]);
            exit;
        }
        $horarios_data = [];
        $horario_ids_in_event = []; // To store all horario_ids for the event
        while ($horario = mysqli_fetch_assoc($result_horarios)) {
            $horario['valor_diaria'] = !empty($horario['valor_diaria']) ? floatval($horario['valor_diaria']) : $event_valor_diaria_padrao;
            $horarios_data[] = $horario;
            $horario_ids_in_event[] = $horario['horario_id'];
        }

        // Fetch presenca por horario e valor_pago para este evento
        $presencas_map = []; // [candidato_id => [horario_id => presente (1/0)]]
        $presencas_geral = []; // [candidato_id => 1 se teve presenca em algum horario]
        $valor_pago_map = []; // [candidato_id => total_pago]
        try {
            $q_pres = mysqli_query($conexao, "SELECT candidato_id, horario_id, presente, valor_pago FROM presenca WHERE evento_id = {$evento_id}");
            if ($q_pres) {
                while ($p = mysqli_fetch_assoc($q_pres)) {
                    $c_id = (int)$p['candidato_id'];
                    $h_id = (int)($p['horario_id'] ?? 0);
                    $pres_val = intval($p['presente']);
                    if (!isset($presencas_map[$c_id])) {
                        $presencas_map[$c_id] = [];
                    }
                    $presencas_map[$c_id][$h_id] = $pres_val;
                    if ($pres_val === 1) {
                        $presencas_geral[$c_id] = 1;
                        $valor_pago_map[$c_id] = ($valor_pago_map[$c_id] ?? 0) + floatval($p['valor_pago']);
                    }
                }
            }
        } catch (Throwable $e) {
            $presencas_map = [];
        }

        $avaliados_map = [];
        try {
            $q_aval = mysqli_query($conexao, "SELECT DISTINCT candidato_id FROM avaliacoes WHERE evento_id = {$evento_id}");
            if ($q_aval) {
                while ($av = mysqli_fetch_assoc($q_aval)) {
                    $avaliados_map[$av['candidato_id']] = 1;
                }
            }
        } catch (Throwable $e) {
            $avaliados_map = [];
        }

        // 2. Fetch all candidates (active and training) with rodizio >= 1
        $query_candidatos = "SELECT c.candidato_id, c.nome, c.rodizio, i.url, c.ativo, c.PIX, c.Telefone, c.CPF
                             FROM candidatos c
                             LEFT JOIN imagens i ON c.imagem_id = i.imagem_id
                             WHERE c.ativo != -1 AND c.rodizio >= 1
                             ORDER BY c.ativo DESC, c.rodizio ASC"; // Order by rodizio for consistent display
        $result_candidatos = mysqli_query($conexao, $query_candidatos);
        if (!$result_candidatos) {
            echo json_encode(['success' => false, 'message' => 'Erro na consulta de candidatos: ' . mysqli_error($conexao)]);
            exit;
        }
        $candidatos_data = [];
        $candidatos_map = []; // Map for quick lookup by candidato_id
        while ($candidato = mysqli_fetch_assoc($result_candidatos)) {
            $c_id = $candidato['candidato_id'];
            $data_string = "evento_id={$evento_id}&candidato_id={$c_id}";
            $sig = hash_hmac('sha256', $data_string, 'ProjetoAME_ChaveSecreta_#2025@');
            $candidato['link_avaliacao'] = "avaliar_atendente?evento_id={$evento_id}&candidato_id={$c_id}&sig={$sig}";
            $candidato['presencas_por_horario'] = $presencas_map[$c_id] ?? [];
            $candidato['presenca_confirmada'] = $presencas_geral[$c_id] ?? 0;
            $candidato['ja_avaliado'] = $avaliados_map[$c_id] ?? 0;
            $candidato['total_valor_diarias'] = $valor_pago_map[$c_id] ?? 0.00;
            $candidato['horarios_status'] = []; // Initialize status for each horario
            $candidatos_data[] = $candidato;
            $candidatos_map[$c_id] = &$candidatos_data[count($candidatos_data) - 1]; // Reference to the last added candidate
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

            // Computa frequência e métricas por aluno/atendente
            $tot_escalado = 0;
            $tot_presente = 0;
            foreach ($candidato['horarios_status'] as $hId => $st) {
                if ($st['is_escalado'] == 1) {
                    $tot_escalado++;
                    if (!empty($candidato['presencas_por_horario'][$hId]) && $candidato['presencas_por_horario'][$hId] == 1) {
                        $tot_presente++;
                    }
                }
            }
            // Se o evento tiver presenças confirmadas sem horario_id específico (legado)
            if ($tot_presente === 0 && !empty($candidato['presencas_por_horario'][0])) {
                $tot_presente = $candidato['presencas_por_horario'][0] == 1 ? $tot_escalado : 0;
            }
            $candidato['total_escalas'] = $tot_escalado;
            $candidato['total_presencas'] = $tot_presente;
            $candidato['frequencia_pct'] = ($tot_escalado > 0) ? round(($tot_presente / $tot_escalado) * 100) : 0;
            $candidato['apto_certificado'] = ($candidato['frequencia_pct'] >= $event_linha_corte);
        }
        unset($candidato); // Break the reference

        echo json_encode([
            'success' => true,
            'horarios' => $horarios_data,
            'candidatos' => $candidatos_data,
            'event_image_url' => $event_image_url,
            'event_uuid' => $event_uuid,
            'event_nome' => $event_nome,
            'event_tipo' => $event_tipo,
            'event_tipo_evento' => $event_tipo_evento,
            'event_linha_corte' => $event_linha_corte,
            'event_valor_diaria_padrao' => $event_valor_diaria_padrao,
            'link_avaliacao_contratante' => "https://projetoame.org/avaliacao/{$event_uuid}"
        ]);
        exit;
    }

if (isset($data['action']) && $data['action'] === 'get_credenciamento') {
    $evento_id = intval($data['evento_id']);
    
    // Fetch event name
    $query_event_name = "SELECT nome FROM eventos_marcados WHERE id = {$evento_id}";
    $res_event = mysqli_query($conexao, $query_event_name);
    $evento_nome = ($res_event && $row = mysqli_fetch_assoc($res_event)) ? $row['nome'] : "Evento #{$evento_id}";

    // 1. Coordinators (Always included)
    $query_coordenadores = "SELECT candidato_id, nome, Nascimento, RG, CPF, camisa, 'Coordenador(a)' as papel 
                            FROM candidatos 
                            WHERE candidato_id IN (199, 200) 
                               OR nome LIKE '%Paulo Addair%' 
                               OR nome LIKE '%Regina Justo%' 
                            ORDER BY candidato_id DESC";
    $res_coord = mysqli_query($conexao, $query_coordenadores);
    $coordenadores = [];
    $coord_ids = [];
    if ($res_coord) {
        while ($c = mysqli_fetch_assoc($res_coord)) {
            $c['Nascimento_fmt'] = format_data_nascimento($c['Nascimento']);
            $coordenadores[] = $c;
            $coord_ids[] = (int)$c['candidato_id'];
        }
    }

    // 2. Scaled candidates for this event (excluding coordinators if already listed)
    $exclude_sql = !empty($coord_ids) ? " AND c.candidato_id NOT IN (" . implode(',', $coord_ids) . ")" : "";
    $query_escalados = "SELECT DISTINCT c.candidato_id, c.nome, c.Nascimento, c.RG, c.CPF, c.camisa, 'Atendente' as papel 
                        FROM candidatos c 
                        JOIN disponibilidade d ON c.candidato_id = d.candidato_id 
                        JOIN horarios h ON d.atividade_id = h.horario_id 
                        WHERE h.evento_id = {$evento_id} AND d.escalado = 1 {$exclude_sql} 
                        ORDER BY c.nome ASC";
    $res_escalados = mysqli_query($conexao, $query_escalados);
    $escalados = [];
    if ($res_escalados) {
        while ($e = mysqli_fetch_assoc($res_escalados)) {
            $e['Nascimento_fmt'] = format_data_nascimento($e['Nascimento']);
            $escalados[] = $e;
        }
    }

    $todos_credenciados = array_merge($coordenadores, $escalados);

    echo json_encode([
        'success' => true,
        'evento_nome' => $evento_nome,
        'total_coordenadores' => count($coordenadores),
        'total_escalados' => count($escalados),
        'total_geral' => count($todos_credenciados),
        'credenciados' => $todos_credenciados,
        'equipe' => $todos_credenciados
    ]);
    exit;
}

if (isset($data['action']) && $data['action'] === 'suggest_escala') {
    $evento_id = intval($data['evento_id']);

    // 1. Fetch all horarios for the event
    $query_horarios = "SELECT horario_id, vagas FROM horarios WHERE evento_id = {$evento_id} ORDER BY data_inicio ASC";
    $result_horarios = mysqli_query($conexao, $query_horarios);
    if (!$result_horarios) {
        echo json_encode(['success' => false, 'message' => 'Erro na consulta de horários: ' . mysqli_error($conexao)]);
        exit;
    }

    $sugestao = []; // [horario_id => [candidato_id1, candidato_id2, ...]]
    $candidatos_ja_sugeridos = []; // Para evitar que a mesma pessoa seja sugerida para dois turnos no mesmo evento

    while ($horario = mysqli_fetch_assoc($result_horarios)) {
        $horario_id = $horario['horario_id'];
        $vagas = intval($horario['vagas']);

        // 2. Fetch candidates available for this horario, ordered by rodizio
        // We exclude candidates already suggested for other shifts in the same event
        $exclude_sql = "";
        if (!empty($candidatos_ja_sugeridos)) {
            $exclude_sql = " AND d.candidato_id NOT IN (" . implode(',', $candidatos_ja_sugeridos) . ")";
        }

        $query_disponiveis = "SELECT d.candidato_id 
                              FROM disponibilidade d 
                              JOIN candidatos c ON d.candidato_id = c.candidato_id
                              WHERE d.atividade_id = {$horario_id} 
                              AND c.ativo = 1 
                              AND c.rodizio >= 1
                              {$exclude_sql}
                              ORDER BY c.rodizio ASC 
                              LIMIT {$vagas}";
        
        $result_disponiveis = mysqli_query($conexao, $query_disponiveis);
        $sugeridos_neste_horario = [];
        while ($row = mysqli_fetch_assoc($result_disponiveis)) {
            $sugeridos_neste_horario[] = $row['candidato_id'];
            $candidatos_ja_sugeridos[] = $row['candidato_id'];
        }
        $sugestao[$horario_id] = $sugeridos_neste_horario;
    }

    echo json_encode([
        'success' => true,
        'sugestao' => $sugestao
    ]);
    exit;
}

if (isset($data['action']) && ($data['action'] === 'save_escala' || $data['action'] === 'salvar_escala')) {
    $evento_id = intval($data['evento_id']);
    $escalados_por_horario = $data['escalados'] ?? [];
    $query_get_horarios = "SELECT horario_id FROM horarios WHERE evento_id = {$evento_id}";
    $result_horarios = mysqli_query($conexao, $query_get_horarios);
    $horario_ids = [];
    while($row = mysqli_fetch_assoc($result_horarios)) {
        $horario_ids[] = intval($row['horario_id']);
    }
    if (!empty($horario_ids)) {
        $ids_string = implode(',', $horario_ids);
        $query_limpar = "UPDATE disponibilidade SET escalado = 0 WHERE atividade_id IN ({$ids_string})";
        mysqli_query($conexao, $query_limpar);
    }
    foreach ($escalados_por_horario as $horario_id => $candidatos_ids) {
        if (!empty($candidatos_ids)) {
            $h_id = intval($horario_id);
            foreach ($candidatos_ids as $c_raw) {
                $c_id = intval($c_raw);
                if ($c_id <= 0) continue;
                // Atualiza se já existir registro na tabela, ou insere se foi escalado manualmente sem ficha prévia
                $check = mysqli_query($conexao, "SELECT id FROM disponibilidade WHERE atividade_id = {$h_id} AND candidato_id = {$c_id} LIMIT 1");
                if ($check && mysqli_num_rows($check) > 0) {
                    mysqli_query($conexao, "UPDATE disponibilidade SET escalado = 1 WHERE atividade_id = {$h_id} AND candidato_id = {$c_id}");
                } else {
                    mysqli_query($conexao, "INSERT INTO disponibilidade (candidato_id, atividade_id, escalado) VALUES ({$c_id}, {$h_id}, 1)");
                }
            }
        }
    }
    // Automatically close scale when saved
    mysqli_query($conexao, "UPDATE eventos_marcados SET escala_fechada = 1 WHERE id = {$evento_id}");

    echo json_encode(['success' => true, 'message' => 'Escala salva com sucesso!']);
    exit;
}

if (isset($data['action']) && $data['action'] === 'toggle_escala_status') {
    $evento_id = intval($data['evento_id']);
    $status = intval($data['escala_fechada']);
    mysqli_query($conexao, "UPDATE eventos_marcados SET escala_fechada = {$status} WHERE id = {$evento_id}");
    echo json_encode(['success' => true, 'escala_fechada' => $status]);
    exit;
}

if (isset($data['action']) && $data['action'] === 'confirmar_presencas') {
    $evento_id = intval($data['evento_id']);
    $horario_id = isset($data['horario_id']) ? intval($data['horario_id']) : 0;
    $presencas = $data['presencas'] ?? []; // [candidato_id => 1 (compareceu) ou 0 (ausente/justificado)]
    $realizar_rodizio = isset($data['realizar_rodizio']) ? (intval($data['realizar_rodizio']) === 1) : true;
    
    $q_ev = mysqli_query($conexao, "SELECT nome, tipo, tipo_evento, linha_corte_presenca, valor_diaria_padrao FROM eventos_marcados WHERE id = {$evento_id}");
    $ev_info = ($q_ev && $r_ev = mysqli_fetch_assoc($q_ev)) ? $r_ev : [
        'nome' => "Evento #{$evento_id}", 
        'tipo' => 'Trabalho', 
        'tipo_evento' => 'atendimento',
        'linha_corte_presenca' => 75,
        'valor_diaria_padrao' => 200.00
    ];
    $ev_nome = $ev_info['nome'];
    $valor_diaria_padrao = floatval($ev_info['valor_diaria_padrao'] ?? 200.00);

    // Identifica os horários afetados
    $horarios_alvo = [];
    if ($horario_id > 0) {
        $q_h = mysqli_query($conexao, "SELECT horario_id, valor_diaria, data_inicio FROM horarios WHERE horario_id = {$horario_id}");
        if ($q_h && $r_h = mysqli_fetch_assoc($q_h)) {
            $horarios_alvo[] = $r_h;
        }
    } else {
        $q_h = mysqli_query($conexao, "SELECT horario_id, valor_diaria, data_inicio FROM horarios WHERE evento_id = {$evento_id} ORDER BY data_inicio ASC");
        while ($r_h = mysqli_fetch_assoc($q_h)) {
            $horarios_alvo[] = $r_h;
        }
    }

    $processados_sucesso = 0;
    $preservados_ausencia = 0;

    foreach ($presencas as $cand_id => $compareceu) {
        $candidato_id = intval($cand_id);
        $compareceu = intval($compareceu);

        $q_cand = mysqli_query($conexao, "SELECT rodizio, nome, Email, Telefone, PIX FROM candidatos WHERE candidato_id = {$candidato_id}");
        if ($q_cand && $cand_data = mysqli_fetch_assoc($q_cand)) {
            $rodizio_atual = intval($cand_data['rodizio']);
            $cand_nome = mysqli_real_escape_string($conexao, $cand_data['nome']);
            $cand_email = mysqli_real_escape_string($conexao, $cand_data['Email'] ?? '');
            $cand_tel = mysqli_real_escape_string($conexao, $cand_data['Telefone'] ?? '');

            foreach ($horarios_alvo as $h_info) {
                $h_id = intval($h_info['horario_id']);
                $v_diaria = !empty($h_info['valor_diaria']) ? floatval($h_info['valor_diaria']) : $valor_diaria_padrao;
                $valor_pago = ($ev_info['tipo'] === 'Trabalho' && $compareceu === 1) ? $v_diaria : 0.00;

                // Grava registro na tabela oficial de presencas por horário
                mysqli_query($conexao, "INSERT INTO presenca (evento_id, horario_id, candidato_id, presente, valor_pago, data_confirmacao, confirmadopor, nome, responsavel, email, telefone, comentario) 
                                        VALUES ({$evento_id}, {$h_id}, {$candidato_id}, {$compareceu}, {$valor_pago}, NOW(), 'coordenacao', '{$cand_nome}', 0, '{$cand_email}', '{$cand_tel}', '') 
                                        ON DUPLICATE KEY UPDATE presente = {$compareceu}, valor_pago = {$valor_pago}, data_confirmacao = NOW()");

                // Garante que o candidato fique registrado como escalado neste horário
                $check_disp = mysqli_query($conexao, "SELECT id FROM disponibilidade WHERE atividade_id = {$h_id} AND candidato_id = {$candidato_id} LIMIT 1");
                if ($check_disp && mysqli_num_rows($check_disp) > 0) {
                    mysqli_query($conexao, "UPDATE disponibilidade SET escalado = 1 WHERE atividade_id = {$h_id} AND candidato_id = {$candidato_id}");
                } else {
                    mysqli_query($conexao, "INSERT INTO disponibilidade (candidato_id, atividade_id, disponivel, escalado) VALUES ({$candidato_id}, {$h_id}, 1, 1)");
                }
            }

            if (!$realizar_rodizio) {
                // NÃO REALIZAR RODÍZIO (Curso, Treinamento ou opção desmarcada)
                $tipo_mov = ($compareceu === 1) ? 'curso_presenca' : 'curso_ausencia';
                $status_desc = ($compareceu === 1) ? 'Presença confirmada' : 'Ausência registrada';
                $desc_hor = ($horario_id > 0) ? " (Aula/Horário ID #{$horario_id})" : "";
                $obs = mysqli_real_escape_string($conexao, "{$status_desc} no curso/evento {$ev_nome}{$desc_hor} - Rodízio mantido em #{$rodizio_atual} (sem movimentação de fila)");
                mysqli_query($conexao, "INSERT INTO historico_rodizio (candidato_id, rodizio_anterior, rodizio_novo, evento_id, tipo_movimento, observacao) VALUES ({$candidato_id}, {$rodizio_atual}, {$rodizio_atual}, {$evento_id}, '{$tipo_mov}', '{$obs}')");
                $processados_sucesso++;
            } else {
                if ($compareceu === 1) {
                    // Atendente COMPARECEU em atividade com rodízio: move para o final da fila (MAX + 1)
                    $res_max = mysqli_query($conexao, "SELECT MAX(rodizio) as max_rodizio FROM candidatos");
                    $max_rodizio = intval(mysqli_fetch_assoc($res_max)['max_rodizio']) + 1;

                    mysqli_query($conexao, "UPDATE candidatos SET rodizio = {$max_rodizio} WHERE candidato_id = {$candidato_id}");

                    $desc_hor = ($horario_id > 0) ? " (Turno ID #{$horario_id})" : "";
                    $obs = mysqli_real_escape_string($conexao, "Presença confirmada no evento {$ev_nome}{$desc_hor}");
                    mysqli_query($conexao, "INSERT INTO historico_rodizio (candidato_id, rodizio_anterior, rodizio_novo, evento_id, tipo_movimento, observacao) VALUES ({$candidato_id}, {$rodizio_atual}, {$max_rodizio}, {$evento_id}, 'pos_evento', '{$obs}')");
                    $processados_sucesso++;
                } else {
                    // Atendente FALTOU / AUSÊNCIA JUSTIFICADA: rodízio mantido!
                    $desc_hor = ($horario_id > 0) ? " (Turno ID #{$horario_id})" : "";
                    $obs = mysqli_real_escape_string($conexao, "Ausência justificada no evento {$ev_nome}{$desc_hor} - rodízio preservado em #{$rodizio_atual}");
                    mysqli_query($conexao, "INSERT INTO historico_rodizio (candidato_id, rodizio_anterior, rodizio_novo, evento_id, tipo_movimento, observacao) VALUES ({$candidato_id}, {$rodizio_atual}, {$rodizio_atual}, {$evento_id}, 'ausencia_justificada', '{$obs}')");
                    $preservados_ausencia++;
                }
            }
        }
    }

    mysqli_query($conexao, "UPDATE eventos_marcados SET rodizio_processado = 1, status_evento = 'realizado' WHERE id = {$evento_id}");

    $msg = $realizar_rodizio 
        ? "Confirmação de presenças concluída com sucesso! {$processados_sucesso} participante(s) com presenças/diárias computadas e rodízio atualizado."
        : "Presenças registradas com sucesso! Como a opção de rodízio não foi aplicada (curso/capacitação), a posição de rodízio de todos os {$processados_sucesso} participantes foi preservada intacta.";

    echo json_encode([
        'success' => true, 
        'message' => $msg,
        'realizar_rodizio' => $realizar_rodizio,
        'horario_id' => $horario_id
    ]);
    exit;
}

if (isset($data['action']) && $data['action'] === 'toggle_presenca_individual') {
    $evento_id = intval($data['evento_id']);
    $candidato_id = intval($data['candidato_id']);
    $presente = intval($data['presente']);

    mysqli_query($conexao, "INSERT INTO presenca (evento_id, candidato_id, presente, data_confirmacao, confirmadopor) 
                            VALUES ({$evento_id}, {$candidato_id}, {$presente}, NOW(), 'coordenacao_checkin') 
                            ON DUPLICATE KEY UPDATE presente = {$presente}, data_confirmacao = NOW()");

    $data_string = "evento_id={$evento_id}&candidato_id={$candidato_id}";
    $sig = hash_hmac('sha256', $data_string, 'ProjetoAME_ChaveSecreta_#2025@');
    $link_avaliacao = "avaliar_atendente?evento_id={$evento_id}&candidato_id={$candidato_id}&sig={$sig}";

    echo json_encode([
        'success' => true,
        'presente' => $presente,
        'link_avaliacao' => $link_avaliacao,
        'message' => $presente ? 'Presença confirmada! Ficha de avaliação do atendente liberada.' : 'Presença desmarcada.'
    ]);
    exit;
}

if (isset($data['action']) && $data['action'] === 'get_historico_rodizio') {
    $candidato_id = isset($data['candidato_id']) ? intval($data['candidato_id']) : 0;
    $where = $candidato_id > 0 ? "WHERE h.candidato_id = {$candidato_id}" : "";
    
    $query_hist = "SELECT h.*, c.nome as candidato_nome, e.nome as evento_nome 
                   FROM historico_rodizio h 
                   LEFT JOIN candidatos c ON h.candidato_id = c.candidato_id 
                   LEFT JOIN eventos_marcados e ON h.evento_id = e.id 
                   {$where} 
                   ORDER BY h.id DESC LIMIT 100";
    $res_hist = mysqli_query($conexao, $query_hist);
    $logs = [];
    if ($res_hist) {
        while ($l = mysqli_fetch_assoc($res_hist)) {
            $logs[] = $l;
        }
    }
    echo json_encode(['success' => true, 'logs' => $logs]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Ação inválida']);
exit;
} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno na API de escala: ' . $e->getMessage()
    ]);
    exit;
}
?>
