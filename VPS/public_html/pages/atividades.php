<?php
// pages/atividades.php - Histórico e Vitrine Institucional de Atividades e Eventos do Projeto AME
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Busca eventos futuros e em andamento
$q_futuros = "SELECT e.*, i.url AS imagem_url 
              FROM eventos_marcados e 
              LEFT JOIN imagens i ON e.imagem_id = i.imagem_id 
              WHERE e.final >= CURDATE() 
              ORDER BY e.inicio ASC";
$res_futuros = mysqli_query($conexao, $q_futuros);

// 2. Busca histórico de eventos realizados (passados)
$q_passados = "SELECT e.*, i.url AS imagem_url 
               FROM eventos_marcados e 
               LEFT JOIN imagens i ON e.imagem_id = i.imagem_id 
               WHERE e.final < CURDATE() 
               ORDER BY e.inicio DESC LIMIT 24";
$res_passados = mysqli_query($conexao, $q_passados);

// 3. Totais para indicadores
$tot_eventos = mysqli_fetch_assoc(mysqli_query($conexao, "SELECT COUNT(*) as total FROM eventos_marcados"))['total'] ?? 0;
$tot_atendimentos = mysqli_fetch_assoc(mysqli_query($conexao, "SELECT COUNT(*) as total FROM disponibilidade WHERE escalado = 1"))['total'] ?? 0;
?>

