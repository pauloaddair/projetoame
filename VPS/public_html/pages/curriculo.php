<?php
$app_web_root = $GLOBALS['app_web_root'] ?? '/';
$titulo = "Currículo Inclusivo";

if (isset($parametros[1])) {
    $_GET['candidato_id'] = (int)$parametros[1];
    $_GET['visualizar'] = 1;
}

// Processar salvamento de detalhes
if (isset($_POST['salvar_detalhes']) && isset($_POST['candidato_id']) && isset($_POST['detalhes'])) {
    $candidato_id = (int)$_POST['candidato_id'];
    $detalhes = trim($_POST['detalhes']);
    
    $query_update = "UPDATE candidatos SET detalhes = ? WHERE candidato_id = ?";
    $stmt_update = mysqli_prepare($conexao, $query_update);
    mysqli_stmt_bind_param($stmt_update, "si", $detalhes, $candidato_id);
    
    if (mysqli_stmt_execute($stmt_update)) {
        $mensagem_sucesso = "Detalhes salvos com sucesso!";
    } else {
        $mensagem_erro = "Erro ao salvar detalhes: " . mysqli_error($conexao);
    }
}

// Verificar se foi solicitada a geração do PDF
if (isset($_GET['gerar_pdf']) && isset($_GET['candidato_id'])) {
    $candidato_id = (int)$_GET['candidato_id'];
    
    // Buscar dados do candidato com imagem
    $query = "SELECT c.*, i.url as imagem_url FROM candidatos c 
              LEFT JOIN imagens i ON c.imagem_id = i.imagem_id 
              WHERE c.candidato_id = ?";
    $stmt = mysqli_prepare($conexao, $query);
    mysqli_stmt_bind_param($stmt, "i", $candidato_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $candidato = mysqli_fetch_assoc($result);
    
    if ($candidato) {
        // Incluir FPDF
        require_once(__DIR__ . '/../fpdf/fpdf.php');
        
        // Criar PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        
        // Adicionar imagem de perfil se existir
        $tem_foto = false;
        if (!empty($candidato['imagem_url'])) {
            $img_path = __DIR__ . '/../' . ltrim($candidato['imagem_url'], '/');
            if (file_exists($img_path)) {
                $pdf->Image($img_path, 160, 10, 30, 40);
                $tem_foto = true;
            }
        }
        
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'CURRICULO VITAE', 0, 1, 'C');
        
        if ($tem_foto) {
            $pdf->SetY(52);
        } else {
            $pdf->Ln(10);
        }
        
        // Perfil / Apresentação
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 8, iconv("UTF-8", "ISO-8859-1", 'PERFIL PROFISSIONAL'), 0, 1);
        $pdf->SetFont('Arial', '', 12);
        
        $nomes = explode(' ', trim($candidato['nome']));
        $nome_curto = $nomes[0] . (isset($nomes[1]) ? ' ' . $nomes[1] : '');
        $nome_curto_iso = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $nome_curto);
        
        $texto_perfil = $nome_curto_iso . iconv("UTF-8", "ISO-8859-1", " é participante do Projeto A.M.E. Realizou cursos de capacitação inclusiva, treinamentos práticos de hospitalidade e participa ativamente de atividades e eventos com dedicação e excelência.");
        $pdf->MultiCell(0, 6, $texto_perfil);
        $pdf->Ln(5);
        
        // Dados pessoais
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 8, 'DADOS PESSOAIS', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        
        $pdf->Cell(0, 6, 'Nome: ' . iconv("UTF-8", "ISO-8859-1", $candidato['nome']), 0, 1);
        if ($candidato['Email']) $pdf->Cell(0, 6, 'Email: ' . $candidato['Email'], 0, 1);
        if ($candidato['Telefone']) $pdf->Cell(0, 6, 'Telefone: ' . $candidato['Telefone'], 0, 1);
        
        // Idade
        if ($candidato['Nascimento']) {
            $nascimento = trim($candidato['Nascimento']);
            $idade_str = '';
            try {
                $data_nasc = null;
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $nascimento)) {
                    $data_nasc = new DateTime($nascimento);
                } elseif (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $nascimento)) {
                    $data_nasc = DateTime::createFromFormat('d/m/Y', $nascimento);
                }
                if ($data_nasc) {
                    $hoje = new DateTime();
                    $diff = $hoje->diff($data_nasc);
                    $idade_str = ' (' . $diff->y . ' anos)';
                }
            } catch (Exception $e) {}
            $pdf->Cell(0, 6, 'Nascimento: ' . $candidato['Nascimento'] . iconv("UTF-8", "ISO-8859-1", $idade_str), 0, 1);
        }
        
        if ($candidato['genero']) $pdf->Cell(0, 6, iconv("UTF-8", "ISO-8859-1", 'Gênero: ') . iconv("UTF-8", "ISO-8859-1", $candidato['genero']), 0, 1);
        if ($candidato['RG']) $pdf->Cell(0, 6, 'RG: ' . $candidato['RG'], 0, 1);
        if ($candidato['CPF']) $pdf->Cell(0, 6, 'CPF: ' . $candidato['CPF'], 0, 1);
        
        $pdf->Cell(0, 6, iconv("UTF-8", "ISO-8859-1", 'Crachá Virtual: ') . 'https://contacte.me/projetoame?a=' . $candidato['candidato_id'], 0, 1);
        $pdf->Ln(5);
        
        // Endereço
        if ($candidato['endereco'] || $candidato['cidade']) {
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 8, iconv("UTF-8", "ISO-8859-1", 'ENDEREÇO'), 0, 1);
            $pdf->SetFont('Arial', '', 12);
            
            if ($candidato['endereco']) $pdf->Cell(0, 6, iconv("UTF-8", "ISO-8859-1", 'Endereço: ') . iconv("UTF-8", "ISO-8859-1", $candidato['endereco']), 0, 1);
            if ($candidato['complemento']) $pdf->Cell(0, 6, 'Complemento: ' . iconv("UTF-8", "ISO-8859-1", $candidato['complemento']), 0, 1);
            if ($candidato['cidade']) $pdf->Cell(0, 6, 'Cidade: ' . iconv("UTF-8", "ISO-8859-1", $candidato['cidade']) . ' - ' . $candidato['UF'], 0, 1);
            if ($candidato['CEP']) $pdf->Cell(0, 6, 'CEP: ' . $candidato['CEP'], 0, 1);
            $pdf->Ln(5);
        }
        
        // Qualificações
        if (!empty($candidato['cursos_externos']) || !empty($candidato['detalhes'])) {
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 8, iconv("UTF-8", "ISO-8859-1", 'CURSOS & QUALIFICAÇÕES'), 0, 1);
            $pdf->SetFont('Arial', '', 12);
            if (!empty($candidato['cursos_externos'])) {
                $pdf->MultiCell(0, 6, iconv("UTF-8", "ISO-8859-1", $candidato['cursos_externos']));
            }
            if (!empty($candidato['detalhes'])) {
                $pdf->MultiCell(0, 6, iconv("UTF-8", "ISO-8859-1", $candidato['detalhes']));
            }
            $pdf->Ln(5);
        }
        
        // Histórico de Eventos
        $query_eventos = "SELECT em.nome as evento_nome, MIN(h.data_inicio) as data_inicio, MAX(h.data_final) as data_final
                          FROM disponibilidade d
                          JOIN horarios h ON d.atividade_id = h.horario_id
                          JOIN eventos_marcados em ON h.evento_id = em.id
                          WHERE d.candidato_id = ? AND d.escalado = 1
                          GROUP BY em.id
                          ORDER BY MIN(h.data_inicio) DESC";
        $stmt_eventos = mysqli_prepare($conexao, $query_eventos);
        mysqli_stmt_bind_param($stmt_eventos, "i", $candidato_id);
        mysqli_stmt_execute($stmt_eventos);
        $result_eventos = mysqli_stmt_get_result($stmt_eventos);
        
        if (mysqli_num_rows($result_eventos) > 0) {
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 8, iconv("UTF-8", "ISO-8859-1", 'ATUAÇÃO NO PROJETO A.M.E. (HISTÓRICO)'), 0, 1);
            $pdf->SetFont('Arial', '', 12);
            
            while ($evt = mysqli_fetch_assoc($result_eventos)) {
                $d_ini = new DateTime($evt['data_inicio']);
                $d_fim = new DateTime($evt['data_final']);
                $data_str = ($d_ini->format('Y-m-d') == $d_fim->format('Y-m-d')) ? $d_ini->format('d/m/Y') : $d_ini->format('d/m/Y') . ' a ' . $d_fim->format('d/m/Y');
                
                $pdf->Cell(130, 6, iconv("UTF-8", "ISO-8859-1", $evt['evento_nome']), 0, 0);
                $pdf->Cell(0, 6, $data_str, 0, 1, 'R');
            }
            $pdf->Ln(5);
        }
        
        // Rodapé
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->Cell(0, 6, iconv("UTF-8", "ISO-8859-1", 'Currículo gerado em: ') . date('d/m/Y H:i:s'), 0, 1, 'C');
        $pdf->Cell(0, 6, 'Projeto A.M.E. - Atendentes Muito Especiais', 0, 1, 'C');
        
        $nome_arquivo = 'curriculo_' . preg_replace('/[^a-zA-Z0-9]/', '_', $candidato['nome']) . '.pdf';
        $pdf->Output('D', $nome_arquivo);
        exit;
    }
}

