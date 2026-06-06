<?php
// include_once('./include/conexao.php');
// include_once('./include/funcoes.php');
include_once('./include/head-table.php');
/*
$query = "SELECT 
    data_realizada,
    descricao,
    valor_realizado,
    @saldo := @saldo + valor_realizado AS saldo_acumulado
FROM (
    SELECT 
        'Saldo Anterior' AS descricao,
        NULL AS data_realizada,
        IFNULL(SUM(valor_realizado), 0) AS valor_realizado
    FROM 
        contabil_movimento
    WHERE 
        data_realizada < '2024-10-01'
    
    UNION ALL
    
    SELECT 
        descricao,
        data_realizada,
        valor_realizado
    FROM 
        contabil_movimento
    WHERE 
        YEAR(data_realizada) = 2024 
        AND MONTH(data_realizada) = 10
    ORDER BY 
        data_realizada
) AS extrato,
(SELECT @saldo := 0) AS inicializador;";
*/
$query = "SELECT 
    mes_ano,
    total_valor,
	qtd_itens,
    @saldo_acumulado := @saldo_acumulado + total_valor AS saldo_final
FROM (
    SELECT 
        DATE_FORMAT(data_prevista, '%Y-%m') AS mes_ano,
        SUM(valor_previsto) AS total_valor,
        COUNT(valor_previsto) AS qtd_itens
    FROM 
        contabil_movimento
    GROUP BY 
        YEAR(data_prevista), 
        MONTH(data_prevista)
    ORDER BY 
        data_prevista ASC
) AS movimentos,
(SELECT @saldo_acumulado := 0) AS inicializador;";
$result = mysqli_query($conexao,$query);
$query = 'SELECT sum(valor_previsto) AS saldo 
FROM `contabil_movimento`;';
$saldo_total = mysqli_query($conexao,$query);
$saldo = mysqli_fetch_array($saldo_total);
$total = $saldo['saldo'];
/*
$query = 'SELECT sum(valor_realizado) AS saldo 
FROM contabil_movimento;';
$saldo_atual = mysqli_query($conexao,$query);
$saldo = mysqli_fetch_array($saldo_atual);
$atual = $saldo['saldo'];
*/
$query = 'SELECT count(id) AS pendencias 
FROM `contabil_movimento` WHERE valor_realizado IS Null;';
$resultado = mysqli_query($conexao,$query);
$pendencias = mysqli_fetch_array($resultado);
$pendente = $pendencias['pendencias'];

// A PAGAR
$query = "SELECT SUM(valor_previsto) AS pagar, COUNT(valor_previsto) AS qtd
FROM contabil_movimento 
WHERE valor_previsto <= 0 
AND data_prevista > NOW();";
$apagar = mysqli_query($conexao,$query);
$pagar = mysqli_fetch_array($apagar);

// A RECEBER
$query = "SELECT SUM(valor_previsto) AS receber, COUNT(valor_previsto) AS qtd
FROM contabil_movimento 
WHERE valor_previsto > 0 
AND data_prevista > NOW();";
$areceber = mysqli_query($conexao,$query);
$receber = mysqli_fetch_array($areceber);
$atual = $total-$pagar['pagar']-$receber['receber'];

// --- ATIVIDADES (PRÓXIMAS ATIVIDADES) ---
$result_atividades = false;
try {
    $query_atividades = "SELECT em.*, le.whatsapp, le.telefone, le.contato AS expositor_contato, le.nome AS expositor_nome 
                         FROM eventos_marcados em 
                         LEFT JOIN leads_expositores le ON em.expositor_id = le.id 
                         WHERE em.inicio >= NOW() 
                         ORDER BY em.inicio ASC LIMIT 5";
    $result_atividades = mysqli_query($conexao, $query_atividades);
} catch (Exception $e) {
    $result_atividades = false;
} catch (Throwable $t) {
    $result_atividades = false;
}

if (!$result_atividades) {
    // If the join fails due to database discrepancy, query just eventos_marcados
    try {
        $query_atividades = "SELECT * FROM eventos_marcados WHERE inicio >= NOW() ORDER BY inicio ASC LIMIT 5";
        $result_atividades = mysqli_query($conexao, $query_atividades);
    } catch (Exception $e) {
        $result_atividades = false;
    } catch (Throwable $t) {
        $result_atividades = false;
    }
}

