<?php
// pages/adminativarcandidato.php - Ativação Rápida de Candidato pela Administração
if (!isset($conexao)) {
    require_once __DIR__ . '/../database/conexao.php';
    require_once __DIR__ . '/../include/funcoes.php';
}

$db = isset($conexao) ? $conexao : (isset($conn) ? $conn : null);

$candidato_id = 0;
if (isset($parametros[2]) && is_numeric($parametros[2])) {
    $candidato_id = (int)$parametros[2];
} elseif (isset($parametros[1]) && is_numeric($parametros[1])) {
    $candidato_id = (int)$parametros[1];
} elseif (isset($_GET['candidato_id']) && is_numeric($_GET['candidato_id'])) {
    $candidato_id = (int)$_GET['candidato_id'];
} elseif (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $candidato_id = (int)$_GET['id'];
}

$msg = "";
$sucesso = false;
$target_status = 0; // Padrão: Treinando para novas inscrições (ainda sem capacitação)
if (isset($_GET['status'])) {
    $st = strtolower(trim($_GET['status']));
    if ($st === '1' || $st === 'atendente' || $st === 'ativo') {
        $target_status = 1;
    } elseif ($st === '0' || $st === 'treinando' || $st === 'treinamento') {
        $target_status = 0;
    }
} elseif (isset($_GET['tipo'])) {
    $st = strtolower(trim($_GET['tipo']));
    if ($st === '1' || $st === 'atendente' || $st === 'ativo') {
        $target_status = 1;
    } elseif ($st === '0' || $st === 'treinando' || $st === 'treinamento') {
        $target_status = 0;
    }
}

if ($candidato_id > 0) {
    // Busca candidato
    $q_cand = "SELECT candidato_id, nome, Email, rodizio, ativo FROM candidatos WHERE candidato_id = '$candidato_id'";
    $res_cand = mysqli_query($db, $q_cand);
    
    if ($res_cand && $cand = mysqli_fetch_assoc($res_cand)) {
        // Se o candidato não tiver rodízio válido (<= 0 ou null), calcula o próximo da fila
        $rodizio_update = "";
        if (empty($cand['rodizio']) || (int)$cand['rodizio'] <= 0) {
            $q_max = "SELECT (COALESCE(MAX(rodizio), 0) + 1) AS max_rod FROM candidatos WHERE rodizio >= 1";
            $r_max = mysqli_query($db, $q_max);
            $row_max = mysqli_fetch_assoc($r_max);
            $novo_rod = (int)$row_max['max_rod'];
            $rodizio_update = ", rodizio = $novo_rod";
        }

        $status_label = ($target_status === 1) ? 'Atendente Pleno' : 'Treinando';
        $badge_class = ($target_status === 1) ? 'badge-primary' : 'badge-warning text-dark';

        // Atualiza o status do candidato
        $q_update = "UPDATE candidatos SET ativo = $target_status $rodizio_update WHERE candidato_id = '$candidato_id'";
        if (mysqli_query($db, $q_update)) {
            $sucesso = true;
            $msg = "O candidato <strong>" . htmlspecialchars($cand['nome']) . "</strong> (ID #$candidato_id) foi cadastrado como <span class='badge {$badge_class} px-2 py-1'>" . $status_label . "</span> com sucesso no sistema!";
        } else {
            $msg = "Erro ao atualizar candidato: " . mysqli_error($db);
        }
    } else {
        $msg = "Candidato ID #$candidato_id não foi localizado no banco de dados.";
    }
} else {
    $msg = "ID de candidato inválido.";
}
?>

<div class="container my-5 py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-lg rounded-4 text-center p-4">
                <div class="card-body">
                    <?php if ($sucesso): ?>
                        <div class="display-1 text-success mb-3"><i class="bi bi-check-circle-fill"></i></div>
                        <h3 class="fw-bold text-dark mb-3">Status Atualizado!</h3>
                        <p class="text-secondary fs-6 mb-4"><?php echo $msg; ?></p>
                        
                        <div class="mb-4">
                            <?php if ($target_status === 0): ?>
                                <a href="<?php echo $GLOBALS['app_web_root']; ?>admin/ativarcandidato/<?php echo $candidato_id; ?>?status=atendente" class="btn btn-outline-success btn-sm rounded-pill px-3 shadow-sm">
                                    <i class="fas fa-star mr-1"></i> Promover para Atendente Pleno
                                </a>
                            <?php else: ?>
                                <a href="<?php echo $GLOBALS['app_web_root']; ?>admin/ativarcandidato/<?php echo $candidato_id; ?>?status=treinando" class="btn btn-outline-warning btn-sm rounded-pill px-3 text-dark shadow-sm">
                                    <i class="fas fa-graduation-cap mr-1"></i> Mudar para Em Treinamento
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex justify-content-center flex-wrap gap-2">
                            <a href="<?php echo $GLOBALS['app_web_root']; ?>admin/candidatos" class="btn btn-primary rounded-pill px-4 py-2">
                                Ver Lista de Candidatos ➔
                            </a>
                            <a href="<?php echo $GLOBALS['app_web_root']; ?>admin/atividades" class="btn btn-outline-secondary rounded-pill px-4 py-2 ml-2">
                                Ver Atividades & Escalas
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="display-1 text-danger mb-3"><i class="bi bi-exclamation-triangle-fill"></i></div>
                        <h3 class="fw-bold text-dark mb-3">Ops!</h3>
                        <p class="text-secondary fs-6 mb-4"><?php echo $msg; ?></p>
                        <a href="<?php echo $GLOBALS['app_web_root']; ?>eventos" class="btn btn-outline-secondary rounded-pill px-4 py-2">
                            Voltar ao Início
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
