<?php
date_default_timezone_set('America/Sao_Paulo');
include_once('./include/head.php');
include_once('./include/conexao.php');

// --- LÓGICA DE BACKEND (Responde a requisições AJAX) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);

    // Ação: Buscar detalhes da escala para um evento
    if (isset($data['action']) && $data['action'] === 'get_escala_details') {
        $evento_id = intval($data['evento_id']);
        $query = "SELECT c.candidato_id, c.nome, c.rodizio, i.url, d.escalado
                  FROM disponibilidade d
                  JOIN candidatos c ON d.candidato_id = c.candidato_id
                  LEFT JOIN imagens i ON c.imagem_id = i.imagem_id
                  WHERE d.evento_id = {$evento_id} ORDER BY c.rodizio ASC";
        
        $result = mysqli_query($conexao, $query);
        $atendentes = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $atendentes[] = $row;
        }
        echo json_encode(['success' => true, 'atendentes' => $atendentes]);
        exit;
    }

    // Ação: Salvar a escala de um evento
    if (isset($data['action']) && $data['action'] === 'save_escala') {
        $evento_id = intval($data['evento_id']);
        $escalados_ids = $data['escalados_ids'] ?? [];

        // 1. Limpa a escala atual para o evento
        $query_limpar = "UPDATE disponibilidade SET escalado = 0 WHERE evento_id = {$evento_id}";
        mysqli_query($conexao, $query_limpar);

        // 2. Marca os selecionados como escalados
        if (!empty($escalados_ids)) {
            $ids_string = implode(',', array_map('intval', $escalados_ids));
            $query_escalar = "UPDATE disponibilidade SET escalado = 1 WHERE evento_id = {$evento_id} AND candidato_id IN ({$ids_string})";
            mysqli_query($conexao, $query_escalar);
        }

        echo json_encode(['success' => true, 'message' => 'Escala salva com sucesso!']);
        exit;
    }
}

// --- LÓGICA DE FRONTEND (Renderiza a página) ---

// Variáveis para pré-seleção
$evento_id_selecionado = null;
$atendentes_selecionados = [];
$nome_evento_selecionado = '';

// Verifica se um evento foi passado via GET
if (isset($_GET['evento_id'])) {
    $evento_id_selecionado = intval($_GET['evento_id']);

    // Busca os atendentes para o evento pré-selecionado
    $query_atendentes = "SELECT c.candidato_id, c.nome, c.rodizio, i.url, d.escalado
                         FROM disponibilidade d
                         JOIN candidatos c ON d.candidato_id = c.candidato_id
                         LEFT JOIN imagens i ON c.imagem_id = i.imagem_id
                         WHERE d.evento_id = {$evento_id_selecionado} ORDER BY c.rodizio ASC";
    $result_atendentes = mysqli_query($conexao, $query_atendentes);
    while ($row = mysqli_fetch_assoc($result_atendentes)) {
        $atendentes_selecionados[] = $row;
    }
}

// Busca todos os eventos para o dropdown
$query_eventos = "SELECT id, nome FROM eventos_marcados WHERE status_evento = 'agendado' AND escala_fechada = 0 ORDER BY inicio ASC";
$result_eventos = mysqli_query($conexao, $query_eventos);

?>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Gestão de Escala</h1>

    <div class="form-group">
        <label for="evento-select">Selecione um Evento:</label>
        <select class="form-control" id="evento-select">
            <option value="">-- Escolha um evento --</option>
            <?php while($evento = mysqli_fetch_assoc($result_eventos)): 
                $selected = ($evento['id'] == $evento_id_selecionado) ? 'selected' : '';
                if ($selected) {
                    $nome_evento_selecionado = $evento['nome'];
                }
            ?>
                <option value="<?php echo $evento['id']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($evento['nome']); ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <hr>

    <div id="escala-container" class="<?php echo $evento_id_selecionado ? '' : 'd-none'; ?>">
        <h3 id="escala-title">Montar Escala para: <?php echo htmlspecialchars($nome_evento_selecionado); ?></h3>
        <div id="mensagem-escala"></div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Escalar</th>
                    <th>Foto</th>
                    <th>Nome</th>
                    <th>Rodízio Atual</th>
                </tr>
            </thead>
            <tbody id="escala-table-body">
                <?php foreach ($atendentes_selecionados as $atendente): ?>
                <tr>
                    <td><input type="checkbox" class="form-check-input" value="<?php echo $atendente['candidato_id']; ?>" <?php echo $atendente['escalado'] == 1 ? 'checked' : ''; ?>></td>
                    <td><img src="<?php echo $GLOBALS['app_web_root']; ?><?php echo $atendente['url']; ?>" class="img-thumbnail" width="40"></td>
                    <td><?php echo htmlspecialchars($atendente['nome']); ?></td>
                    <td><?php echo $atendente['rodizio']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button class="btn btn-primary" id="salvar-escala-btn">Salvar Escala</button>
    </div>
</div>

<script>
// Função para carregar detalhes do evento, agora reutilizável
function carregarDetalhesEvento(eventoId) {
    const escalaContainer = document.getElementById('escala-container');
    const tableBody = document.getElementById('escala-table-body');
    const escalaTitle = document.getElementById('escala-title');
    const select = document.getElementById('evento-select');
    tableBody.innerHTML = ''; // Limpa a tabela

    if (!eventoId) {
        escalaContainer.classList.add('d-none');
        return;
    }

    escalaTitle.innerText = `Montar Escala para: ${select.options[select.selectedIndex].text}`;
    escalaContainer.classList.remove('d-none');

    fetch('adminescala.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'get_escala_details', evento_id: eventoId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            data.atendentes.forEach(atendente => {
                const row = `<tr>
                    <td><input type="checkbox" class="form-check-input" value="${atendente.candidato_id}" ${atendente.escalado == 1 ? 'checked' : ''}></td>
                    <td><img src="<?php echo $GLOBALS['app_web_root']; ?>${atendente.url}" class="img-thumbnail" width="40"></td>
                    <td>${atendente.nome}</td>
                    <td>${atendente.rodizio}</td>
                </tr>`;
                tableBody.innerHTML += row;
            });
        }
    });
}

// Event listener para o dropdown
document.getElementById('evento-select').addEventListener('change', function() {
    carregarDetalhesEvento(this.value);
});

// Salvar a escala
document.getElementById('salvar-escala-btn').addEventListener('click', function() {
    const eventoId = document.getElementById('evento-select').value;
    const checkboxes = document.querySelectorAll('#escala-table-body input[type="checkbox"]:checked');
    const escalados_ids = Array.from(checkboxes).map(cb => cb.value);
    const msgDiv = document.getElementById('mensagem-escala');

    fetch('adminescala.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
            action: 'save_escala', 
            evento_id: eventoId, 
            escalados_ids: escalados_ids 
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            msgDiv.className = 'alert alert-success';
            msgDiv.innerHTML = data.message;
        } else {
            msgDiv.className = 'alert alert-danger';
            msgDiv.innerHTML = 'Erro: ' + (data.message || 'Ocorreu uma falha.');
        }
        setTimeout(() => { msgDiv.innerHTML = ''; msgDiv.className = ''; }, 3000);
    });
});

// Se um evento já veio selecionado (via PHP), o JS não precisa fazer nada extra na carga inicial.
// A lógica de popular a tabela já foi feita no PHP.
</script>

</body>
</html>
