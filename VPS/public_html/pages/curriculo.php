<?php
$titulo = "Currículo";
// include_once('include/conexao.php');

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
              WHERE c.candidato_id = ? AND c.ativo = 1";
    $stmt = mysqli_prepare($conexao, $query);
    mysqli_stmt_bind_param($stmt, "i", $candidato_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $candidato = mysqli_fetch_assoc($result);
    
    if ($candidato) {
        // Incluir FPDF
        require_once('fpdf/fpdf.php');
        
        // Criar PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        
        // Adicionar imagem de perfil se existir
        if ($candidato['imagem_url']) {
            // Posicionar imagem no canto superior direito
            $pdf->Image('' . $candidato['imagem_url'], 160, 10, 30, 40);
        }
        
        $pdf->SetFont('Arial', 'B', 16);
        
        // Título
        $pdf->Cell(0, 10, 'CURRICULO VITAE', 0, 1, 'C');
        $pdf->Ln(15); // Espaço extra para a imagem
        
        // Dados pessoais
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 8, 'DADOS PESSOAIS', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        
        $pdf->Cell(0, 6, 'Nome: ' . iconv("UTF-8", "ISO-8859-1", $candidato['nome']), 0, 1);
        if ($candidato['Email']) $pdf->Cell(0, 6, 'Email: ' . $candidato['Email'], 0, 1);
        if ($candidato['Telefone']) $pdf->Cell(0, 6, 'Telefone: ' . $candidato['Telefone'], 0, 1);
        if ($candidato['Nascimento']) $pdf->Cell(0, 6, 'Data de Nascimento: ' . $candidato['Nascimento'], 0, 1);
        if ($candidato['genero']) $pdf->Cell(0, 6, iconv("UTF-8", "ISO-8859-1", 'Gênero: ') . iconv("UTF-8", "ISO-8859-1", $candidato['genero']), 0, 1);
        if ($candidato['RG']) $pdf->Cell(0, 6, 'RG: ' . $candidato['RG'], 0, 1);
        if ($candidato['CPF']) $pdf->Cell(0, 6, 'CPF: ' . $candidato['CPF'], 0, 1);
        
        // Link do crachá virtual
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
        
        // Dados profissionais
        if ($candidato['CTPS'] || $candidato['PIX']) {
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 8, 'DADOS PROFISSIONAIS', 0, 1);
            $pdf->SetFont('Arial', '', 12);
            
            if ($candidato['CTPS']) {
                $ctps = $candidato['CTPS'];
                if ($candidato['CTPS_serie']) $ctps .= ' - Série: ' . $candidato['CTPS_serie'];
                $pdf->Cell(0, 6, 'CTPS: ' . $ctps, 0, 1);
            }
            if ($candidato['PIX']) $pdf->Cell(0, 6, 'PIX: ' . $candidato['PIX'], 0, 1);
            
            $pdf->Ln(5);
        }
        
        // Detalhes complementares (cursos, experiências, etc.)
        if ($candidato['detalhes']) {
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 8, iconv("UTF-8", "ISO-8859-1", 'QUALIFICAÇÕES E CURSOS'), 0, 1);
            $pdf->SetFont('Arial', '', 12);
            $pdf->MultiCell(0, 6, iconv("UTF-8", "ISO-8859-1", $candidato['detalhes']));
            $pdf->Ln(5);
        }
        
        // Informações adicionais
        if ($candidato['camisa'] || $candidato['calca'] || $candidato['sapato']) {
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 8, iconv("UTF-8", "ISO-8859-1", 'INFORMAÇÕES ADICIONAIS'), 0, 1);
            $pdf->SetFont('Arial', '', 12);
            
            if ($candidato['camisa']) $pdf->Cell(0, 6, 'Tamanho Camisa: ' . $candidato['camisa'], 0, 1);
            if ($candidato['calca']) $pdf->Cell(0, 6, iconv("UTF-8", "ISO-8859-1", 'Tamanho Calça: ') . $candidato['calca'], 0, 1);
            if ($candidato['sapato']) $pdf->Cell(0, 6, 'Tamanho Sapato: ' . $candidato['sapato'], 0, 1);
            
            $pdf->Ln(5);
        }
        
        // Responsável (se houver)
        if ($candidato['responsavel']) {
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 8, iconv("UTF-8", "ISO-8859-1", 'RESPONSÁVEL'), 0, 1);
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 6, iconv("UTF-8", "ISO-8859-1", 'Responsável: ') . iconv("UTF-8", "ISO-8859-1", $candidato['responsavel']), 0, 1);
            if ($candidato['CPF_RESP']) $pdf->Cell(0, 6, iconv("UTF-8", "ISO-8859-1", 'CPF do Responsável: ') . $candidato['CPF_RESP'], 0, 1);
            
            // Tipo de curatela
            $curatela_tipos = [
                0 => iconv("UTF-8", "ISO-8859-1", 'Não informado'),
                1 => 'Total',
                2 => 'Parcial',
                3 => 'Sem curatela'
            ];
            $pdf->Cell(0, 6, 'Curatela: ' . $curatela_tipos[$candidato['curatela']], 0, 1);
        }
        
        // Mensagem adicional
        if ($candidato['mensagem']) {
            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 8, iconv("UTF-8", "ISO-8859-1", 'OBSERVAÇÕES'), 0, 1);
            $pdf->SetFont('Arial', '', 12);
            $pdf->MultiCell(0, 6, iconv("UTF-8", "ISO-8859-1", $candidato['mensagem']));
        }
        
        // Rodapé
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->Cell(0, 6, iconv("UTF-8", "ISO-8859-1", 'Currículo gerado em: ') . date('d/m/Y H:i:s'), 0, 1, 'C');
        $pdf->Cell(0, 6, 'Projeto A.M.E. - Atendentes Muito Especiais', 0, 1, 'C');
        
        // Adicionar texto da certificação em corpo 6
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(0, 4, iconv("UTF-8", "ISO-8859-1", 'profissional capacitado pela Ass. Bras. de Inclusão Através do Trabalho'), 0, 1, 'C');
        
        // Output do PDF
        $nome_arquivo = 'curriculo_' . preg_replace('/[^a-zA-Z0-9]/', '_', $candidato['nome']) . '.pdf';
        $pdf->Output('D', $nome_arquivo);
        exit;
    }
}
// include_once('include/head.php');

