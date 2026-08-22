<?php
/**
 * API to Get Leads list by Event ID
 * Returns HTML block to inject into the expanded row
 */
session_start();

if (!isset($_SESSION['id']) || $_SESSION['id'] === "") {
    echo "<div class='alert alert-danger'>Acesso restrito.</div>";
    exit;
}

$base_path = dirname(__DIR__) . '/';
include_once($base_path . 'database/conexao.php');

if (!$conexao) {
    echo "<div class='alert alert-danger'>Erro de conexão com o banco de dados.</div>";
    exit;
}

$evento_id = isset($_GET['evento_id']) ? intval($_GET['evento_id']) : 0;
if ($evento_id <= 0) {
    echo "<div class='alert alert-warning'>ID de evento inválido.</div>";
    exit;
}

$query = "SELECT * FROM leads_expositores WHERE evento_id = ? ORDER BY nome ASC";
$stmt = mysqli_prepare($conexao, $query);
if (!$stmt) {
    echo "<div class='alert alert-danger'>Erro ao preparar consulta.</div>";
    exit;
}

mysqli_stmt_bind_param($stmt, "i", $evento_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    echo "<div class='p-3 text-center text-muted'>";
    echo "Nenhum expositor cadastrado para este evento.<br>";
    echo "Clique em <strong class='text-primary'><i class='fas fa-robot'></i> Buscar Expositores (AI)</strong> acima para iniciar uma busca automática de leads na web.";
    echo "</div>";
    mysqli_stmt_close($stmt);
    exit;
}
?>

<div class="table-responsive p-3 bg-light border rounded">
    <h6 class="mb-3 text-secondary font-weight-bold"><i class="fas fa-users"></i> Expositores / Leads Prospectados</h6>
    <table class="table table-sm table-hover table-striped table-bordered mb-0" style="font-size: 0.9rem;">
        <thead class="thead-dark">
            <tr>
                <th>Empresa</th>
                <th>Contato</th>
                <th>Telefone / WhatsApp</th>
                <th>E-mail</th>
                <th>Site</th>
                <th>Última Interação</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            while ($lead = mysqli_fetch_assoc($result)): 
                $lead_id = intval($lead['id']);
                
                // Fetch latest contact interaction
                $contact_query = "SELECT tipo_contato, data_contato, observacao, status, data_followup 
                                  FROM leads_contatos 
                                  WHERE expositor_id = ? 
                                  ORDER BY data_update DESC LIMIT 1";
                $c_stmt = mysqli_prepare($conexao, $contact_query);
                $latest_interaction = "Sem interações registradas";
                $status_badge = "<span class='badge badge-secondary'>Pendente</span>";
                
                if ($c_stmt) {
                    mysqli_stmt_bind_param($c_stmt, "i", $lead_id);
                    mysqli_stmt_execute($c_stmt);
                    $c_res = mysqli_stmt_get_result($c_stmt);
                    if ($c_row = mysqli_fetch_assoc($c_res)) {
                        $interaction_date = date('d/m/Y', strtotime($c_row['data_contato']));
                        $obs_preview = !empty($c_row['observacao']) ? ': ' . mb_strimwidth($c_row['observacao'], 0, 50, '...') : '';
                        
                        $type_icon = '<i class="fas fa-comment"></i>';
                        if ($c_row['tipo_contato'] === 'whatsapp') $type_icon = '<i class="fab fa-whatsapp text-success"></i>';
                        elseif ($c_row['tipo_contato'] === 'email') $type_icon = '<i class="far fa-envelope text-primary"></i>';
                        elseif ($c_row['tipo_contato'] === 'telefone') $type_icon = '<i class="fas fa-phone-alt text-info"></i>';
                        
                        $latest_interaction = "$type_icon $interaction_date" . $obs_preview;
                        
                        // Set status badge
                        $status = $c_row['status'];
                        if ($status === 'em andamento') {
                            $status_badge = "<span class='badge badge-info'>Em Andamento</span>";
                        } elseif ($status === 'convertido') {
                            $status_badge = "<span class='badge badge-success'>Convertido</span>";
                        } elseif ($status === 'perdido') {
                            $status_badge = "<span class='badge badge-danger'>Perdido</span>";
                        }
                    }
                    mysqli_stmt_close($c_stmt);
                }
                
                // Clean whatsapp number
                $raw_whatsapp = $lead['whatsapp'] ?? $lead['telefone'] ?? '';
                $clean_whatsapp = preg_replace('/\D/', '', $raw_whatsapp);
                if (!empty($clean_whatsapp) && strlen($clean_whatsapp) < 12) {
                    // Prepend Brazil country code if not present
                    if (substr($clean_whatsapp, 0, 2) !== '55') {
                        $clean_whatsapp = '55' . $clean_whatsapp;
                    }
                }
            ?>
            <tr>
                <td class="font-weight-bold"><?= htmlspecialchars($lead['nome']) ?></td>
                <td><?= htmlspecialchars($lead['contato'] ?? '-') ?></td>
                <td>
                    <?php if (!empty($clean_whatsapp)): ?>
                        <a href="https://wa.me/<?= $clean_whatsapp ?>" target="_blank" class="text-success font-weight-bold">
                            <i class="fab fa-whatsapp"></i> <?= htmlspecialchars($raw_whatsapp) ?>
                        </a>
                    <?php else: ?>
                        <?= htmlspecialchars($lead['telefone'] ?? '-') ?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!empty($lead['email'])): ?>
                        <a href="mailto:<?= htmlspecialchars($lead['email']) ?>"><i class="far fa-envelope"></i> <?= htmlspecialchars($lead['email']) ?></a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!empty($lead['site'])): ?>
                        <a href="<?= htmlspecialchars($lead['site']) ?>" target="_blank" class="text-primary"><i class="fas fa-external-link-alt"></i> Visitar Site</a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td>
                    <div style="max-width: 250px;" class="text-truncate" title="<?= htmlspecialchars(strip_tags($latest_interaction)) ?>">
                        <?= $status_badge ?> <?= $latest_interaction ?>
                    </div>
                </td>
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-primary btn-sm btn-registrar-contato" 
                                data-lead-id="<?= $lead_id ?>" 
                                data-lead-name="<?= htmlspecialchars($lead['nome']) ?>" 
                                title="Registrar Contato">
                            <i class="fas fa-plus"></i> <i class="fas fa-phone-alt"></i>
                        </button>
                        <?php if (!empty($clean_whatsapp)): ?>
                            <a href="https://wa.me/<?= $clean_whatsapp ?>" target="_blank" class="btn btn-success btn-sm" title="Conversar no WhatsApp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php
mysqli_stmt_close($stmt);
mysqli_close($conexao);
?>
