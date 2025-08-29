<?php
date_default_timezone_set('America/Sao_Paulo');
include_once('./include/conexao.php');
// --- LÓGICA DE BACKEND (Responde a requisições AJAX) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['action']) && $data['action'] === 'get_escala_details') {
        $evento_id = intval($data['evento_id']);
        
        $query_horarios = "SELECT horario_id, data_inicio, data_final, vagas FROM horarios WHERE evento_id = {$evento_id} ORDER BY data_inicio ASC";
        $result_horarios = mysqli_query($conexao, $query_horarios);

        if (!$result_horarios) {
            echo json_encode(['success' => false, 'message' => 'Erro na consulta de horários: ' . mysqli_error($conexao)]);
            exit;
        }
        
        $horarios_data = [];
        while ($horario = mysqli_fetch_assoc($result_horarios)) {
            $horario_id = $horario['horario_id'];
            
            $query_atendentes = "SELECT c.candidato_id, c.nome, c.rodizio, i.url, IF(d.id IS NOT NULL, 1, 0) as is_disponivel, IF(d.escalado = 1, 1, 0) as is_escalado FROM candidatos c LEFT JOIN imagens i ON c.imagem_id = i.imagem_id LEFT JOIN disponibilidade d ON c.candidato_id = d.candidato_id AND d.atividade_id = {$horario_id} WHERE c.ativo = 1 ORDER BY c.rodizio ASC";
            $result_atendentes = mysqli_query($conexao, $query_atendentes);

            if (!$result_atendentes) {
                echo json_encode(['success' => false, 'message' => 'Erro na consulta de atendentes para o horário ' . $horario_id . ': ' . mysqli_error($conexao)]);
                exit;
            }
            
            $atendentes = [];
            while ($atendente = mysqli_fetch_assoc($result_atendentes)) {
                $atendentes[] = $atendente;
            }
            
            $horario['atendentes'] = $atendentes;
            $horarios_data[] = $horario;
        }
        
        echo json_encode(['success' => true, 'horarios' => $horarios_data]);
        exit;
    }

    if (isset($data['action']) && $data['action'] === 'save_escala') {
        $evento_id = intval($data['evento_id']);
        $escalados_por_horario = $data['escalados'] ?? [];
        $query_get_horarios = "SELECT horario_id FROM horarios WHERE evento_id = {$evento_id}";
        $result_horarios = mysqli_query($conexao, $query_get_horarios);
        $horario_ids = [];
        while($row = mysqli_fetch_assoc($result_horarios)) {
            $horario_ids[] = $row['horario_id'];
        }
        if (!empty($horario_ids)) {
            $ids_string = implode(',', $horario_ids);
            $query_limpar = "UPDATE disponibilidade SET escalado = 0 WHERE atividade_id IN ({$ids_string})";
            mysqli_query($conexao, $query_limpar);
        }
        foreach ($escalados_por_horario as $horario_id => $candidatos_ids) {
            if (!empty($candidatos_ids)) {
                $candidatos_ids_string = implode(',', array_map('intval', $candidatos_ids));
                $query_escalar = "UPDATE disponibilidade SET escalado = 1 WHERE atividade_id = ".intval($horario_id)." AND candidato_id IN ({$candidatos_ids_string})";
                mysqli_query($conexao, $query_escalar);
                $query_max_rodizio = "SELECT MAX(rodizio) as max_rodizio FROM candidatos";
                $result_max = mysqli_query($conexao, $query_max_rodizio);
                $max_rodizio = mysqli_fetch_assoc($result_max)['max_rodizio'];
                foreach ($candidatos_ids as $candidato_id) {
                    $max_rodizio++;
                    $query_update_rodizio = "UPDATE candidatos SET rodizio = {$max_rodizio} WHERE candidato_id = ".intval($candidato_id);
                    mysqli_query($conexao, $query_update_rodizio);
                }
            }
        }
        echo json_encode(['success' => true, 'message' => 'Escala salva com sucesso!']);
        exit;
    }
}

