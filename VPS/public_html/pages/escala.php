<?php
// pages/escala.php - Escala Pública / Matriz de Rodízio & Disponibilidade (Livre Acesso)
if (!isset($conexao)) {
    require_once __DIR__ . '/../database/conexao.php';
    require_once __DIR__ . '/../include/funcoes.php';
}

$db = isset($conexao) ? $conexao : (isset($conn) ? $conn : null);

$evento_id = isset($_GET['evento_id']) ? (int)$_GET['evento_id'] : 0;

if (!$evento_id) {
    echo "<div class='container my-5 text-center'>
            <h2 class='text-danger'>Evento não especificado</h2>
            <p class='text-muted'>Por favor, informe o ID do evento para visualizar a escala.</p>
            <a href='{$GLOBALS['app_web_root']}eventos' class='btn btn-outline-primary mt-3'>Ver Todos os Eventos</a>
          </div>";
    return;
}

// 1. Busca evento
$query_evento = "SELECT e.*, i.url as imagem_url 
                FROM eventos_marcados e 
                LEFT JOIN imagens i ON e.imagem_id = i.imagem_id 
                WHERE e.id = '$evento_id' LIMIT 1";
$res_evento = mysqli_query($db, $query_evento);
$evento = $res_evento ? mysqli_fetch_assoc($res_evento) : null;

