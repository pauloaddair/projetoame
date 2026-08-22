<?php
// pages/eventopublico.php - Página Pública e Institucional do Evento / Parceria
if (!isset($conexao)) {
    require_once __DIR__ . '/../database/conexao.php';
    require_once __DIR__ . '/../include/funcoes.php';
}

$db = isset($conexao) ? $conexao : (isset($conn) ? $conn : null);

$ano_req = isset($parametros[0]) ? (int)$parametros[0] : date('Y');
$slug_req = isset($parametros[1]) ? trim(mysqli_real_escape_string($db, $parametros[1])) : '';

// Busca evento por ano e slug (com fallback de LIKE no nome)
$query_evento = "SELECT e.*, i.url as imagem_url 
                FROM eventos_marcados e 
                LEFT JOIN imagens i ON e.imagem_id = i.imagem_id 
                WHERE YEAR(e.inicio) = '$ano_req' 
                AND (
                    e.slug = '$slug_req' 
                    OR LOWER(e.nome) LIKE '%$slug_req%'
                    OR LOWER(REPLACE(REPLACE(e.nome, ' ', '-'), '/', '-')) LIKE '%$slug_req%'
                ) 
                ORDER BY e.inicio ASC LIMIT 1";

$res_evento = mysqli_query($db, $query_evento);
$evento = $res_evento ? mysqli_fetch_assoc($res_evento) : null;

if (!$evento) {
    echo "<div class='container my-5 text-center'>
            <h2 class='text-danger'>Evento não encontrado</h2>
            <p class='text-muted'>Não encontramos nenhum registro para o evento solicitado em $ano_req.</p>
            <a href='{$GLOBALS['app_web_root']}eventos' class='btn btn-outline-primary mt-3'>Ver Todos os Eventos</a>
          </div>";
    return;
}

$evento_id = $evento['id'];
$escala_fechada = (isset($evento['escala_fechada']) && $evento['escala_fechada'] == 1);
$subtitulo_escala = $escala_fechada ? "Escala Definitiva" : "Rodízio e Disponibilidade";
$badge_color = $escala_fechada ? "bg-success" : "bg-warning text-dark";

// Verifica se o evento é futuro/ativo ou passado
$data_final_ts = !empty($evento['final']) ? strtotime($evento['final']) : strtotime($evento['inicio']);
$eh_evento_futuro = ($data_final_ts >= strtotime('today'));

// Busca atendentes escalados JOIN tabela imagens pelo c.imagem_id
$query_escalados = "SELECT c.candidato_id as cand_id, c.nome as atendente_nome, img_cand.url as foto_url, h.data_inicio, h.data_final
                    FROM disponibilidade d
                    JOIN candidatos c ON d.candidato_id = c.candidato_id
                    LEFT JOIN imagens img_cand ON c.imagem_id = img_cand.imagem_id
                    JOIN horarios h ON d.atividade_id = h.horario_id
                    WHERE h.evento_id = '$evento_id' AND d.escalado = 1
                    ORDER BY h.data_inicio ASC, c.nome ASC";
$res_escalados = mysqli_query($db, $query_escalados);

$escalados = [];
if ($res_escalados) {
    while ($row = mysqli_fetch_assoc($res_escalados)) {
        $escalados[] = $row;
    }
}

// Formatações de data
$data_inicio_fmt = date('d/m/Y', strtotime($evento['inicio']));
$data_final_fmt = date('d/m/Y', strtotime($evento['final']));
$datas_exibicao = ($data_inicio_fmt === $data_final_fmt) ? $data_inicio_fmt : "$data_inicio_fmt a $data_final_fmt";

// Imagem padrão de evento (ame2023.jpg) se não houver ou se for nula
$imagem_capa = (!empty($evento['imagem_url']) && strpos($evento['imagem_url'], 'conarh.jpg') === false)
    ? $GLOBALS['app_web_root'] . $evento['imagem_url'] 
    : $GLOBALS['app_web_root'] . 'img/ame2023.jpg';
?>

