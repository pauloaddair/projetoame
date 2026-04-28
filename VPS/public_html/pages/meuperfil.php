<?php
// meuperfil.php - Portal do Associado com Wizard Completo
// include_once('./include/head.php');
// include_once('./include/conexao.php');

// Verifica se está logado
$logado = isset($_SESSION['id']);
$usuario_id = $logado ? $_SESSION['id'] : 0;

// Se logado, busca os candidatos vinculados
$candidatos_vinculados = [];
if ($logado) {
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
            <header class="mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="font-weight-bold">Meu Perfil</h2>
                    <p class="text-muted">Gerencie as informações dos associados sob sua responsabilidade.</p>
                </div>
                <a href="/logout" class="btn btn-outline-danger btn-sm rounded-pill">Sair</a>
            </header>

            <?php if (count($candidatos_vinculados) > 1 && !isset($_GET['cand_id'])): ?>
                <div class="row">
                    <?php foreach ($candidatos_vinculados as $c): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card shadow-sm border-0">
                                <div class="card-body d-flex align-items-center">
                                    <div class="mr-3">
                                        <i class="fas fa-user-graduate fa-2x text-primary"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0"><?php echo $c['nome']; ?></h5>
                                        <a href="?cand_id=<?php echo $c['candidato_id']; ?>" class="stretched-link small text-primary">Gerenciar Perfil</a>
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
                <!-- WIZARD DE PERFIL -->
                <div class="card shadow border-0">
                    <div class="card-header bg-white py-3">
                        <ul class="nav nav-pills nav-justified" id="wizard-steps">
                            <li class="nav-item"><a class="nav-link active" data-step="1" href="#">Identificação</a></li>
                            <li class="nav-item"><a class="nav-link" data-step="2" href="#">Saúde</a></li>
                            <li class="nav-item"><a class="nav-link" data-step="3" href="#">Logística</a></li>
                            <li class="nav-item"><a class="nav-link" data-step="4" href="#">Endereço</a></li>
                        </ul>
                    </div>
                    <div class="card-body p-4">
                        <form id="wizard-form">
                            <input type="hidden" name="candidato_id" value="<?php echo $cand_id; ?>">
                            
                            <!-- Passo 1: Identificação -->
                            <div class="step-content" id="step-1">
                                <h5 class="mb-4 text-primary">Dados de Identificação</h5>
                                <div class="row">
                                    <div class="col-md-8 md-form"><label>Nome Completo</label><input type="text" name="nome" class="form-control" value="<?php echo $cand['nome']; ?>"></div>
                                    <div class="col-md-4 md-form"><label>Data Nascimento</label><input type="date" name="nascimento" class="form-control" value="<?php echo $cand['Nascimento']; ?>"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 md-form"><label>CPF do Atendente</label><input type="text" name="cpf" class="form-control" value="<?php echo $cand['CPF']; ?>"></div>
                                    <div class="col-md-6 md-form"><label>Chave PIX</label><input type="text" name="chave_pix" class="form-control" placeholder="E-mail, CPF ou Celular"></div>
                                </div>
                                <button type="button" class="btn btn-primary next-step float-right">Próximo</button>
                            </div>

                            <!-- Passo 2: Saúde -->
                            <div class="step-content" id="step-2" style="display:none;">
                                <h5 class="mb-4 text-primary">Saúde e Cuidados</h5>
                                <div class="md-form mb-3"><label>Restrições Alimentares</label><textarea name="restricoes_alimentares" class="form-control md-textarea"><?php echo $cand['restricoes_alimentares']; ?></textarea></div>
                                <div class="md-form mb-3"><label>Medicação Continuada</label><textarea name="medicacao_continuada" class="form-control md-textarea"><?php echo $cand['medicacao_continuada']; ?></textarea></div>
                                <div class="md-form mb-3"><label>Orientações ao Projeto</label><textarea name="orientacoes_responsaveis" class="form-control md-textarea"><?php echo $cand['orientacoes_responsaveis']; ?></textarea></div>
                                <button type="button" class="btn btn-link prev-step">Voltar</button>
                                <button type="button" class="btn btn-primary next-step float-right">Próximo</button>
                            </div>

                            <!-- Passo 3: Logística -->
                            <div class="step-content" id="step-3" style="display:none;">
                                <h5 class="mb-4 text-primary">Tamanhos e Uniforme</h5>
                                <div class="row">
                                    <div class="col-4">
                                        <label>Camisa</label>
                                        <select name="camisa" class="form-control">
                                            <option value="PP">PP</option><option value="P">P</option><option value="M">M</option><option value="G">G</option><option value="GG">GG</option>
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <label>Calça</label>
                                        <input type="number" name="calca" class="form-control" placeholder="Ex: 40">
                                    </div>
                                    <div class="col-4">
                                        <label>Calçado</label>
                                        <input type="number" name="calcado" class="form-control" placeholder="Ex: 38">
                                    </div>
                                </div>
                                <button type="button" class="btn btn-link prev-step mt-4">Voltar</button>
                                <button type="button" class="btn btn-primary next-step float-right mt-4">Próximo</button>
                            </div>

                            <!-- Passo 4: Endereço -->
                            <div class="step-content" id="step-4" style="display:none;">
                                <h5 class="mb-4 text-primary">Endereço de Residência</h5>
                                <div class="row">
                                    <div class="col-md-4 md-form"><label>CEP</label><input type="text" id="cep" name="cep" class="form-control"></div>
                                    <div class="col-md-8 md-form"><label>Rua/Logradouro</label><input type="text" id="logradouro" name="logradouro" class="form-control"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 md-form"><label>Número</label><input type="text" name="numero" class="form-control"></div>
                                    <div class="col-md-8 md-form"><label>Complemento</label><input type="text" name="complemento" class="form-control"></div>
                                </div>
                                <button type="button" class="btn btn-link prev-step">Voltar</button>
                                <button type="submit" class="btn btn-success float-right shadow">Salvar Tudo</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
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
        });
    </script>
</body>
