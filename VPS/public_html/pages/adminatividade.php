<?php
// adminevento.php - Gerenciamento de detalhes do evento (Horários/Turnos)
// Acessível via /admin/atividade/{id_ou_slug}
include_once('./include/funcoes.php');
// include_once('./include/conexao.php');
// include_once('./include/head.php');

$identificador = isset($parametros[2]) ? $parametros[2] : 0;
$msg = "";
$evento = null;
$horarios = [];

// Busca detalhes do evento (suporta ID numérico ou futuramente SLUG)
if ($identificador) {
    if (is_numeric($identificador)) {
        $queryEvento = "SELECT * FROM eventos_marcados WHERE id = $identificador";
    } else {
        $identificador = mysqli_real_escape_string($conexao, $identificador);
        $queryEvento = "SELECT * FROM eventos_marcados WHERE slug = '$identificador'";
    }
    
    $respEvento = mysqli_query($conexao, $queryEvento);
    if ($respEvento && mysqli_num_rows($respEvento) > 0) {
        $evento = mysqli_fetch_assoc($respEvento);
        $evento_id = $evento['id'];
    } else {
        die("<div class='container mt-5'><div class='alert alert-danger'>Atividade não encontrada.</div></div>");
    }
} else {
    die("<div class='container mt-5'><div class='alert alert-warning'>Identificador da atividade não fornecido.</div></div>");
}

// Processa o formulário de adição de horário
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_horario'])) {
    $data_inicio = mysqli_real_escape_string($conexao, $_POST['data_inicio']);
    $data_final = mysqli_real_escape_string($conexao, $_POST['data_final']);
    $vagas = (int)$_POST['vagas'];
    $tipo = mysqli_real_escape_string($conexao, $_POST['tipo']);
    $empresa_id = (int)$_POST['empresa_id'];

    if ($empresa_id > 0) {
        $queryInsertHorario = "INSERT INTO horarios (evento_id, empresa_id, data_inicio, data_final, vagas, tipo) 
                               VALUES ($evento_id, $empresa_id, '$data_inicio', '$data_final', $vagas, '$tipo')";
        if (mysqli_query($conexao, $queryInsertHorario)) {
            $msg = "<div class='alert alert-success'>Horário adicionado com sucesso!</div>";
        } else {
            $msg = "<div class='alert alert-danger'>Erro ao adicionar horário: " . mysqli_error($conexao) . "</div>";
        }
    } else {
        $msg = "<div class='alert alert-danger'>Selecione uma empresa válida.</div>";
    }
}

// Busca horários existentes para esta atividade
$queryHorarios = "SELECT h.*, e.empresa as nome_empresa FROM horarios h JOIN empresas e ON h.empresa_id = e.empresa_id WHERE h.evento_id = $evento_id ORDER BY h.data_inicio ASC";
$respHorarios = mysqli_query($conexao, $queryHorarios);
if ($respHorarios) {
    while ($row = mysqli_fetch_assoc($respHorarios)) {
        $horarios[] = $row;
    }
}

include_once('./include/nav.php');
include_once('./include/admin_sidebar.php');
?>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<div class="container mt-4">
        <header>
            <h1 class="text-center">Gerenciar: <?php echo htmlspecialchars($evento['nome']); ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/atividades">Atividades</a></li>
                    <li class="breadcrumb-item active">Gerenciar</li>
                </ol>
            </nav>
        </header>

        <?php echo $msg; ?>

        <div class="row">
            <!-- Formulário de Adição de Horário -->
            <div class="col-md-5">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">Adicionar Turno</div>
                    <div class="card-body">
                        <form method="post">
                            <input type="hidden" name="add_horario" value="1">
                            <div class="md-form mb-3">
                                <label>Início do Turno</label>
                                <input type="datetime-local" name="data_inicio" class="form-control" required>
                            </div>
                            <div class="md-form mb-3">
                                <label>Fim do Turno</label>
                                <input type="datetime-local" name="data_final" class="form-control" required>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <label>Vagas</label>
                                    <input type="number" name="vagas" class="form-control" min="1" value="1" required>
                                </div>
                                <div class="col-6">
                                    <label>Tipo</label>
                                    <input type="text" name="tipo" class="form-control" placeholder="Atendimento..." required>
                                </div>
                            </div>
                            <div class="md-form mt-3">
                                <label for="busca_expositor">Expositor</label>
                                <input type="text" id="busca_expositor" class="form-control" placeholder="Buscar empresa...">
                                <input type="hidden" id="empresa_id" name="empresa_id" required>
                            </div>
                            <button type="submit" class="btn btn-success btn-block rounded-pill mt-4">Salvar Turno</button>
                        </form>
                    </div>
                </div>

                <!-- Card de Avaliação -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">Avaliação do Evento</div>
                    <div class="card-body">
                        <?php if (!empty($evento['uuid'])): ?>
                            <p class="text-xs text-muted mb-2">Compartilhe este link com as empresas e parceiros do evento para coletar avaliações dos atendentes.</p>
                            <div class="input-group mb-3">
                                <input type="text" id="eval_link_input" class="form-control text-xs" readonly value="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $GLOBALS['app_web_root'] . 'avaliacao/' . $evento['uuid']; ?>">
                                <div class="input-group-append">
                                    <button class="btn btn-info m-0 px-3 py-2" type="button" id="btn_copy_eval_link" title="Copiar Link"><i class="fas fa-copy"></i></button>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning text-xs mb-0">
                                <i class="fas fa-exclamation-triangle mr-1"></i> UUID de avaliação não gerado para este evento.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Listagem de Turnos -->
            <div class="col-md-7">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">Turnos Cadastrados</div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Empresa</th>
                                        <th class="text-center">Vagas</th>
                                        <th>Início</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($horarios)): ?>
                                        <tr><td colspan="4" class="text-center text-muted">Nenhum turno cadastrado.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($horarios as $h): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($h['tipo']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($h['nome_empresa']); ?></td>
                                                <td class="text-center"><?php echo $h['vagas']; ?></td>
                                                <td><small><?php echo date('d/m/Y H:i', strtotime($h['data_inicio'])); ?></small></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="mt-3 text-right">
                    <a href="/admin/escala?evento_id=<?php echo $evento_id; ?>" class="btn btn-primary rounded-pill">Ver Escala e Rodízio</a>
                </div>
            </div>
        </div>
    </div>

    <?php include_once('./include/footer.php'); ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script>
    $(function() {
        $("#busca_expositor").autocomplete({
            source: "/pages/buscar_expositores.php",
            minLength: 3,
            select: function(event, ui) {
                $("#empresa_id").val(ui.item.id);
            }
        });
        
        // Copiar link de avaliação
        const btnCopy = document.getElementById('btn_copy_eval_link');
        if (btnCopy) {
            btnCopy.addEventListener('click', function(e) {
                e.preventDefault();
                var copyText = document.getElementById('eval_link_input');
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                navigator.clipboard.writeText(copyText.value).then(function() {
                    var originalHtml = btnCopy.innerHTML;
                    btnCopy.innerHTML = '<i class="fas fa-check"></i>';
                    btnCopy.className = 'btn btn-success m-0 px-3 py-2';
                    setTimeout(function() {
                        btnCopy.innerHTML = originalHtml;
                        btnCopy.className = 'btn btn-info m-0 px-3 py-2';
                    }, 2000);
                });
            });
        }
    });
    </script>
    <?php include_once('./include/scripts.php'); ?>
<?php include_once('./include/admin_sidebar_footer.php'); ?>
