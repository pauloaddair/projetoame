<?php
date_default_timezone_set('America/Sao_Paulo');

// --- LÓGICA DE BACKEND (Responde a requisições AJAX diretas) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['action']) && $data['action'] === 'get_escala_details') {
        $evento_id = intval($data['evento_id']);
        
        $query_horarios = "SELECT horario_id, data_inicio, data_final, vagas FROM horarios WHERE evento_id = {$evento_id} ORDER BY data_inicio ASC";
        $result_horarios = mysqli_query($conexao, $query_horarios);

        if (!$result_horarios) {
            echo json_encode(['success' => false, 'message' => 'Erro na consulta de horários: ' . mysqli_error($conexao)]);
            exit;
        }
        
        $horarios_data = [];
        while ($horario = mysqli_fetch_assoc($result_horarios)) {
            $horario_id = $horario['horario_id'];
            
            $query_atendentes = "SELECT c.candidato_id, c.nome, c.rodizio, i.url, c.ativo, IF(d.id IS NOT NULL, 1, 0) as is_disponivel, IF(d.escalado = 1, 1, 0) as is_escalado FROM candidatos c LEFT JOIN imagens i ON c.imagem_id = i.imagem_id LEFT JOIN disponibilidade d ON c.candidato_id = d.candidato_id AND d.atividade_id = {$horario_id} WHERE c.ativo IN (0, 1) AND c.rodizio >= 1 ORDER BY c.ativo DESC, c.rodizio ASC";
            $result_atendentes = mysqli_query($conexao, $query_atendentes);

            if (!$result_atendentes) {
                echo json_encode(['success' => false, 'message' => 'Erro na consulta de atendentes para o horário ' . $horario_id . ': ' . mysqli_error($conexao)]);
                exit;
            }
            
            $atendentes = [];
            while ($atendente = mysqli_fetch_assoc($result_atendentes)) {
                $atendentes[] = $atendente;
            }
            
            $horario['atendentes'] = $atendentes;
            $horarios_data[] = $horario;
        }
        
        echo json_encode(['success' => true, 'horarios' => $horarios_data]);
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
            }
        }
        echo json_encode(['success' => true, 'message' => 'Escala salva com sucesso!']);
        exit;
    }
}

// --- LÓGICA DE FRONTEND (Renderiza a página) ---
$evento_id_selecionado = null;
$nome_evento_selecionado = 'Evento não encontrado';
if (isset($_GET['evento_id'])) {
    $evento_id_selecionado = intval($_GET['evento_id']);
    $query_evento_nome = "SELECT nome FROM eventos_marcados WHERE id = {$evento_id_selecionado}";
    $result_nome = mysqli_query($conexao, $query_evento_nome);
    if ($row_nome = mysqli_fetch_assoc($result_nome)) {
        $nome_evento_selecionado = $row_nome['nome'];
    }
}

include_once('./include/nav.php');
include_once('./include/admin_sidebar.php');
?>
<div class="container mt-4">
    <h1 class="mb-4">Gestão de Escala: <?php echo htmlspecialchars($nome_evento_selecionado); ?>
        <img id="event-header-image" src="" alt="Imagem do Evento" class="img-thumbnail ml-3" style="max-height: 80px; display: none;">
    </h1>
    <p><a href="/admin/atividades">&laquo; Voltar para a lista de eventos</a></p>
    <hr>
    <div id="escala-container">
        <?php if (!$evento_id_selecionado): ?>
            <div class="alert alert-danger">Nenhum evento foi especificado.</div>
        <?php else: ?>
            <div id="mensagem-escala"></div>
            <div class="text-center py-4" id="escala-loading">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
                <p class="mt-2 text-muted font-weight-bold">Carregando horários e atendentes da escala...</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de Mensagens -->
<div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalLabel">Mensagem</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="messageModalBody">
                <!-- Conteúdo da mensagem aqui -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Credenciamento -->
