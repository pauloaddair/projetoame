<?php
// adminnovoevento.php - Interface unificada para criação de eventos, imagens e turnos
include_once('./include/funcoes.php');
// include_once('./include/conexao.php');
// include_once('./include/head.php');

$msg = "";
$evento_id = 0;
$imagens = [];

// Busca imagens existentes para o seletor
$queryImagens = "SELECT imagem_id, url FROM imagens ORDER BY url DESC LIMIT 20";
$respImagens = mysqli_query($conexao, $queryImagens);
while ($row = mysqli_fetch_assoc($respImagens)) {
    $imagens[] = $row;
}

// 1. Processamento da Criação do Evento
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['criar_evento'])) {
    $imagem_id = 0;

    // Lógica de Upload de Nova Imagem
    if (isset($_FILES['nova_imagem']) && $_FILES['nova_imagem']['error'] == 0) {
        $target_dir = "img/";
        $filename = basename($_FILES["nova_imagem"]["name"]);
        $target_file = $base_path . $target_dir . $filename;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (move_uploaded_file($_FILES["nova_imagem"]["tmp_name"], $target_file)) {
            $imageUrl = $target_dir . $filename;
            $queryInsertImage = "INSERT INTO imagens (url) VALUES ('$imageUrl')";
            if (mysqli_query($conexao, $queryInsertImage)) {
                $imagem_id = mysqli_insert_id($conexao);
            }
        }
    } elseif (!empty($_POST['imagem_id'])) {
        $imagem_id = (int)$_POST['imagem_id'];
    }

    if ($imagem_id > 0) {
        $nome = mysqli_real_escape_string($conexao, $_POST['nome']);
        $tipo = mysqli_real_escape_string($conexao, $_POST['tipo_atividade']);
        $inicio = mysqli_real_escape_string($conexao, $_POST['inicio']);
        $final = mysqli_real_escape_string($conexao, $_POST['final']);
        $local = mysqli_real_escape_string($conexao, $_POST['local']);
        $endereco = mysqli_real_escape_string($conexao, $_POST['endereco']);
        $maps = mysqli_real_escape_string($conexao, $_POST['maps']);
        $obs = mysqli_real_escape_string($conexao, $_POST['obs']);
        $expositor_id = !empty($_POST['expositor_id']) ? (int)$_POST['expositor_id'] : 'NULL';

        $queryInsert = "INSERT INTO eventos_marcados (nome, tipo, inicio, final, local, endereco, maps, imagem_id, obs, aval_grupo, escala_fechada, expositor_id)
                        VALUES ('$nome', '$tipo', '$inicio', '$final', '$local', '$endereco', '$maps', $imagem_id, '$obs', 1, 1, $expositor_id)";
        if (mysqli_query($conexao, $queryInsert)) {
            $evento_id = mysqli_insert_id($conexao);
            header("Location: /admin/evento/" . $evento_id . "?msg=success");
            exit;
        } else {
            $msg = "<div class='alert alert-danger'>Erro ao cadastrar evento: " . mysqli_error($conexao) . "</div>";
        }
    } else {
        $msg = "<div class='alert alert-warning'>Selecione ou envie uma imagem para o evento.</div>";
    }
}

// 2. Processamento da Adição de Turnos (via AJAX ou Post)
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['add_horario'])) {
    $evento_id = (int)$_POST['evento_id'];
    $data_inicio = mysqli_real_escape_string($conexao, $_POST['data_inicio']);
    $data_final = mysqli_real_escape_string($conexao, $_POST['data_final']);
    $vagas = (int)$_POST['vagas'];
    $tipo = mysqli_real_escape_string($conexao, $_POST['tipo']);
    $empresa_id = (int)$_POST['empresa_id'];

    $queryInsertHorario = "INSERT INTO horarios (evento_id, empresa_id, data_inicio, data_final, vagas, tipo) 
                            VALUES ($evento_id, $empresa_id, '$data_inicio', '$data_final', $vagas, '$tipo')";
    
    if (mysqli_query($conexao, $queryInsertHorario)) {
        $msg = "<div class='alert alert-success'>Turno adicionado com sucesso!</div>";
    } else {
        $msg = "<div class='alert alert-danger'>Erro ao adicionar turno: " . mysqli_error($conexao) . "</div>";
    }
}

