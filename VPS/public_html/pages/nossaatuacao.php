<?php
$titulo = "Nossa Atuação";

// Queries para obter dados consolidados dinamicamente do banco
// 1. Total de Eventos de Trabalho realizados (filtra cursos e reuniões por palavras-chave no nome)
$query_eventos = "SELECT COUNT(*) as total FROM eventos_marcados 
                  WHERE status_evento = 'realizado' 
                    AND NOT (nome LIKE '%curso%' OR nome LIKE '%treinamento%' OR nome LIKE '%capacita%' OR nome LIKE '%rodizio%' OR nome LIKE '%reunia%' OR nome LIKE '%palestra%')";
$res_eventos = mysqli_query($conexao, $query_eventos);
$total_eventos = $res_eventos ? mysqli_fetch_assoc($res_eventos)['total'] : 0;

// 2. Total de Cursos/Treinamentos realizados (busca por palavras-chave no nome)
$query_cursos = "SELECT COUNT(*) as total FROM eventos_marcados 
                 WHERE status_evento = 'realizado' 
                   AND (nome LIKE '%curso%' OR nome LIKE '%treinamento%' OR nome LIKE '%capacita%' OR nome LIKE '%rodizio%' OR nome LIKE '%reunia%' OR nome LIKE '%palestra%')";
$res_cursos = mysqli_query($conexao, $query_cursos);
$total_cursos = $res_cursos ? mysqli_fetch_assoc($res_cursos)['total'] : 0;

// 3. Total de Horas de Atendimento Prestadas
// Filtra discrepâncias de data e soma o tempo total de todos os atendentes escalados
$query_horas = "SELECT SUM(
                    CASE 
                        WHEN ABS(TIMESTAMPDIFF(HOUR, h.data_inicio, h.data_final)) <= 24 
                        THEN ABS(TIMESTAMPDIFF(HOUR, h.data_inicio, h.data_final)) 
                        ELSE 8 
                    END
                ) as total 
                FROM disponibilidade d
                JOIN horarios h ON d.atividade_id = h.horario_id
                WHERE d.escalado = 1";
$res_horas = mysqli_query($conexao, $query_horas);
$total_horas = $res_horas ? mysqli_fetch_assoc($res_horas)['total'] : 0;

// 4. Número de Atendentes Capacitados (Ativos)
$query_capacitados = "SELECT COUNT(*) as total FROM candidatos WHERE ativo = 1";
$res_capacitados = mysqli_query($conexao, $query_capacitados);
$total_capacitados = $res_capacitados ? mysqli_fetch_assoc($res_capacitados)['total'] : 0;

// 5. Número Total de Inscritos no Histórico
$query_inscritos = "SELECT COUNT(*) as total FROM candidatos";
$res_inscritos = mysqli_query($conexao, $query_inscritos);
$total_inscritos = $res_inscritos ? mysqli_fetch_assoc($res_inscritos)['total'] : 0;

// 6. Data de início das atividades
$query_inicio = "SELECT DATE_FORMAT(MIN(data_inicio), '%d/%m/%Y') as data_ini FROM horarios";
$res_inicio = mysqli_query($conexao, $query_inicio);
$data_inicio_atividades = $res_inicio ? mysqli_fetch_assoc($res_inicio)['data_ini'] : '15/10/2012';

// 7. Listagem dos principais eventos recentes
$query_recentes = "SELECT em.nome, DATE_FORMAT(MIN(h.data_inicio), '%M/%Y') as data_mes, COUNT(DISTINCT d.candidato_id) as atendentes
                   FROM disponibilidade d
                   JOIN horarios h ON d.atividade_id = h.horario_id
                   JOIN eventos_marcados em ON h.evento_id = em.id
                   WHERE d.escalado = 1
                   GROUP BY em.id
                   ORDER BY MIN(h.data_inicio) DESC
                   LIMIT 5";
$res_recentes = mysqli_query($conexao, $query_recentes);
?>

