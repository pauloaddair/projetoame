<?php
// pages/avaliacao.php - Ficha Pública de Avaliação de Atendentes do Evento
date_default_timezone_set('America/Sao_Paulo');
$app_web_root = $GLOBALS['app_web_root'] ?? '/';
$titulo = "Avaliação de Atendentes";
include_once('./include/funcoes.php');

define('HMAC_SECRET_KEY', 'ProjetoAME_ChaveSecreta_#2025@');

$evento_uuid = array_key_exists(1, $parametros) ? $parametros[1] : '';
$evento = null;
$atendentes = [];

if (!empty($evento_uuid)) {
    // 1. Busca o evento pelo UUID
    $query_evento = sprintf("SELECT id, nome, status_evento, inicio, final, local FROM eventos_marcados WHERE uuid = '%s' LIMIT 1", mysqli_real_escape_string($conexao, $evento_uuid));
    $result_evento = mysqli_query($conexao, $query_evento);
    $evento = mysqli_fetch_assoc($result_evento);

    if ($evento && $evento['status_evento'] !== 'cancelado') {
        $evento_id = $evento['id'];

        // 2. Busca atendentes escalados e cruza com presencas e avaliacoes
        $query_atendentes = "SELECT DISTINCT c.candidato_id, c.nome, i.url,
                                    COALESCE(p.presente, 0) AS presenca_confirmada,
                                    (SELECT COUNT(*) FROM avaliacoes av WHERE av.evento_id = {$evento_id} AND av.candidato_id = c.candidato_id) AS total_avaliacoes
                             FROM candidatos c
                             JOIN disponibilidade d ON c.candidato_id = d.candidato_id
                             JOIN horarios h ON d.atividade_id = h.horario_id
                             LEFT JOIN imagens i ON c.imagem_id = i.imagem_id
                             LEFT JOIN presenca p ON p.evento_id = {$evento_id} AND p.candidato_id = c.candidato_id
                             WHERE h.evento_id = {$evento_id} AND d.escalado = 1
                             ORDER BY presenca_confirmada DESC, c.nome ASC";
        $result_atendentes = mysqli_query($conexao, $query_atendentes);
        while ($row = mysqli_fetch_assoc($result_atendentes)) {
            $data_string = "evento_id={$evento_id}&candidato_id={$row['candidato_id']}";
            $signature = hash_hmac('sha256', $data_string, HMAC_SECRET_KEY);
            $row['link_seguro'] = "avaliar_atendente?evento_id={$evento_id}&candidato_id={$row['candidato_id']}&sig={$signature}";
            $atendentes[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Avaliação de Atendentes - Projeto AME</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', sans-serif; background-color: #f4f7f6; }
        .card-evento { border-radius: 12px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
        .atendente-item { border-radius: 10px; transition: transform 0.2s, box-shadow 0.2s; border-left: 5px solid #28a745; }
        .atendente-item:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.1); }
        .atendente-item.sem-presenca { border-left-color: #ffc107; }
    </style>
</head>
<body>
<div class="container py-5" style="max-width: 800px;">
    <div class="text-center mb-4">
        <img src="<?php echo $app_web_root; ?>img/ame2023.jpg" alt="Projeto AME" height="65" class="rounded-circle shadow-sm mb-2" onerror="this.style.display='none'">
        <h1 class="h3 font-weight-bold text-dark mb-1">Avaliação de Desempenho</h1>
        <p class="text-muted small">Projeto Atendentes Muito Especiais (A.M.E.)</p>
    </div>

    <?php if ($evento): ?>
        <div class="card card-evento bg-white mb-4">
            <div class="card-header bg-primary text-white py-3 rounded-top">
                <h2 class="h5 mb-1 font-weight-bold"><i class="fas fa-calendar-alt mr-2"></i><?php echo htmlspecialchars($evento['nome']); ?></h2>
                <?php if (!empty($evento['local'])): ?>
                    <small class="d-block text-white-50"><i class="fas fa-map-marker-alt mr-1"></i><?php echo htmlspecialchars($evento['local']); ?></small>
                <?php endif; ?>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-info border-0 rounded shadow-sm mb-4">
                    <i class="fas fa-info-circle mr-1"></i>
                    Sua avaliação é fundamental para o acompanhamento pedagógico e desenvolvimento profissional dos nossos atendentes. 
                    <strong>Clique no atendente desejado abaixo para preencher a ficha de avaliação:</strong>
                </div>

                <?php if (!empty($atendentes)): ?>
                    <div class="list-group">
                        <?php foreach ($atendentes as $atendente): 
                            $perfilImg = !empty($atendente['url']) ? $app_web_root . ltrim($atendente['url'], '/') : $app_web_root . "img/profile.png";
                            $presencaOk = ($atendente['presenca_confirmada'] == 1);
                            $jaAvaliado = ($atendente['total_avaliacoes'] > 0);
                        ?>
                            <div class="list-group-item list-group-item-action d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2 p-3 atendente-item <?php echo $presencaOk ? '' : 'sem-presenca'; ?>">
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo $perfilImg; ?>" class="rounded-circle mr-3 shadow-sm" style="width: 54px; height: 54px; object-fit: cover;" onerror="this.src='<?php echo $app_web_root; ?>img/profile.png';">
                                    <div>
                                        <h5 class="mb-1 font-weight-bold text-dark"><?php echo htmlspecialchars($atendente['nome']); ?></h5>
                                        <div>
                                            <?php if ($presencaOk): ?>
                                                <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i>Presença Confirmada</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-clock mr-1"></i>Escalado para o Evento</span>
                                            <?php endif; ?>

                                            <?php if ($jaAvaliado): ?>
                                                <span class="badge badge-info ml-1 px-2 py-1"><i class="fas fa-star mr-1"></i><?php echo $atendente['total_avaliacoes']; ?> Avaliação(ões) Registrada(s)</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <a href="<?php echo $app_web_root . $atendente['link_seguro']; ?>" class="btn btn-primary font-weight-bold rounded-pill px-4 shadow-sm">
                                        <i class="fas fa-star mr-1"></i> Avaliar ➔
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning text-center py-4 rounded">
                        <i class="fas fa-user-clock fa-2x mb-2 d-block text-warning"></i>
                        <strong>Nenhum atendente escalado ou confirmado para este evento até o momento.</strong>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="card card-evento bg-white p-5 text-center">
            <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
            <h2 class="h4 font-weight-bold text-dark">Link de Evento Inválido ou Expirado</h2>
            <p class="text-muted">Não foi possível localizar as informações do evento solicitado. Verifique se o link está correto ou entre em contato com a coordenação do Projeto AME.</p>
        </div>
    <?php endif; ?>

    <footer class="text-center text-muted small mt-4">
        Associação Brasileira de Inclusão Através do Trabalho (Projeto A.M.E.)<br>
        <a href="https://projetoame.org" target="_blank" class="text-primary font-weight-bold">projetoame.org</a>
    </footer>
</div>
</body>
</html>