// Se o evento_id vier via GET (após o post inicial ou edição)
if (isset($_GET['id'])) {
    $evento_id = (int)$_GET['id'];
}

// Busca dados do evento atual se houver
$evento_atual = null;
if ($evento_id > 0) {
    $res = mysqli_query($conexao, "SELECT * FROM eventos_marcados WHERE id = $evento_id");
    $evento_atual = mysqli_fetch_assoc($res);
}

?>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<body>
    <div class="container mt-4">
        <header>
            <h1 class="text-center"><?php echo ($evento_id > 0) ? "Gerenciar Evento: " . $evento_atual['nome'] : "Novo Evento AME"; ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/eventos">Eventos</a></li>
                    <li class="breadcrumb-item active">Novo Evento</li>
                </ol>
            </nav>
        </header>

        <?php echo $msg; ?>

        <div class="row">
            <!-- Coluna 1: Dados do Evento -->
            <div class="col-lg-<?php echo ($evento_id > 0) ? '5' : '12'; ?>">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">1. Informações do Evento</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="criar_evento" value="1">
                            <div class="md-form mb-3">
                                <label for="nome">Nome do Evento</label>
                                <input type="text" id="nome" name="nome" class="form-control" value="<?php echo $evento_atual['nome'] ?? ''; ?>" required>
                            </div>
                            <div class="md-form mb-3">
                                <label for="tipo_atividade">Tipo de Atividade</label>
                                <select id="tipo_atividade" name="tipo_atividade" class="form-control">
                                    <option value="Trabalho" <?php echo (isset($evento_atual['tipo']) && $evento_atual['tipo'] == 'Trabalho') ? 'selected' : ''; ?>>Trabalho (Evento/Exposição)</option>
                                    <option value="Curso" <?php echo (isset($evento_atual['tipo']) && $evento_atual['tipo'] == 'Curso') ? 'selected' : ''; ?>>Curso (DJ, Fotografia, Básico)</option>
                                    <option value="Treinamento" <?php echo (isset($evento_atual['tipo']) && $evento_atual['tipo'] == 'Treinamento') ? 'selected' : ''; ?>>Treinamento Prático</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="md-form mb-3">
                                        <label for="inicio">Início</label>
                                        <input type="datetime-local" id="inicio" name="inicio" class="form-control" value="<?php echo isset($evento_atual['inicio']) ? date('Y-m-d\TH:i', strtotime($evento_atual['inicio'])) : ''; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="md-form mb-3">
                                        <label for="final">Fim</label>
                                        <input type="datetime-local" id="final" name="final" class="form-control" value="<?php echo isset($evento_atual['final']) ? date('Y-m-d\TH:i', strtotime($evento_atual['final'])) : ''; ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="md-form mb-3">
                                <label for="local">Local</label>
                                <input type="text" id="local" name="local" class="form-control" value="<?php echo $evento_atual['local'] ?? ''; ?>" required>
                            </div>
                            <div class="md-form mb-3">
                                <label for="endereco">Endereço</label>
                                <input type="text" id="endereco" name="endereco" class="form-control" value="<?php echo $evento_atual['endereco'] ?? ''; ?>">
                            </div>
                            <div class="md-form mb-3">
                                <label for="maps">Link Google Maps</label>
                                <input type="url" id="maps" name="maps" class="form-control" value="<?php echo $evento_atual['maps'] ?? ''; ?>">
                            </div>
                            <div class="md-form mb-3">
                                <label for="busca_expositor_evento">Expositor / Empresa (Opcional)</label>
                                <input type="text" id="busca_expositor_evento" class="form-control" placeholder="Buscar empresa para associar ao evento..." value="<?php 
                                    if(isset($evento_atual['expositor_id'])){
                                        $ex_res = mysqli_query($conexao, "SELECT empresa FROM expositores2024 WHERE expositor_id = ".$evento_atual['expositor_id']);
                                        $ex_row = mysqli_fetch_assoc($ex_res);
                                        echo $ex_row['empresa'];
                                    }
                                ?>">
                                <input type="hidden" id="expositor_id" name="expositor_id" value="<?php echo $evento_atual['expositor_id'] ?? ''; ?>">
                            </div>
                            
                            <hr>
                            <label class="form-label">Imagem do Evento</label>
                            <div class="md-form mb-3">
                                <select name="imagem_id" class="form-control">
                                    <option value="">Selecione imagem existente...</option>
                                    <?php foreach ($imagens as $img): ?>
                                        <option value="<?php echo $img['imagem_id']; ?>" <?php echo (isset($evento_atual['imagem_id']) && $evento_atual['imagem_id'] == $img['imagem_id']) ? 'selected' : ''; ?>><?php echo $img['url']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="md-form mb-3">
                                <label>Ou Upload Nova:</label>
                                <input type="file" name="nova_imagem" class="form-control-file">
                            </div>

                            <button type="submit" class="btn btn-primary btn-block rounded-pill">Salvar Evento</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Coluna 2: Adição de Turnos (Só aparece após o evento ser criado) -->
            <?php if ($evento_id > 0): ?>
            <div class="col-lg-7">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">2. Adicionar Turnos / Horários</h5>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <input type="hidden" name="add_horario" value="1">
                            <input type="hidden" name="evento_id" value="<?php echo $evento_id; ?>">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Início do Turno</label>
                                    <input type="datetime-local" name="data_inicio" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label>Fim do Turno</label>
                                    <input type="datetime-local" name="data_final" class="form-control" required>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label>Vagas</label>
                                    <input type="number" name="vagas" class="form-control" min="1" value="1" required>
                                </div>
                                <div class="col-md-8">
                                    <label>Tipo de Atividade</label>
                                    <input type="text" name="tipo" class="form-control" placeholder="Atendimento, DJ, Fotografia..." required>
                                </div>
                            </div>

                            <div class="md-form mt-3">
                                <label for="busca_expositor">Buscar Expositor / Empresa</label>
                                <input type="text" id="busca_expositor" class="form-control" placeholder="Digite 3 letras...">
                                <input type="hidden" id="empresa_id" name="empresa_id" required>
                            </div>

                            <button type="submit" class="btn btn-success btn-block rounded-pill mt-4">Adicionar Turno</button>
                        </form>

                        <hr>
                        <h6>Turnos já cadastrados:</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Empresa</th>
                                        <th>Vagas</th>
                                        <th>Início</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $qH = "SELECT h.*, e.empresa FROM horarios h JOIN expositores2024 e ON h.empresa_id = e.expositor_id WHERE h.evento_id = $evento_id ORDER BY h.data_inicio ASC";
                                    $rH = mysqli_query($conexao, $qH);
                                    while ($h = mysqli_fetch_assoc($rH)):
                                    ?>
                                    <tr>
                                        <td><?php echo $h['tipo']; ?></td>
                                        <td><?php echo $h['empresa']; ?></td>
                                        <td><?php echo $h['vagas']; ?></td>
                                        <td><?php echo date('d/m H:i', strtotime($h['data_inicio'])); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include_once('./include/footer.php'); ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script>
    $(function() {
        // Autocomplete para o Expositor do Evento
        $("#busca_expositor_evento").autocomplete({
            source: "/pages/buscar_expositores.php",
            minLength: 3,
            select: function(event, ui) {
                $("#expositor_id").val(ui.item.id);
                // Se o campo de turno estiver vazio, sugere o mesmo expositor
                if ($("#empresa_id").val() == "") {
                    $("#empresa_id").val(ui.item.id);
                    $("#busca_expositor").val(ui.item.value);
                }
            }
        });

        // Autocomplete para o Expositor do Turno
        $("#busca_expositor").autocomplete({
            source: "/pages/buscar_expositores.php",
            minLength: 3,
            select: function(event, ui) {
                $("#empresa_id").val(ui.item.id);
            }
        });

        // Preenchimento automático inicial do turno se o evento já tiver expositor
        <?php if (isset($evento_atual['expositor_id'])): ?>
            if ($("#empresa_id").val() == "") {
                $("#empresa_id").val("<?php echo $evento_atual['expositor_id']; ?>");
                $("#busca_expositor").val("<?php echo htmlspecialchars($ex_row['empresa'] ?? ''); ?>");
            }
        <?php endif; ?>
    });
    </script>
    <?php include_once('./include/scripts.php'); ?>
</body>
</html>