// --- LÓGICA DE FRONTEND (Renderiza a página) ---
$evento_id_selecionado = null;
$nome_evento_selecionado = 'Evento não encontrado';
if (isset($_GET['evento_id'])) {
    $evento_id_selecionado = intval($_GET['evento_id']);
    $query_evento_nome = "SELECT nome FROM eventos_marcados WHERE id = {$evento_id_selecionado}";
    $result_nome = mysqli_query($conexao, $query_evento_nome);
    if ($row_nome = mysqli_fetch_assoc($result_nome)) {
        $nome_evento_selecionado = $row_nome['nome'];
    }
}
?>
<body>
<div class="container mt-5">
	<h1 class="mb-4">Gestão de Escala: <?php echo htmlspecialchars($nome_evento_selecionado); ?>
		<img id="event-header-image" src="" alt="Imagem do Evento" class="img-thumbnail ml-3" style="max-height: 80px; display: none;">
	</h1>
<!--    <h1 class="mb-4">Gestão de Escala: <?php echo htmlspecialchars($nome_evento_selecionado); ?></h1>-->
    <p><a href="/admin/eventos">&laquo; Voltar para a lista de eventos</a></p>
    <hr>
	<div id="mensagem-escala"></div>
    <div id="escala-container">
        <?php if (!$evento_id_selecionado): ?>
            <div class="alert alert-danger">Nenhum evento foi especificado.</div>
        <?php endif; ?>
    </div>
