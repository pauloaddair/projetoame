<?php
// pages/eventosgaleria.php - Hub Público da Linha do Tempo e Eventos do Projeto AME
if (!isset($conexao)) {
    require_once __DIR__ . '/../database/conexao.php';
    require_once __DIR__ . '/../include/funcoes.php';
}

$db = isset($conexao) ? $conexao : (isset($conn) ? $conn : null);

// Função auxiliar de slug
if (!function_exists('slugify_event')) {
    function slugify_event($text) {
        $utf8 = array(
            '/[áàâãä]/u' => 'a', '/[ÁÀÂÃÄ]/u' => 'a',
            '/[éèêë]/u'  => 'e', '/[ÉÈÊË]/u'  => 'e',
            '/[íìîï]/u'  => 'i', '/[ÍÌÎÏ]/u'  => 'i',
            '/[óòôõö]/u' => 'o', '/[ÓÒÔÕÖ]/u' => 'o',
            '/[úùûü]/u'  => 'u', '/[ÚÙÛÜ]/u'  => 'u',
            '/[ç]/u'     => 'c', '/[Ç]/u'     => 'c',
            '/[ñ]/u'     => 'n', '/[Ñ]/u'     => 'n',
        );
        $text = preg_replace(array_keys($utf8), array_values($utf8), $text);
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/i', '-', $text);
        return trim($text, '-');
    }
}

// Busca o ano selecionado se houver no parâmetro
$ano_filtro = isset($parametros[1]) && is_numeric($parametros[1]) ? (int)$parametros[1] : null;

// Query para buscar anos disponíveis
$query_anos = "SELECT DISTINCT YEAR(inicio) as ano FROM eventos_marcados WHERE inicio IS NOT NULL ORDER BY ano DESC";
$res_anos = mysqli_query($db, $query_anos);
$anos = [];
if ($res_anos) {
    while ($row = mysqli_fetch_assoc($res_anos)) {
        $anos[] = $row['ano'];
    }
}

// Query para buscar os eventos (filtrado por ano se especificado)
$where_ano = $ano_filtro ? "WHERE YEAR(e.inicio) = '$ano_filtro'" : "";
$query_eventos = "SELECT e.*, i.url as imagem_url,
                  (SELECT COUNT(DISTINCT d.candidato_id) FROM disponibilidade d JOIN horarios h ON d.atividade_id = h.horario_id WHERE h.evento_id = e.id AND d.escalado = 1) as total_escalados
                  FROM eventos_marcados e
                  LEFT JOIN imagens i ON e.imagem_id = i.imagem_id
                  $where_ano
                  ORDER BY e.inicio DESC";
$res_eventos = mysqli_query($db, $query_eventos);
$eventos = [];
if ($res_eventos) {
    while ($row = mysqli_fetch_assoc($res_eventos)) {
        $eventos[] = $row;
    }
}
?>

<div class="bg-light py-4">
    <div class="container">
        <!-- Breadcrumb de Navegação -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-white p-3 rounded-4 shadow-sm mb-0">
                <li class="breadcrumb-item"><a href="<?php echo $GLOBALS['app_web_root']; ?>" class="text-decoration-none"><i class="bi bi-house-door-fill text-primary"></i> Início</a></li>
                <?php if ($ano_filtro): ?>
                    <li class="breadcrumb-item"><a href="<?php echo $GLOBALS['app_web_root']; ?>eventos" class="text-decoration-none">Eventos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Ano <?php echo $ano_filtro; ?></li>
                <?php else: ?>
                    <li class="breadcrumb-item active" aria-current="page">Eventos & Linha do Tempo</li>
                <?php endif; ?>
            </ol>
        </nav>

        <div class="text-center mb-5">
            <span class="badge bg-primary px-3 py-2 rounded-pill mb-2">Memória & Inclusão</span>
            <h1 class="display-5 fw-bold text-dark">Nossos Eventos & Realizações</h1>
            <p class="text-muted lead mx-auto" style="max-width: 650px;">
                Conheça os eventos, feiras e congressos onde nossos Atendentes Muito Especiais atuaram promovendo a inclusão social no mercado de trabalho.
            </p>

            <!-- Seletor de Anos -->
            <div class="d-flex justify-content-center flex-wrap gap-2 mt-4">
                <a href="<?php echo $GLOBALS['app_web_root']; ?>eventos" 
                   class="btn <?php echo !$ano_filtro ? 'btn-primary' : 'btn-outline-secondary'; ?> rounded-pill px-4">
                    Todos os Anos
                </a>
                <?php foreach ($anos as $ano): ?>
                    <a href="<?php echo $GLOBALS['app_web_root']; ?>eventos/<?php echo $ano; ?>" 
                       class="btn <?php echo ($ano_filtro == $ano) ? 'btn-primary' : 'btn-outline-secondary'; ?> rounded-pill px-3">
                        <?php echo $ano; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Grid de Cards dos Eventos -->
        <?php if (count($eventos) > 0): ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-5">
                <?php foreach ($eventos as $ev): ?>
                    <?php 
                    $ano_ev = date('Y', strtotime($ev['inicio']));
                    $slug_clean = !empty($ev['slug']) ? $ev['slug'] : slugify_event($ev['nome']);
                    $link_evento = $GLOBALS['app_web_root'] . "$ano_ev/$slug_clean";
                    // Imagem padrão ime2023.jpg se vazia ou se for conarh.jpg padrão
                    $img_ev = (!empty($ev['imagem_url']) && strpos($ev['imagem_url'], 'conarh.jpg') === false) 
                        ? $GLOBALS['app_web_root'] . $ev['imagem_url'] 
                        : $GLOBALS['app_web_root'] . 'img/ame2023.jpg';
                    ?>
                    <div class="col">
                        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden hover-shadow transition">
                            <img src="<?php echo htmlspecialchars($img_ev); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($ev['nome']); ?>"
                                 style="height: 200px; object-fit: cover;"
                                 onerror="this.src='<?php echo $GLOBALS['app_web_root']; ?>img/ame2023.jpg';">
                            <div class="card-body p-4 d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-secondary rounded-pill"><?php echo $ano_ev; ?></span>
                                    <small class="text-muted"><i class="bi bi-people-fill text-primary"></i> <?php echo $ev['total_escalados']; ?> Atendentes</small>
                                </div>
                                <h5 class="card-title fw-bold text-dark mb-2"><?php echo htmlspecialchars($ev['nome']); ?></h5>
                                <p class="card-text text-muted small mb-3">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i> <?php echo htmlspecialchars($ev['local']); ?>
                                </p>
                                <div class="mt-auto">
                                    <a href="<?php echo $link_evento; ?>" class="btn btn-outline-primary w-full rounded-3 text-center d-block font-monospace" style="font-size: 0.85rem;">
                                        Ver Detalhes do Evento ➔
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center rounded-4 p-5 mb-5">
                <h4>Nenhum evento encontrado</h4>
                <p class="text-muted">Não encontramos eventos cadastrados para o filtro selecionado.</p>
            </div>
        <?php endif; ?>

        <!-- Widget Efeito UAU: Cartão Virtual do Projeto AME -->
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4" style="background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);">
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

    </div>
</div>