<div class="modal fade" id="credenciamentoModal" tabindex="-1" role="dialog" aria-labelledby="credenciamentoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title font-weight-bold" id="credenciamentoModalLabel">
                    <i class="fas fa-id-card mr-2"></i> Ficha de Credenciamento — <span id="cred-evento-nome">Carregando...</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-light border shadow-sm d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <i class="fas fa-info-circle text-info mr-1"></i> 
                        <strong>Lista Oficial para Credenciamento do Contratante.</strong> Inclui os Coordenadores e Atendentes Escalados.
                    </div>
                    <span class="badge badge-info p-2" id="cred-total-badge" style="font-size: 0.9rem;">Carregando...</span>
                </div>
                
                <div id="credenciamento-container" class="table-responsive">
                    <!-- Tabela de Credenciamento gerada via JavaScript -->
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <div>
                    <button type="button" class="btn btn-outline-success font-weight-bold" id="copy-text-btn">
                        <i class="fas fa-copy mr-1"></i> Copiar Texto (WhatsApp / E-mail)
                    </button>
                    <button type="button" class="btn btn-outline-primary font-weight-bold ml-2" id="copy-tsv-btn">
                        <i class="fas fa-file-excel mr-1"></i> Copiar Tabela (para Excel)
                    </button>
                </div>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Presenças (Pós-Evento) -->
