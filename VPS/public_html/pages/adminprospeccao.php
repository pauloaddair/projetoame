<?php
/**
 * Prospecção de Eventos e Captação de Leads via IA
 */

// Check authentication
if (!isset($_SESSION['id']) || $_SESSION['id'] === "") {
    include_once('restrito.php');
    exit;
}

$titulo = "CRM - Prospecção & Captação Inteligente";

// Calculate Statistics
$cnt_total = mysqli_query($conexao, "SELECT COUNT(*) as qtd FROM eventos");
$qtd_total = mysqli_fetch_assoc($cnt_total)['qtd'] ?? 0;

$cnt_confirmadas = mysqli_query($conexao, "SELECT COUNT(*) as qtd FROM eventos WHERE `Data Confirmada` = 1");
$qtd_confirmadas = mysqli_fetch_assoc($cnt_confirmadas)['qtd'] ?? 0;

$cnt_leads = mysqli_query($conexao, "SELECT COUNT(*) as qtd FROM leads_expositores");
$qtd_leads = mysqli_fetch_assoc($cnt_leads)['qtd'] ?? 0;

// Fetch events list
$query_eventos = "SELECT e.*, 
                  (SELECT COUNT(*) FROM leads_expositores WHERE evento_id = e.evento_id) as qtd_leads 
                  FROM eventos e 
                  ORDER BY e.Inicio IS NULL DESC, e.Inicio ASC, e.evento_id DESC";
$result_eventos = mysqli_query($conexao, $query_eventos);
?>

