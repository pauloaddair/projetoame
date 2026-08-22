<?php
date_default_timezone_set('America/Sao_Paulo');
// include_once('./include/head.php');
// include_once('./include/conexao.php');
include_once('./include/funcoes.php');

// Chave secreta para a assinatura. DEVE SER A MESMA do arquivo avaliar_atendente.php
define('HMAC_SECRET_KEY', 'ProjetoAME_ChaveSecreta_#2025@');

$evento_uuid = array_key_exists(1, $parametros) ? $parametros[1] : '';
$evento = null;
$atendentes = [];

if (!empty($evento_uuid)) {
    // 1. Busca o evento pelo UUID
    $query_evento = sprintf("SELECT id, nome, status_evento FROM eventos_marcados WHERE uuid = '%s' LIMIT 1", mysqli_real_escape_string($conexao, $evento_uuid));
    $result_evento = mysqli_query($conexao, $query_evento);
    $evento = mysqli_fetch_assoc($result_evento);

    if ($evento && $evento['status_evento'] !== 'cancelado') {
        // 2. Busca os atendentes escalados para este evento
        $evento_id = $evento['id'];
        $query_atendentes = "SELECT DISTINCT c.candidato_id, c.nome, i.url 
                             FROM candidatos c
                             JOIN disponibilidade d ON c.candidato_id = d.candidato_id
                             JOIN horarios h ON d.atividade_id = h.horario_id
                             LEFT JOIN imagens i ON c.imagem_id = i.imagem_id
                             WHERE h.evento_id = {$evento_id} AND d.escalado = 1
                             ORDER BY c.nome ASC";
        $result_atendentes = mysqli_query($conexao, $query_atendentes);
        while ($row = mysqli_fetch_assoc($result_atendentes)) {
            // 3. Gera o link seguro para cada atendente
            $data_string = "evento_id={$evento_id}&candidato_id={$row['candidato_id']}";
            $signature = hash_hmac('sha256', $data_string, HMAC_SECRET_KEY);
            $row['link_seguro'] = "avaliar_atendente?evento_id={$evento_id}&candidato_id={$row['candidato_id']}&sig={$signature}";
            $atendentes[] = $row;
        }
    }
}

?>
<body>
<div class="container p-1">
    <header class="mt-5 p-2 justify-content-md-center">
        <h1 class="mt-5 text-center">AVALIAÇÃO DE ATENDENTES</h1>
    </header>
    <main class="container">
        <div class="row">
            <div class="col-12">
                <?php if ($evento): ?>
                    <div class="card mb-2 p-1">
                        <div class="card-header">
                            <h2>Evento: <?php echo htmlspecialchars($evento['nome']); ?></h2>
                            <small>Por favor, selecione o atendente que deseja avaliar.</small>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($atendentes)): ?>
                                <div class="list-group">
                                    <?php foreach ($atendentes as $atendente): ?>
                                        <a href="<?php echo $GLOBALS['app_web_root'] . $atendente['link_seguro']; ?>" class="list-group-item list-group-item-action d-flex align-items-center">
                                            <img src="<?php echo $GLOBALS['app_web_root'] . $atendente['url']; ?>" class="img-thumbnail rounded-circle mr-3" style="width: 50px; height: 50px; object-fit: cover;">
                                            <strong><?php echo htmlspecialchars($atendente['nome']); ?></strong>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p>Nenhum atendente escalado para este evento ou a escala ainda não foi confirmada.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger">
                        <h1>Link de Evento Inválido ou Expirado</h1>
                        <p>Não foi possível encontrar o evento associado a este link. Verifique se o link está correto.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>
</body>
</html>