// Buscar candidatos ativos
$query_candidatos = "SELECT candidato_id, nome, Email, Telefone, cidade FROM candidatos WHERE ativo = 1 ORDER BY nome";
$result_candidatos = mysqli_query($conexao, $query_candidatos);
?>

<body class="grey lighten-3">

  <!--Main Navigation-->
  <header>
    <!-- Navbar -->
    <nav class="navbar fixed-top navbar-expand-lg navbar-dark indigo scrolling-navbar">
      <?php include_once('include/header.php'); ?>
    </nav>
    <!-- Navbar -->
  </header>
  <!--Main Navigation-->

  <!--Main layout-->
  <main class="pt-5 mx-lg-5">
    <div class="container-fluid mt-5">

      <!-- Heading -->
      <div class="card mb-4 wow fadeIn">
        <div class="card-body d-sm-flex justify-content-between">
          <h4 class="mb-2 mb-sm-0 pt-1">
            <i class="fas fa-file-pdf mr-2"></i>
            Gerador de Currículo
          </h4>
        </div>
      </div>
      <!-- Heading -->

      <!-- Mensagens de feedback -->
      <?php if (isset($mensagem_sucesso)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="fas fa-check-circle mr-2"></i><?php echo $mensagem_sucesso; ?>
          <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
          </button>
        </div>
      <?php endif; ?>
      
      <?php if (isset($mensagem_erro)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="fas fa-exclamation-circle mr-2"></i><?php echo $mensagem_erro; ?>
          <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
          </button>
        </div>
      <?php endif; ?>

      <!-- Grid row -->
      <div class="row wow fadeIn">
        <!-- Grid column -->
        <div class="col-md-12 mb-4">
          <!-- Card -->
          <div class="card">
            <!-- Card header -->
            <div class="card-header">
              <h5 class="card-title mb-0">
                <i class="fas fa-users mr-2"></i>
                Selecionar Candidato
              </h5>
            </div>
            <!-- Card content -->
            <div class="card-body">
              <div class="row">
                <div class="col-md-12">
                  <form method="GET" action="">
                    <div class="form-group">
                      <label for="candidato_select">Escolha o candidato:</label>
                      <select class="form-control" id="candidato_select" name="candidato_id" required>
                        <option value="">Selecione um candidato...</option>
                        <?php while ($candidato = mysqli_fetch_assoc($result_candidatos)): ?>
                          <option value="<?php echo $candidato['candidato_id']; ?>" 
                                  <?php echo (isset($_GET['candidato_id']) && $_GET['candidato_id'] == $candidato['candidato_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($candidato['nome']); ?>
                            <?php if ($candidato['Email']): ?> - <?php echo htmlspecialchars($candidato['Email']); ?><?php endif; ?>
                            <?php if ($candidato['cidade']): ?> (<?php echo htmlspecialchars($candidato['cidade']); ?>)<?php endif; ?>
                          </option>
                        <?php endwhile; ?>
                      </select>
                    </div>
                    <div class="form-group">
                      <button type="submit" name="visualizar" class="btn btn-info mr-2">
                        <i class="fas fa-eye mr-1"></i>
                        Visualizar Dados
                      </button>
                      <button type="submit" name="gerar_pdf" value="1" class="btn btn-primary">
                        <i class="fas fa-file-pdf mr-1"></i>
                        Gerar PDF
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <!-- Card -->
        </div>
        <!-- Grid column -->
      </div>
      <!-- Grid row -->

      <?php if (isset($_GET['visualizar']) && isset($_GET['candidato_id'])): ?>
        <?php
        $candidato_id = (int)$_GET['candidato_id'];
        // Buscar dados do candidato com imagem
        $query = "SELECT c.*, i.url as imagem_url FROM candidatos c 
                  LEFT JOIN imagens i ON c.imagem_id = i.imagem_id 
                  WHERE c.candidato_id = ? AND c.ativo = 1";
        $stmt = mysqli_prepare($conexao, $query);
        mysqli_stmt_bind_param($stmt, "i", $candidato_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $candidato = mysqli_fetch_assoc($result);
        
        if ($candidato):
        ?>
        
        <!-- Formulário de Detalhes -->
        <div class="row wow fadeIn">
          <div class="col-md-12 mb-4">
            <div class="card">
              <div class="card-header">
                <h5 class="card-title mb-0">
                  <i class="fas fa-edit mr-2"></i>
                  Detalhes Complementares - <?php echo htmlspecialchars($candidato['nome']); ?>
                </h5>
              </div>
              <div class="card-body">
                <form method="POST" action="">
                  <input type="hidden" name="candidato_id" value="<?php echo $candidato['candidato_id']; ?>">
                  <div class="form-group">
                    <label for="detalhes">Qualificações, Cursos e Experiências:</label>
                    <textarea class="form-control" id="detalhes" name="detalhes" rows="6" 
                              placeholder="Ex: Curso de Atendimento ao Cliente (2023)&#10;Experiência em vendas (2 anos)&#10;Curso de Informática Básica&#10;Conhecimento em Excel"><?php echo htmlspecialchars($candidato['detalhes'] ?? ''); ?></textarea>
                    <small class="form-text text-muted">
                      <i class="fas fa-info-circle mr-1"></i>
                      Adicione informações sobre cursos realizados, experiências profissionais, habilidades especiais, etc.
                    </small>
                  </div>
                  <div class="form-group">
                    <button type="submit" name="salvar_detalhes" class="btn btn-success">
                      <i class="fas fa-save mr-1"></i>
                      Salvar Detalhes
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Preview do Currículo -->
        <div class="row wow fadeIn">
          <div class="col-md-12 mb-4">
            <div class="card">
              <div class="card-header">
                <h5 class="card-title mb-0">
                  <i class="fas fa-user mr-2"></i>
                  Preview do Currículo - <?php echo htmlspecialchars($candidato['nome']); ?>
                </h5>
              </div>
              <div class="card-body">
                <div class="curriculum-preview">
                  <!-- Cabeçalho com foto -->
                  <div class="row mb-4">
                    <div class="col-md-8">
                      <h3 class="mb-2">CURRÍCULO VITAE</h3>
                      <h4 class="text-primary"><?php echo htmlspecialchars($candidato['nome']); ?></h4>
                      <p class="mb-2">
                        <a href="https://contacte.me/projetoame?a=<?php echo $candidato['candidato_id']; ?>" 
                           target="_blank" class="btn btn-outline-primary btn-sm">
                          <i class="fas fa-id-card mr-1"></i>
                          Ver Crachá Virtual
                        </a>
                      </p>
                    </div>
                    <div class="col-md-4 text-right">
                      <?php if ($candidato['imagem_url']): ?>
                        <img src="<?php echo htmlspecialchars($candidato['imagem_url']); ?>" 
                             alt="Foto de <?php echo htmlspecialchars($candidato['nome']); ?>" 
                             class="img-fluid rounded" 
                             style="max-width: 150px; max-height: 200px; object-fit: cover; border: 2px solid #ddd;">
                      <?php else: ?>
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                             style="width: 150px; height: 200px; border: 2px solid #ddd;">
                          <i class="fas fa-user fa-3x text-muted"></i>
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-md-6">
                      <h5><i class="fas fa-user-circle mr-2"></i>Dados Pessoais</h5>
                      <table class="table table-sm">
                        <tr><td><strong>Nome:</strong></td><td><?php echo htmlspecialchars($candidato['nome']); ?></td></tr>
                        <?php if ($candidato['Email']): ?><tr><td><strong>Email:</strong></td><td><a href="mailto:<?php echo htmlspecialchars($candidato['Email']); ?>" class="text-primary"><i class="fas fa-envelope mr-1"></i><?php echo htmlspecialchars($candidato['Email']); ?></a></td></tr><?php endif; ?>
                        <?php if ($candidato['Telefone']): ?><tr><td><strong>Telefone:</strong></td><td><a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $candidato['Telefone']); ?>" class="text-primary"><i class="fas fa-phone mr-1"></i><?php echo htmlspecialchars($candidato['Telefone']); ?></a></td></tr><?php endif; ?>
                        <?php if ($candidato['Nascimento']): ?><tr><td><strong>Nascimento:</strong></td><td><?php echo htmlspecialchars($candidato['Nascimento']); ?></td></tr><?php endif; ?>
                        <?php if ($candidato['genero']): ?><tr><td><strong>Gênero:</strong></td><td><?php echo htmlspecialchars($candidato['genero']); ?></td></tr><?php endif; ?>
                        <?php if ($candidato['RG']): ?><tr><td><strong>RG:</strong></td><td><?php echo htmlspecialchars($candidato['RG']); ?></td></tr><?php endif; ?>
                        <?php if ($candidato['CPF']): ?><tr><td><strong>CPF:</strong></td><td><?php echo htmlspecialchars($candidato['CPF']); ?></td></tr><?php endif; ?>
                      </table>
                    </div>
                    
                    <div class="col-md-6">
                      <?php if ($candidato['endereco'] || $candidato['cidade']): ?>
                      <h5><i class="fas fa-map-marker-alt mr-2"></i>Endereço</h5>
                      <table class="table table-sm">
                        <?php if ($candidato['endereco']): ?><tr><td><strong>Endereço:</strong></td><td><?php echo htmlspecialchars($candidato['endereco']); ?></td></tr><?php endif; ?>
                        <?php if ($candidato['complemento']): ?><tr><td><strong>Complemento:</strong></td><td><?php echo htmlspecialchars($candidato['complemento']); ?></td></tr><?php endif; ?>
                        <?php if ($candidato['cidade']): ?><tr><td><strong>Cidade:</strong></td><td><?php echo htmlspecialchars($candidato['cidade']) . ' - ' . $candidato['UF']; ?></td></tr><?php endif; ?>
                        <?php if ($candidato['CEP']): ?><tr><td><strong>CEP:</strong></td><td><?php echo htmlspecialchars($candidato['CEP']); ?></td></tr><?php endif; ?>
                      </table>
                      <?php endif; ?>
                    </div>
                  </div>
                  
                  <?php if ($candidato['detalhes']): ?>
                  <div class="row mt-3">
                    <div class="col-md-12">
                      <h5><i class="fas fa-graduation-cap mr-2"></i>Qualificações e Cursos</h5>
                      <div class="alert alert-light">
                        <?php echo nl2br(htmlspecialchars($candidato['detalhes'])); ?>
                      </div>
                    </div>
                  </div>
                  <?php endif; ?>
                  
                  <?php if ($candidato['CTPS'] || $candidato['PIX']): ?>
                  <div class="row mt-3">
                    <div class="col-md-6">
                      <h5><i class="fas fa-briefcase mr-2"></i>Dados Profissionais</h5>
                      <table class="table table-sm">
                        <?php if ($candidato['CTPS']): ?>
                        <tr><td><strong>CTPS:</strong></td><td><?php echo htmlspecialchars($candidato['CTPS']); ?><?php if ($candidato['CTPS_serie']): ?> - Série: <?php echo htmlspecialchars($candidato['CTPS_serie']); ?><?php endif; ?></td></tr>
                        <?php endif; ?>
                        <?php if ($candidato['PIX']): ?><tr><td><strong>PIX:</strong></td><td><?php echo htmlspecialchars($candidato['PIX']); ?></td></tr><?php endif; ?>
                      </table>
                    </div>
                    
                    <?php if ($candidato['camisa'] || $candidato['calca'] || $candidato['sapato']): ?>
                    <div class="col-md-6">
                      <h5><i class="fas fa-tshirt mr-2"></i>Informações Adicionais</h5>
                      <table class="table table-sm">
                        <?php if ($candidato['camisa']): ?><tr><td><strong>Tamanho Camisa:</strong></td><td><?php echo htmlspecialchars($candidato['camisa']); ?></td></tr><?php endif; ?>
                        <?php if ($candidato['calca']): ?><tr><td><strong>Tamanho Calça:</strong></td><td><?php echo htmlspecialchars($candidato['calca']); ?></td></tr><?php endif; ?>
                        <?php if ($candidato['sapato']): ?><tr><td><strong>Tamanho Sapato:</strong></td><td><?php echo htmlspecialchars($candidato['sapato']); ?></td></tr><?php endif; ?>
                      </table>
                    </div>
                    <?php endif; ?>
                  </div>
                  <?php endif; ?>
                  
                  <?php if ($candidato['responsavel']): ?>
                  <div class="row mt-3">
                    <div class="col-md-12">
                      <h5><i class="fas fa-user-friends mr-2"></i>Responsável</h5>
                      <table class="table table-sm">
                        <tr><td><strong>Responsável:</strong></td><td><?php echo htmlspecialchars($candidato['responsavel']); ?></td></tr>
                        <?php if ($candidato['CPF_RESP']): ?><tr><td><strong>CPF do Responsável:</strong></td><td><?php echo htmlspecialchars($candidato['CPF_RESP']); ?></td></tr><?php endif; ?>
                        <tr><td><strong>Curatela:</strong></td><td>
                          <?php
                          $curatela_tipos = [
                              0 => 'Não informado',
                              1 => 'Total',
                              2 => 'Parcial',
                              3 => 'Sem curatela'
                          ];
                          echo $curatela_tipos[$candidato['curatela']];
                          ?>
                        </td></tr>
                      </table>
                    </div>
                  </div>
                  <?php endif; ?>
                  
                  <?php if ($candidato['mensagem']): ?>
                  <div class="row mt-3">
                    <div class="col-md-12">
                      <h5><i class="fas fa-comment mr-2"></i>Observações</h5>
                      <div class="alert alert-info">
                        <?php echo nl2br(htmlspecialchars($candidato['mensagem'])); ?>
                      </div>
                    </div>
                  </div>
                  <?php endif; ?>
                  
                  <div class="text-center mt-4">
                    <small class="text-muted">
                      <i class="fas fa-calendar mr-1"></i>
                      Data de inscrição: <?php echo date('d/m/Y H:i', strtotime($candidato['data_inscricao'])); ?>
                    </small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php endif; ?>
      <?php endif; ?>

    </div>
  </main>
  <!--Main layout-->

  <!--Footer-->
  <footer class="page-footer text-center font-small primary-color-dark darken-2 mt-4 wow fadeIn">
    <?php include_once('footer.php'); ?>
  </footer>
  <!--/.Footer-->

</body>
</html>