if (!$evento) {
    echo "<div class='container my-5 text-center'>
            <h2 class='text-danger'>Evento não encontrado</h2>
            <p class='text-muted'>Não encontramos nenhum registro para o evento solicitado (ID #$evento_id).</p>
            <a href='{$GLOBALS['app_web_root']}eventos' class='btn btn-outline-primary mt-3'>Ver Todos os Eventos</a>
          </div>";
    return;
}

$ano_ev = date('Y', strtotime($evento['inicio']));
$slug_ev = !empty($evento['slug']) ? $evento['slug'] : 'evento';
$data_final_ts = !empty($evento['final']) ? strtotime($evento['final']) : strtotime($evento['inicio']);
$eh_passado = ($data_final_ts < strtotime('today'));

// 2. Se o evento já ocorreu (passado), redireciona automaticamente para o layout institucional em /{ano}/{slug}
if ($eh_passado) {
    $target_url = $GLOBALS['app_web_root'] . "$ano_ev/$slug_ev";
    header("Location: $target_url");
    echo "<script>window.location.href='$target_url';</script>";
    return;
}

// Evento Futuro / Ativo: Renderiza a Matriz Pública de Disponibilidade e Rodízio
$escala_fechada = (isset($evento['escala_fechada']) && (int)$evento['escala_fechada'] == 1);
$subtitulo_escala = $escala_fechada ? "Escala Definitiva (Momento 2)" : "Rodízio e Disponibilidade Declarada (Momento 1)";
$badge_color = $escala_fechada ? "bg-success text-white" : "bg-warning text-dark";

// 3. Busca Horários do Evento
$query_horarios = "SELECT horario_id, data_inicio, data_final, vagas FROM horarios WHERE evento_id = '$evento_id' ORDER BY data_inicio ASC";
$res_horarios = mysqli_query($db, $query_horarios);
$horarios = [];
if ($res_horarios) {
    while ($h = mysqli_fetch_assoc($res_horarios)) {
        $horarios[] = $h;
    }
}

// 4. Busca Candidatos Ativos e em Treinamento (ativo = 1 ou 0) com rodízio >= 1 e Fotos (ordenados primeiro por Atendentes/Treinandos, depois por Rodízio)
$query_candidatos = "SELECT c.candidato_id, c.nome, c.rodizio, c.ativo, img.url as foto_url 
                     FROM candidatos c 
                     LEFT JOIN imagens img ON c.imagem_id = img.imagem_id 
                     WHERE c.ativo IN (0, 1) AND c.rodizio >= 1 
                     ORDER BY c.ativo DESC, c.rodizio ASC";
$res_candidatos = mysqli_query($db, $query_candidatos);
$candidatos = [];
if ($res_candidatos) {
    while ($c = mysqli_fetch_assoc($res_candidatos)) {
        $candidatos[] = $c;
    }
}

// 5. Busca Disponividades e Status de Escala
$query_disp = "SELECT d.candidato_id, d.atividade_id, d.escalado 
               FROM disponibilidade d 
               JOIN horarios h ON d.atividade_id = h.horario_id 
               WHERE h.evento_id = '$evento_id'";
$res_disp = mysqli_query($db, $query_disp);
$disp_map = []; // [candidato_id][horario_id] = ['is_disponivel' => 1, 'is_escalado' => 1]
$candidatos_escalados_ids = [];

if ($res_disp) {
    while ($d = mysqli_fetch_assoc($res_disp)) {
        $cand_id = (int)$d['candidato_id'];
        $hor_id = (int)$d['atividade_id'];
        $escalado = (int)$d['escalado'];
        $disp_map[$cand_id][$hor_id] = [
            'is_disponivel' => 1,
            'is_escalado' => $escalado
        ];
        if ($escalado == 1) {
            $candidatos_escalados_ids[$cand_id] = true;
        }
    }
}

// Formatações de data
$data_inicio_fmt = date('d/m/Y', strtotime($evento['inicio']));
$data_final_fmt = date('d/m/Y', strtotime($evento['final']));
$datas_exibicao = ($data_inicio_fmt === $data_final_fmt) ? $data_inicio_fmt : "$data_inicio_fmt a $data_final_fmt";

// Imagem padrão e Meta Tags para Redes Sociais / WhatsApp (OpenGraph)
$raw_img = !empty($evento['imagem_url']) ? $evento['imagem_url'] : 'img/ame2023.jpg';
$imagem_capa = (strpos($raw_img, 'http') === 0) ? $raw_img : 'https://projetoame.org/' . ltrim($raw_img, '/');

$titulo = $evento['nome'] . " — " . $subtitulo_escala;
$og_title = $evento['nome'] . " (" . $subtitulo_escala . ")";
$og_description = "📅 Período: {$datas_exibicao} | 📍 Local: " . $evento['local'] . ". Acompanhe a ordem de rodízio e a escala dos Atendentes Muito Especiais (Projeto AME).";
$og_image = $imagem_capa;
$og_url = "https://projetoame.org/escala?evento_id={$evento_id}";
?>

<div class="bg-light py-4">
    <div class="container-fluid px-md-5">
        <!-- Breadcrumb de Navegação -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-white p-3 rounded-4 shadow-sm mb-0">
                <li class="breadcrumb-item"><a href="<?php echo $GLOBALS['app_web_root']; ?>" class="text-decoration-none"><i class="fas fa-home text-primary"></i> Início</a></li>
                <li class="breadcrumb-item"><a href="<?php echo $GLOBALS['app_web_root']; ?>eventos" class="text-decoration-none">Eventos</a></li>
                <li class="breadcrumb-item"><a href="<?php echo $GLOBALS['app_web_root']; ?>eventos/<?php echo $ano_ev; ?>" class="text-decoration-none"><?php echo $ano_ev; ?></a></li>
                <li class="breadcrumb-item active text-truncate" style="max-width: 300px;" aria-current="page"><?php echo htmlspecialchars($evento['nome']); ?></li>
            </ol>
        </nav>

        <!-- Banner e Cabeçalho do Evento -->
        <div class="card border-0 shadow-lg overflow-hidden rounded-4 mb-4">
            <div class="row g-0 align-items-center">
                <div class="col-md-3 bg-dark text-center p-3">
                    <img src="<?php echo htmlspecialchars($imagem_capa); ?>" alt="<?php echo htmlspecialchars($evento['nome']); ?>" class="img-fluid rounded-3 shadow-sm" style="max-height: 160px; object-fit: cover;">
                </div>
                <div class="col-md-9 p-4 bg-white">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-primary px-3 py-2 rounded-pill">Acompanhamento da Escala</span>
                        <span class="badge <?php echo $badge_color; ?> px-3 py-2 rounded-pill fs-6"><?php echo $subtitulo_escala; ?></span>
                    </div>
                    <h2 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($evento['nome']); ?></h2>
                    <h5 class="text-primary font-monospace mb-3"><?php echo $subtitulo_escala; ?></h5>
                    
                    <p class="text-muted small mb-3">
                        <i class="fas fa-info-circle text-info me-1"></i> 
                        <strong>Critério Transparente de Escala:</strong> A vaga é atribuída prioritariamente conforme a ordem de <strong>Rodízio</strong> e a <strong>Precedência da Disponibilidade</strong> registrada pelos pais/atendentes na página de inscrição.
                    </p>

                    <div class="d-flex flex-wrap gap-4 text-muted small">
                        <div><i class="fas fa-calendar-alt text-primary me-1"></i> <strong>Período:</strong> <?php echo $datas_exibicao; ?></div>
                        <div><i class="fas fa-map-marker-alt text-danger me-1"></i> <strong>Local:</strong> <?php echo htmlspecialchars($evento['local']); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aviso de Orientação conforme o Momento -->
        <?php if (!$escala_fechada): ?>
            <div class="alert alert-warning border-0 shadow-sm rounded-4 d-flex align-items-center gap-3 p-3 mb-4">
                <span class="fs-2 text-warning"><i class="fas fa-history"></i></span>
                <div>
                    <strong class="d-block text-dark" style="font-size: 1.05rem;">Momento 1: Consulta de Rodízio e Disponibilidade Declarada</strong>
                    <span class="text-secondary small">
                        Confira abaixo a ordem oficial do rodízio e os horários que você declarou na página de atendimento. 
                        <strong>A escala definitiva ainda não foi fechada.</strong> Se notar qualquer divergência no rodízio ou na sua disponibilidade, avise no grupo <strong>"Projeto AME Capacitação"</strong> antes da publicação final.
                    </span>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-success border-0 shadow-sm rounded-4 d-flex align-items-center gap-3 p-3 mb-4">
                <span class="fs-2 text-success"><i class="fas fa-check-circle"></i></span>
                <div>
                    <strong class="d-block text-dark" style="font-size: 1.05rem;">Momento 2: Escala Definitiva Confirmada!</strong>
                    <span class="text-secondary small">
                        A escala final para este evento já foi gerada e confirmada pela coordenação. Os atendentes escalados estão destacados com o selo <strong>ESCALADO</strong>.
                    </span>
                </div>
            </div>
        <?php endif; ?>

        <!-- Legenda e Status da Escala -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div class="d-flex gap-3 align-items-center">
                <span class="badge bg-white text-dark border p-2 rounded-3"><i class="fas fa-check text-success fs-6 me-1"></i> Disponibilidade Declarada</span>
                <?php if ($escala_fechada): ?>
                    <span class="badge bg-success text-white p-2 rounded-3"><i class="fas fa-user-check me-1"></i> ESCALADO</span>
                <?php endif; ?>
                <span class="badge bg-warning text-dark p-2 rounded-3">Treinamento</span>
            </div>
            <div>
                <span class="text-muted small font-monospace">Exibindo <?php echo count($candidatos); ?> candidatos ativos no rodízio</span>
            </div>
        </div>

        <!-- Matriz de Rodízio & Disponibilidade (READ-ONLY) -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" style="width: 80px;">Rodízio</th>
                            <th class="text-center" style="width: 70px;">Foto</th>
                            <th>Nome do Atendente</th>
                            <th class="text-center" style="width: 140px;">Status</th>
                            <?php foreach ($horarios as $h): ?>
                                <?php 
                                $dt_ini = strtotime($h['data_inicio']);
                                $dt_fim = strtotime($h['data_final']);
                                ?>
                                <th class="text-center" style="min-width: 150px;">
                                    <?php echo date('d/m/Y', $dt_ini); ?><br>
                                    <small class="font-monospace text-warning"><?php echo date('H:i', $dt_ini) . ' - ' . date('H:i', $dt_fim); ?></small><br>
                                    <span class="badge bg-secondary rounded-pill font-normal" style="font-size: 0.75rem;"><?php echo $h['vagas']; ?> vagas</span>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($candidatos as $cand): ?>
                            <?php 
                            $cand_id = (int)$cand['candidato_id'];
                            $esta_escalado_em_algum = isset($candidatos_escalados_ids[$cand_id]);
                            
                            // Se a escala está fechada e o candidato foi escalado, destaca a linha em verde claro
                            $tr_class = ($escala_fechada && $esta_escalado_em_algum) ? 'table-success border-success' : '';
                            $foto_url = !empty($cand['foto_url']) ? $GLOBALS['app_web_root'] . $cand['foto_url'] : $GLOBALS['app_web_root'] . 'img/ame2023.jpg';
                            ?>
                            <tr class="<?php echo $tr_class; ?>">
                                <td class="text-center fw-bold font-monospace"><?php echo $cand['rodizio']; ?></td>
                                <td class="text-center">
                                    <img src="<?php echo htmlspecialchars($foto_url); ?>" 
                                         class="rounded-circle shadow-sm" 
                                         style="width: 40px; height: 40px; object-fit: cover;" 
                                         alt="<?php echo htmlspecialchars($cand['nome']); ?>"
                                         onerror="this.src='<?php echo $GLOBALS['app_web_root']; ?>img/ame2023.jpg';">
                                </td>
                                <td class="fw-semibold text-dark">
                                    <?php echo htmlspecialchars($cand['nome']); ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($cand['ativo'] == 0): ?>
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1">Treinamento</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary rounded-pill px-3 py-1">Atendente</span>
                                    <?php endif; ?>

                                    <?php if ($escala_fechada && $esta_escalado_em_algum): ?>
                                        <div class="mt-1">
                                            <span class="badge bg-success text-white px-2 py-1 rounded-pill" style="font-size: 0.75rem;">
                                                <i class="fas fa-star me-1"></i> ESCALADO
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <?php foreach ($horarios as $h): ?>
                                    <?php 
                                    $hor_id = (int)$h['horario_id'];
                                    $disp_info = isset($disp_map[$cand_id][$hor_id]) ? $disp_map[$cand_id][$hor_id] : null;
                                    $is_disponivel = $disp_info && $disp_info['is_disponivel'] == 1;
                                    $is_escalado = $disp_info && $disp_info['is_escalado'] == 1;

                                    $td_bg = ($escala_fechada && $is_escalado) ? 'bg-success bg-opacity-25 fw-bold text-success' : '';
                                    ?>
                                    <td class="text-center <?php echo $td_bg; ?>">
                                        <?php if ($escala_fechada && $is_escalado): ?>
                                            <span class="badge bg-success text-white rounded-pill px-3 py-2 shadow-sm" style="font-size: 0.8rem;">
                                                <i class="fas fa-check-circle me-1"></i> ESCALADO
                                            </span>
                                        <?php elseif ($is_disponivel): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 border border-success border-opacity-25 rounded-pill fw-bold" title="Disponibilidade Declarada">
                                                <i class="fas fa-check me-1"></i> SIM
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted opacity-25">-</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
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
                            📱 Abrir Cartão Virtual ➔
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rodapé Limpo & Orientação aos Pais -->
        <div class="text-center pt-4 border-top">
            <p class="fw-semibold text-primary mb-3" style="font-size: 0.95rem;">
                💬 Caso tenha alguma dúvida, comente no grupo "Projeto AME Capacitação".
            </p>
            <img src="<?php echo $GLOBALS['app_web_root']; ?>img/logo_ame.png" alt="Projeto AME" style="height: 45px;" class="mb-2" onerror="this.src='<?php echo $GLOBALS['app_web_root']; ?>img/ame2023.jpg';">
            <p class="text-muted small m-0">Associação Brasileira de Inclusão Através do Trabalho (Projeto A.M.E.)</p>
            <p class="text-muted small mb-0">Promovendo a autonomia e protagonismo de pessoas com deficiência intelectual no mercado de trabalho.</p>
        </div>
    </div>
</div>