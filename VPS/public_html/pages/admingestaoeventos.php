<?php
// include_once('include/conexao.php');
// include_once('include/head.php');

$titulo = "Gestão de Eventos e Horários";

// Ação de criar horário
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['evento_id'])) {
    $evento_id = intval($_POST['evento_id']);
    $empresa_id = intval($_POST['empresa_id']);
    $data_inicio = $_POST['data_inicio'];
    $data_final = $_POST['data_final'];
    $vagas = intval($_POST['vagas']);
    $tipo = mysqli_real_escape_string($conexao, $_POST['tipo']);

    $sql = "INSERT INTO horarios (evento_id, empresa_id, data_inicio, data_final, vagas, tipo) 
            VALUES ($evento_id, $empresa_id, '$data_inicio', '$data_final', $vagas, '$tipo')";
    mysqli_query($conexao, $sql);
}

// Buscar Eventos
$eventos = mysqli_query($conexao, "SELECT * FROM eventos_marcados ORDER BY id DESC");
?>

<div class="container mt-5">
    <h2>Gestão de Eventos</h2>
    <table class="table table-hover mt-4">
        <thead>
            <tr><th>Evento</th><th>Data</th><th>Ações</th></tr>
        </thead>
        <tbody>
            <?php while($e = mysqli_fetch_assoc($eventos)): ?>
                <tr>
                    <td><?php echo $e['name']; ?></td>
                    <td><?php echo $e['date_start']; ?></td>
                    <td>
                        <button class="btn btn-sm btn-info" data-toggle="collapse" data-target="#horarios-<?php echo $e['id']; ?>">Horários</button>
                    </td>
                </tr>
                <tr class="collapse" id="horarios-<?php echo $e['id']; ?>">
                    <td colspan="3">
                        <div class="bg-light p-3">
                            <h6>Horários e Escalas deste Evento</h6>
                            <?php
                            $horarios = mysqli_query($conexao, "SELECT * FROM horarios WHERE evento_id = " . $e['id']);
                            while ($h = mysqli_fetch_assoc($horarios)):
                            ?>
                            <div class="card mb-2">
                                <div class="card-header">
                                    <strong>Turno: <?php echo $h['tipo']; ?></strong> | <?php echo $h['data_inicio']; ?>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group">
                                        <?php
                                        $escala = mysqli_query($conexao, "SELECT d.id, c.nome, d.escalado FROM disponibilidade d JOIN candidatos c ON d.candidato_id = c.candidato_id WHERE d.atividade_id = " . $h['horario_id']);
                                        while ($s = mysqli_fetch_assoc($escala)):
                                        ?>
                                            <li class="list-group-item d-flex justify-content-between">
                                                <?php echo $s['nome']; ?>
                                                <input type="checkbox" <?php echo $s['escalado'] ? 'checked' : ''; ?> 
                                                       onchange="toggleEscala(<?php echo $s['id']; ?>, this.checked)">
                                            </li>
                                        <?php endwhile; ?>
                                    </ul>
                                    <div class="mt-2">
                                        <input type="text" class="form-control busca-candidato" placeholder="Adicionar atendente..." data-horario-id="<?php echo $h['horario_id']; ?>">
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </td>
                </tr>                <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
                <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
                <script>
                function toggleEscala(id, status) {
                    $.post('/database/api_toggle_escala.php', { action: 'toggle', id: id, escalado: status });
                }

                $(document).ready(function() {
                    $(".busca-candidato").autocomplete({
                        source: "/database/api_busca_candidatos.php",
                        minLength: 2,
                        select: function(event, ui) {
                            var atividade_id = $(this).data('horario-id');
                            $.post('/database/api_toggle_escala.php', { 
                                action: 'add', 
                                atividade_id: atividade_id, 
                                candidato_id: ui.item.id 
                            }, function() {
                                location.reload(); // Recarrega para mostrar o novo nome na lista
                            });
                        }
                    });
                });
                </script>            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include_once('include/footer.php'); ?>