<div class="bg-light py-4">
    <div class="container">
        <!-- Breadcrumb de Navegação -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-white p-3 rounded-4 shadow-sm mb-0">
                <li class="breadcrumb-item"><a href="<?php echo $GLOBALS['app_web_root']; ?>" class="text-decoration-none"><i class="bi bi-house-door-fill text-primary"></i> Início</a></li>
                <li class="breadcrumb-item"><a href="<?php echo $GLOBALS['app_web_root']; ?>eventos" class="text-decoration-none">Eventos</a></li>
                <li class="breadcrumb-item"><a href="<?php echo $GLOBALS['app_web_root']; ?>eventos/<?php echo $ano_req; ?>" class="text-decoration-none"><?php echo $ano_req; ?></a></li>
                <li class="breadcrumb-item active text-truncate" style="max-width: 300px;" aria-current="page"><?php echo htmlspecialchars($evento['nome']); ?></li>
            </ol>
        </nav>

        <!-- Banner e Cabeçalho Institucional -->
        <div class="card border-0 shadow-lg overflow-hidden rounded-4 mb-5">
            <div class="row g-0 align-items-center">
                <div class="col-md-5 bg-dark text-center p-4">
                    <img src="<?php echo htmlspecialchars($imagem_capa); ?>" alt="<?php echo htmlspecialchars($evento['nome']); ?>" class="img-fluid rounded-3 shadow-sm" style="max-height: 280px; object-fit: cover;">
                </div>
                <div class="col-md-7 p-4 p-lg-5 bg-white">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-primary px-3 py-2 rounded-pill">Parceria Social ESG</span>
                        <span class="badge <?php echo $badge_color; ?> px-3 py-2 rounded-pill"><?php echo $subtitulo_escala; ?></span>
                    </div>
                    <h1 class="display-6 fw-bold text-dark mb-1"><?php echo htmlspecialchars($evento['nome']); ?></h1>
                    <h5 class="text-primary font-monospace mb-3"><?php echo $subtitulo_escala; ?></h5>
                    
                    <p class="text-muted fs-6 mb-4">
                        Uma parceria oficial de Inclusão Social através do Trabalho entre o <strong>Projeto AME</strong> e a organização do evento.
                    </p>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 border-start border-4 border-primary">
                                <small class="text-muted d-block font-monospace">📅 PERÍODO</small>
                                <strong><?php echo $datas_exibicao; ?></strong>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 border-start border-4 border-success">
                                <small class="text-muted d-block font-monospace">📍 LOCAL</small>
                                <strong><?php echo htmlspecialchars($evento['local']); ?></strong>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($evento['link_artigo'])): ?>
                        <div>
                            <a href="<?php echo htmlspecialchars($evento['link_artigo']); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-dark rounded-pill px-4 py-2 fw-semibold">
                                📖 Ler Cobertura Completa no Blog ➔
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Equipe de Atendentes Escalados -->
        <div class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark m-0">Equipe de Atendentes Muito Especiais</h3>
                    <p class="text-muted m-0">Status da Escala: <strong><?php echo $subtitulo_escala; ?></strong></p>
                </div>
                <span class="badge bg-primary text-white fs-6 px-3 py-2 rounded-pill">
                    <?php echo count($escalados); ?> Atendentes Escalados
                </span>
            </div>

            <?php if (count($escalados) > 0): ?>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                    <?php foreach ($escalados as $atendente): ?>
                        <?php 
                        $foto_atendente = (!empty($atendente['foto_url'])) 
                            ? $GLOBALS['app_web_root'] . $atendente['foto_url'] 
                            : $GLOBALS['app_web_root'] . 'img/ame2023.jpg';
                        $horario_str = date('d/m H:i', strtotime($atendente['data_inicio'])) . ' - ' . date('H:i', strtotime($atendente['data_final']));
                        ?>
                        <div class="col">
                            <div class="card h-100 border-0 shadow-sm rounded-4 text-center p-3 hover-shadow transition">
                                <img src="<?php echo htmlspecialchars($foto_atendente); ?>" 
                                     class="rounded-circle mx-auto mb-3 shadow-sm" 
                                     alt="<?php echo htmlspecialchars($atendente['atendente_nome']); ?>"
                                     style="width: 100px; height: 100px; object-fit: cover;"
                                     onerror="this.src='<?php echo $GLOBALS['app_web_root']; ?>img/ame2023.jpg';">
                                <h6 class="fw-bold mb-1 text-dark"><?php echo htmlspecialchars($atendente['atendente_nome']); ?></h6>
                                <span class="badge bg-light text-secondary border rounded-pill font-monospace" style="font-size: 0.75rem;">
                                    <?php echo $horario_str; ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info rounded-3 p-4 text-center">
                    <p class="m-0">A escala deste evento está sendo finalizada pela coordenação do Projeto AME.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Widget Efeito UAU: Cartão Virtual do Projeto AME -->
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-5" style="background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);">
            <div class="card-body p-4 p-md-5 text-white">
                <div class="row align-items-center">
                    <div class="col-lg-8 mb-3 mb-lg-0 text-center text-lg-start">
                        <div class="d-flex align-items-center justify-content-center justify-content-lg-start gap-2 mb-2">
                            <span class="badge bg-warning text-dark font-monospace px-3 py-1 rounded-pill">CARTÃO VIRTUAL</span>
                            <span class="text-white-50 small">Conectividade & Contatos</span>
                        </div>
                        <h3 class="fw-bold m-0 mb-2">Conecte-se ao Cartão Virtual do Projeto AME</h3>
                        <p class="text-light opacity-75 mb-0" style="font-size: 0.95rem;">
                            Acesse nossos canais oficiais, salve o contato em seu celular, conheça nossas redes sociais e formas de apoio em um único clique.
                        </p>
                    </div>
                    <div class="col-lg-4 text-center text-lg-end">
                        <a href="https://contacte.me/projetoame" target="_blank" rel="noopener noreferrer" class="btn btn-warning btn-lg px-4 py-3 rounded-pill fw-bold text-dark shadow-sm hover-scale transition">
                            🎴 Abrir Cartão Virtual ➔
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rodapé Limpo & Orientação aos Pais (Apenas se evento for futuro/ativo) -->
        <div class="text-center pt-4 border-top">
            <?php if ($eh_evento_futuro): ?>
                <p class="fw-semibold text-primary mb-3" style="font-size: 0.95rem;">
                    💬 Caso tenha alguma dúvida, comente no grupo "Projeto AME Capacitação".
                </p>
            <?php endif; ?>
            <img src="<?php echo $GLOBALS['app_web_root']; ?>img/logo_ame.png" alt="Projeto AME" style="height: 45px;" class="mb-2" onerror="this.src='<?php echo $GLOBALS['app_web_root']; ?>img/ame2023.jpg';">
            <p class="text-muted small m-0">Associação Brasileira de Inclusão Através do Trabalho (Projeto A.M.E.)</p>
            <p class="text-muted small mb-0">Promovendo a autonomia e protagonismo de pessoas com deficiência intelectual no mercado de trabalho.</p>
        </div>
    </div>
</div>
