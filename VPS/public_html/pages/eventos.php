<?php
// pages/eventos.php - Vitrine e Gestão de Eventos / Atividades do Projeto AME
// Integração automática: se o usuário for administrador/coordenador logado (nível >= 3),
// exibe a tabela de gestão operacional com DataTable e atalhos de escalas.
// Caso contrário (público / visitantes / famílias), carrega a vitrine institucional completa de atividades.

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$eh_admin = (isset($_SESSION['nivel']) && (int)$_SESSION['nivel'] >= 3);

// Se não for admin, ou se foi solicitado explicitamente o modo vitrine pública
if (!$eh_admin || (isset($_GET['modo']) && $_GET['modo'] === 'publico')) {
    include_once __DIR__ . '/atividades.php';
    return;
}

// -------------------------------------------------------------
// ÁREA ADMINISTRATIVA: Listagem e Gestão Operacional de Eventos
// -------------------------------------------------------------
$titulo = "Gestão de Eventos e Atividades";

// Busca os eventos oficiais em eventos_marcados com totais de escalas
$query = "SELECT em.*, i.url AS imagem_url,
          (SELECT COUNT(DISTINCT d.candidato_id) 
           FROM disponibilidade d 
           JOIN horarios h ON d.atividade_id = h.horario_id 
           WHERE h.evento_id = em.id AND d.escalado = 1) AS total_escalados
          FROM eventos_marcados em
          LEFT JOIN imagens i ON em.imagem_id = i.imagem_id
          ORDER BY em.inicio DESC";
$resp = mysqli_query($conexao, $query);
?>

<div class="container-fluid px-lg-5 my-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 border-bottom">
        <div>
            <h2 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-calendar-alt text-primary mr-2"></i>Gestão de Eventos e Atividades
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-0 small">
                    <li class="breadcrumb-item"><a href="<?php echo $GLOBALS['app_web_root']; ?>admin">Painel</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo $GLOBALS['app_web_root']; ?>atividades">Vitrine Pública</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Eventos Cadastrados</li>
                </ol>
            </nav>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="<?php echo $GLOBALS['app_web_root']; ?>admin/novaatividade" class="btn btn-primary rounded-pill btn-sm px-3 shadow-sm font-weight-bold">
                <i class="fas fa-plus mr-1"></i> Nova Atividade / Evento
            </a>
            <a href="<?php echo $GLOBALS['app_web_root']; ?>eventos?modo=publico" class="btn btn-outline-secondary rounded-pill btn-sm px-3 shadow-sm ml-2">
                <i class="fas fa-eye mr-1"></i> Visualizar Vitrine Pública
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-lg mb-4">
        <div class="card-body p-3 p-md-4">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0" id="table">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Evento / Atividade</th>
                            <th>Tipo</th>
                            <th>Período</th>
                            <th>Local</th>
                            <th class="text-center">Escalados</th>
                            <th>Status</th>
                            <th class="text-right" style="min-width: 170px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        if ($resp && mysqli_num_rows($resp) > 0):
                            while ($row = mysqli_fetch_assoc($resp)):
                                $dt_inicio = date('d/m/Y', strtotime($row['inicio']));
                                $dt_fim = date('d/m/Y', strtotime($row['final']));
                                $periodo = ($dt_inicio === $dt_fim) ? $dt_inicio : "$dt_inicio a $dt_fim";
                                
                                $is_futuro = (strtotime($row['final']) >= strtotime('today'));
                                $badge_status = $is_futuro ? 'badge-success' : 'badge-secondary';
                                $label_status = $is_futuro ? 'Em Aberto' : 'Realizado';
                                
                                $tipo_badge = 'badge-primary';
                                if (($row['tipo'] ?? '') === 'Curso') $tipo_badge = 'badge-info';
                                elseif (($row['tipo'] ?? '') === 'Treinamento') $tipo_badge = 'badge-warning text-dark';
                        ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($row['nome']); ?></strong>
                                <small class="d-block text-muted">ID: #<?php echo $row['id']; ?></small>
                            </td>
                            <td>
                                <span class="badge <?php echo $tipo_badge; ?> px-2 py-1 rounded-pill">
                                    <?php echo htmlspecialchars($row['tipo'] ?? 'Trabalho'); ?>
                                </span>
                            </td>
                            <td><small class="font-weight-bold text-nowrap"><?php echo $periodo; ?></small></td>
                            <td><small class="text-muted"><?php echo htmlspecialchars($row['local'] ?? 'São Paulo / SP'); ?></small></td>
                            <td class="text-center">
                                <span class="badge badge-light border font-weight-bold px-2 py-1">
                                    <?php echo (int)$row['total_escalados']; ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?php echo $badge_status; ?> px-2 py-1">
                                    <?php echo $label_status; ?>
                                </span>
                            </td>
                            <td class="text-right text-nowrap">
                                <a href="<?php echo $GLOBALS['app_web_root']; ?>admin/escala?evento_id=<?php echo $row['id']; ?>" class="btn btn-outline-primary btn-sm rounded-pill px-2 py-1" title="Gerenciar Escala">
                                    <i class="fas fa-users mr-1"></i> Escala
                                </a>
                                <a href="<?php echo $GLOBALS['app_web_root']; ?>escala?evento_id=<?php echo $row['id']; ?>" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-2 py-1" title="Ver Link Público">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </td>
                        </tr>
                        <?php 
                            endwhile;
                        endif;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        if (typeof $ !== 'undefined' && $('#table').length && !$.fn.DataTable.isDataTable('#table')) {
            $('#table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
                },
                "order": [[ 0, "asc" ]],
                "responsive": true,
                "pageLength": 25
            });
        }
    });
</script>