if (!$result_atividades || mysqli_num_rows($result_atividades) == 0) {
    // Fallback local: show latest activities in DB
    $fallback_success = false;
    try {
        $query_atividades = "SELECT em.*, le.whatsapp, le.telefone, le.contato AS expositor_contato, le.nome AS expositor_nome 
                             FROM eventos_marcados em 
                             LEFT JOIN leads_expositores le ON em.expositor_id = le.id 
                             ORDER BY em.inicio DESC LIMIT 5";
        $result_atividades = mysqli_query($conexao, $query_atividades);
        $fallback_success = true;
    } catch (Exception $e) {
        $fallback_success = false;
    } catch (Throwable $t) {
        $fallback_success = false;
    }
    
    if (!$fallback_success) {
        try {
            $query_atividades = "SELECT * FROM eventos_marcados ORDER BY inicio DESC LIMIT 5";
            $result_atividades = mysqli_query($conexao, $query_atividades);
        } catch (Exception $e) {
            $result_atividades = false;
        } catch (Throwable $t) {
            $result_atividades = false;
        }
    }
}

// --- PROSPECÇÃO & NEGOCIAÇÕES ---
$query_cnt_eventos = "SELECT COUNT(*) as qtd FROM eventos";
$resp_cnt_eventos = mysqli_query($conexao, $query_cnt_eventos);
$qtd_eventos_cadastrados = 0;
if ($resp_cnt_eventos) {
    $row_cnt_eventos = mysqli_fetch_assoc($resp_cnt_eventos);
    $qtd_eventos_cadastrados = $row_cnt_eventos['qtd'];
}

$query_cnt_negociacoes = "SELECT COUNT(DISTINCT e.id) as qtd FROM leads_expositores e JOIN leads_contatos c ON c.expositor_id = e.id WHERE c.status = 'em andamento'";
$resp_cnt_negociacoes = mysqli_query($conexao, $query_cnt_negociacoes);
$qtd_negociacoes = 0;
if ($resp_cnt_negociacoes) {
    $row_cnt_negociacoes = mysqli_fetch_assoc($resp_cnt_negociacoes);
    $qtd_negociacoes = $row_cnt_negociacoes['qtd'];
}

$query_list_neg = "SELECT DISTINCT e.id as lead_id, e.nome as empresa, e.whatsapp, e.contato, c.observacao, c.data_followup 
                   FROM leads_expositores e 
                   JOIN leads_contatos c ON c.expositor_id = e.id 
                   WHERE c.status = 'em andamento' 
                   ORDER BY c.data_update DESC LIMIT 5";