<div class="container py-5">
    <!-- Hero Header -->
    <div class="text-center mb-5">
        <span class="badge badge-primary px-3 py-2 rounded-pill font-weight-bold text-uppercase mb-2 shadow-sm">
            <i class="fas fa-calendar-check mr-1"></i> Inclusão em Ação
        </span>
        <h1 class="display-5 font-weight-bold text-dark mt-2">Nossas Atividades & Eventos</h1>
        <p class="lead text-muted mx-auto" style="max-width: 750px;">
            Conheça o histórico de feiras, congressos e capacitações onde os Atendentes Muito Especiais atuam com excelência, acolhimento e profissionalismo.
        </p>
        
        <!-- Indicadores de Impacto -->
        <div class="row justify-content-center mt-4">
            <div class="col-md-3 col-6 mb-3">
                <div class="card border-0 shadow-sm rounded-lg p-3 bg-light">
                    <h3 class="font-weight-bold text-primary mb-0"><?php echo $tot_eventos; ?>+</h3>
                    <small class="text-muted font-weight-bold">Eventos Realizados</small>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="card border-0 shadow-sm rounded-lg p-3 bg-light">
                    <h3 class="font-weight-bold text-success mb-0"><?php echo $tot_atendimentos; ?>+</h3>
                    <small class="text-muted font-weight-bold">Escalas Cumpridas</small>
                </div>
            </div>
            <div class="col-md-4 col-12 mb-3 d-flex align-items-center justify-content-center">
                <a href="https://wa.me/5511918267460?text=Olá!%20Gostaria%20de%20solicitar%20uma%20equipe%20do%20Projeto%20AME%20para%20nosso%20evento." target="_blank" class="btn btn-primary btn-lg rounded-pill shadow px-4 py-2 w-100 font-weight-bold">
                    <i class="fab fa-whatsapp mr-2"></i> Contratar para seu Evento
                </a>
            </div>
        </div>
    </div>

    <!-- Seção de Próximos Eventos -->
    <?php if ($res_futuros && mysqli_num_rows($res_futuros) > 0): ?>
        <div class="mb-5">
            <div class="d-flex align-items-center mb-4">
                <h3 class="font-weight-bold text-dark mb-0"><i class="fas fa-bullhorn text-warning mr-2"></i>Próximas Feiras & Eventos</h3>
                <span class="ml-3 badge badge-warning text-dark font-weight-bold">Em breve</span>
            </div>
            <div class="row">
                <?php while ($evt = mysqli_fetch_assoc($res_futuros)): ?>
                    <?php 
                    $img = !empty($evt['imagem_url']) ? $evt['imagem_url'] : 'img/ame2023.jpg';
                    $dt_inicio = date('d/m/Y', strtotime($evt['inicio']));
                    $dt_fim = date('d/m/Y', strtotime($evt['final']));
                    $periodo = ($dt_inicio === $dt_fim) ? $dt_inicio : "$dt_inicio a $dt_fim";
                    ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm rounded-lg overflow-hidden transition-hover">
                            <div class="position-relative">
                                <img src="<?php echo $GLOBALS['app_web_root'] . ltrim($img, '/'); ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="<?php echo htmlspecialchars($evt['nome']); ?>">
                                <span class="badge badge-success position-absolute" style="top: 12px; right: 12px; font-size: 0.85rem;">Confirmado</span>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title font-weight-bold text-dark mb-2"><?php echo htmlspecialchars($evt['nome']); ?></h5>
                                <p class="card-text text-muted small mb-2">
                                    <i class="far fa-calendar-alt text-primary mr-1"></i> <strong>Data:</strong> <?php echo $periodo; ?>
                                </p>
                                <p class="card-text text-muted small mb-3">
                                    <i class="fas fa-map-marker-alt text-danger mr-1"></i> <strong>Local:</strong> <?php echo htmlspecialchars($evt['local'] ?? 'São Paulo / SP'); ?>
                                </p>
                                <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center">
                                    <a href="<?php echo $GLOBALS['app_web_root']; ?>escala?evento_id=<?php echo $evt['id']; ?>" class="btn btn-outline-primary btn-sm rounded-pill font-weight-bold">
                                        <i class="fas fa-users mr-1"></i> Acompanhar Escala
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Seção de Histórico de Eventos Realizados -->
    <div class="mb-5">
        <div class="d-flex align-items-center mb-4">
            <h3 class="font-weight-bold text-dark mb-0"><i class="fas fa-history text-primary mr-2"></i>Histórico de Atuações</h3>
            <span class="ml-3 text-muted small">Eventos e feiras realizadas</span>
        </div>
        
        <div class="row">
            <?php if ($res_passados && mysqli_num_rows($res_passados) > 0): ?>
                <?php while ($evt = mysqli_fetch_assoc($res_passados)): ?>
                    <?php 
                    $img = !empty($evt['imagem_url']) ? $evt['imagem_url'] : 'img/ame2023.jpg';
                    $dt_inicio = date('d/m/Y', strtotime($evt['inicio']));
                    $dt_fim = date('d/m/Y', strtotime($evt['final']));
                    $periodo = ($dt_inicio === $dt_fim) ? $dt_inicio : "$dt_inicio a $dt_fim";
                    ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm rounded-lg overflow-hidden">
                            <img src="<?php echo $GLOBALS['app_web_root'] . ltrim($img, '/'); ?>" class="card-img-top" style="height: 150px; object-fit: cover;" alt="<?php echo htmlspecialchars($evt['nome']); ?>">
                            <div class="card-body p-3 d-flex flex-column">
                                <h6 class="font-weight-bold text-dark mb-1 text-truncate" title="<?php echo htmlspecialchars($evt['nome']); ?>">
                                    <?php echo htmlspecialchars($evt['nome']); ?>
                                </h6>
                                <small class="text-muted mb-2">
                                    <i class="far fa-calendar-alt mr-1"></i> <?php echo $periodo; ?>
                                </small>
                                <small class="text-muted text-truncate mb-3" title="<?php echo htmlspecialchars($evt['local'] ?? ''); ?>">
                                    <i class="fas fa-map-marker-alt mr-1"></i> <?php echo htmlspecialchars($evt['local'] ?? 'São Paulo / SP'); ?>
                                </small>
                                <div class="mt-auto">
                                    <a href="<?php echo $GLOBALS['app_web_root']; ?>escala?evento_id=<?php echo $evt['id']; ?>" class="btn btn-outline-secondary btn-block btn-sm rounded-pill">
                                        Ver Registro
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5 text-muted">
                    <p>Nenhum evento registrado anteriormente.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Banner Chamada para Ação -->
    <div class="card bg-primary text-white border-0 rounded-lg shadow-lg p-4 p-md-5 text-center">
        <h2 class="font-weight-bold mb-3">Leve a Inclusão Produtiva para o seu Evento</h2>
        <p class="lead mx-auto mb-4" style="max-width: 680px;">
            A contratação da equipe do Projeto AME segue rigorosamente nosso modelo de rodízio democrático e transparente, garantindo oportunidades a todos os atendentes capacitados.
        </p>
        <div class="d-flex justify-content-center flex-wrap gap-3">
            <a href="https://wa.me/5511918267460?text=Olá!%20Gostaria%20de%20solicitar%20uma%20equipe%20do%20Projeto%20AME%20para%20nosso%20evento." target="_blank" class="btn btn-light text-primary btn-lg rounded-pill font-weight-bold px-4 py-2 m-2 shadow">
                <i class="fab fa-whatsapp mr-1 text-success"></i> Falar com a Coordenação
            </a>
            <a href="<?php echo $GLOBALS['app_web_root']; ?>painel" class="btn btn-outline-light btn-lg rounded-pill font-weight-bold px-4 py-2 m-2">
                <i class="fas fa-user-lock mr-1"></i> Portal do Responsável
            </a>
        </div>
    </div>
</div>
