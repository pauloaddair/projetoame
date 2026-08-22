<?php
// pages/adminativarcandidato.php - Ativação Rápida de Candidato pela Administração
if (!isset($conexao)) {
    require_once __DIR__ . '/../database/conexao.php';
    require_once __DIR__ . '/../include/funcoes.php';
}

$db = isset($conexao) ? $conexao : (isset($conn) ? $conn : null);

$candidato_id = 0;
if (isset($parametros[1]) && is_numeric($parametros[1])) {
    $candidato_id = (int)$parametros[1];
} elseif (isset($_GET['candidato_id']) && is_numeric($_GET['candidato_id'])) {
    $candidato_id = (int)$_GET['candidato_id'];
}

$msg = "";
$sucesso = false;

if ($candidato_id > 0) {
    // Busca candidato
    $q_cand = "SELECT candidato_id, nome, Email FROM candidatos WHERE candidato_id = '$candidato_id'";
    $res_cand = mysqli_query($db, $q_cand);
    
    if ($res_cand && $cand = mysqli_fetch_assoc($res_cand)) {
        // Ativa o candidato
        $q_update = "UPDATE candidatos SET ativo = 1 WHERE candidato_id = '$candidato_id'";
        if (mysqli_query($db, $q_update)) {
            $sucesso = true;
            $msg = "O candidato <strong>" . htmlspecialchars($cand['nome']) . "</strong> (ID #$candidato_id) foi <strong>APROVADO E ATIVADO</strong> com sucesso no sistema!";
        } else {
            $msg = "Erro ao ativar candidato: " . mysqli_error($db);
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
                        <h3 class="fw-bold text-dark mb-3">Candidato Ativado!</h3>
                        <p class="text-secondary fs-6 mb-4"><?php echo $msg; ?></p>
                        <a href="<?php echo $GLOBALS['app_web_root']; ?>admin/atendentes" class="btn btn-primary rounded-pill px-4 py-2">
                            Ver Lista de Atendentes ➔
                        </a>
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