<div class="modal fade" id="presencasModal" tabindex="-1" role="dialog" aria-labelledby="presencasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title font-weight-bold" id="presencasModalLabel">
                    <i class="fas fa-user-check mr-2"></i> Confirmar Presença e Processar Rodízio
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info border shadow-sm mb-3">
                    <i class="fas fa-info-circle mr-1"></i>
                    <strong>Apenas os atendentes marcados com presença confirmada</strong> serão movidos para o final da fila de rodízio (MAX + 1). Caso o atendente tenha faltado por doença ou motivo justificado, desmarque a caixa para que a posição dele seja preservada no rodízio.
                </div>
                <form id="presencasForm">
                    <div id="presencas-candidatos-list" class="list-group mb-3">
                        <!-- Lista de atendentes escalados -->
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success font-weight-bold" id="salvar-presencas-btn">
                    <i class="fas fa-check-double mr-1"></i> Confirmar Presenças e Atualizar Rodízio
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Envio de Avaliação ao Contratante (WhatsApp) -->
<div class="modal fade" id="contratanteModal" tabindex="-1" role="dialog" aria-labelledby="contratanteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title font-weight-bold" id="contratanteModalLabel">
                    <i class="fab fa-whatsapp mr-2"></i> Enviar Ficha de Avaliação ao Contratante
                </h5>
                <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-light border shadow-sm mb-3">
                    <i class="fas fa-info-circle text-warning mr-1"></i>
                    Confirme o telefone do contratante para enviar a mensagem por WhatsApp contendo o link de avaliação dos atendentes escalados.
                </div>

                <div class="form-group">
                    <label for="contratante-telefone" class="font-weight-bold">
                        <i class="fas fa-phone-alt text-success mr-1"></i> Telefone do Contratante (com DDD):
                    </label>
                    <input type="text" class="form-control form-control-lg font-weight-bold text-dark" id="contratante-telefone" placeholder="Ex: (11) 99999-8888" value="">
                </div>

                <div class="form-group">
                    <label for="contratante-mensagem" class="font-weight-bold">
                        <i class="fas fa-comment-alt text-primary mr-1"></i> Mensagem a ser enviada:
                    </label>
                    <textarea class="form-control font-monospace" id="contratante-mensagem" rows="6" readonly></textarea>
                </div>

                <div class="card bg-light border p-3">
                    <small class="text-muted font-weight-bold d-block mb-1"><i class="fas fa-link mr-1"></i> Link Direto de Avaliação Público:</small>
                    <a href="#" id="contratante-link-preview" target="_blank" class="font-weight-bold text-primary text-break">Carregando link...</a>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-dark font-weight-bold" id="copy-contratante-msg-btn">
                        <i class="fas fa-copy mr-1"></i> Copiar Mensagem
                    </button>
                    <a href="#" target="_blank" class="btn btn-success font-weight-bold ml-2" id="abrir-wa-web-btn">
                        <i class="fab fa-whatsapp mr-1"></i> Abrir no WhatsApp ➔
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const escalaContainer = document.getElementById('escala-container');
    const urlParams = new URLSearchParams(window.location.search);
    const eventoId = urlParams.get('evento_id');
    var AppWebRoot = '<?php echo $GLOBALS["app_web_root"]; ?>';

    function carregarDetalhesEvento(id) {
        if (!id) return;
     
        fetch(AppWebRoot + 'include/api_escala.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'get_escala_details', evento_id: id })
        })
        .then(async response => {
            const text = await response.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch (jsonErr) {
                console.error("Resposta não-JSON do servidor:", text);
                throw new Error(text.replace(/<[^>]*>?/gm, ' ').substring(0, 180).trim() || 'Resposta inválida do servidor.');
            }
            if (!response.ok) throw new Error('HTTP ' + response.status + ': ' + (data.message || 'Erro no servidor'));
            return data;
        })
        .then(data => {
            if (data.success && data.horarios && data.candidatos) {
                let isScheduleSaved = false;
                data.candidatos.forEach(candidato => {
                    for (const horarioId in candidato.horarios_status) {
                        if (candidato.horarios_status[horarioId].is_escalado == 1) {
                            isScheduleSaved = true;
                            break;
                        }
                    }
                });
    
                escalaContainer.innerHTML = '<div id="mensagem-escala"></div>';
                const eventImage = document.getElementById('event-header-image');
                if (eventImage) {
                    if (data.event_image_url) {
                        eventImage.src = AppWebRoot + data.event_image_url;
                        eventImage.style.display = 'inline-block';
                    } else {
                        eventImage.style.display = 'none';
                    }
                }

                const preSelectedCounts = new Map();
                let tableHtml = `
                    <table class="table table-striped table-bordered align-middle">
                        <thead class="thead-dark">
                            <tr>
                                <th>Rodízio</th>
                                <th>Foto</th>
                                <th>Nome</th>
                                <th>Status</th>
                `;
    
                data.horarios.forEach(horario => {
                    tableHtml += `<th>${new Date(horario.data_inicio).toLocaleDateString('pt-BR')}
                        <br>${new Date(horario.data_inicio).toLocaleTimeString('pt-BR', {hour: '2-digit', minute:'2-digit'})} - ${new Date(horario.data_final).toLocaleTimeString('pt-BR', {hour: '2-digit', minute:'2-digit'})}
                        <br>(${horario.vagas} vagas)</th>`;
                });
    
                tableHtml += `
                            </tr>
                        </thead>
                        <tbody>
                `;
    
                data.candidatos.forEach(candidato => {
                    const rowClass = candidato.ativo == 0 ? 'table-success' : '';
                    tableHtml += `<tr class="${rowClass}">`;
                    tableHtml += `<td>${candidato.rodizio}</td>`;
                    tableHtml += `<td><img src="${AppWebRoot}${candidato.url}" class="img-thumbnail rounded-circle" width="40" height="40" style="object-fit:cover;"></td>`;
                    tableHtml += `<td class="font-weight-bold">
                        ${candidato.nome}
                        ${candidato.presenca_confirmada == 1 ? '<span class="badge badge-success ml-1"><i class="fas fa-check mr-1"></i>Presente</span>' : ''}
                        ${candidato.ja_avaliado == 1 ? '<span class="badge badge-warning text-dark ml-1"><i class="fas fa-star mr-1"></i>Avaliado</span>' : ''}
                    </td>`;
                    tableHtml += `<td>${candidato.ativo == 1 ? 'Atendente' : 'Treinamento'}</td>`;
    
                    data.horarios.forEach(horario => {
                        const status = candidato.horarios_status[horario.horario_id] || { is_disponivel: 0, is_escalado: 0 };
                        const isAvailable = status.is_disponivel == 1;
                        let isChecked = status.is_escalado == 1 ? 'checked' : '';
    
                        tableHtml += `
                            <td>
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="switch-${candidato.candidato_id}-${horario.horario_id}" data-horario-id="${horario.horario_id}" value="${candidato.candidato_id}" ${isChecked}>
                                            <label class="custom-control-label" for="switch-${candidato.candidato_id}-${horario.horario_id}"></label>
                                        </div>
                                    </div>
                                    <div class="col">
                                        ${isAvailable ? '<i class="fas fa-check text-success font-weight-bold"></i>' : ''}
                                    </div>
                                </div>
                            </td>
                        `;
                    });
    
                    tableHtml += `</tr>`;
                });
    
                tableHtml += `
                        </tbody>
                    </table>
                `;
                escalaContainer.innerHTML = tableHtml;
    
                const btnContainer = document.createElement('div');
                btnContainer.className = 'mt-3 mb-4 d-flex flex-wrap gap-2';
                
                const saveButton = document.createElement('button');
                saveButton.className = 'btn btn-primary font-weight-bold';
                saveButton.id = 'salvar-escala-btn';
                saveButton.innerHTML = '<i class="fas fa-save mr-1"></i> Salvar Escala';
                btnContainer.appendChild(saveButton);

                const suggestButton = document.createElement('button');
                suggestButton.className = 'btn btn-secondary font-weight-bold ml-2';
                suggestButton.id = 'sugerir-escala-btn';
                suggestButton.innerHTML = '<i class="fas fa-magic mr-1"></i> Sugerir Escala (Rodízio)';
                btnContainer.appendChild(suggestButton);

                const credenciamentoButton = document.createElement('button');
                credenciamentoButton.className = 'btn btn-info font-weight-bold ml-2';
                credenciamentoButton.id = 'credenciamento-btn';
                credenciamentoButton.innerHTML = '<i class="fas fa-id-card mr-1"></i> Ficha de Credenciamento';
                btnContainer.appendChild(credenciamentoButton);

                const presencasButton = document.createElement('button');
                presencasButton.className = 'btn btn-success font-weight-bold ml-2';
                presencasButton.id = 'presencas-btn';
                presencasButton.innerHTML = '<i class="fas fa-user-check mr-1"></i> Confirmar Presença (No Dia / Pós-Evento)';
                btnContainer.appendChild(presencasButton);

                const contratanteButton = document.createElement('button');
                contratanteButton.className = 'btn btn-warning font-weight-bold ml-2';
                contratanteButton.id = 'contratante-btn';
                contratanteButton.innerHTML = '<i class="fab fa-whatsapp mr-1"></i> Enviar Avaliação (Contratante)';
                btnContainer.appendChild(contratanteButton);

                escalaContainer.appendChild(btnContainer);

                attachSaveListener();
                attachSuggestListener();
                attachCredenciamentoListener();
                attachPresencasListener(data);
                attachContratanteListener(data);
            } else {
                escalaContainer.innerHTML = '<div class="alert alert-danger">Erro ao carregar detalhes da escala: ' + (data.message || 'Resposta inválida.') + '</div>';
            }
        })
        .catch(err => {
            escalaContainer.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle mr-2"></i>Erro de conexão: ' + err.message + '</div>';
        });
    }

    function attachSaveListener() {
        const saveButton = document.getElementById('salvar-escala-btn');
        if (saveButton) {
            saveButton.addEventListener('click', function(event) {
                event.preventDefault();
                const checkboxes = document.querySelectorAll('#escala-container input[type="checkbox"]:checked');
                let escalados = {};
                checkboxes.forEach(cb => {
                    const horarioId = cb.dataset.horarioId;
                    if (!escalados[horarioId]) {
                        escalados[horarioId] = [];
                    }
                    escalados[horarioId].push(cb.value);
                });
                fetch(AppWebRoot + 'include/api_escala.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        action: 'salvar_escala',
                        evento_id: eventoId, 
                        escalados: escalados
                    })
                })
                .then(response => response.json())
                .then(data => {
                    const mensagemDiv = document.getElementById('mensagem-escala');
                    if (data.success) {
                        mensagemDiv.innerHTML = '<div class="alert alert-success">Escala salva com sucesso!</div>';
                    } else {
                        mensagemDiv.innerHTML = '<div class="alert alert-danger">Erro ao salvar a escala: ' + data.message + '</div>';
                    }
                })
                .catch(error => {
                    console.error('Erro na requisição AJAX:', error);
                    const mensagemDiv = document.getElementById('mensagem-escala');
                    mensagemDiv.innerHTML = '<div class="alert alert-danger">Erro ao salvar a escala. Verifique o console para mais detalhes.</div>';
                });
            });
        }
    }

    function attachSuggestListener() {
        const suggestButton = document.getElementById('sugerir-escala-btn');
        if (suggestButton) {
            suggestButton.addEventListener('click', function(e) {
                e.preventDefault();
                fetch(AppWebRoot + 'include/api_escala.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'get_escala_details', evento_id: eventoId })
                })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;
                    document.querySelectorAll('#escala-container input[type="checkbox"]').forEach(cb => cb.checked = false);
                    const counts = new Map();
                    data.candidatos.forEach(c => {
                        data.horarios.forEach(h => {
                            const status = c.horarios_status[h.horario_id];
                            if (status && status.is_disponivel == 1) {
                                const current = counts.get(h.horario_id) || 0;
                                if (current < h.vagas) {
                                    const cb = document.getElementById(`switch-${c.candidato_id}-${h.horario_id}`);
                                    if (cb) {
                                        cb.checked = true;
                                        counts.set(h.horario_id, current + 1);
                                    }
                                }
                            }
                        });
                    });
                });
            });
        }
    }

    function attachCredenciamentoListener() {
        const btn = document.getElementById('credenciamento-btn');
        if (!btn) return;
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const modalEl = document.getElementById('credenciamentoModal');
            const tbody = document.getElementById('credenciamento-tbody');
            const printBtn = document.getElementById('print-credenciamento-btn');
            const copyBtn = document.getElementById('copy-credenciamento-btn');

            if (tbody) tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4"><i class="fas fa-spinner fa-spin mr-2"></i>Carregando dados da equipe...</td></tr>';
            
            if (window.jQuery && typeof $('#credenciamentoModal').modal === 'function') {
                $('#credenciamentoModal').modal('show');
            } else if (modalEl) {
                modalEl.classList.add('show');
                modalEl.style.display = 'block';
                modalEl.style.backgroundColor = 'rgba(0,0,0,0.5)';
            }

            fetch(AppWebRoot + 'include/api_escala.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'get_credenciamento', evento_id: eventoId })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const eventTitleEl = document.getElementById('credenciamento-evento-nome');
                    if (eventTitleEl) eventTitleEl.innerText = data.evento_nome || '';

                    let html = '';
                    let seq = 1;
                    
                    data.equipe.forEach(p => {
                        const papelBadge = p.papel === 'Coordenador(a)' 
                            ? '<span class="badge badge-dark">Coordenador(a)</span>' 
                            : (p.papel === 'Atendente' ? '<span class="badge badge-primary">Atendente</span>' : '<span class="badge badge-info">Treinamento</span>');
                        
                        html += `
                            <tr>
                                <td class="text-center font-weight-bold">${seq++}</td>
                                <td class="font-weight-bold text-dark">${p.nome}</td>
                                <td>${papelBadge}</td>
                                <td>${p.Nascimento_fmt || '-'}</td>
                                <td>${p.RG || '-'}</td>
                                <td>${p.CPF || '-'}</td>
                                <td class="text-center font-weight-bold">${p.camisa || '-'}</td>
                            </tr>
                        `;
                    });
                    
                    if (tbody) tbody.innerHTML = html;

                    if (printBtn) {
                        printBtn.onclick = function() {
                            const printContents = document.getElementById('credenciamento-print-area').innerHTML;
                            const origContents = document.body.innerHTML;
                            document.body.innerHTML = printContents;
                            window.print();
                            document.body.innerHTML = origContents;
                            location.reload();
                        };
                    }

                    if (copyBtn) {
                        copyBtn.onclick = function() {
                            let text = `FICHA DE CREDENCIAMENTO - ${data.evento_nome}\n\n`;
                            text += `Seq\tNome\tPapel\tNascimento\tRG\tCPF\tCamisa\n`;
                            let s = 1;
                            data.equipe.forEach(p => {
                                text += `${s++}\t${p.nome}\t${p.papel}\t${p.Nascimento_fmt || ''}\t${p.RG || ''}\t${p.CPF || ''}\t${p.camisa || ''}\n`;
                            });
                            navigator.clipboard.writeText(text).then(() => {
                                alert('Dados copiados para a área de transferência!');
                            });
                        };
                    }
                } else {
                    if (tbody) tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">${data.message || 'Erro ao carregar dados.'}</td></tr>`;
                }
            })
            .catch(err => {
                if (tbody) tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">Erro de comunicação: ${err.message}</td></tr>`;
            });
        });
    }

    document.querySelectorAll('#credenciamentoModal [data-dismiss="modal"]').forEach(btn => {
        btn.addEventListener('click', function() {
            if (window.jQuery && typeof $('#credenciamentoModal').modal === 'function') {
                $('#credenciamentoModal').modal('hide');
            } else {
                const modalEl = document.getElementById('credenciamentoModal');
                if (modalEl) {
                    modalEl.classList.remove('show');
                    modalEl.style.display = 'none';
                }
            }
        });
    });

    function attachPresencasListener(dataDetails) {
        const presencasBtn = document.getElementById('presencas-btn');
        if (!presencasBtn) return;

        presencasBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const modalEl = document.getElementById('presencasModal');
            const listContainer = document.getElementById('presencas-candidatos-list');

            const escaladosMap = new Map();
            if (dataDetails && dataDetails.candidatos) {
                dataDetails.candidatos.forEach(c => {
                    let escalado = false;
                    for (const hId in c.horarios_status) {
                        if (c.horarios_status[hId].is_escalado == 1) {
                            escalado = true;
                            break;
                        }
                    }
                    if (escalado) {
                        escaladosMap.set(c.candidato_id, c);
                    }
                });
            }

            if (escaladosMap.size === 0) {
                alert('Nenhum atendente está marcado como escalado para este evento.');
                return;
            }

            let html = '';
            escaladosMap.forEach(c => {
                const imgUrl = c.url ? AppWebRoot + c.url : AppWebRoot + 'img/ame2023.jpg';
                const linkAval = c.link_avaliacao ? AppWebRoot + c.link_avaliacao : '#';
                const fullEvalUrl = window.location.origin + linkAval;
                html += `
                    <div class="list-group-item d-flex align-items-center justify-content-between flex-wrap gap-2 p-3">
                        <div class="d-flex align-items-center">
                            <img src="${imgUrl}" class="rounded-circle mr-3 shadow-sm" width="46" height="46" style="object-fit:cover;" onerror="this.src='${AppWebRoot}img/ame2023.jpg';">
                            <div>
                                <strong class="d-block text-dark">${c.nome}</strong>
                                <small class="text-muted">Rodízio: #${c.rodizio} | ${c.ativo == 1 ? 'Atendente' : 'Treinamento'}</small>
                                ${c.ja_avaliado == 1 ? '<span class="badge badge-warning text-dark ml-1"><i class="fas fa-star mr-1"></i>Já Avaliado</span>' : ''}
                            </div>
                        </div>
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <a href="${linkAval}" target="_blank" class="btn btn-sm btn-outline-info font-weight-bold mr-1" title="Preencher Ficha de Avaliação do Atendente">
                                <i class="fas fa-star mr-1"></i> Avaliar Atendente
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-copy-cand-eval mr-3" data-url="${fullEvalUrl}" title="Copiar link de avaliação deste atendente">
                                <i class="fas fa-copy"></i>
                            </button>
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" class="custom-control-input presenca-checkbox" id="presenca-cand-${c.candidato_id}" value="${c.candidato_id}" checked>
                                <label class="custom-control-label font-weight-bold text-success" for="presenca-cand-${c.candidato_id}">
                                    <i class="fas fa-check-circle mr-1"></i> Compareceu ao Evento
                                </label>
                            </div>
                        </div>
                    </div>
                `;
            });
            if (listContainer) listContainer.innerHTML = html;

            document.querySelectorAll('.btn-copy-cand-eval').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.getAttribute('data-url');
                    const originalHtml = this.innerHTML;
                    const el = this;
                    navigator.clipboard.writeText(url).then(() => {
                        el.innerHTML = '<i class="fas fa-check text-success"></i>';
                        setTimeout(() => { el.innerHTML = originalHtml; }, 1500);
                    });
                });
            });

            if (window.jQuery && typeof $('#presencasModal').modal === 'function') {
                $('#presencasModal').modal('show');
            } else if (modalEl) {
                modalEl.classList.add('show');
                modalEl.style.display = 'block';
                modalEl.style.backgroundColor = 'rgba(0,0,0,0.5)';
            }
        });
    }

    document.querySelectorAll('#presencasModal [data-dismiss="modal"]').forEach(btn => {
        btn.addEventListener('click', function() {
            if (window.jQuery && typeof $('#presencasModal').modal === 'function') {
                $('#presencasModal').modal('hide');
            } else {
                const modalEl = document.getElementById('presencasModal');
                if (modalEl) {
                    modalEl.classList.remove('show');
                    modalEl.style.display = 'none';
                }
            }
        });
    });

    const salvarPresencasBtn = document.getElementById('salvar-presencas-btn');
    if (salvarPresencasBtn) {
        salvarPresencasBtn.addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('.presenca-checkbox');
            let presencas = {};
            checkboxes.forEach(cb => {
                presencas[cb.value] = cb.checked ? 1 : 0;
            });

            salvarPresencasBtn.disabled = true;
            salvarPresencasBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Processando...';

            fetch(AppWebRoot + 'include/api_escala.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'confirmar_presencas',
                    evento_id: eventoId,
                    presencas: presencas
                })
            })
            .then(r => r.json())
            .then(res => {
                salvarPresencasBtn.disabled = false;
                salvarPresencasBtn.innerHTML = '<i class="fas fa-check-double mr-1"></i> Confirmar Presenças e Atualizar Rodízio';

                if (window.jQuery && typeof $('#presencasModal').modal === 'function') {
                    $('#presencasModal').modal('hide');
                } else {
                    const modalEl = document.getElementById('presencasModal');
                    if (modalEl) {
                        modalEl.classList.remove('show');
                        modalEl.style.display = 'none';
                    }
                }

                alert(res.message || 'Presenças processadas com sucesso!');
                location.reload();
            })
            .catch(err => {
                salvarPresencasBtn.disabled = false;
                salvarPresencasBtn.innerHTML = '<i class="fas fa-check-double mr-1"></i> Confirmar Presenças e Atualizar Rodízio';
                alert('Erro ao processar presenças: ' + err.message);
            });
        });
    }

    function attachContratanteListener(dataDetails) {
        const contratanteBtn = document.getElementById('contratante-btn');
        if (!contratanteBtn) return;

        contratanteBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const modalEl = document.getElementById('contratanteModal');
            const phoneInput = document.getElementById('contratante-telefone');
            const msgTextarea = document.getElementById('contratante-mensagem');
            const linkPreview = document.getElementById('contratante-link-preview');
            const abrirWaBtn = document.getElementById('abrir-wa-web-btn');

            const evNome = dataDetails ? (dataDetails.event_nome || 'Evento') : 'Evento';
            const evLink = dataDetails ? (dataDetails.link_avaliacao_contratante || 'https://projetoame.org') : 'https://projetoame.org';

            if (linkPreview) {
                linkPreview.href = evLink;
                linkPreview.innerText = evLink;
            }

            function updateMessage() {
                const rawPhone = phoneInput ? phoneInput.value.replace(/\D/g, '') : '';
                const msg = `Olá! Agradecemos a parceria com o Projeto AME no evento *${evNome}*.\n\nPor favor, acesse o link abaixo para avaliar a nossa equipe de atendentes:\n${evLink}\n\nSua opinião é fundamental para a inclusão e autonomia dos nossos atendentes! ❤️`;
                
                if (msgTextarea) msgTextarea.value = msg;

                if (abrirWaBtn) {
                    const targetPhone = rawPhone.length > 0 ? (rawPhone.startsWith('55') ? rawPhone : '55' + rawPhone) : '';
                    const encodedMsg = encodeURIComponent(msg);
                    abrirWaBtn.href = targetPhone ? `https://api.whatsapp.com/send?phone=${targetPhone}&text=${encodedMsg}` : `https://api.whatsapp.com/send?text=${encodedMsg}`;
                }
            }

            if (phoneInput) {
                phoneInput.removeEventListener('input', updateMessage);
                phoneInput.addEventListener('input', updateMessage);
            }

            updateMessage();

            if (window.jQuery && typeof $('#contratanteModal').modal === 'function') {
                $('#contratanteModal').modal('show');
            } else if (modalEl) {
                modalEl.classList.add('show');
                modalEl.style.display = 'block';
                modalEl.style.backgroundColor = 'rgba(0,0,0,0.5)';
            }
        });
    }

    document.querySelectorAll('#contratanteModal [data-dismiss="modal"]').forEach(btn => {
        btn.addEventListener('click', function() {
            if (window.jQuery && typeof $('#contratanteModal').modal === 'function') {
                $('#contratanteModal').modal('hide');
            } else {
                const modalEl = document.getElementById('contratanteModal');
                if (modalEl) {
                    modalEl.classList.remove('show');
                    modalEl.style.display = 'none';
                }
            }
        });
    });

    const copyContratanteMsgBtn = document.getElementById('copy-contratante-msg-btn');
    if (copyContratanteMsgBtn) {
        copyContratanteMsgBtn.addEventListener('click', function() {
            const msgTextarea = document.getElementById('contratante-mensagem');
            const text = msgTextarea ? msgTextarea.value : '';
            execCopy(text, '📱 Mensagem com o link de avaliação copiada com sucesso!\nPronto para enviar ao contratante.');
        });
    }

    if (eventoId) {
        carregarDetalhesEvento(eventoId);
    }
});
</script>
<?php include_once('./include/admin_sidebar_footer.php'); ?>
