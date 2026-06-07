<?php
// Inclui arquivos essenciais
include_once('./include/funcoes.php');
// include_once('./include/conexao.php');
// include_once('./include/head.php');

$evento_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$msg = "";
$evento = null;
$horarios = [];

// Busca detalhes do evento
if ($evento_id > 0) {
    $queryEvento = "SELECT * FROM eventos_marcados WHERE id = $evento_id";
    $respEvento = mysqli_query($conexao, $queryEvento);
    if ($respEvento && mysqli_num_rows($respEvento) > 0) {
        $evento = mysqli_fetch_assoc($respEvento);
    } else {
        die("Evento não encontrado.");
    }
} else {
    die("ID do evento não fornecido.");
}

// Processa o formulário de adição de horário
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_horario'])) {
    $data_inicio = mysqli_real_escape_string($conexao, $_POST['data_inicio']);
    $data_final = mysqli_real_escape_string($conexao, $_POST['data_final']);
    $vagas = (int)$_POST['vagas'];
    $tipo = mysqli_real_escape_string($conexao, $_POST['tipo']);
    $empresa_id = (int)$_POST['empresa_id']; // ID do expositor

    if ($empresa_id > 0) {
        $queryInsertHorario = "INSERT INTO horarios (evento_id, empresa_id, data_inicio, data_final, vagas, tipo) VALUES ($evento_id, $empresa_id, '$data_inicio', '$data_final', $vagas, '$tipo')";
        if (mysqli_query($conexao, $queryInsertHorario)) {
            $msg = "<div class='alert alert-success'>Horário adicionado com sucesso!</div>";
        } else {
            $msg = "<div class='alert alert-danger'>Erro ao adicionar horário: " . mysqli_error($conexao) . "</div>";
        }
    } else {
        $msg = "<div class='alert alert-danger'>Você deve selecionar uma empresa válida.</div>";
    }
}

// Busca horários existentes para este evento
$queryHorarios = "SELECT h.*, e.empresa as nome_empresa FROM horarios h JOIN empresas e ON h.empresa_id = e.empresa_id WHERE h.evento_id = $evento_id ORDER BY h.data_inicio ASC";
$respHorarios = mysqli_query($conexao, $queryHorarios);
if ($respHorarios) {
    while ($row = mysqli_fetch_assoc($respHorarios)) {
        $horarios[] = $row;
    }
}

?>
<!-- Adiciona CSS do jQuery UI para o Autocomplete -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<body>
    <div class="container">
        <header>
            <h1 class="text-center">Gerenciar Evento: <?php echo htmlspecialchars($evento['nome']); ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/atendentes">Atividades</a></li>
                    <li class="breadcrumb-item"><a href="/pages/novoevento.php">Novo Evento</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Gerenciar Evento</li>
                </ol>
            </nav>
        </header>

        <?php echo $msg; ?>

        <!-- Seção para Adicionar Horários -->
        <div class="card mb-4">
            <div class="card-header">Adicionar Novo Horário</div>
            <div class="card-body">
                <form method="post" action="gerenciar_evento.php?id=<?php echo $evento_id; ?>">
                    <input type="hidden" name="add_horario" value="1">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="data_inicio" class="form-label">Início do Turno</label>
                            <input type="datetime-local" class="form-control" id="data_inicio" name="data_inicio" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="data_final" class="form-label">Fim do Turno</label>
                            <input type="datetime-local" class="form-control" id="data_final" name="data_final" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="vagas" class="form-label">Nº de Vagas</label>
                            <input type="number" class="form-control" id="vagas" name="vagas" min="1" required>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label for="tipo" class="form-label">Tipo de Atividade</label>
                            <input type="text" class="form-control" id="tipo" name="tipo" placeholder="Ex: Atendimento, Apoio, etc." required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="busca_empresa" class="form-label">Buscar Empresa/Expositor</label>
                        <input type="text" class="form-control" id="busca_empresa" placeholder="Digite para buscar...">
                        <input type="hidden" id="empresa_id" name="empresa_id">
                    </div>
                    <button type="submit" class="btn btn-primary">Adicionar Horário</button>
                </form>
            </div>
        </div>

        <!-- Seção para Listar Horários Cadastrados -->
        <div class="card">
            <div class="card-header">Horários Cadastrados</div>
            <div class="card-body">
                <ul class="list-group">
                    <?php if (empty($horarios)) : ?>
                        <li class="list-group-item">Nenhum horário cadastrado para este evento ainda.</li>
                    <?php else : ?>
                        <?php foreach ($horarios as $horario) : ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?php echo htmlspecialchars($horario['tipo']); ?></strong> (<?php echo htmlspecialchars($horario['nome_empresa']); ?>)<br>
                                    <small>
                                        <?php echo date("d/m/Y H:i", strtotime($horario['data_inicio'])); ?>
                                        até <?php echo date("d/m/Y H:i", strtotime($horario['data_final'])); ?>
                                        - <?php echo $horario['vagas']; ?> vagas
                                    </small>
                                </div>
                                <!-- Adicionar botões de ação (editar/excluir) aqui no futuro -->
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <?php include_once('./include/footer.php'); ?>
    <!-- Adiciona jQuery e jQuery UI para o Autocomplete -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script>
    $(function() {
        $("#busca_empresa").autocomplete({
            source: "buscar_empresas.php",
            minLength: 3,
            select: function(event, ui) {
                // ui.item.id e ui.item.value são retornados pelo JSON
                $("#empresa_id").val(ui.item.id); 
            }
        });
    });
    </script>
    <?php include_once('./include/scripts.php'); ?>
</body>

</html>
<?php include_once('./include/end.php'); ?>