<body class="grey lighten-3">
  <!-- Navbar -->
  <header>
    <nav class="navbar fixed-top navbar-expand-lg navbar-dark indigo scrolling-navbar">
      <?php include_once('include/header.php'); ?>
    </nav>
  </header>

  <!-- Layout Principal -->
  <main class="pt-5 mx-lg-5">
    <div class="container-fluid mt-5">

      <!-- Banner de Introdução -->
      <div class="card mb-4 wow fadeIn text-white" style="background: linear-gradient(135deg, #3f51b5 0%, #1a237e 100%); border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
        <div class="card-body p-4 p-md-5 text-center text-md-left">
          <div class="row align-items-center">
            <div class="col-md-9">
              <h1 class="h2-responsive font-weight-bold mb-3">Atendentes Muito Especiais (A.M.E.)</h1>
              <p class="lead mb-0" style="opacity: 0.9;">
                Nossa atuação prática em números. A.B.I.A.T. — Promovendo autonomia, inclusão real e desenvolvimento profissional de jovens e adultos com deficiência intelectual no mercado de eventos.
              </p>
            </div>
            <div class="col-md-3 text-center d-none d-md-block">
              <i class="fas fa-chart-line fa-5x" style="opacity: 0.25;"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Grid de Estatísticas (Cartões Premium) -->
      <div class="row mb-4">
        <!-- Horas de Atendimento -->
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card text-white" style="background: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%); border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h3 class="font-weight-bold mb-1"><?php echo number_format($total_horas, 0, ',', '.'); ?>h</h3>
                  <p class="card-text text-uppercase font-small mb-0">Horas de Atendimento</p>
                </div>
                <i class="fas fa-clock fa-2x" style="opacity: 0.3;"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Eventos Realizados -->
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card text-white" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h3 class="font-weight-bold mb-1"><?php echo $total_eventos; ?></h3>
                  <p class="card-text text-uppercase font-small mb-0">Eventos Realizados</p>
                </div>
                <i class="fas fa-calendar-check fa-2x" style="opacity: 0.3;"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Atendentes Capacitados -->
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card text-white" style="background: linear-gradient(135deg, #ff9966 0%, #ff5e62 100%); border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h3 class="font-weight-bold mb-1"><?php echo $total_capacitados; ?></h3>
                  <p class="card-text text-uppercase font-small mb-0">Atendentes Capacitados</p>
                </div>
                <i class="fas fa-user-check fa-2x" style="opacity: 0.3;"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Cursos / Treinamentos -->
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card text-white" style="background: linear-gradient(135deg, #8a2be2 0%, #4a00e0 100%); border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h3 class="font-weight-bold mb-1"><?php echo $total_cursos; ?></h3>
                  <p class="card-text text-uppercase font-small mb-0">Cursos e Capacitações</p>
                </div>
                <i class="fas fa-graduation-cap fa-2x" style="opacity: 0.3;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Detalhes Finais & Histórico -->
      <div class="row">
        <!-- Lado Esquerdo: Linha do Tempo e Histórico do AME -->
        <div class="col-md-7 mb-4 order-2 order-md-1">
          <div class="card" style="border-radius: 10px;">
            <div class="card-header bg-white border-0 py-3">
              <h5 class="card-title font-weight-bold mb-0 text-primary">
                <i class="fas fa-award mr-2"></i>Destaque Recente em Grandes Feiras
              </h5>
            </div>
            <div class="card-body pt-0">
              <p class="text-muted">Veja as atividades recentes onde o Projeto A.M.E. atuou ativamente com equipes integradas:</p>
              
              <div class="list-group list-group-flush">
                <?php if ($res_recentes && mysqli_num_rows($res_recentes) > 0): ?>
                  <?php while ($evt = mysqli_fetch_assoc($res_recentes)): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                      <div>
                        <h6 class="font-weight-bold mb-1"><?php echo htmlspecialchars($evt['nome']); ?></h6>
                        <small class="text-muted"><i class="far fa-calendar mr-1"></i><?php echo htmlspecialchars($evt['data_mes']); ?></small>
                      </div>
                      <span class="badge badge-primary badge-pill p-2">
                        <?php echo $evt['atendentes']; ?> atendentes participaram
                      </span>
                    </div>
                  <?php endwhile; ?>
                <?php else: ?>
                  <p class="text-muted">Nenhum evento registrado recentemente.</p>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Lado Direito: Perfil Institucional e Metas -->
        <div class="col-md-5 mb-4 order-1 order-md-2">
          <div class="card" style="border-radius: 10px;">
            <div class="card-header bg-white border-0 py-3">
              <h5 class="card-title font-weight-bold mb-0 text-success">
                <i class="fas fa-heart mr-2"></i>Nossa Trajetória
              </h5>
            </div>
            <div class="card-body pt-0">
              <ul class="list-group list-group-flush">
                <li class="list-group-item px-0 py-3">
                  <strong>Primeiro evento (surgimento da ideia):</strong>
                  <span class="float-right text-muted">15 de outubro de 2012</span>
                </li>
                <li class="list-group-item px-0 py-3">
                  <strong>Início das atividades como associação:</strong>
                  <span class="float-right text-muted">01/09/2017</span>
                </li>
                <li class="list-group-item px-0 py-3">
                  <strong>Total de Candidatos Inscritos:</strong>
                  <span class="float-right text-muted"><?php echo $total_inscritos; ?> associados</span>
                </li>
                <li class="list-group-item px-0 py-3">
                  <strong>Atuação Geográfica:</strong>
                  <span class="float-right text-muted">Estado de São Paulo (Capital e Interior)</span>
                </li>
                <li class="list-group-item px-0 py-3">
                  <strong>Pilar Fundamental:</strong>
                  <span class="float-right text-muted">Remuneração justa e autonomia real</span>
                </li>
              </ul>
              
              <div class="alert alert-success mt-4 mb-0 text-center" style="border-radius: 8px;">
                <h6 class="font-weight-bold mb-2">Quer levar o A.M.E. para o seu evento?</h6>
                <p class="small mb-0">Nossos atendentes altamente qualificados estão prontos para garantir excelência em atendimento, credenciamento e hospitalidade.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Botões de Ação CTA (Cafezinho e Contacte-me) -->
      <div class="row mb-4 wow fadeIn">
        <div class="col-md-12">
          <div class="card" style="border-radius: 12px; background-color: #ffffff; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <div class="card-body p-4 text-center">
              <h5 class="font-weight-bold mb-3 text-secondary">Apoie e Conecte-se com o Projeto A.M.E.</h5>
              <div class="d-flex flex-column flex-sm-row justify-content-center align-items-center">
                
                <!-- Botão Cafezinho.social -->
                <a href="https://cafezinho.social/projetoame" target="_blank" class="btn text-white btn-lg px-4 py-3 mx-2 my-2 font-weight-bold" style="background: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%); border: none; border-radius: 30px; box-shadow: 0 4px 15px rgba(242, 153, 74, 0.3); text-transform: none; font-size: 1.05rem;">
                  <i class="fas fa-coffee mr-2"></i>Ofereça um Cafezinho
                </a>
                
                <!-- Botão Contacte.me -->
                <a href="https://contacte.me/projetoame" target="_blank" class="btn text-white btn-lg px-4 py-3 mx-2 my-2 font-weight-bold" style="background: linear-gradient(135deg, #1fddff 0%, #0075ff 100%); border: none; border-radius: 30px; box-shadow: 0 4px 15px rgba(0, 117, 255, 0.3); text-transform: none; font-size: 1.05rem;">
                  <i class="fas fa-id-card mr-2"></i>Entre em contato
                </a>
                
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </main>

  <!-- Rodapé -->
  <footer class="page-footer text-center font-small primary-color-dark darken-2 mt-4 wow fadeIn">
    <?php include_once('footer.php'); ?>
  </footer>
</body>
</html>