// Buscar candidatos para o select
$query_candidatos = "SELECT candidato_id, nome, Email, Telefone, cidade, ativo FROM candidatos ORDER BY ativo DESC, nome ASC";
$result_candidatos = mysqli_query($conexao, $query_candidatos);
?>

<body class="bg-light">
  <?php include_once('./include/nav.php'); ?>
  
  <div class="container py-5 mt-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
      <div>
        <h1 class="h3 font-weight-bold text-dark mb-1">
          <i class="fas fa-file-pdf text-danger mr-2"></i>Currículo Inclusivo
        </h1>
        <p class="text-muted small mb-0">Visualização, enriquecimento curricular e emissão em PDF para associados do Projeto AME.</p>
      </div>
      <div class="mt-2 mt-md-0">
        <a href="<?php echo $app_web_root; ?>casting" class="btn btn-outline-primary btn-sm rounded-pill">
          <i class="fas fa-arrow-left mr-1"></i> Voltar ao Casting
        </a>
      </div>
    </div>

    <!-- Mensagens de feedback -->
    <?php if (isset($mensagem_sucesso)): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle mr-2"></i><?php echo $mensagem_sucesso; ?>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
      </div>
    <?php endif; ?>
    
    <?php if (isset($mensagem_erro)): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle mr-2"></i><?php echo $mensagem_erro; ?>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
      </div>
    <?php endif; ?>

    <!-- Seletor de Candidato -->
    <div class="card shadow-sm border-0 mb-4">
      <div class="card-header bg-white font-weight-bold">
        <i class="fas fa-search mr-2 text-primary"></i>Selecionar Atendente / Candidato
      </div>
      <div class="card-body">
        <form method="GET" action="<?php echo $app_web_root; ?>curriculo">
          <div class="form-row align-items-end">
            <div class="form-group col-md-8 mb-2">
              <label for="candidato_select" class="small font-weight-bold text-muted">Escolha o candidato:</label>
              <select class="form-control" id="candidato_select" name="candidato_id" required>
                <option value="">Selecione um candidato...</option>
                <?php while ($cand = mysqli_fetch_assoc($result_candidatos)): ?>
                  <?php $status_label = ($cand['ativo'] == 1) ? 'Ativo' : 'Inativo'; ?>
                  <option value="<?php echo $cand['candidato_id']; ?>" 
                          <?php echo (isset($_GET['candidato_id']) && $_GET['candidato_id'] == $cand['candidato_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cand['nome']); ?> [<?php echo $status_label; ?>]
                    <?php if ($cand['cidade']): ?> (<?php echo htmlspecialchars($cand['cidade']); ?>)<?php endif; ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="form-group col-md-4 mb-2">
              <button type="submit" name="visualizar" value="1" class="btn btn-primary rounded-pill btn-sm px-3">
                <i class="fas fa-eye mr-1"></i> Visualizar
              </button>
              <button type="submit" name="gerar_pdf" value="1" class="btn btn-outline-danger rounded-pill btn-sm px-3 ml-1">
                <i class="fas fa-file-pdf mr-1"></i> Baixar PDF
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <?php if (isset($_GET['visualizar']) && isset($_GET['candidato_id'])): ?>
      <?php
      $candidato_id = (int)$_GET['candidato_id'];
      $query = "SELECT c.*, i.url as imagem_url FROM candidatos c 
                LEFT JOIN imagens i ON c.imagem_id = i.imagem_id 
                WHERE c.candidato_id = ?";
      $stmt = mysqli_prepare($conexao, $query);
      mysqli_stmt_bind_param($stmt, "i", $candidato_id);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
      $candidato = mysqli_fetch_assoc($result);
      
      if ($candidato):
        $perfil_img = !empty($candidato['imagem_url']) ? $app_web_root . ltrim($candidato['imagem_url'], '/') : $app_web_root . 'img/profile.png';
      ?>
      
      <!-- Detalhes e Qualificações -->
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white font-weight-bold">
          <i class="fas fa-edit mr-2 text-primary"></i>Qualificações e Cursos Adicionais
        </div>
        <div class="card-body">
          <form method="POST" action="">
            <input type="hidden" name="candidato_id" value="<?php echo $candidato['candidato_id']; ?>">
            <div class="form-group">
              <label for="detalhes" class="small text-muted font-weight-bold">Cursos, habilidades e experiências práticas:</label>
              <textarea class="form-control" id="detalhes" name="detalhes" rows="4" 
                        placeholder="Ex: Curso de Atendimento Inclusivo (2024)&#10;Oficina de Finanças para a Vida&#10;Experiência como Atendente em Feiras"><?php echo htmlspecialchars($candidato['detalhes'] ?? ''); ?></textarea>
            </div>
            <button type="submit" name="salvar_detalhes" class="btn btn-success btn-sm rounded-pill px-4">
              <i class="fas fa-save mr-1"></i> Salvar Qualificações
            </button>
          </form>
        </div>
      </div>
      
      <!-- Preview do Currículo -->
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
          <h5 class="mb-0 font-weight-bold text-dark">
            <i class="fas fa-user-circle mr-2 text-primary"></i>Preview do Currículo
          </h5>
          <a href="<?php echo $app_web_root; ?>curriculo?candidato_id=<?php echo $candidato['candidato_id']; ?>&gerar_pdf=1" class="btn btn-danger btn-sm rounded-pill shadow-sm">
            <i class="fas fa-file-pdf mr-1"></i> Exportar PDF
          </a>
        </div>
        <div class="card-body p-4">
          <div class="row mb-4 align-items-center">
            <div class="col-md-8">
              <span class="badge badge-primary px-2 py-1 mb-2">CURRÍCULO VITAE</span>
              <h2 class="font-weight-bold text-dark mb-1"><?php echo htmlspecialchars($candidato['nome']); ?></h2>
              <p class="text-muted mb-2">
                <i class="fas fa-id-badge mr-1"></i> ID: <?php echo $candidato['candidato_id']; ?> &bull; 
                <?php echo (isset($candidato['ativo']) && $candidato['ativo'] == 1) ? '<span class="text-success font-weight-bold">Atendente Ativo</span>' : '<span class="text-secondary">Cadastro em Treinamento</span>'; ?>
              </p>
              <a href="https://contacte.me/projetoame?a=<?php echo $candidato['candidato_id']; ?>" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill">
                <i class="fas fa-qrcode mr-1"></i> Ver Crachá Virtual
              </a>
            </div>
            <div class="col-md-4 text-md-right mt-3 mt-md-0">
              <img src="<?php echo $perfil_img; ?>" 
                   alt="<?php echo htmlspecialchars($candidato['nome']); ?>" 
                   class="img-thumbnail rounded shadow-sm" 
                   style="max-width: 140px; max-height: 180px; object-fit: cover;">
            </div>
          </div>
          
          <hr>

          <!-- Perfil Profissional -->
          <div class="mb-4">
            <h5 class="font-weight-bold text-dark mb-2"><i class="fas fa-briefcase text-primary mr-2"></i>Perfil Profissional</h5>
            <div class="alert alert-light border bg-white p-3 rounded">
              <?php
              $nomes = explode(' ', trim($candidato['nome']));
              $nome_curto = $nomes[0] . (isset($nomes[1]) ? ' ' . $nomes[1] : '');
              ?>
              <strong><?php echo htmlspecialchars($nome_curto); ?></strong> é participante do Projeto A.M.E. Realizou cursos de capacitação inclusiva, treinamentos práticos de hospitalidade e participa ativamente de atividades e eventos com dedicação e excelência.
            </div>
          </div>
          
          <!-- Dados Pessoais e Endereço -->
          <div class="row mb-4">
            <div class="col-md-6 mb-3">
              <h5 class="font-weight-bold text-dark mb-2"><i class="fas fa-user text-primary mr-2"></i>Dados Pessoais</h5>
              <ul class="list-group list-group-flush border rounded">
                <li class="list-group-item py-2"><strong>Nome:</strong> <?php echo htmlspecialchars($candidato['nome']); ?></li>
                <?php if (!empty($candidato['Email'])): ?><li class="list-group-item py-2"><strong>Email:</strong> <?php echo htmlspecialchars($candidato['Email']); ?></li><?php endif; ?>
                <?php if (!empty($candidato['Telefone'])): ?><li class="list-group-item py-2"><strong>Telefone:</strong> <?php echo telephone($candidato['Telefone']); ?></li><?php endif; ?>
                <?php if (!empty($candidato['Nascimento'])): ?><li class="list-group-item py-2"><strong>Nascimento:</strong> <?php echo htmlspecialchars($candidato['Nascimento']); ?></li><?php endif; ?>
                <?php if (!empty($candidato['genero'])): ?><li class="list-group-item py-2"><strong>Gênero:</strong> <?php echo htmlspecialchars($candidato['genero']); ?></li><?php endif; ?>
              </ul>
            </div>
            
            <div class="col-md-6 mb-3">
              <h5 class="font-weight-bold text-dark mb-2"><i class="fas fa-map-marker-alt text-primary mr-2"></i>Endereço</h5>
              <ul class="list-group list-group-flush border rounded">
                <li class="list-group-item py-2"><strong>Endereço:</strong> <?php echo htmlspecialchars($candidato['endereco'] ?? 'Não informado'); ?></li>
                <li class="list-group-item py-2"><strong>Complemento:</strong> <?php echo htmlspecialchars($candidato['complemento'] ?? '—'); ?></li>
                <li class="list-group-item py-2"><strong>Cidade:</strong> <?php echo htmlspecialchars(($candidato['cidade'] ?? '') . ' - ' . ($candidato['UF'] ?? '')); ?></li>
                <li class="list-group-item py-2"><strong>CEP:</strong> <?php echo htmlspecialchars($candidato['CEP'] ?? '—'); ?></li>
              </ul>
            </div>
          </div>
          
          <!-- Cursos e Qualificações -->
          <?php if (!empty($candidato['cursos_externos']) || !empty($candidato['detalhes'])): ?>
          <div class="mb-4">
            <h5 class="font-weight-bold text-dark mb-2"><i class="fas fa-graduation-cap text-primary mr-2"></i>Cursos & Qualificações</h5>
            <div class="alert alert-light border bg-white p-3 rounded">
              <?php if (!empty($candidato['cursos_externos'])): ?>
                <div class="mb-2"><?php echo nl2br(htmlspecialchars($candidato['cursos_externos'])); ?></div>
              <?php endif; ?>
              <?php if (!empty($candidato['detalhes'])): ?>
                <div><?php echo nl2br(htmlspecialchars($candidato['detalhes'])); ?></div>
              <?php endif; ?>
            </div>
          </div>
          <?php endif; ?>
          
          <!-- Histórico de Eventos -->
          <?php
          $query_eventos = "SELECT em.nome as evento_nome, MIN(h.data_inicio) as data_inicio, MAX(h.data_final) as data_final
                            FROM disponibilidade d
                            JOIN horarios h ON d.atividade_id = h.horario_id
                            JOIN eventos_marcados em ON h.evento_id = em.id
                            WHERE d.candidato_id = ? AND d.escalado = 1
                            GROUP BY em.id
                            ORDER BY MIN(h.data_inicio) DESC";
          $stmt_eventos = mysqli_prepare($conexao, $query_eventos);
          mysqli_stmt_bind_param($stmt_eventos, "i", $candidato_id);
          mysqli_stmt_execute($stmt_eventos);
          $result_eventos = mysqli_stmt_get_result($stmt_eventos);
          if (mysqli_num_rows($result_eventos) > 0):
          ?>
          <div class="mb-4">
            <h5 class="font-weight-bold text-dark mb-2"><i class="fas fa-history text-primary mr-2"></i>Atuações no Projeto A.M.E.</h5>
            <div class="list-group">
              <?php while ($evt = mysqli_fetch_assoc($result_eventos)): ?>
                <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                  <strong><?php echo htmlspecialchars($evt['evento_nome']); ?></strong>
                  <span class="text-muted small">
                    <i class="far fa-calendar-alt mr-1"></i>
                    <?php 
                    $d_ini = new DateTime($evt['data_inicio']);
                    $d_fim = new DateTime($evt['data_final']);
                    echo ($d_ini->format('Y-m-d') == $d_fim->format('Y-m-d')) ? $d_ini->format('d/m/Y') : $d_ini->format('d/m/Y') . ' a ' . $d_fim->format('d/m/Y');
                    ?>
                  </span>
                </div>
              <?php endwhile; ?>
            </div>
          </div>
          <?php endif; ?>

        </div>
      </div>
      <?php else: ?>
        <div class="alert alert-warning">
          <i class="fas fa-exclamation-triangle mr-2"></i>Candidato ID <?php echo $candidato_id; ?> não encontrado.
        </div>
      <?php endif; ?>
    <?php endif; ?>

  </div>

  <?php
  include_once('./include/footer-database.php');
  include_once('./include/end.php');
  ?>