$result_list_neg = mysqli_query($conexao, $query_list_neg);
if (!$result_list_neg || mysqli_num_rows($result_list_neg) == 0) {
    // Fallback local: show latest leads
    $query_list_neg = "SELECT DISTINCT e.id as lead_id, e.nome as empresa, e.whatsapp, e.contato, c.observacao, c.data_followup 
                       FROM leads_expositores e 
                       JOIN leads_contatos c ON c.expositor_id = e.id 
                       ORDER BY c.data_update DESC LIMIT 5";
    $result_list_neg = mysqli_query($conexao, $query_list_neg);
}
if (!$result_list_neg) {
    // Fallback: select just from leads_expositores
    $query_list_neg = "SELECT id as lead_id, nome as empresa, whatsapp, contato, '' as observacao, NULL as data_followup FROM leads_expositores ORDER BY id DESC LIMIT 5";
    $result_list_neg = mysqli_query($conexao, $query_list_neg);
}
?>
<body>
    <?php 
    include_once('./include/nav.php');
    ?>
    <div class="container mt-5">
        <?php 
        if ($_SESSION['id']<>""){
        ?>
        <header class="mb-4">
            <h1 class="display-4 font-weight-bold">Balanço Geral</h1>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb bg-light p-3 rounded shadow-sm">
                <li class="breadcrumb-item active" aria-current="page">Geral</li>
                <li class="breadcrumb-item"><a href="/admin/saldo/atual">Saldo atual</a></li>
                <li class="breadcrumb-item"><a href="/admin/receber">A receber</a></li>
                <li class="breadcrumb-item"><a href="/admin/pagar">A pagar</a></li>
                <li class="breadcrumb-item"><a href="/admin/saldo/final">Saldo final</a></li>
              </ol>
            </nav>
        </header>

        <div class="row wow fadeIn animated">
            <!-- Coluna Principal (Financeira) -->
            <div class="col-md-12 col-lg-8 mb-4">
                
                <!-- Cards Financeiros Internos -->
                <div class="row mb-4">
                    <div class="col-sm-6 mb-3">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            <a href="/admin/saldo/atual">Saldo Atual</a>
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            R$ <?php echo number_format($atual,2,",",".")?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <a href="/admin/saldo/atual">
                                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 mb-3">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            <a href="/admin/pagar">A Pagar</a>
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            R$ <?php echo number_format(abs($pagar['pagar']),2,",",".")?>
                                            <small class="text-muted d-block">(<?php echo $pagar['qtd']?> lançamentos)</small>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <a href="/admin/pagar">
                                            <i class="fas fa-pen-square fa-2x text-gray-300"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 mb-3">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            <a href="/admin/receber">A Receber</a>
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            R$ <?php echo number_format($receber['receber'],2,",",".")?>
                                            <small class="text-muted d-block">(<?php echo $receber['qtd']?> lançamentos)</small>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <a href="/admin/receber">
                                            <i class="fas fa-user fa-2x text-gray-300"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 mb-3">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            <a href="/admin/saldo/final">Saldo Final</a>
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            R$ <?php echo number_format($total,2,",",".")?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <a href="/admin/saldo/final">
                                            <i class="fas fa-building fa-2x text-gray-300"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráfico com Layout Aprimorado -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-light">
                        <h6 class="m-0 font-weight-bold text-primary">Evolução do Saldo Mensal</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area" style="position: relative; height: 320px;">
                            <canvas id="saldoChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Tabela Financeira -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-light">
                        <h6 class="m-0 font-weight-bold text-primary">Fluxo Mensal de Lançamentos</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table" class="table table-striped table-hover w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Período (ano/mês)</th>
                                        <th class="text-right">Total</th>
                                        <th class="text-right">Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php 
                                $i=1;
                                mysqli_data_seek($result, 0); // Reset result pointer if it was read before
                                while ($row=mysqli_fetch_array($result)){
                                    echo "<tr>
                                        <td>".$i."</td>
                                        <td>
                                            <a href='/admin/extrato/".before('-',$row['mes_ano'])."/".after('-',$row['mes_ano'])."'>
                                                ".$row['mes_ano']." (".strtoupper(mes(intval(after('-',$row['mes_ano']))-1)).") 
                                                <i class='fa fa-arrow-right ml-1 text-info'></i>
                                            </a>
                                        </td>
                                        <td class='text-right'>".number_format($row['total_valor'],2,",",".")." (".$row['qtd_itens'].")</td>
                                        <td class='text-right'>".number_format($row['saldo_final'],2,",",".")."</td>
                                    </tr>";
                                    $i++;
                                }	
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Coluna Lateral -->
            <div class="col-md-12 col-lg-4 mb-4">
                
                <!-- Card 1: Próximas Atividades -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-light d-flex align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-calendar-alt mr-2"></i>Próximas Atividades
                        </h6>
                        <span class="badge badge-secondary"><?php echo $result_atividades ? mysqli_num_rows($result_atividades) : 0; ?></span>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                        <?php if (!$result_atividades || mysqli_num_rows($result_atividades) == 0): ?>
                            <div class="p-3 text-muted text-center text-xs">Nenhuma atividade agendada.</div>
                        <?php else: ?>
                            <?php while ($atividade = mysqli_fetch_assoc($result_atividades)): ?>
                                <?php 
                                $escala_fechada = intval($atividade['escala_fechada'] ?? 0);
                                $badge_class = ($escala_fechada === 1) ? 'badge-success' : 'badge-warning';
                                $badge_text = ($escala_fechada === 1) ? 'Escala Fechada' : 'Pendente';
                                
                                // Format date/time
                                $start_time = strtotime($atividade['inicio'] ?? 'now');
                                $formatted_date = date('d/m/Y H:i', $start_time);
                                
                                // Determine the phone
                                $act_phone = !empty($atividade['whatsapp']) ? $atividade['whatsapp'] : (!empty($atividade['telefone']) ? $atividade['telefone'] : '');
                                ?>
                                <div class="list-group-item p-3">
                                    <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0 font-weight-bold text-gray-800 text-xs"><?php echo htmlspecialchars($atividade['nome'] ?? 'Sem nome'); ?></h6>
                                        <span class="badge <?php echo $badge_class; ?>" style="font-size: 0.75rem;"><?php echo $badge_text; ?></span>
                                    </div>
                                    <p class="text-xs text-muted mb-2">
                                        <i class="fas fa-map-marker-alt mr-1"></i><?php echo htmlspecialchars($atividade['local'] ?? 'Não informado'); ?><br>
                                        <i class="fas fa-clock mr-1"></i><?php echo $formatted_date; ?>
                                    </p>
                                    <?php if ($escala_fechada !== 1): ?>
                                        <button class="btn btn-sm btn-outline-secondary btn-enviar-lembrete w-100" 
                                                data-phone="<?php echo htmlspecialchars($act_phone); ?>" 
                                                data-event-name="<?php echo htmlspecialchars($atividade['nome'] ?? ''); ?>"
                                                data-event-date="<?php echo $formatted_date; ?>">
                                            <i class="fab fa-whatsapp mr-1 text-success"></i> Enviar Lembrete
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php endwhile; ?>
                        <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Prospecção & Negociações -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-light">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-users-cog mr-2"></i>Prospecção & Negociações
                        </h6>
                    </div>
                    <div class="card-body">
                        <!-- Métricas Rápidas -->
                        <div class="row text-center mb-3">
                            <div class="col-6 border-right">
                                <div class="text-xs font-weight-bold text-muted text-uppercase" style="font-size: 0.7rem;">Eventos no Cadastro</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $qtd_eventos_cadastrados; ?></div>
                            </div>
                            <div class="col-6">
                                <div class="text-xs font-weight-bold text-muted text-uppercase" style="font-size: 0.7rem;">Negociações Ativas</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $qtd_negociacoes; ?></div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="list-group list-group-flush mt-2">
                            <h6 class="font-weight-bold text-xs text-muted text-uppercase mb-2" style="font-size: 0.7rem;">Últimas Negociações Ativas</h6>
                            <?php if (!$result_list_neg || mysqli_num_rows($result_list_neg) == 0): ?>
                                <div class="text-muted text-center text-xs py-2">Nenhuma negociação em andamento.</div>
                            <?php else: ?>
                                <?php while ($neg = mysqli_fetch_assoc($result_list_neg)): ?>
                                    <?php 
                                    $obs_truncated = !empty($neg['observacao']) ? mb_strimwidth($neg['observacao'], 0, 70, '...') : 'Sem observações';
                                    $lead_phone = !empty($neg['whatsapp']) ? preg_replace('/\D/', '', $neg['whatsapp']) : '';
                                    ?>
                                    <div class="list-group-item px-0 py-2 border-bottom">
                                        <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                            <span class="font-weight-bold text-xs text-gray-900"><?php echo htmlspecialchars($neg['empresa'] ?? 'Sem empresa'); ?></span>
                                            <small class="text-xs text-muted"><?php echo htmlspecialchars($neg['contato'] ?? ''); ?></small>
                                        </div>
                                        <p class="text-xs text-muted mb-2 font-italic">"<?php echo htmlspecialchars($obs_truncated); ?>"</p>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <?php if (!empty($lead_phone)): ?>
                                                <!-- WhatsApp direct wa.me link -->
                                                <a href="https://wa.me/<?php echo $lead_phone; ?>" target="_blank" class="btn btn-xs btn-outline-success font-weight-bold py-1 px-2" style="font-size: 0.7rem;">
                                                    <i class="fab fa-whatsapp mr-1"></i> WhatsApp
                                                </a>
                                                <!-- Webhook alert trigger button -->
                                                <button class="btn btn-xs btn-outline-primary btn-alerta-negocio py-1 px-2" style="font-size: 0.7rem;"
                                                        data-phone="<?php echo htmlspecialchars($lead_phone); ?>"
                                                        data-contact="<?php echo htmlspecialchars($neg['contato'] ?? ''); ?>"
                                                        data-company="<?php echo htmlspecialchars($neg['empresa'] ?? ''); ?>">
                                                    <i class="fas fa-bell mr-1"></i> Notificar
                                                </button>
                                            <?php else: ?>
                                                <span class="text-xs text-danger font-italic" style="font-size: 0.7rem;"><i class="fas fa-exclamation-circle mr-1"></i> Sem WhatsApp</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <?php 
        }
        ?>
    </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            fetch('/include/get_saldo_mensal.php')
            .then(response => response.json())
            .then(data => {
                let labels = data.map(item => item.mes_ano);
                let totalMes = data.map(item => parseFloat(item.total_mes));
                let saldoAcumulado = data.map(item => parseFloat(item.saldo_acumulado));

                const ctx = document.getElementById('saldoChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Lançamento Mensal',
                                data: totalMes,
                                borderColor: '#4e73df',
                                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                                fill: true,
                                tension: 0.3
                            },
                            {
                                label: 'Saldo Acumulado',
                                data: saldoAcumulado,
                                borderColor: '#1cc88a',
                                backgroundColor: 'rgba(28, 200, 138, 0.05)',
                                fill: true,
                                tension: 0.3
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'top' }
                        },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            })
            .catch(error => console.error('Erro ao buscar os dados:', error));
        });
    </script>
