<?php
date_default_timezone_set('America/Sao_Paulo');
include_once('./include/head.php');
include_once('./include/conexao.php');

// Lógica para buscar todos os eventos
$query_eventos = "SELECT id, nome, inicio, tipo_evento, status_evento, escala_fechada, rodizio_processado FROM eventos_marcados ORDER BY inicio DESC";
$result_eventos = mysqli_query($conexao, $query_eventos);

?>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Gestão de Atividades</h1>

    <div id="mensagem-status"></div>

    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>Evento</th>
                <th>Data</th>
                <th>Tipo</th>
                <th>Escala Fechada</th>
                <th>Status do Evento</th>
                <th>Confirmar Presença</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php while($evento = mysqli_fetch_assoc($result_eventos)) { ?>
            <tr id="evento-<?php echo $evento['id']; ?>">
                <td><?php echo htmlspecialchars($evento['nome']); ?></td>
                <td><?php echo date('d/m/Y', strtotime($evento['inicio'])); ?></td>
                <td><?php echo htmlspecialchars($evento['tipo_evento']); ?></td>
                <td>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="escala-<?php echo $evento['id']; ?>" <?php echo ($evento['escala_fechada'] ? 'checked' : ''); ?> onchange="atualizarStatus(<?php echo $evento['id']; ?>)">
                        <label class="custom-control-label" for="escala-<?php echo $evento['id']; ?>"></label>
                    </div>
                </td>
                <td>
                    <select class="form-control" id="status-<?php echo $evento['id']; ?>" onchange="atualizarStatus(<?php echo $evento['id']; ?>)">
                        <option value="agendado" <?php echo ($evento['status_evento'] == 'agendado' ? 'selected' : ''); ?>>Agendado</option>
                        <option value="realizado" <?php echo ($evento['status_evento'] == 'realizado' ? 'selected' : ''); ?>>Realizado</option>
                        <option value="cancelado" <?php echo ($evento['status_evento'] == 'cancelado' ? 'selected' : ''); ?>>Cancelado</option>
                    </select>
                </td>
                <td>
                    <!-- DEBUG: app_web_root = '<?php echo htmlspecialchars($GLOBALS['app_web_root']); ?>' -->
                    <a href="<?php echo $GLOBALS['app_web_root']; ?>admin/ver_escala?evento_id=<?php echo $evento['id']; ?>" class="btn btn-info btn-sm">Ver Escala</a>
                </td>
                <td>
                    <?php if ($evento['tipo_evento'] == 'atendimento' && $evento['status_evento'] == 'realizado' && !$evento['rodizio_processado']) { ?>
                        <button class="btn btn-primary btn-sm" onclick="processarRodizio('<?php echo $evento['inicio']; ?>')">Processar Rodízio</button>
                    <?php } elseif ($evento['rodizio_processado']) { ?>
                        <span class="badge badge-success">Processado</span>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<script>
function atualizarStatus(eventoId) {
    const escala_fechada = document.getElementById(`escala-${eventoId}`).checked ? 1 : 0;
    const status_evento = document.getElementById(`status-${eventoId}`).value;

    fetch('../include/update_event_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ 
            id: eventoId, 
            escala_fechada: escala_fechada, 
            status_evento: status_evento 
        }),
    })
    .then(response => response.json())
    .then(data => {
        const msgDiv = document.getElementById('mensagem-status');
        if (data.success) {
            msgDiv.className = 'alert alert-success';
            msgDiv.innerHTML = data.message;
            // Recarrega a página para mostrar o botão de processar, se for o caso
            setTimeout(() => location.reload(), 1500);
        } else {
            msgDiv.className = 'alert alert-danger';
            msgDiv.innerHTML = 'Erro: ' + data.message;
        }
    });
}

function processarRodizio(dataEvento) {
    if (!confirm('Tem certeza que deseja processar o rodízio para todos os eventos de atendimento realizados nesta data? Esta ação não pode ser desfeita.')) {
        return;
    }

    fetch('../include/update_event_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ 
            action: 'processar_rodizio', 
            data_evento: dataEvento
        }),
    })
    .then(response => response.json())
    .then(data => {
        const msgDiv = document.getElementById('mensagem-status');
        if (data.success) {
            msgDiv.className = 'alert alert-success';
            msgDiv.innerHTML = data.message;
            setTimeout(() => location.reload(), 2000);
        } else {
            msgDiv.className = 'alert alert-danger';
            msgDiv.innerHTML = 'Erro: ' + data.message;
        }
    });
}
</script>

</body>
</html>