<?php include_once('./include/nav.php'); ?>
<?php include_once('./include/admin_sidebar.php'); ?>
<main class="flex-shrink-0">
    <div class="container mt-4">
        <header class="pt-4 mt-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-2">
                    <li class="breadcrumb-item"><a href="<?php echo $app_web_root; ?>admin">Painel</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Prospecção de Eventos</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <div>
                    <h1 class="h2 mb-1 font-weight-bold text-gray-800"><i class="fas fa-search-dollar text-primary"></i> Prospecção & Captação Inteligente</h1>
                    <p class="text-muted mb-0">Gerencie feiras e utilize IA para buscar datas e expositores automaticamente.</p>
                </div>
                <div class="mt-2">
                    <button class="btn btn-primary btn-md rounded-pill shadow-sm" type="button" data-toggle="collapse" data-target="#collapseNovoEvento" aria-expanded="false" aria-controls="collapseNovoEvento">
                        <i class="fas fa-plus"></i> Novo Evento
                    </button>
                    <button id="btnAtualizarFila" class="btn btn-outline-primary btn-md rounded-pill shadow-sm ml-2" type="button">
                        <i class="fas fa-sync-alt"></i> Atualizar Próximos 5 (Lote AI)
                    </button>
                </div>
            </div>
        </header>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-xl-4 col-md-6 mb-3">
                <div class="card border-left-primary shadow-sm h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total de Eventos</div>
                                <div class="h4 mb-0 font-weight-bold text-gray-800"><?= number_format($qtd_total, 0, ',', '.') ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 mb-3">
                <div class="card border-left-success shadow-sm h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Datas Confirmadas</div>
                                <div class="h4 mb-0 font-weight-bold text-gray-800"><?= number_format($qtd_confirmadas, 0, ',', '.') ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 mb-3">
                <div class="card border-left-info shadow-sm h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Leads / Expositores Prospectados</div>
                                <div class="h4 mb-0 font-weight-bold text-gray-800" id="totalLeadsCount"><?= number_format($qtd_leads, 0, ',', '.') ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users-cog fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Novo Evento Colapsável -->
        <div class="collapse mb-4" id="collapseNovoEvento">
            <div class="card card-body border-0 shadow-sm p-4">
                <h5 class="card-title font-weight-bold text-gray-800 mb-3"><i class="fas fa-calendar-plus text-primary"></i> Cadastrar Novo Evento para Prospecção</h5>
                <form id="formNovoEvento" method="POST">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="nome" class="font-weight-bold text-secondary">Nome do Evento</label>
                            <input type="text" class="form-control" id="nome" name="nome" placeholder="Ex: FESPA Brasil" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="local" class="font-weight-bold text-secondary">Local (Pavilhão/Espaço)</label>
                            <input type="text" class="form-control" id="local" name="local" placeholder="Ex: Expo Center Norte">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label for="data" class="font-weight-bold text-secondary">Data Prevista / Início</label>
                            <input type="date" class="form-control" id="data" name="data">
                        </div>
                        <div class="col-md-8 form-group">
                            <label for="site" class="font-weight-bold text-secondary">Site Oficial</label>
                            <input type="url" class="form-control" id="site" name="site" placeholder="Ex: https://www.fespabrasil.com.br">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-2">
                        <button type="button" class="btn btn-secondary rounded-pill mr-2" data-toggle="collapse" data-target="#collapseNovoEvento">Cancelar</button>
                        <button type="submit" class="btn btn-primary rounded-pill">Salvar Evento</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabela de Eventos -->
        <div class="card border-0 shadow-sm mb-5">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="m-0 font-weight-bold text-gray-800"><i class="fas fa-list-ul"></i> Feiras & Eventos Cadastrados</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="tabelaEventos">
                        <thead class="bg-light">
                            <tr>
                                <th>Evento</th>
                                <th>Local</th>
                                <th class="text-center">Data Início</th>
                                <th class="text-center">Data Fim</th>
                                <th class="text-center">Status Data</th>
                                <th class="text-center">Leads</th>
                                <th class="text-right">Ações Inteligentes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result_eventos) > 0): ?>
                                <?php while($e = mysqli_fetch_assoc($result_eventos)): 
                                    $evento_id = intval($e['evento_id']);
                                    $has_dates = !empty($e['Inicio']) && !empty($e['Final']);
                                    $is_confirmed = intval($e['Data Confirmada']) === 1;
                                    
                                    $status_badge = "<span class='badge badge-danger'>Pendente (Sem data)</span>";
                                    if ($has_dates) {
                                        $status_badge = $is_confirmed 
                                            ? "<span class='badge badge-success'><i class='fas fa-check'></i> Confirmada</span>" 
                                            : "<span class='badge badge-warning'>Previsão</span>";
                                    }
                                ?>
                                <tr id="row-evento-<?= $evento_id ?>">
                                    <td class="font-weight-bold align-middle">
                                        <?= htmlspecialchars($e['Evento']) ?>
                                        <?php if (!empty($e['SITE'])): ?>
                                            <a href="<?= htmlspecialchars($e['SITE']) ?>" target="_blank" class="ml-1 text-muted text-xs" title="Visitar site oficial">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle"><?= htmlspecialchars($e['Local'] ?? '-') ?></td>
                                    <td class="text-center align-middle font-weight-bold date-inicio">
                                        <?= !empty($e['Inicio']) ? date('d/m/Y', strtotime($e['Inicio'])) : '-' ?>
                                    </td>
                                    <td class="text-center align-middle font-weight-bold date-final">
                                        <?= !empty($e['Final']) ? date('d/m/Y', strtotime($e['Final'])) : '-' ?>
                                    </td>
                                    <td class="text-center align-middle status-data"><?= $status_badge ?></td>
                                    <td class="text-center align-middle font-weight-bold text-primary leads-count">
                                        <?= intval($e['qtd_leads']) ?>
                                    </td>
                                    <td class="text-right align-middle">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-info btn-sm btn-atualizar-data" data-id="<?= $evento_id ?>" title="Atualizar data por IA">
                                                <i class="fas fa-calendar-day"></i> AI Datas
                                            </button>
                                            <button type="button" class="btn btn-outline-primary btn-sm btn-buscar-expositores" data-id="<?= $evento_id ?>" title="Buscar expositores por IA">
                                                <i class="fas fa-robot"></i> AI Expositores
                                            </button>
                                            <button type="button" class="btn btn-info btn-sm btn-expand-leads" data-id="<?= $evento_id ?>" data-expanded="false" title="Ver Leads">
                                                <i class="fas fa-eye"></i> Leads
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Nenhum evento cadastrado para prospecção.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal Registrar Contato -->
<div class="modal fade" id="modalContato" tabindex="-1" role="dialog" aria-labelledby="modalContatoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formRegistrarContato" method="POST">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold" id="modalContatoLabel"><i class="fas fa-phone-alt"></i> Registrar Interação / Contato</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="expositor_id" id="modalLeadId">
                    <div class="form-group">
                        <label class="font-weight-bold text-secondary">Expositor / Lead</label>
                        <input type="text" class="form-control" id="modalLeadName" readonly>
                    </div>
                    <div class="form-group">
                        <label for="tipo_contato" class="font-weight-bold text-secondary">Tipo de Contato</label>
                        <select name="tipo_contato" id="tipo_contato" class="form-control" required>
                            <option value="whatsapp">WhatsApp</option>
                            <option value="telefone">Telefone</option>
                            <option value="email">E-mail</option>
                            <option value="instagram">Instagram</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="data_contato" class="font-weight-bold text-secondary">Data do Contato</label>
                        <input type="date" name="data_contato" id="data_contato" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="observacao" class="font-weight-bold text-secondary">Observação / Comentário</label>
                        <textarea name="observacao" id="observacao" class="form-control" rows="3" placeholder="Resumo do contato, propostas enviadas, etc..." required></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="status" class="font-weight-bold text-secondary">Status do Lead</label>
                            <select name="status" id="status" class="form-control">
                                <option value="em andamento">Em Andamento</option>
                                <option value="convertido">Convertido (Fechado)</option>
                                <option value="perdido">Perdido</option>
                                <option value="pendente">Pendente</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="data_followup" class="font-weight-bold text-secondary">Data de Follow-up (Retorno)</label>
                            <input type="date" name="data_followup" id="data_followup" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-pill" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary rounded-pill">Salvar Registro</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    
    // Inicializar DataTable
    var tabela = $('#tabelaEventos').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json"
        },
        "order": [[ 4, "desc" ], [ 0, "asc" ]], // Ordenar por status (Badge de data) e depois por nome
        "columnDefs": [
            { "orderable": false, "targets": [1, 5, 6] } // Desativa ordenação para Local, Leads e Ações
        ],
        "pageLength": 10,
        "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "Todos"] ]
    });
    
    // Cadastrar Novo Evento via AJAX
    $('#formNovoEvento').submit(function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Salvando...');
        
        $.ajax({
            url: '<?php echo $app_web_root; ?>include/cadastra_evento.php',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Sucesso: ' + response.message);
                    location.reload();
                } else {
                    alert('Erro: ' + response.message);
                    submitBtn.prop('disabled', false).text('Salvar Evento');
                }
            },
            error: function() {
                alert('Erro na requisição. Verifique a conexão.');
                submitBtn.prop('disabled', false).text('Salvar Evento');
            }
        });
    });

    // Atualizar datas de um evento por IA (Individual)
    $('.btn-atualizar-data').click(function(e) {
        e.preventDefault();
        var btn = $(this);
        var id = btn.data('id');
        var row = $('#row-evento-' + id);
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Atualizando...');
        
        $.ajax({
            url: '<?php echo $app_web_root; ?>include/api_update_event_date_single.php',
            type: 'POST',
            data: { evento_id: id },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Datas de "' + response.evento + '" atualizadas via IA!\nInício: ' + response.inicio + '\nFim: ' + response.final);
                    
                    // Update layout
                    var clean_start = response.inicio.split('-').reverse().join('/');
                    var clean_final = response.final.split('-').reverse().join('/');
                    row.find('.date-inicio').text(clean_start);
                    row.find('.date-final').text(clean_final);
                    row.find('.status-data').html('<span class="badge badge-success"><i class="fas fa-check"></i> Confirmada</span>');
                    
                    btn.removeClass('btn-outline-info').addClass('btn-success').html('<i class="fas fa-check"></i> Atualizado');
                } else {
                    alert('Não foi possível encontrar datas para este evento ou ocorreu um erro:\n' + response.message);
                    btn.prop('disabled', false).html('<i class="fas fa-calendar-day"></i> AI Datas');
                }
            },
            error: function() {
                alert('Ocorreu um erro ao processar. Verifique a rede.');
                btn.prop('disabled', false).html('<i class="fas fa-calendar-day"></i> AI Datas');
            }
        });
    });

    // Buscar expositores por IA
    $('.btn-buscar-expositores').click(function(e) {
        e.preventDefault();
        var btn = $(this);
        var id = btn.data('id');
        var row = $('#row-evento-' + id);
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Buscando...');
        
        $.ajax({
            url: '<?php echo $app_web_root; ?>include/api_find_exhibitors.php',
            type: 'POST',
            data: { evento_id: id },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var msg = 'Busca por expositores de "' + response.evento + '" concluída!\n\n' +
                              'Extraídos: ' + response.extracted_count + ' empresas.\n' +
                              'Novas inseridas: ' + response.inserted_count + ' leads.\n' +
                              'Duplicadas ignoradas: ' + response.duplicates_count + '.';
                    alert(msg);
                    
                    // Update counts
                    row.find('.leads-count').text(parseInt(row.find('.leads-count').text()) + response.inserted_count);
                    
                    // If expanded, reload leads list
                    var trEvent = $('#row-evento-' + id);
                    var datatableRow = tabela.row(trEvent);
                    if (datatableRow.child.isShown()) {
                        carregarLeads(id);
                    }
                    
                    btn.removeClass('btn-outline-primary').addClass('btn-success').html('<i class="fas fa-check"></i> Capturado');
                } else {
                    alert('Erro na extração de expositores: ' + response.message);
                    btn.prop('disabled', false).html('<i class="fas fa-robot"></i> AI Expositores');
                }
            },
            error: function() {
                alert('Erro ao processar busca de expositores.');
                btn.prop('disabled', false).html('<i class="fas fa-robot"></i> AI Expositores');
            }
        });
    });

    // Expandir e carregar lista de leads via DataTables Child Rows
    $('#tabelaEventos tbody').on('click', '.btn-expand-leads', function(e) {
        e.preventDefault();
        var btn = $(this);
        var id = btn.data('id');
        var tr = btn.closest('tr');
        var row = tabela.row(tr);
        var isExpanded = btn.attr('data-expanded') === 'true';
        
        if (isExpanded) {
            row.child.hide();
            tr.removeClass('shown');
            btn.attr('data-expanded', 'false').removeClass('btn-dark').addClass('btn-info').html('<i class="fas fa-eye"></i> Leads');
        } else {
            // Cria o container dinamicamente
            var childHtml = '<div class="leads-container p-2 bg-light border rounded" id="leads-container-' + id + '">' +
                            '<div class="text-center text-muted py-2"><i class="fas fa-spinner fa-spin"></i> Carregando expositores...</div>' +
                            '</div>';
            row.child(childHtml).show();
            tr.addClass('shown');
            btn.attr('data-expanded', 'true').removeClass('btn-info').addClass('btn-dark').html('<i class="fas fa-eye-slash"></i> Ocultar');
            carregarLeads(id);
        }
    });

    function carregarLeads(eventoId) {
        var container = $('#leads-container-' + eventoId);
        container.html('<div class="text-center text-muted py-2"><i class="fas fa-spinner fa-spin"></i> Carregando expositores via banco...</div>');
        
        $.ajax({
            url: '<?php echo $app_web_root; ?>include/api_get_leads_by_event.php',
            type: 'GET',
            data: { evento_id: eventoId },
            success: function(html) {
                container.html(html);
                
                // Bind click event for dynamic generated registrar-contato buttons
                container.find('.btn-registrar-contato').click(function() {
                    var leadId = $(this).data('lead-id');
                    var leadName = $(this).data('lead-name');
                    abrirModalContato(leadId, leadName);
                });
            },
            error: function() {
                container.html('<div class="alert alert-danger">Erro ao carregar os expositores do evento.</div>');
            }
        });
    }

    // Modal Contato Handler
    function abrirModalContato(leadId, leadName) {
        $('#modalLeadId').val(leadId);
        $('#modalLeadName').val(leadName);
        $('#observacao').val('');
        $('#data_followup').val('');
        $('#modalContato').modal('show');
    }

    // Registrar Contato Submit via AJAX
    $('#formRegistrarContato').submit(function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Salvando...');
        
        $.ajax({
            url: '<?php echo $app_web_root; ?>include/registra_contato.php',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    $('#modalContato').modal('hide');
                    
                    // Reload leads of the active expanded rows
                    $('.btn-expand-leads[data-expanded="true"]').each(function() {
                        var id = $(this).data('id');
                        carregarLeads(id);
                    });
                } else {
                    alert('Erro: ' + response.message);
                }
                submitBtn.prop('disabled', false).text('Salvar Registro');
            },
            error: function() {
                alert('Erro na requisição. Contato não registrado.');
                submitBtn.prop('disabled', false).text('Salvar Registro');
            }
        });
    });

    // Atualizar Lote de 5 (Fila AI)
    $('#btnAtualizarFila').click(function(e) {
        e.preventDefault();
        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processando fila...');
        
        $.ajax({
            url: '<?php echo $app_web_root; ?>include/update_events_dates.php',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Processamento concluído!\n\nProcessados: ' + response.total_processed + '\nAtualizados com sucesso: ' + response.updated_count);
                    location.reload();
                } else {
                    alert('Erro no processamento da fila: ' + response.message);
                    btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Atualizar Próximos 5 (Lote AI)');
                }
            },
            error: function() {
                alert('Ocorreu um erro de conexão. Tente novamente.');
                btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Atualizar Próximos 5 (Lote AI)');
            }
        });
    });

});
</script>
<?php include_once('./include/admin_sidebar_footer.php'); ?>