</div>
<!-- Modal de Mensagens -->
<div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="messageModalLabel">Mensagem</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="messageModalBody">
				<!-- Conteúdo da mensagem aqui -->
				</div>
				<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
			</div>
		</div>
	</div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const escalaContainer = document.getElementById('escala-container');
    const urlParams = new URLSearchParams(window.location.search);
    const eventoId = urlParams.get('evento_id');
    var AppWebRoot = '<?php echo $GLOBALS["app_web_root"]; ?>';

    function carregarDetalhesEvento(id) {
          if (!id) return;
     
          fetch(AppWebRoot + 'include/api_escala.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ action: 'get_escala_details', evento_id: id })
          })
          .then(response => response.json())
         .then(data => {
             const msgDiv = document.getElementById('mensagem-escala');
             if (data.success && data.horarios && data.candidatos) {
                 // Determina se alguma escala já foi salva para este evento
                 let isScheduleSaved = false;
                 data.candidatos.forEach(candidato => {
                     for (const horarioId in candidato.horarios_status) {
                         if (candidato.horarios_status[horarioId].is_escalado == 1) {
                             isScheduleSaved = true;
                             break;
                         }
                     }
                     if (isScheduleSaved) {
                         return; // Exit outer loop
                     }
                 });
    
                 escalaContainer.innerHTML = '<div id="mensagem-escala"></div>';
				 	const eventImage = document.getElementById('event-header-image');
					if (data.event_image_url) {
						eventImage.src = '<?php echo $GLOBALS['app_web_root']; ?>' + data.event_image_url;
						eventImage.style.display = 'inline-block';
					} else {
						eventImage.style.display = 'none';
					}
				 const preSelectedCounts = new Map(); // Inicializa mapa para rastrear contagens pré-selecionadas por horário
	 			 let tableHtml = `
                     <table class="table table-striped table-bordered">
                         <thead>
                             <tr>
                                 <th>Rodízio</th>
                                 <th>Foto</th>
                                 <th>Nome</th>
                                 <th>Status</th>
                 `;
    
                 // Add horario headers
                 data.horarios.forEach(horario => {
                     tableHtml += `<th>${new Date(horario.data_inicio).toLocaleDateString('pt-BR')}
       <br> ${new Date(horario.data_inicio).toLocaleTimeString('pt-BR', {hour: '2-digit', minute:
       '2-digit'})} - ${new Date(horario.data_final).toLocaleTimeString('pt-BR', {hour: '2-digit',
       minute:'2-digit'})} <br> (${horario.vagas} vagas)</th>`;
                 });
    
                 tableHtml += `
                             </tr>
                         </thead>
                         <tbody>
                 `;
    
                 // Add candidate rows
                 data.candidatos.forEach(candidato => {
                     const rowClass = candidato.ativo == 0 ? 'table-success' : ''; // Light green for inactive
                     tableHtml += `<tr class="${rowClass}">`;
                     tableHtml += `<td>${candidato.rodizio}</td>`;
                     tableHtml += `<td><img src="<?php echo $GLOBALS['app_web_root']; ?>${candidato.url}" class="img-thumbnail" width="40"></td>`;
                     tableHtml += `<td>${candidato.nome}</td>`;
                     tableHtml += `<td>${candidato.ativo == 1 ? 'Atendente' : 'Treinamento'}</td>`;
     // Updated Status column
    
                     data.horarios.forEach(horario => {
                         const status = candidato.horarios_status[horario.horario_id] || {
     is_disponivel: 0, is_escalado: 0};
                         const isAvailable = status.is_disponivel == 1;
                         let isChecked = '';
    
                         if (isScheduleSaved) {
                             isChecked = status.is_escalado == 1 ? 'checked' : '';
                         } else {
								// Obtém a contagem atual para este horário
								const currentCount = preSelectedCounts.get(horario.horario_id) || 0;

								if (isAvailable && currentCount < horario.vagas) {
									isChecked = 'checked';
						            preSelectedCounts.set(horario.horario_id, currentCount + 1); // Incrementa a contagem para este horário
						   }}
    
                         tableHtml += `
                             <td>
								<div class="row">
									<div class="col">
 										<div class="custom-control custom-switch">
											<input type="checkbox" class="custom-control-input" id="switch-${candidato.candidato_id}-${horario.horario_id}" data-horario-id="${horario.horario_id}" value="${candidato.candidato_id}" ${isChecked}>
											<label class="custom-control-label" for="switch-${candidato.candidato_id}-${horario.horario_id}"></label>
										</div>
									</div>
									<div class="col">
                                 		${isAvailable ? '<i class="fas fa-check text-success"></i>' : ''}
									</div>
								</div>
                            </td>
                         `;
                     });
    
                     tableHtml += `</tr>`;
                 });
    
                 tableHtml += `
                         </tbody>
                     </table>
                 `;
                 escalaContainer.innerHTML += tableHtml;
    
                 const saveButton = document.createElement('button');
                 saveButton.className = 'btn btn-primary mt-3';
                 saveButton.id = 'salvar-escala-btn';
                 saveButton.innerText = 'Salvar Escala';
                 escalaContainer.appendChild(saveButton);
                 attachSaveListener();
             } else {
                 msgDiv.className = 'alert alert-danger';
                msgDiv.innerHTML = 'Erro ao carregar detalhes: ' + (data.message || 'Resposta inválida do servidor.');
            }
        });
    }

    function attachSaveListener() {
        const saveButton = document.getElementById('salvar-escala-btn');
        if(saveButton) {
            saveButton.addEventListener('click', function(event) {
				event.preventDefault();
                const checkboxes = document.querySelectorAll('#escala-container input[type="checkbox"]:checked');
                const msgDiv = document.getElementById('mensagem-escala');
                let escalados = {};
                checkboxes.forEach(cb => {
                    const horarioId = cb.dataset.horarioId;
                    if (!escalados[horarioId]) {
                        escalados[horarioId] = [];
                    }
                    escalados[horarioId].push(cb.value);
                });
                fetch(AppWebRoot + 'include/api_escala.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        action: 'save_escala', 
                        evento_id: eventoId, 
                        escalados: escalados
                    })
                })
                .then(response => response.json())
                .then(data => {
				 const modalTitle = document.getElementById('messageModalLabel');
				 const modalBody = document.getElementById('messageModalBody');
				 const messageModal = $('#messageModal'); // Usa jQuery para o modal

				 if (data.success) {
					 modalTitle.innerText = 'Sucesso!';
					 modalTitle.className = 'modal-title text-success'; // Opcional: cor verde para sucesso
				 } else {
					modalTitle.innerText = 'Erro!';
					modalTitle.className = 'modal-title text-danger'; // Opcional: cor vermelha para erro
				}
				modalBody.innerHTML = data.message || 'Ocorreu uma falha na operação.';

				messageModal.modal('show'); // Exibe o modal
/*
                     msgDiv.className = data.success ? 'alert alert-success' : 'alert alert-danger';
                    msgDiv.innerHTML = data.message || 'Ocorreu uma falha.';
                    setTimeout(() => { msgDiv.innerHTML = ''; msgDiv.className = ''; }, 3000);
*/
                });
            });
        }
    }

    if (eventoId) {
        carregarDetalhesEvento(eventoId);
    }
});
</script>
</body>
</html>