</body>
<?php
include_once('./include/footer-database-noorder.php');
?>
<script>
	$(document).ready(function () {
		var table = new DataTable('#table', {
			language: {
				url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json',
			},
			order: [[1, 'desc']]
		});

        // Enviar Lembrete Click Handler
        $('.btn-enviar-lembrete').on('click', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var phone = $btn.data('phone');
            var eventName = $btn.data('event-name');
            var eventDate = $btn.data('event-date');
            
            if (!phone) {
                alert('Telefone de contato não encontrado para este evento.');
                return;
            }
            
            var message = 'Olá! Lembramos você da escala no evento ' + eventName + ' em ' + eventDate + '.';
            
            sendWhatsappAlert($btn, phone, message);
        });

        // Notificar Negócio/Prospect Click Handler
        $('.btn-alerta-negocio').on('click', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var phone = $btn.data('phone');
            var contact = $btn.data('contact');
            var company = $btn.data('company');
            
            if (!phone) {
                alert('Telefone de contato não encontrado para este prospect.');
                return;
            }
            
            var message = 'Olá ' + (contact ? contact : 'parceiro') + ', gostaríamos de dar andamento à nossa negociação com a ' + company + '.';
            
            sendWhatsappAlert($btn, phone, message);
        });

        function sendWhatsappAlert($btn, phone, message) {
            var originalHtml = $btn.html();
            
            // Disable button and show spinner
            $btn.prop('disabled', true);
            $btn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Enviando...');
            
            $.ajax({
                url: '/include/api_send_whatsapp_alert.php',
                type: 'POST',
                data: {
                    phone: phone,
                    message: message
                },
                dataType: 'json',
                success: function(response) {
                    if (response && response.success) {
                        $btn.removeClass('btn-outline-secondary btn-outline-primary').addClass('btn-success');
                        $btn.html('<i class="fas fa-check mr-1"></i> Enviado!');
                        alert('Notificação enviada com sucesso!');
                    } else {
                        var errMsg = (response && response.message) ? response.message : 'Erro desconhecido.';
                        $btn.removeClass('btn-outline-secondary btn-outline-primary').addClass('btn-danger');
                        $btn.html('<i class="fas fa-times mr-1"></i> Erro!');
                        $btn.prop('disabled', false);
                        alert('Erro ao enviar mensagem: ' + errMsg);
                    }
                },
                error: function(xhr, status, error) {
                    $btn.removeClass('btn-outline-secondary btn-outline-primary').addClass('btn-danger');
                    $btn.html('<i class="fas fa-times mr-1"></i> Erro!');
                    $btn.prop('disabled', false);
                    alert('Erro de rede/comunicação ao enviar mensagem: ' + error);
                }
            });
        }
	});
</script>
<?php 
include_once('./include/scripts.php');
?>
