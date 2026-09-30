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
    <div id="mensagem-escala" class="mb-3"></div>
    <div id="escala-container">
        <?php if (!$evento_id_selecionado): ?>
            <div class="alert alert-danger">Nenhum evento foi especificado.</div>
        <?php else: ?>
            <div class="text-center py-4" id="escala-loading">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
                <p class="mt-2 text-muted font-weight-bold">Carregando horários e atendentes da escala...</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.modal-dialog-scrollable {
    display: flex;
    max-height: calc(100vh - 3.5rem);
}
.modal-dialog-scrollable .modal-content {
    max-height: calc(100vh - 3.5rem);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.modal-dialog-scrollable .modal-header,
.modal-dialog-scrollable .modal-footer {
    flex-shrink: 0;
}
.modal-dialog-scrollable .modal-body {
    overflow-y: auto;
}
#presencas-candidatos-list::-webkit-scrollbar,
#credenciamento-container::-webkit-scrollbar,
#diarias-table-container::-webkit-scrollbar,
.modal-body::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
#presencas-candidatos-list::-webkit-scrollbar-thumb,
#credenciamento-container::-webkit-scrollbar-thumb,
#diarias-table-container::-webkit-scrollbar-thumb,
.modal-body::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
#presencas-candidatos-list::-webkit-scrollbar-thumb:hover,
#credenciamento-container::-webkit-scrollbar-thumb:hover,
#diarias-table-container::-webkit-scrollbar-thumb:hover,
.modal-body::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
.table-sticky-header thead th {
    position: sticky;
    top: 0;
    z-index: 5;
    background-color: #343a40;
    color: #fff;
}
.table-sticky-header-light thead th {
    position: sticky;
    top: 0;
    z-index: 5;
    background-color: #f8f9fa;
    color: #333;
}
</style>

<!-- Modal de Mensagens -->
<div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
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
    <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
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
                
                <div id="credenciamento-container" class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
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
                    <button type="button" class="btn btn-outline-secondary font-weight-bold ml-2" id="print-cred-btn">
                        <i class="fas fa-print mr-1"></i> Imprimir
                    </button>
                </div>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Presenças (Pós-Evento) -->
<div class="modal fade" id="presencasModal" tabindex="-1" role="dialog" aria-labelledby="presencasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
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
                <!-- Controle de Realização de Rodízio -->
                <div class="card mb-2 border-primary shadow-sm" style="background-color: #f8faff;">
                    <div class="card-body py-2 px-3">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="realizar-rodizio-switch" checked>
                            <label class="custom-control-label font-weight-bold text-dark mb-0" for="realizar-rodizio-switch" style="cursor: pointer;">
                                <i class="fas fa-sync-alt text-primary mr-1"></i> Realizar Rodízio ao Confirmar Presenças
                            </label>
                        </div>
                        <small id="realizar-rodizio-help" class="form-text mt-1 text-muted">
                            Atividades de trabalho realizam o rodízio. Cursos e treinamentos não devem movimentar a fila de rodízio.
                        </small>
                    </div>
                </div>

                <div class="alert alert-info border shadow-sm mb-2 py-2 px-3" id="presencas-info-alert" style="font-size: 0.88rem;">
                    <i class="fas fa-info-circle mr-1"></i>
                    <span id="presencas-info-text">
                        <strong>Apenas os atendentes marcados com presença confirmada</strong> serão movidos para o final da fila de rodízio (MAX + 1). Caso o atendente tenha faltado por doença ou motivo justificado, desmarque a caixa para que a posição dele seja preservada no rodízio.
                    </span>
                </div>
                <!-- Seletor de Horários / Aulas (para chamadas por turno ou por aula) -->
                <div id="presencas-horarios-nav-container" class="mb-2 d-none">
                    <label class="font-weight-bold text-dark d-block mb-1">
                        <i class="fas fa-calendar-alt text-info mr-1"></i> Selecione a Aula / Turno para Chamada:
                    </label>
                    <div id="presencas-horarios-pills" class="d-flex flex-wrap gap-2"></div>
                </div>

                <form id="presencasForm">
                    <div id="presencas-candidatos-list" class="list-group mb-2" style="max-height: 52vh; overflow-y: auto; padding-right: 4px; border: 1px solid #e9ecef; border-radius: 6px;">
                        <!-- Lista de atendentes escalados -->
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success font-weight-bold" id="salvar-presencas-btn">
                    <i class="fas fa-check-double mr-1"></i> <span id="salvar-presencas-btn-text">Confirmar Presenças e Atualizar Rodízio</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Envio de Avaliação ao Contratante (WhatsApp) -->
<div class="modal fade" id="contratanteModal" tabindex="-1" role="dialog" aria-labelledby="contratanteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
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

<!-- Modal de Folha de Fechamento / Diárias (PIX) -->
<div class="modal fade" id="diariasModal" tabindex="-1" role="dialog" aria-labelledby="diariasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title font-weight-bold" id="diariasModalLabel">
                    <i class="fas fa-money-bill-wave text-success mr-2"></i> Folha de Diárias e Fechamento PIX
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-7">
                        <h4 id="diarias-evento-nome" class="font-weight-bold text-dark mb-1">Nome do Evento</h4>
                        <p class="text-muted mb-0" id="diarias-evento-subtitulo">Fechamento de remuneração por turnos/aulas cumpridas.</p>
                    </div>
                    <div class="col-md-5 text-md-right mt-2 mt-md-0">
                        <span class="badge badge-success px-3 py-2 font-weight-bold" style="font-size: 1.1rem;" id="diarias-total-geral-badge">
                            Total Geral: R$ 0,00
                        </span>
                    </div>
                </div>

                <div class="alert alert-light border shadow-sm mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <i class="fas fa-info-circle text-primary mr-1"></i>
                        <span>Diária Padrão: <strong id="diarias-valor-padrao-txt">R$ 200,00</strong> por turno/dia trabalhado. As diárias são calculadas sobre as presenças confirmadas.</span>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-success font-weight-bold" id="copy-diarias-resumo-btn">
                            <i class="fab fa-whatsapp mr-1"></i> Copiar Resumo (Financeiro)
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary font-weight-bold ml-2" id="copy-diarias-tsv-btn">
                            <i class="fas fa-file-excel mr-1"></i> Copiar TSV (Excel)
                        </button>
                    </div>
                </div>

                <div class="table-responsive" id="diarias-table-container" style="max-height: 55vh; overflow-y: auto;">
                    <table class="table table-hover table-bordered align-middle table-sticky-header-light" id="diarias-table">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                <th>Atendente</th>
                                <th class="text-center">Turnos Cumpridos</th>
                                <th class="text-right">Valor Unitário</th>
                                <th class="text-right">Total a Pagar</th>
                                <th>Chave PIX Cadastrada</th>
                                <th class="text-center" style="width: 140px;">Ação</th>
                            </tr>
                        </thead>
                        <tbody id="diarias-tbody">
                            <!-- Inserido dinamicamente via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <div>
                    <button type="button" class="btn btn-outline-secondary font-weight-bold mr-2" id="print-diarias-btn">
                        <i class="fas fa-print mr-1"></i> Imprimir Folha
                    </button>
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
    let currentEscalaData = null;
    let currentSelectedPresencaHorarioId = 0;

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
                currentEscalaData = data;
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

                const diariasButton = document.createElement('button');
                diariasButton.className = 'btn btn-dark font-weight-bold ml-2';
                diariasButton.id = 'diarias-btn';
                diariasButton.innerHTML = '<i class="fas fa-money-bill-wave text-success mr-1"></i> Folha de Diárias (PIX)';
                btnContainer.appendChild(diariasButton);

                escalaContainer.appendChild(btnContainer);

                // Sincronização em tempo real dos switches da tabela com currentEscalaData
                escalaContainer.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                    cb.addEventListener('change', function() {
                        const candId = this.value;
                        const hId = this.dataset.horarioId;
                        if (currentEscalaData && currentEscalaData.candidatos) {
                            const c = currentEscalaData.candidatos.find(item => String(item.candidato_id) === String(candId));
                            if (c) {
                                if (!c.horarios_status) c.horarios_status = {};
                                if (!c.horarios_status[hId]) c.horarios_status[hId] = { is_disponivel: 1, is_escalado: 0 };
                                c.horarios_status[hId].is_escalado = this.checked ? 1 : 0;
                            }
                        }
                    });
                });

                attachSaveListener();
                attachSuggestListener();
                attachCredenciamentoListener();
                attachPresencasListener(data);
                attachContratanteListener(data);
                attachDiariasListener(data);
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
                .then(async response => {
                    const text = await response.text();
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        throw new Error("Resposta inválida do servidor: " + text.replace(/<[^>]*>?/gm, ' ').substring(0, 150));
                    }
                })
                .then(data => {
                    let mensagemDiv = document.getElementById('mensagem-escala');
                    if (!mensagemDiv) {
                        mensagemDiv = document.createElement('div');
                        mensagemDiv.id = 'mensagem-escala';
                        mensagemDiv.className = 'mb-3';
                        const container = document.getElementById('escala-container');
                        if (container && container.parentNode) {
                            container.parentNode.insertBefore(mensagemDiv, container);
                        }
                    }
                    if (data.success) {
                        // Sincroniza o estado em memória para que o modal de presenças e credenciamento reflitam imediatamente
                        if (currentEscalaData && currentEscalaData.candidatos) {
                            currentEscalaData.candidatos.forEach(c => {
                                for (const hId in c.horarios_status) {
                                    const cb = document.getElementById(`switch-${c.candidato_id}-${hId}`);
                                    if (cb) {
                                        c.horarios_status[hId].is_escalado = cb.checked ? 1 : 0;
                                    }
                                }
                            });
                        }
                        mensagemDiv.innerHTML = '<div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle mr-2"></i>Escala salva com sucesso!<button type="button" class="close" data-dismiss="alert">&times;</button></div>';
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    } else {
                        mensagemDiv.innerHTML = '<div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-triangle mr-2"></i>Erro ao salvar a escala: ' + data.message + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>';
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                })
                .catch(error => {
                    console.error('Erro na requisição AJAX:', error);
                    let mensagemDiv = document.getElementById('mensagem-escala');
                    if (!mensagemDiv) {
                        mensagemDiv = document.createElement('div');
                        mensagemDiv.id = 'mensagem-escala';
                        mensagemDiv.className = 'mb-3';
                        const container = document.getElementById('escala-container');
                        if (container && container.parentNode) {
                            container.parentNode.insertBefore(mensagemDiv, container);
                        }
                    }
                    mensagemDiv.innerHTML = '<div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-triangle mr-2"></i>Erro ao salvar a escala: ' + error.message + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>';
                    window.scrollTo({ top: 0, behavior: 'smooth' });
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
            const container = document.getElementById('credenciamento-container');
            const credNome = document.getElementById('cred-evento-nome');
            const credBadge = document.getElementById('cred-total-badge');
            const copyTextBtn = document.getElementById('copy-text-btn');
            const copyTsvBtn = document.getElementById('copy-tsv-btn');
            const printBtn = document.getElementById('print-cred-btn');

            if (credNome) credNome.textContent = 'Carregando...';
            if (credBadge) credBadge.textContent = 'Carregando...';
            if (container) {
                container.innerHTML = '<div class="text-center py-5 text-muted"><i class="fas fa-spinner fa-spin fa-2x mb-2 text-info"></i><p class="font-weight-bold">Carregando dados da equipe para credenciamento...</p></div>';
            }
            
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
            .then(async r => {
                const text = await r.text();
                try {
                    return JSON.parse(text);
                } catch (err) {
                    throw new Error("Resposta inválida: " + text.replace(/<[^>]*>?/gm, ' ').substring(0, 150));
                }
            })
            .then(data => {
                if (data.success) {
                    const equipe = data.credenciados || data.equipe || [];
                    if (credNome) credNome.textContent = data.evento_nome || '';
                    if (credBadge) credBadge.textContent = `${data.total_geral || equipe.length} Pessoas (${data.total_coordenadores || 0} Coord. + ${data.total_escalados || 0} Atendentes)`;

                    let rows = '';
                    let seq = 1;
                    equipe.forEach(p => {
                        const papelBadge = p.papel === 'Coordenador(a)' 
                            ? '<span class="badge badge-dark px-2 py-1">Coordenador(a)</span>' 
                            : (p.papel === 'Atendente' ? '<span class="badge badge-primary px-2 py-1">Atendente</span>' : '<span class="badge badge-info px-2 py-1">Treinamento</span>');
                        
                        rows += `
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

                    if (container) {
                        container.innerHTML = `
                            <div id="credenciamento-print-area">
                                <table class="table table-bordered table-striped table-hover align-middle mb-0 table-sticky-header">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th class="text-center" style="width: 50px;">#</th>
                                            <th>Nome Completo</th>
                                            <th>Função / Papel</th>
                                            <th>Nascimento</th>
                                            <th>RG</th>
                                            <th>CPF</th>
                                            <th class="text-center">Tamanho Camisa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${rows || '<tr><td colspan="7" class="text-center py-3 text-muted">Nenhum integrante encontrado na escala deste evento.</td></tr>'}
                                    </tbody>
                                </table>
                            </div>
                        `;
                    }

                    if (copyTextBtn) {
                        copyTextBtn.onclick = function() {
                            let text = `*FICHA DE CREDENCIAMENTO — ${data.evento_nome}*\n\n`;
                            text += `*Equipe do Projeto A.M.E. (${equipe.length} pessoas)*\n\n`;
                            let s = 1;
                            equipe.forEach(p => {
                                text += `${s++}. ${p.nome} (${p.papel})\n   Nasc: ${p.Nascimento_fmt || '-'} | RG: ${p.RG || '-'} | CPF: ${p.CPF || '-'} | Camisa: ${p.camisa || '-'}\n\n`;
                            });
                            navigator.clipboard.writeText(text).then(() => {
                                const orig = copyTextBtn.innerHTML;
                                copyTextBtn.innerHTML = '<i class="fas fa-check text-success mr-1"></i> Copiado!';
                                setTimeout(() => { copyTextBtn.innerHTML = orig; }, 2000);
                            });
                        };
                    }

                    if (copyTsvBtn) {
                        copyTsvBtn.onclick = function() {
                            let tsv = "Seq\tNome\tFuncao\tNascimento\tRG\tCPF\tCamisa\n";
                            let s = 1;
                            equipe.forEach(p => {
                                tsv += `${s++}\t${p.nome}\t${p.papel}\t${p.Nascimento_fmt || ''}\t${p.RG || ''}\t${p.CPF || ''}\t${p.camisa || ''}\n`;
                            });
                            navigator.clipboard.writeText(tsv).then(() => {
                                const orig = copyTsvBtn.innerHTML;
                                copyTsvBtn.innerHTML = '<i class="fas fa-check text-success mr-1"></i> Tabela Copiada!';
                                setTimeout(() => { copyTsvBtn.innerHTML = orig; }, 2000);
                            });
                        };
                    }

                    if (printBtn) {
                        printBtn.onclick = function() {
                            const printArea = document.getElementById('credenciamento-print-area');
                            if (!printArea) return;
                            const printContents = printArea.innerHTML;
                            const origContents = document.body.innerHTML;
                            document.body.innerHTML = `<div style="padding: 20px;"><h2>Ficha de Credenciamento - ${data.evento_nome}</h2>${printContents}</div>`;
                            window.print();
                            document.body.innerHTML = origContents;
                            location.reload();
                        };
                    }
                } else {
                    if (credBadge) credBadge.textContent = 'Erro';
                    if (container) {
                        container.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle mr-2"></i>Erro ao carregar dados: ${data.message || 'Resposta inválida.'}</div>`;
                    }
                }
            })
            .catch(err => {
                if (credBadge) credBadge.textContent = 'Erro';
                if (container) {
                    container.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle mr-2"></i>Erro de comunicação: ${err.message}</div>`;
                }
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
            const switchRodizio = document.getElementById('realizar-rodizio-switch');
            const helpRodizio = document.getElementById('realizar-rodizio-help');
            const infoText = document.getElementById('presencas-info-text');
            const btnText = document.getElementById('salvar-presencas-btn-text');
            const salvarBtn = document.getElementById('salvar-presencas-btn');
            const navContainer = document.getElementById('presencas-horarios-nav-container');
            const pillsContainer = document.getElementById('presencas-horarios-pills');

            const activeData = currentEscalaData || dataDetails;
            const isCurso = (activeData && (activeData.event_tipo === 'Curso' || activeData.event_tipo === 'Treinamento' || activeData.event_tipo_evento === 'curso'));

            function updateRodizioUI(rodizioAtivo) {
                if (rodizioAtivo) {
                    if (salvarBtn) {
                        salvarBtn.classList.remove('btn-info');
                        salvarBtn.classList.add('btn-success');
                    }
                    if (btnText) btnText.textContent = 'Confirmar Presenças e Atualizar Rodízio';
                    if (helpRodizio) {
                        helpRodizio.innerHTML = '<span class="text-success font-weight-bold"><i class="fas fa-check-circle mr-1"></i> Atividade de Trabalho:</span> Atendentes presentes serão movidos para o final da fila de rodízio (MAX + 1).';
                    }
                    if (infoText) {
                        infoText.innerHTML = '<strong>Apenas os atendentes marcados com presença confirmada</strong> serão movidos para o final da fila de rodízio (MAX + 1). Caso o atendente tenha faltado por doença ou motivo justificado, desmarque a caixa para que a posição dele seja preservada no rodízio.';
                    }
                } else {
                    if (salvarBtn) {
                        salvarBtn.classList.remove('btn-success');
                        salvarBtn.classList.add('btn-info');
                    }
                    if (btnText) btnText.textContent = 'Confirmar Presenças (Sem Alterar Rodízio)';
                    if (helpRodizio) {
                        helpRodizio.innerHTML = isCurso 
                            ? '<span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-graduation-cap mr-1"></i> Curso / Treinamento Detectado:</span> O rodízio vem desligado por padrão para que os alunos não percam sua vez na fila de trabalho.'
                            : '<span class="text-info font-weight-bold"><i class="fas fa-pause-circle mr-1"></i> Rodízio Desativado:</span> As presenças serão registradas normalmente, mas a posição de rodízio de todos os atendentes permanecerá inalterada.';
                    }
                    if (infoText) {
                        infoText.innerHTML = '<strong>Presença sem Rodízio:</strong> As presenças serão registradas para fins de histórico e emissão de atestados/certificados, mas <strong>a posição de rodízio de nenhum participante será modificada</strong>.';
                    }
                }
            }

            if (switchRodizio) {
                // Se for Curso/Treinamento, desmarca por padrão! Se for Trabalho, marca por padrão.
                switchRodizio.checked = !isCurso;
                updateRodizioUI(switchRodizio.checked);

                switchRodizio.onchange = function() {
                    updateRodizioUI(this.checked);
                };
            }

            // Identifica candidatos marcados na tabela (DOM) e/ou no objeto de dados
            const checkedCandIds = new Set();
            document.querySelectorAll('#escala-container input[type="checkbox"]:checked').forEach(cb => {
                if (cb.value) {
                    checkedCandIds.add(String(cb.value));
                }
            });

            const escaladosMap = new Map();
            if (activeData && activeData.candidatos) {
                activeData.candidatos.forEach(c => {
                    let isEscalado = checkedCandIds.has(String(c.candidato_id));
                    if (!isEscalado) {
                        for (const hId in c.horarios_status) {
                            if (c.horarios_status[hId].is_escalado == 1) {
                                isEscalado = true;
                                break;
                            }
                        }
                    }
                    if (!isEscalado && c.presenca_confirmada == 1) {
                        isEscalado = true;
                    }
                    if (isEscalado) {
                        escaladosMap.set(c.candidato_id, c);
                    }
                });
            }

            if (escaladosMap.size === 0) {
                alert('Nenhum atendente está marcado como escalado ou selecionado na tabela. Por favor, marque os participantes na tabela antes de confirmar presenças.');
                return;
            }

            // Função para renderizar a lista de candidatos de acordo com o horário selecionado
            function renderListaPresencas(targetHorarioId) {
                currentSelectedPresencaHorarioId = targetHorarioId;
                let html = '';

                escaladosMap.forEach(c => {
                    const imgUrl = c.url ? AppWebRoot + c.url : AppWebRoot + 'img/ame2023.jpg';
                    const linkAval = c.link_avaliacao ? AppWebRoot + c.link_avaliacao : '#';
                    const fullEvalUrl = window.location.origin + linkAval;

                    // Determina se o candidato compareceu/está marcado presente
                    let isChecked = true;
                    if (targetHorarioId > 0) {
                        if (c.presencas_por_horario && c.presencas_por_horario[targetHorarioId] !== undefined) {
                            isChecked = (c.presencas_por_horario[targetHorarioId] == 1);
                        } else {
                            isChecked = true;
                        }
                    } else {
                        isChecked = (c.presenca_confirmada == 1);
                    }

                    // Seletor de badges de frequência / certificado ou diárias
                    let badgeInfo = '';
                    if (isCurso) {
                        const freq = c.frequencia_pct !== undefined ? c.frequencia_pct : 0;
                        const apto = c.apto_certificado;
                        const presTot = c.total_presencas !== undefined ? c.total_presencas : 0;
                        const escTot = c.total_escalas !== undefined ? c.total_escalas : 0;
                        const badgeClass = apto ? 'badge-success' : (freq > 0 ? 'badge-warning text-dark' : 'badge-secondary');
                        const statusTxt = apto ? 'Apto p/ Certificado' : (freq > 0 ? 'Horas Parciais' : 'Sem Presenças');
                        badgeInfo = `<span class="badge ${badgeClass} ml-2 p-1" style="font-size: 0.8rem;"><i class="fas fa-graduation-cap mr-1"></i>${presTot}/${escTot} aulas (${freq}%) - ${statusTxt}</span>`;
                    } else {
                        const presTot = c.total_presencas !== undefined ? c.total_presencas : (c.presenca_confirmada == 1 ? 1 : 0);
                        const escTot = c.total_escalas !== undefined ? c.total_escalas : 1;
                        const valTotal = c.total_valor_diarias > 0 ? parseFloat(c.total_valor_diarias) : (presTot * parseFloat(activeData.event_valor_diaria_padrao || 200));
                        badgeInfo = `<span class="badge badge-info ml-2 p-1" style="font-size: 0.8rem;"><i class="fas fa-money-bill-wave mr-1"></i>${presTot}/${escTot} turnos | R$ ${valTotal.toFixed(2).replace('.', ',')}</span>`;
                    }

                    const checkLabel = targetHorarioId > 0 ? 'Presente nesta Aula/Turno' : 'Compareceu ao Evento (Geral)';

                    html += `
                        <div class="list-group-item d-flex align-items-center justify-content-between flex-wrap gap-2 py-2 px-3">
                            <div class="d-flex align-items-center">
                                <img src="${imgUrl}" class="rounded-circle mr-3 shadow-sm" width="42" height="42" style="object-fit:cover;" onerror="this.src='${AppWebRoot}img/ame2023.jpg';">
                                <div>
                                    <div class="d-flex align-items-center flex-wrap">
                                        <strong class="text-dark mr-1">${c.nome}</strong>
                                        ${badgeInfo}
                                    </div>
                                    <small class="text-muted">Rodízio: #${c.rodizio} | ${c.ativo == 1 ? 'Atendente' : 'Treinamento'}</small>
                                    ${c.ja_avaliado == 1 ? '<span class="badge badge-warning text-dark ml-1"><i class="fas fa-star mr-1"></i>Já Avaliado</span>' : ''}
                                </div>
                            </div>
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <a href="${linkAval}" target="_blank" class="btn btn-sm btn-outline-info font-weight-bold mr-1" title="Preencher Ficha de Avaliação do Atendente">
                                    <i class="fas fa-star mr-1"></i> Avaliar
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-secondary btn-copy-cand-eval mr-1" data-url="${fullEvalUrl}" title="Copiar link de avaliação deste atendente">
                                    <i class="fas fa-copy"></i>
                                </button>
                                <a href="${AppWebRoot}atestados/${c.candidato_id}" target="_blank" class="btn btn-sm btn-outline-success font-weight-bold mr-2" title="Emitir Atestado de Matrícula ou Certificado deste participante">
                                    <i class="fas fa-certificate mr-1"></i> Atestado
                                </a>
                                <div class="custom-control custom-checkbox custom-control-inline ml-1">
                                    <input type="checkbox" class="custom-control-input presenca-checkbox" id="presenca-cand-${c.candidato_id}" value="${c.candidato_id}" ${isChecked ? 'checked' : ''}>
                                    <label class="custom-control-label font-weight-bold ${isChecked ? 'text-success' : 'text-muted'}" for="presenca-cand-${c.candidato_id}">
                                        <i class="fas fa-check-circle mr-1"></i> ${checkLabel}
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
            }

            // Configuração dos Botões / Pills de Horários / Aulas
            if (activeData.horarios && activeData.horarios.length > 1) {
                if (navContainer) navContainer.classList.remove('d-none');
                if (pillsContainer) {
                    let pillsHtml = `
                        <button type="button" class="btn btn-sm btn-primary font-weight-bold presenca-horario-pill" data-horario-id="0">
                            <i class="fas fa-layer-group mr-1"></i> Todos os Horários (Geral)
                        </button>
                    `;

                    activeData.horarios.forEach((h, idx) => {
                        let dtLabel = '';
                        if (h.data_inicio) {
                            const d = new Date(h.data_inicio.replace(' ', 'T'));
                            if (!isNaN(d)) {
                                dtLabel = d.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
                            }
                        }
                        const prefix = isCurso ? `Aula ${idx + 1}` : `Turno ${idx + 1}`;
                        const labelText = dtLabel ? `${prefix} (${dtLabel})` : `${prefix} #${h.horario_id}`;

                        pillsHtml += `
                            <button type="button" class="btn btn-sm btn-outline-primary font-weight-bold presenca-horario-pill" data-horario-id="${h.horario_id}">
                                <i class="fas fa-clock mr-1"></i> ${labelText}
                            </button>
                        `;
                    });

                    pillsContainer.innerHTML = pillsHtml;

                    pillsContainer.querySelectorAll('.presenca-horario-pill').forEach(pill => {
                        pill.addEventListener('click', function() {
                            pillsContainer.querySelectorAll('.presenca-horario-pill').forEach(p => {
                                p.classList.remove('btn-primary');
                                p.classList.add('btn-outline-primary');
                            });
                            this.classList.remove('btn-outline-primary');
                            this.classList.add('btn-primary');

                            const hId = parseInt(this.getAttribute('data-horario-id')) || 0;
                            renderListaPresencas(hId);
                        });
                    });
                }
                renderListaPresencas(0);
            } else {
                if (navContainer) navContainer.classList.add('d-none');
                const defaultHId = (activeData.horarios && activeData.horarios[0]) ? parseInt(activeData.horarios[0].horario_id) : 0;
                renderListaPresencas(defaultHId);
            }

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

            const switchRodizio = document.getElementById('realizar-rodizio-switch');
            const realizarRodizioVal = switchRodizio ? (switchRodizio.checked ? 1 : 0) : 1;

            // Coleta também os switches da tabela para garantir sincronismo
            const checkedBoxes = document.querySelectorAll('#escala-container input[type="checkbox"]:checked');
            let escaladosObj = {};
            checkedBoxes.forEach(cb => {
                const horarioId = cb.dataset.horarioId;
                if (horarioId) {
                    if (!escaladosObj[horarioId]) escaladosObj[horarioId] = [];
                    escaladosObj[horarioId].push(cb.value);
                }
            });

            salvarPresencasBtn.disabled = true;
            salvarPresencasBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Processando...';

            fetch(AppWebRoot + 'include/api_escala.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'confirmar_presencas',
                    evento_id: eventoId,
                    horario_id: currentSelectedPresencaHorarioId,
                    presencas: presencas,
                    realizar_rodizio: realizarRodizioVal,
                    escalados: escaladosObj
                })
            })
            .then(r => r.json())
            .then(res => {
                salvarPresencasBtn.disabled = false;
                const txt = realizarRodizioVal ? 'Confirmar Presenças e Atualizar Rodízio' : 'Confirmar Presenças (Sem Alterar Rodízio)';
                salvarPresencasBtn.innerHTML = '<i class="fas fa-check-double mr-1"></i> <span id="salvar-presencas-btn-text">' + txt + '</span>';

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
                const txt = realizarRodizioVal ? 'Confirmar Presenças e Atualizar Rodízio' : 'Confirmar Presenças (Sem Alterar Rodízio)';
                salvarPresencasBtn.innerHTML = '<i class="fas fa-check-double mr-1"></i> <span id="salvar-presencas-btn-text">' + txt + '</span>';
                alert('Erro ao processar presenças: ' + err.message);
            });
        });
    }

    function attachDiariasListener(dataDetails) {
        const btn = document.getElementById('diarias-btn');
        if (!btn) return;

        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const activeData = currentEscalaData || dataDetails;
            if (!activeData) return;

            const modalEl = document.getElementById('diariasModal');
            const nomeEl = document.getElementById('diarias-evento-nome');
            const subEl = document.getElementById('diarias-evento-subtitulo');
            const badgeGeral = document.getElementById('diarias-total-geral-badge');
            const valPadraoTxt = document.getElementById('diarias-valor-padrao-txt');
            const tbody = document.getElementById('diarias-tbody');
            const copyResumoBtn = document.getElementById('copy-diarias-resumo-btn');
            const copyTsvBtn = document.getElementById('copy-diarias-tsv-btn');
            const printBtn = document.getElementById('print-diarias-btn');

            const vPadrao = parseFloat(activeData.event_valor_diaria_padrao || 200.00);
            if (nomeEl) nomeEl.textContent = activeData.event_nome || `Evento #${eventoId}`;
            if (subEl) subEl.textContent = `Fechamento de diárias e remuneração da equipe (${activeData.event_tipo || 'Trabalho'}).`;
            if (valPadraoTxt) valPadraoTxt.textContent = `R$ ${vPadrao.toFixed(2).replace('.', ',')}`;

            // Filtra participantes escalados ou com presença confirmada
            const participantes = [];
            if (activeData.candidatos) {
                activeData.candidatos.forEach(c => {
                    let isEscalado = false;
                    for (const hId in c.horarios_status) {
                        if (c.horarios_status[hId].is_escalado == 1) {
                            isEscalado = true;
                            break;
                        }
                    }
                    if (isEscalado || c.presenca_confirmada == 1 || (c.total_presencas && c.total_presencas > 0)) {
                        participantes.push(c);
                    }
                });
            }

            let totalGeral = 0;
            let htmlRows = '';
            let seq = 1;

            participantes.forEach(c => {
                const imgUrl = c.url ? AppWebRoot + c.url : AppWebRoot + 'img/ame2023.jpg';
                const turnosPres = c.total_presencas !== undefined ? c.total_presencas : (c.presenca_confirmada == 1 ? 1 : 0);
                const totalCand = c.total_valor_diarias > 0 ? parseFloat(c.total_valor_diarias) : (turnosPres * vPadrao);
                totalGeral += totalCand;
                const pixVal = (c.PIX && c.PIX.trim() !== '') ? c.PIX.trim() : '';

                htmlRows += `
                    <tr>
                        <td class="text-center font-weight-bold text-muted">${seq++}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="${imgUrl}" class="rounded-circle mr-2 shadow-sm" width="36" height="36" style="object-fit:cover;" onerror="this.src='${AppWebRoot}img/ame2023.jpg';">
                                <div>
                                    <strong class="text-dark d-block">${c.nome}</strong>
                                    <small class="text-muted">CPF: ${c.CPF || 'Não informado'} | ${c.ativo == 1 ? 'Atendente' : 'Treinamento'}</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge ${turnosPres > 0 ? 'badge-success' : 'badge-light border'} px-2 py-1 font-weight-bold">
                                ${turnosPres} turno(s)
                            </span>
                        </td>
                        <td class="text-right font-weight-bold text-muted">
                            R$ ${vPadrao.toFixed(2).replace('.', ',')}
                        </td>
                        <td class="text-right font-weight-bold text-success" style="font-size: 1.05rem;">
                            R$ ${totalCand.toFixed(2).replace('.', ',')}
                        </td>
                        <td>
                            ${pixVal ? `<code class="bg-light px-2 py-1 text-dark border rounded font-weight-bold" style="font-size:0.95rem;">${pixVal}</code>` : '<span class="text-danger small font-italic"><i class="fas fa-exclamation-circle mr-1"></i>PIX não informado</span>'}
                        </td>
                        <td class="text-center">
                            ${pixVal ? `<button type="button" class="btn btn-sm btn-outline-success font-weight-bold btn-copy-pix" data-pix="${pixVal}"><i class="fas fa-copy mr-1"></i> Copiar PIX</button>` : '<button type="button" class="btn btn-sm btn-outline-secondary" disabled>Sem PIX</button>'}
                        </td>
                    </tr>
                `;
            });

            if (participantes.length === 0) {
                htmlRows = '<tr><td colspan="7" class="text-center text-muted py-4">Nenhum participante escalado ou com presença registrada para este evento.</td></tr>';
            }

            if (tbody) tbody.innerHTML = htmlRows;
            if (badgeGeral) badgeGeral.textContent = `Total Geral da Folha: R$ ${totalGeral.toFixed(2).replace('.', ',')}`;

            // Bind copy PIX individual
            document.querySelectorAll('.btn-copy-pix').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const pix = this.getAttribute('data-pix');
                    const orig = this.innerHTML;
                    const el = this;
                    navigator.clipboard.writeText(pix).then(() => {
                        el.innerHTML = '<i class="fas fa-check text-success mr-1"></i> Copiado!';
                        setTimeout(() => { el.innerHTML = orig; }, 2000);
                    });
                });
            });

            // Copy resumo WhatsApp / Financeiro
            if (copyResumoBtn) {
                copyResumoBtn.onclick = function() {
                    let msg = `*FOLHA DE FECHAMENTO / DIÁRIAS PIX*\n`;
                    msg += `*Evento:* ${activeData.event_nome || 'Evento'}\n`;
                    msg += `*Total Geral:* R$ ${totalGeral.toFixed(2).replace('.', ',')}\n`;
                    msg += `*Data:* ${new Date().toLocaleDateString('pt-BR')}\n\n`;
                    let s = 1;
                    participantes.forEach(c => {
                        const turnosPres = c.total_presencas !== undefined ? c.total_presencas : (c.presenca_confirmada == 1 ? 1 : 0);
                        const totalCand = c.total_valor_diarias > 0 ? parseFloat(c.total_valor_diarias) : (turnosPres * vPadrao);
                        const pixVal = (c.PIX && c.PIX.trim() !== '') ? c.PIX.trim() : 'NÃO INFORMADO';
                        msg += `${s++}. *${c.nome}*\n   Turnos: ${turnosPres} | Total: R$ ${totalCand.toFixed(2).replace('.', ',')}\n   Chave PIX: \`${pixVal}\`\n\n`;
                    });
                    navigator.clipboard.writeText(msg).then(() => {
                        const orig = copyResumoBtn.innerHTML;
                        copyResumoBtn.innerHTML = '<i class="fas fa-check text-success mr-1"></i> Resumo Copiado!';
                        setTimeout(() => { copyResumoBtn.innerHTML = orig; }, 2500);
                    });
                };
            }

            // Copy TSV (Excel)
            if (copyTsvBtn) {
                copyTsvBtn.onclick = function() {
                    let tsv = "Seq\tNome\tCPF\tTurnos\tValor_Unitario\tTotal\tChave_PIX\n";
                    let s = 1;
                    participantes.forEach(c => {
                        const turnosPres = c.total_presencas !== undefined ? c.total_presencas : (c.presenca_confirmada == 1 ? 1 : 0);
                        const totalCand = c.total_valor_diarias > 0 ? parseFloat(c.total_valor_diarias) : (turnosPres * vPadrao);
                        const pixVal = (c.PIX && c.PIX.trim() !== '') ? c.PIX.trim() : '';
                        tsv += `${s++}\t${c.nome}\t${c.CPF || ''}\t${turnosPres}\tR$ ${vPadrao.toFixed(2)}\tR$ ${totalCand.toFixed(2)}\t${pixVal}\n`;
                    });
                    navigator.clipboard.writeText(tsv).then(() => {
                        const orig = copyTsvBtn.innerHTML;
                        copyTsvBtn.innerHTML = '<i class="fas fa-check text-success mr-1"></i> Tabela Copiada!';
                        setTimeout(() => { copyTsvBtn.innerHTML = orig; }, 2500);
                    });
                };
            }

            // Print Folha
            if (printBtn) {
                printBtn.onclick = function() {
                    const tableContents = document.getElementById('diarias-table-container').innerHTML;
                    const origContents = document.body.innerHTML;
                    document.body.innerHTML = `
                        <div style="padding: 20px; font-family: Arial, sans-serif;">
                            <h2>Folha de Fechamento de Diárias (PIX) - ${activeData.event_nome}</h2>
                            <p><strong>Total Geral:</strong> R$ ${totalGeral.toFixed(2).replace('.', ',')} | <strong>Data:</strong> ${new Date().toLocaleDateString('pt-BR')}</p>
                            ${tableContents}
                        </div>
                    `;
                    window.print();
                    document.body.innerHTML = origContents;
                    location.reload();
                };
            }

            if (window.jQuery && typeof $('#diariasModal').modal === 'function') {
                $('#diariasModal').modal('show');
            } else if (modalEl) {
                modalEl.classList.add('show');
                modalEl.style.display = 'block';
                modalEl.style.backgroundColor = 'rgba(0,0,0,0.5)';
            }
        });
    }

    document.querySelectorAll('#diariasModal [data-dismiss="modal"]').forEach(btn => {
        btn.addEventListener('click', function() {
            if (window.jQuery && typeof $('#diariasModal').modal === 'function') {
                $('#diariasModal').modal('hide');
            } else {
                const modalEl = document.getElementById('diariasModal');
                if (modalEl) {
                    modalEl.classList.remove('show');
                    modalEl.style.display = 'none';
                }
            }
        });
    });

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
