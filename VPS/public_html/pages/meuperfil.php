<?php
// meuperfil.php - Portal do Associado com Wizard Completo
// include_once('./include/head.php');
// include_once('./include/conexao.php');

// Verifica se está logado
$logado = isset($_SESSION['id']);
$usuario_id = $logado ? $_SESSION['id'] : 0;

// Se logado, busca os candidatos vinculados e dados de perfil do usuário
$candidatos_vinculados = [];
$user_info = null;
if ($logado) {
    // Busca dados do usuário
    $qu = "SELECT * FROM usuarios WHERE usuario_id = $usuario_id";
    $resu = mysqli_query($conexao, $qu);
    $user_info = mysqli_fetch_assoc($resu);

    // Busca candidatos vinculados
    $q = "SELECT c.* FROM candidatos c 
          JOIN candidatos_usuarios cu ON c.candidato_id = cu.candidato_id 
          WHERE cu.usuario_id = $usuario_id";
    $res = mysqli_query($conexao, $q);
    while($row = mysqli_fetch_assoc($res)) { $candidatos_vinculados[] = $row; }
}
?>
<body class="bg-light">
    <div class="container mt-4 mb-5">
        <?php if (!$logado): ?>
            <!-- Lógica de Login/Ativação já implementada anteriormente -->
            <div id="auth-container">
                <!-- (Conteúdo do auth-step-1 e auth-step-2 que já criamos) -->
            </div>
        <?php else: ?>
            <style>
                #perfilMenuTabs .nav-link {
                    color: rgba(255, 255, 255, 0.85) !important;
                    transition: all 0.2s ease-in-out;
                }
                #perfilMenuTabs .nav-link:hover {
                    color: #fff !important;
                    background-color: rgba(255, 255, 255, 0.15);
                }
                #perfilMenuTabs .nav-link.active {
                    color: #0056b3 !important;
                    background-color: #fff !important;
                    border-bottom: 3px solid #0056b3 !important;
                }
            </style>

            <header class="mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="font-weight-bold">Meu Perfil</h2>
                    <p class="text-muted">Gerencie suas informações e os associados sob sua responsabilidade.</p>
                </div>
                <div>
                    <button id="btnInvite" class="btn btn-outline-primary btn-sm rounded-pill">Convidar Responsável</button>
                    <a href="<?php echo $GLOBALS['app_web_root']; ?>logout" class="btn btn-outline-danger btn-sm rounded-pill ml-2">Sair</a>
                </div>
            </header>

            <!-- Abas de Navegação -->
            <ul class="nav nav-tabs md-tabs bg-primary bg-gradient rounded mb-4" id="perfilMenuTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active font-weight-bold" id="meus-dados-tab" data-toggle="tab" href="#meus-dados" role="tab" aria-controls="meus-dados" aria-selected="true">Meus Dados Cadastrais</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" id="associados-tab" data-toggle="tab" href="#associados" role="tab" aria-controls="associados" aria-selected="false">Dependentes / Associados (<?php echo count($candidatos_vinculados); ?>)</a>
                </li>
            </ul>

            <div class="tab-content" id="perfilMenuTabsContent">
                <!-- Aba 1: Meus Dados Cadastrais -->
                <div class="tab-pane fade show active" id="meus-dados" role="tabpanel" aria-labelledby="meus-dados-tab">
                    <div class="card shadow border-0 mb-4">
                        <div class="card-body p-4">
                            <h5 class="mb-4 text-primary font-weight-bold"><i class="fas fa-user-cog mr-2"></i>Minhas Informações Pessoais</h5>
                            <div class="row">
                                <!-- Foto de Perfil do Usuário -->
                                <div class="col-lg-3 text-center mb-4 border-right">
                                    <h6 class="font-weight-bold text-muted mb-3">Foto de Perfil</h6>
                                    <?php 
                                    $user_photo_url = "img/profile.png";
                                    if (!empty($user_info['imagem_id'])) {
                                        $res_p = mysqli_query($conexao, "SELECT url FROM imagens WHERE imagem_id = " . (int)$user_info['imagem_id']);
                                        if ($res_p && mysqli_num_rows($res_p) > 0) {
                                            $row_p = mysqli_fetch_assoc($res_p);
                                            $user_photo_url = $row_p['url'];
                                        }
                                    }
                                    ?>
                                    <div class="position-relative d-inline-block">
                                        <img src="<?php echo $GLOBALS['app_web_root'] . $user_photo_url; ?>" class="rounded-circle img-thumbnail shadow-sm mb-3" style="width: 130px; height: 130px; object-fit: cover;" alt="Foto de Perfil">
                                    </div>
                                    <div>
                                        <a href="<?php echo $GLOBALS['app_web_root']; ?>trocafoto-usuario" class="btn btn-outline-primary btn-sm rounded-pill"><i class="fas fa-camera mr-1"></i> Alterar Foto</a>
                                    </div>
                                </div>
                                
                                <!-- Formulário de Dados -->
                                <div class="col-lg-9">
                                    <form id="user-profile-form">
                                        <div class="row">
                                            <div class="col-md-6 md-form">
                                                <label class="active font-weight-bold">Nome</label>
                                                <input type="text" name="nome" class="form-control" value="<?php echo htmlspecialchars($user_info['nome'] ?? ''); ?>" required>
                                            </div>
                                            <div class="col-md-6 md-form">
                                                <label class="active font-weight-bold">Sobrenome</label>
                                                <input type="text" name="sobrenome" class="form-control" value="<?php echo htmlspecialchars($user_info['sobrenome'] ?? ''); ?>" required>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-6 md-form">
                                                <label class="active font-weight-bold">E-mail</label>
                                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user_info['email'] ?? ''); ?>" required>
                                            </div>
                                            <div class="col-md-6 md-form">
                                                <label class="active font-weight-bold">Telefone / WhatsApp</label>
                                                <input type="text" name="telefone" class="form-control" value="<?php echo htmlspecialchars($user_info['telefone'] ?? ''); ?>" required>
                                            </div>
                                        </div>
                                        <hr class="my-4">
                                        <h5 class="mb-4 text-warning font-weight-bold"><i class="fas fa-key mr-2"></i>Alterar Senha</h5>
                                        <div class="row">
                                            <div class="col-md-6 md-form">
                                                <label class="active font-weight-bold">Nova Senha</label>
                                                <input type="password" name="senha" class="form-control" placeholder="Deixe em branco para manter a atual">
                                            </div>
                                            <div class="col-md-6 md-form">
                                                <label class="active font-weight-bold">Confirmar Nova Senha</label>
                                                <input type="password" name="senha_confirm" class="form-control" placeholder="Repita a nova senha">
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm float-right mt-3"><i class="fas fa-save mr-1"></i> Salvar Meus Dados</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aba 2: Dependentes / Associados -->
                <div class="tab-pane fade" id="associados" role="tabpanel" aria-labelledby="associados-tab">
                    <?php if (count($candidatos_vinculados) === 0): ?>
                        <div class="alert alert-warning text-center p-5 shadow-sm border-0 bg-white rounded-lg">
                            <i class="fas fa-exclamation-circle fa-3x text-warning mb-3"></i>
                            <h4>Nenhum associado vinculado à sua conta</h4>
                            <p class="text-muted">No momento, você não possui nenhum candidato ou atendente sob sua responsabilidade cadastrado no sistema.</p>
                            <p class="text-muted">Se você acabou de se cadastrar ou precisa vincular um associado existente, entre em contato com a administração do Projeto AME para regularizar seu acesso.</p>
                            <div class="mt-4">
                                <a href="<?php echo $GLOBALS['app_web_root']; ?>inscrever" class="btn btn-primary rounded-pill px-4 shadow-sm"><i class="fas fa-user-plus mr-1"></i> Realizar Nova Inscrição</a>
                            </div>
                        </div>
                    <?php elseif (count($candidatos_vinculados) > 1 && !isset($_GET['cand_id'])): ?>
                        <div class="row">
                            <?php foreach ($candidatos_vinculados as $c): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card shadow-sm border-0">
                                        <div class="card-body d-flex align-items-center">
                                            <div class="mr-3">
                                                <i class="fas fa-user-graduate fa-2x text-primary"></i>
                                            </div>
                                            <div>
                                                <h5 class="mb-0 font-weight-bold"><?php echo htmlspecialchars($c['nome']); ?></h5>
                                                <a href="?cand_id=<?php echo $c['candidato_id']; ?>" class="stretched-link small text-primary font-weight-bold">Gerenciar Perfil</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: 
                        // Pega o candidato selecionado ou o único existente
                        $cand_id = isset($_GET['cand_id']) ? (int)$_GET['cand_id'] : $candidatos_vinculados[0]['candidato_id'];
                        $res_c = mysqli_query($conexao, "SELECT * FROM candidatos WHERE candidato_id = $cand_id");
                        $cand = mysqli_fetch_assoc($res_c);
                    ?>
                        <?php if (count($candidatos_vinculados) > 1): ?>
                            <div class="mb-3">
                                <a href="<?php echo $GLOBALS['app_web_root']; ?>meuperfil" class="btn btn-outline-secondary btn-sm rounded-pill">
                                    <i class="fas fa-arrow-left mr-1"></i> Voltar para Lista de Associados
                                </a>
                            </div>
                        <?php endif; ?>
                        <!-- WIZARD DE PERFIL -->
                        <div class="card shadow border-0">
                            <div class="card-header bg-white py-3">
                                <ul class="nav nav-pills nav-justified" id="wizard-steps">
                                    <li class="nav-item"><a class="nav-link active font-weight-bold" data-step="1" href="#"><i class="fas fa-id-card mr-1"></i> Identificação</a></li>
                                    <li class="nav-item"><a class="nav-link font-weight-bold" data-step="2" href="#"><i class="fas fa-heartbeat mr-1"></i> Saúde & Cuidados</a></li>
                                    <li class="nav-item"><a class="nav-link font-weight-bold" data-step="3" href="#"><i class="fas fa-tshirt mr-1"></i> Logística</a></li>
                                    <li class="nav-item"><a class="nav-link font-weight-bold" data-step="4" href="#"><i class="fas fa-graduation-cap mr-1"></i> Cursos & Formação</a></li>
                                    <li class="nav-item"><a class="nav-link font-weight-bold" data-step="5" href="#"><i class="fas fa-map-marker-alt mr-1"></i> Endereço</a></li>
                                </ul>
                            </div>
                            <div class="card-body p-4">
                                <form id="wizard-form">
                                    <input type="hidden" name="candidato_id" value="<?php echo $cand_id; ?>">
                                    
                                    <!-- Passo 1: Identificação -->
                                    <div class="step-content" id="step-1">
                                        <h5 class="mb-4 text-primary font-weight-bold"><i class="fas fa-id-badge mr-2"></i>Dados de Identificação</h5>
                                        <div class="row">
                                            <!-- Foto de Perfil do Candidato -->
                                            <div class="col-md-3 text-center mb-4 border-right">
                                                <label class="font-weight-bold text-muted d-block mb-3">Foto Oficial do Atendente</label>
                                                <?php 
                                                $cand_photo_url = "img/profile.png";
                                                if (!empty($cand['imagem_id'])) {
                                                    $res_cp = mysqli_query($conexao, "SELECT url FROM imagens WHERE imagem_id = " . (int)$cand['imagem_id']);
                                                    if ($res_cp && mysqli_num_rows($res_cp) > 0) {
                                                        $row_cp = mysqli_fetch_assoc($res_cp);
                                                        $cand_photo_url = $row_cp['url'];
                                                    }
                                                }
                                                ?>
                                                <img src="<?php echo $GLOBALS['app_web_root'] . $cand_photo_url; ?>" class="rounded-circle img-thumbnail shadow-sm mb-3" style="width: 120px; height: 120px; object-fit: cover;" alt="Foto do Candidato">
                                                <div>
                                                    <a href="<?php echo $GLOBALS['app_web_root']; ?>trocafoto/<?php echo $cand['candidato_id']; ?>" class="btn btn-outline-primary btn-sm rounded-pill py-1"><i class="fas fa-camera mr-1"></i> Alterar Foto</a>
                                                </div>
                                            </div>
                                            
                                            <!-- Campos de Identificação -->
                                            <div class="col-md-9">
                                                <div class="row">
                                                    <div class="col-md-8 md-form"><label class="active font-weight-bold">Nome Completo</label><input type="text" name="nome" class="form-control" value="<?php echo htmlspecialchars($cand['nome'] ?? ''); ?>" required></div>
                                                    <div class="col-md-4 md-form"><label class="active font-weight-bold">Data de Nascimento</label><input type="date" name="nascimento" class="form-control" value="<?php echo $cand['Nascimento']; ?>" required></div>
                                                </div>
                                                <div class="row mt-2">
                                                    <div class="col-md-6 md-form"><label class="active font-weight-bold">CPF do Atendente</label><input type="text" name="cpf" class="form-control" value="<?php echo htmlspecialchars($cand['CPF'] ?? ''); ?>"></div>
                                                    <div class="col-md-6 md-form"><label class="active font-weight-bold">Chave PIX (Ajuda de Custo)</label><input type="text" name="chave_pix" class="form-control" placeholder="E-mail, CPF ou Celular" value="<?php echo htmlspecialchars($cand['PIX'] ?? ''); ?>"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-primary next-step float-right mt-3 rounded-pill px-4">Próximo <i class="fas fa-arrow-right ml-1"></i></button>
                                    </div>

                                    <!-- Passo 2: Saúde e Cuidados -->
                                    <div class="step-content" id="step-2" style="display:none;">
                                        <h5 class="mb-4 text-primary font-weight-bold"><i class="fas fa-heartbeat mr-2"></i>Saúde, Medicação e Cuidados Especiais</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="md-form mb-4">
                                                    <label class="active font-weight-bold">Restrições Alimentares / Alergias</label>
                                                    <textarea name="restricoes_alimentares" class="form-control md-textarea" rows="3" placeholder="Ex: Intolerância à lactose, alergia a corantes..."><?php echo htmlspecialchars($cand['restricoes_alimentares'] ?? ''); ?></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="md-form mb-4">
                                                    <label class="active font-weight-bold">Medicação Continuada (Nomes e Dosagens)</label>
                                                    <textarea name="medicacao_continuada" class="form-control md-textarea" rows="3" placeholder="Ex: Risperidona 1mg, Levotiroxina 50mcg..."><?php echo htmlspecialchars($cand['medicacao_continuada'] ?? ''); ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="md-form mb-4">
                                                    <label class="active font-weight-bold">Horários e Instruções de Medicação</label>
                                                    <textarea name="medicacao_horarios" class="form-control md-textarea" rows="3" placeholder="Ex: 08:00 (em jejum), 14:00 (após almoço)..."><?php echo htmlspecialchars($cand['medicacao_horarios'] ?? ''); ?></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="md-form mb-4">
                                                    <label class="active font-weight-bold">Cuidados Especiais / Sensoriais</label>
                                                    <textarea name="cuidados_especiais" class="form-control md-textarea" rows="3" placeholder="Ex: Sensibilidade a ruídos muito altos, pausas regulares para água..."><?php echo htmlspecialchars($cand['cuidados_especiais'] ?? ''); ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="md-form mb-4">
                                            <label class="active font-weight-bold">Orientações Gerais aos Monitores do Projeto AME</label>
                                            <textarea name="orientacoes_responsaveis" class="form-control md-textarea" rows="3" placeholder="Observações de rotina que facilitam o acolhimento do atendente durante as feiras..."><?php echo htmlspecialchars($cand['orientacoes_responsaveis'] ?? ''); ?></textarea>
                                        </div>
                                        <button type="button" class="btn btn-outline-secondary prev-step rounded-pill px-4"><i class="fas fa-arrow-left mr-1"></i> Voltar</button>
                                        <button type="button" class="btn btn-primary next-step float-right rounded-pill px-4">Próximo <i class="fas fa-arrow-right ml-1"></i></button>
                                    </div>

                                    <!-- Passo 3: Logística e Uniforme -->
                                    <div class="step-content" id="step-3" style="display:none;">
                                        <h5 class="mb-4 text-primary font-weight-bold"><i class="fas fa-tshirt mr-2"></i>Tamanhos de Uniforme & Calçados</h5>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="font-weight-bold text-muted">Tamanho da Camisa</label>
                                                <select name="camisa" class="form-control custom-select">
                                                    <option value="PP" <?php echo (($cand['camisa'] ?? '') == 'PP') ? 'selected' : ''; ?>>PP</option>
                                                    <option value="P" <?php echo (($cand['camisa'] ?? '') == 'P') ? 'selected' : ''; ?>>P</option>
                                                    <option value="M" <?php echo (($cand['camisa'] ?? '') == 'M') ? 'selected' : ''; ?>>M</option>
                                                    <option value="G" <?php echo (($cand['camisa'] ?? '') == 'G') ? 'selected' : ''; ?>>G</option>
                                                    <option value="GG" <?php echo (($cand['camisa'] ?? '') == 'GG') ? 'selected' : ''; ?>>GG</option>
                                                    <option value="XG" <?php echo (($cand['camisa'] ?? '') == 'XG') ? 'selected' : ''; ?>>XG</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="font-weight-bold text-muted">Número da Calça</label>
                                                <input type="number" name="calca" class="form-control" placeholder="Ex: 40" value="<?php echo htmlspecialchars($cand['calca'] ?? ''); ?>">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="font-weight-bold text-muted">Número do Calçado</label>
                                                <input type="number" name="calcado" class="form-control" placeholder="Ex: 38" value="<?php echo htmlspecialchars($cand['sapato'] ?? ''); ?>">
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-outline-secondary prev-step mt-4 rounded-pill px-4"><i class="fas fa-arrow-left mr-1"></i> Voltar</button>
                                        <button type="button" class="btn btn-primary next-step float-right mt-4 rounded-pill px-4">Próximo <i class="fas fa-arrow-right ml-1"></i></button>
                                    </div>

                                    <!-- Passo 4: Cursos e Qualificações Externas -->
                                    <div class="step-content" id="step-4" style="display:none;">
                                        <h5 class="mb-4 text-primary font-weight-bold"><i class="fas fa-graduation-cap mr-2"></i>Cursos Externos & Formação</h5>
                                        <p class="text-muted small">Adicione cursos, oficinas e capacitações realizadas pelo atendente fora do Projeto AME. Esses dados serão incorporados automaticamente ao currículo oficial.</p>
                                        <div class="md-form mb-4">
                                            <label class="active font-weight-bold">Cursos, Certificados e Formações (Um por linha)</label>
                                            <textarea name="cursos_externos" class="form-control md-textarea" rows="4" placeholder="Ex:
- Curso de Informática Básica (SENAI, 2024)
- Oficina de Atendimento ao Público (2023)
- Capacitação em Barista / Gastronomia (2025)"><?php echo htmlspecialchars($cand['cursos_externos'] ?? ''); ?></textarea>
                                        </div>
                                        <button type="button" class="btn btn-outline-secondary prev-step rounded-pill px-4"><i class="fas fa-arrow-left mr-1"></i> Voltar</button>
                                        <button type="button" class="btn btn-primary next-step float-right rounded-pill px-4">Próximo <i class="fas fa-arrow-right ml-1"></i></button>
                                    </div>

                                    <!-- Passo 5: Endereço -->
                                    <div class="step-content" id="step-5" style="display:none;">
                                        <h5 class="mb-4 text-primary font-weight-bold"><i class="fas fa-map-marked-alt mr-2"></i>Endereço de Residência</h5>
                                        <div class="row">
                                            <div class="col-md-4 md-form"><label class="active font-weight-bold">CEP</label><input type="text" id="cep" name="cep" class="form-control" value="<?php echo htmlspecialchars($cand['CEP'] ?? ''); ?>"></div>
                                            <div class="col-md-8 md-form"><label class="active font-weight-bold">Rua / Logradouro</label><input type="text" id="logradouro" name="logradouro" class="form-control" value="<?php echo htmlspecialchars(explode(',', $cand['endereco'] ?? '')[0]); ?>"></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 md-form"><label class="active font-weight-bold">Número</label><input type="text" name="numero" class="form-control" value="<?php echo htmlspecialchars(trim(explode(',', $cand['endereco'] ?? '')[1] ?? '')); ?>"></div>
                                            <div class="col-md-8 md-form"><label class="active font-weight-bold">Complemento / Bairro</label><input type="text" name="complemento" class="form-control" value="<?php echo htmlspecialchars($cand['complemento'] ?? ''); ?>"></div>
                                        </div>
                                        <button type="button" class="btn btn-outline-secondary prev-step rounded-pill px-4"><i class="fas fa-arrow-left mr-1"></i> Voltar</button>
                                        <button type="submit" class="btn btn-success float-right shadow rounded-pill px-5"><i class="fas fa-save mr-1"></i> Salvar Tudo</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Card de Trajetória, Currículo & Álbum de Fotos -->
                        <div class="card shadow border-0 mt-4">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="font-weight-bold text-primary mb-0"><i class="fas fa-images mr-2"></i>Álbum de Atuações & Currículo Profissional</h5>
                                    <div>
                                        <a href="<?php echo $GLOBALS['app_web_root']; ?>curriculo/<?php echo $cand['candidato_id']; ?>" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill font-weight-bold mr-2">
                                            <i class="fas fa-file-pdf mr-1"></i> Ver Currículo PDF
                                        </a>
                                        <button class="btn btn-outline-success btn-sm rounded-pill font-weight-bold" onclick="alert('Funcionalidade de Portfólio Impresso em parceria com a AlphaGraphics será ativada em breve!');">
                                            <i class="fas fa-book-open mr-1"></i> Montar Portfólio Impresso
                                        </button>
                                    </div>
                                </div>
                                <p class="text-muted small">Fotos reconhecidas automaticamente pela inteligência biométrica durante as participações do atendente em feiras e eventos oficiais:</p>

                                <?php
                                // Busca fotos reconhecidas para o candidato
                                $q_fotos = "SELECT f.*, e.nome AS evento_nome FROM fotos_reconhecidas f 
                                            LEFT JOIN eventos_marcados e ON f.evento_id = e.id 
                                            WHERE f.candidato_id = $cand_id 
                                            ORDER BY f.data_reconhecimento DESC LIMIT 12";
                                $res_fotos = mysqli_query($conexao, $q_fotos);
                                ?>

                                <?php if ($res_fotos && mysqli_num_rows($res_fotos) > 0): ?>
                                    <div class="row">
                                        <?php while ($ft = mysqli_fetch_assoc($res_fotos)): ?>
                                            <div class="col-md-3 col-sm-6 mb-3">
                                                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                                                    <img src="<?php echo htmlspecialchars($ft['url_foto']); ?>" class="card-img-top" style="height: 160px; object-fit: cover;" alt="Atuação em Feira">
                                                    <div class="card-body p-2 text-center bg-light">
                                                        <small class="font-weight-bold text-dark d-block text-truncate"><?php echo htmlspecialchars($ft['evento_nome'] ?? 'Evento AME'); ?></small>
                                                        <small class="text-muted"><?php echo date('d/m/Y', strtotime($ft['data_reconhecimento'])); ?></small>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-4 bg-light rounded border border-dashed text-muted">
                                        <i class="fas fa-camera fa-2x mb-2 text-secondary"></i>
                                        <p class="mb-0">Nenhuma foto de evento vinculada ainda. As fotos das próximas feiras serão catalogadas automaticamente aqui.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Envia formulário de perfil do usuário via AJAX
            $('#user-profile-form').submit(function(e) {
                e.preventDefault();
                
                const senha = $(this).find('input[name="senha"]').val();
                const senhaConfirm = $(this).find('input[name="senha_confirm"]').val();
                if (senha !== "" && senha.length < 6) {
                    alert('A nova senha deve conter pelo menos 6 caracteres.');
                    return;
                }
                if (senha !== senhaConfirm) {
                    alert('As senhas não coincidem.');
                    return;
                }

                const formData = $(this).serialize();
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Salvando...');
                
                $.ajax({
                    url: '<?php echo $GLOBALS['app_web_root']; ?>api_usuario_save',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            window.location.reload();
                        } else {
                            alert('Erro: ' + response.message);
                        }
                        submitBtn.prop('disabled', false).html(originalText);
                    },
                    error: function() {
                        alert('Erro de conexão ao salvar os dados. Tente novamente.');
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Lógica de Navegação do Wizard
            $('.next-step').click(function() {
                const current = $(this).closest('.step-content');
                const nextStepNum = parseInt(current.attr('id').replace('step-', '')) + 1;
                current.hide();
                $(`#step-${nextStepNum}`).fadeIn();
                $(`#wizard-steps a[data-step="${nextStepNum}"]`).addClass('active');
            });

            $('.prev-step').click(function() {
                const current = $(this).closest('.step-content');
                const prevStepNum = parseInt(current.attr('id').replace('step-', '')) - 1;
                current.hide();
                $(`#step-${prevStepNum}`).fadeIn();
                $(`#wizard-steps a[data-step="${prevStepNum+1}"]`).removeClass('active');
            });

            // Busca de CEP (ViaCEP)
            $('#cep').blur(function() {
                const cep = $(this).val().replace(/\D/g, '');
                if (cep.length === 8) {
                    $.getJSON(`https://viacep.com.br/ws/${cep}/json/`, function(data) {
                        if (!("erro" in data)) {
                            $('#logradouro').val(data.logradouro).addClass('active');
                            $('#wizard-form input[name="bairro"]').val(data.bairro); // Se houver campo
                        }
                    });
                }
            });

            // Envia formulário via AJAX
            $('#wizard-form').submit(function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Salvando...');
                
                $.ajax({
                    url: '<?php echo $GLOBALS['app_web_root']; ?>api_perfil_save',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            window.location.reload();
                        } else {
                            alert('Erro: ' + response.message);
                        }
                        submitBtn.prop('disabled', false).html(originalText);
                    },
                    error: function() {
                        alert('Erro de conexão ao salvar os dados. Tente novamente.');
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });
        });
    </script>
<!-- Invite Responsible Modal -->
<div class="modal fade" id="inviteModal" tabindex="-1" role="dialog" aria-labelledby="inviteModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="inviteModalLabel">Convidar Responsável</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="inviteForm">
          <div class="form-group">
            <label for="inviteEmail">E‑mail do responsável</label>
            <input type="email" class="form-control" id="inviteEmail" name="email" required />
          </div>
          <input type="hidden" name="candidato_id" value="<?php echo $cand_id; ?>" />
          <button type="submit" class="btn btn-primary">Enviar convite</button>
        </form>
        <div id="inviteResult" class="mt-2"></div>
      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function(){
  $('#btnInvite').on('click', function(){ $('#inviteModal').modal('show'); });
  $('#inviteForm').on('submit', function(e){
    e.preventDefault();
    const data = $(this).serialize();
    $.post('/api/invite_responsavel.php', data, function(resp){
      $('#inviteResult').html('<div class="alert alert-success">'+resp.message+'</div>');
    }).fail(function(xhr){
      const err = xhr.responseJSON ? xhr.responseJSON.message : 'Erro ao enviar convite';
      $('#inviteResult').html('<div class="alert alert-danger">'+err+'</div>');
    });
  });
});
</script>
</body>
