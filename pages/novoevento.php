<?php
// Inclui arquivos essenciais
include_once('./include/funcoes.php');
include_once('./include/conexao.php');
include_once('./include/head.php');

$msg = "";
$imagens = [];

// Busca imagens para o seletor
$queryImagens = "SELECT imagem_id, url FROM imagens ORDER BY url ASC";
$respImagens = mysqli_query($conexao, $queryImagens);
while ($row = mysqli_fetch_assoc($respImagens)) {
    $imagens[] = $row;
}

// Processa o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $imagem_id = 0;

    // 1. Lógica de Upload de Nova Imagem
    if (isset($_FILES['nova_imagem']) && $_FILES['nova_imagem']['error'] == 0) {
        $target_dir = "../img/"; // Diretório de destino
        $target_file = $target_dir . basename($_FILES["nova_imagem"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $uploadOk = 1;

        // Validações básicas (pode ser mais robusto)
        $check = getimagesize($_FILES["nova_imagem"]["tmp_name"]);
        if ($check === false) {
            $msg .= "<div class='alert alert-danger'>Arquivo não é uma imagem válida.</div>";
            $uploadOk = 0;
        }

        if ($uploadOk && move_uploaded_file($_FILES["nova_imagem"]["tmp_name"], $target_file)) {
            $imageUrl = "img/" . basename($_FILES["nova_imagem"]["name"]);
            $queryInsertImage = "INSERT INTO imagens (url) VALUES ('$imageUrl')";
            if (mysqli_query($conexao, $queryInsertImage)) {
                $imagem_id = mysqli_insert_id($conexao);
            } else {
                $msg .= "<div class='alert alert-danger'>Erro ao salvar a imagem no banco de dados.</div>";
                $uploadOk = 0;
            }
        } elseif ($uploadOk) {
            $msg .= "<div class='alert alert-danger'>Erro ao fazer o upload do arquivo.</div>";
            $uploadOk = 0;
        }
    } else {
        // Usa imagem existente se nenhuma nova for enviada
        if (!empty($_POST['imagem_id'])) {
            $imagem_id = (int)$_POST['imagem_id'];
        } else {
            $msg .= "<div class='alert alert-danger'>Você deve selecionar uma imagem existente ou enviar uma nova.</div>";
        }
    }

    // 2. Insere o evento se a imagem foi definida
    if ($imagem_id > 0) {
        $nome = mysqli_real_escape_string($conexao, $_POST['nome']);
        $inicio = mysqli_real_escape_string($conexao, $_POST['inicio']);
        $final = mysqli_real_escape_string($conexao, $_POST['final']);
        $local = mysqli_real_escape_string($conexao, $_POST['local']);
        $endereco = mysqli_real_escape_string($conexao, $_POST['endereco']);
        $maps = mysqli_real_escape_string($conexao, $_POST['maps']);
        $obs = mysqli_real_escape_string($conexao, $_POST['obs']);
        $aval_grupo = isset($_POST['aval_grupo']) ? 1 : 0;
        $escala_fechada = isset($_POST['escala_fechada']) ? 1 : 0;

        $queryInsert = "INSERT INTO eventos_marcados (nome, inicio, final, local, endereco, maps, imagem_id, obs, aval_grupo, escala_fechada) VALUES ('$nome', '$inicio', '$final', '$local', '$endereco', '$maps', $imagem_id, '$obs', $aval_grupo, $escala_fechada)";

        if (mysqli_query($conexao, $queryInsert)) {
            $evento_id = mysqli_insert_id($conexao);
            // 3. Redireciona para a página de gerenciamento
            header("Location: gerenciar_evento.php?id=" . $evento_id);
            exit(); // Encerra o script após o redirecionamento
        } else {
            $msg .= "<div class='alert alert-danger' role='alert'>Erro ao cadastrar o evento: " . mysqli_error($conexao) . "</div>";
        }
    }
}
?>

<body>
    <div class="container">
        <header>
            <h1 class="text-center">Cadastrar Novo Evento</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/atendentes">Atividades</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Novo Evento</li>
                </ol>
            </nav>
        </header>

        <?php echo $msg; ?>

        <form method="post" action="novoevento.php" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nome" class="form-label">Nome do Evento</label>
                    <input type="text" class="form-control" id="nome" name="nome" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="local" class="form-label">Local</label>
                    <input type="text" class="form-control" id="local" name="local" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="inicio" class="form-label">Data e Hora de Início</label>
                    <input type="datetime-local" class="form-control" id="inicio" name="inicio" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="final" class="form-label">Data e Hora de Término</label>
                    <input type="datetime-local" class="form-control" id="final" name="final" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="endereco" class="form-label">Endereço Completo</label>
                <input type="text" class="form-control" id="endereco" name="endereco" required>
            </div>

            <div class="mb-3">
                <label for="maps" class="form-label">URL do Google Maps</label>
                <input type="url" class="form-control" id="maps" name="maps" placeholder="https://maps.app.goo.gl/..." required>
            </div>

            <hr>
            <p class="text-center">Selecione uma imagem existente OU envie uma nova.</p>

            <div class="mb-3">
                <label for="imagem_id" class="form-label">Imagem Existente</label>
                <select class="form-select" id="imagem_id" name="imagem_id">
                    <option selected disabled value="">Selecione uma imagem...</option>
                    <?php foreach ($imagens as $imagem) : ?>
                        <option value="<?php echo $imagem['imagem_id']; ?>"><?php echo $imagem['url']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="nova_imagem" class="form-label">Ou Envie Nova Imagem</label>
                <input class="form-control" type="file" id="nova_imagem" name="nova_imagem">
            </div>

            <hr>

            <div class="mb-3">
                <label for="obs" class="form-label">Observações</label>
                <textarea class="form-control" id="obs" name="obs" rows="3"></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="aval_grupo" name="aval_grupo" value="1" checked>
                        <label class="form-check-label" for="aval_grupo">Avaliação em Grupo</label>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="escala_fechada" name="escala_fechada" value="1" checked>
                        <label class="form-check-label" for="escala_fechada">Escala Fechada</label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">Cadastrar e Gerenciar Horários</button>
        </form>
    </div>

    <?php include_once('./include/footer.php'); ?>
    <?php include_once('./include/scripts.php'); ?>
</body>

</html>
<?php include_once('./include/end.php'); ?>