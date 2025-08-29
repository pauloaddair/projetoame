<?php
$titulo = "Escala de Atendentes";
$base_path = __DIR__ . '/../';
include_once($base_path . 'include/conexao.php');
include_once($base_path . 'include/funcoes.php');
include_once($base_path . 'include/head-table.php');
//if (isset($_SESSION['nivel']) && $_SESSION['nivel'] >= 3) {

    $mensagem_sucesso = '';
    // Processa o formulário de salvar escala
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acao']) && $_POST['acao'] == 'salvar_escala') {
        if (isset($_POST['escala']) && is_array($_POST['escala'])) {
            
            mysqli_begin_transaction($conexao);

            try {
                // Buscar o maior valor de rodízio atual
                $query_max_rodizio = "SELECT MAX(rodizio) as max_rodizio FROM candidatos";
                $resp_max = mysqli_query($conexao, $query_max_rodizio);
                $row_max = mysqli_fetch_assoc($resp_max);
                $novo_rodizio = $row_max['max_rodizio'] + 1;

                foreach ($_POST['escala'] as $horario_id => $disponibilidade_ids) {
                    foreach ($disponibilidade_ids as $disponibilidade_id => $candidato_id) {
                        // 1. Atualizar a disponibilidade para escalado = 1
                        $query_update_disp = "UPDATE disponibilidade SET escalado = 1 WHERE id = ?";
                        $stmt_disp = mysqli_prepare($conexao, $query_update_disp);
                        mysqli_stmt_bind_param($stmt_disp, 'i', $disponibilidade_id);
                        mysqli_stmt_execute($stmt_disp);

                        // 2. Atualizar o rodízio do candidato
                        $query_update_cand = "UPDATE candidatos SET rodizio = ? WHERE candidato_id = ?";
                        $stmt_cand = mysqli_prepare($conexao, $query_update_cand);
                        mysqli_stmt_bind_param($stmt_cand, 'ii', $novo_rodizio, $candidato_id);
                        mysqli_stmt_execute($stmt_cand);

                        $novo_rodizio++; // Incrementar para o próximo candidato
                    }
                }

                mysqli_commit($conexao);
                $mensagem_sucesso = "<div class='alert alert-success'>Escala salva com sucesso!</div>";
            } catch (Exception $e) {
                mysqli_rollback($conexao);
                $mensagem_sucesso = "<div class='alert alert-danger'>Erro ao salvar a escala: " . $e->getMessage() . "</div>";
            }
        }
    }

    // Query para buscar os eventos futuros
    $query_eventos = "SELECT id, nome FROM eventos_marcados WHERE final >= CURDATE() ORDER BY final ASC;";
    $resp_eventos = mysqli_query($conexao, $query_eventos);

    $evento_selecionado_id = $_POST['evento_id'] ?? '';

	include_once($base_path . 'include/nav.php');
?>
		<div class="container">
			<header class="mt-5 p-2 justify-content-md-center">
				<h1 class="text-center">Escala de Atendentes</h1>
				<nav aria-label="breadcrumb">
				  <ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="<?php echo $GLOBALS['app_web_root']; ?>admin">Admin</a></li>
					<li class="breadcrumb-item active" aria-current-page">Escala de Atendentes</li>
				  </ol>
				</nav>
			</header>

            <?php if (!empty($mensagem_sucesso)) { echo $mensagem_sucesso; } ?>

            <form method="POST" action="<?php echo $GLOBALS['app_web_root']; ?>escala">
                <div class="row mb-3">
                    <div class="col-md-8">
                        <label for="evento_id" class="form-label"><strong>Selecione o Evento:</strong></label>
                        <select class="form-select" id="evento_id" name="evento_id">
                            <option value="">-- Escolha um evento --</option>
                            <?php
                            while ($row_evento = mysqli_fetch_assoc($resp_eventos)) {
                                $selected = ($row_evento['id'] == $evento_selecionado_id) ? 'selected' : '';
                                echo "<option value='{$row_evento['id']}' {$selected}>{$row_evento['nome']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Carregar Atendentes Disponíveis</button>
                    </div>
                </div>
            </form>

			<?php
			// Se um evento foi selecionado, a lógica para exibir a tabela virá aqui.
			if ($evento_selecionado_id) {
                $query_horarios = "
                    SELECT
                        h.horario_id,
                        h.data_inicio,
                        h.data_final,
                        h.vagas,
                        h.tipo,
                        c.candidato_id,
                        c.nome,
                        c.rodizio,
                        i.url as perfil,
                        d.id as disponibilidade_id
                    FROM horarios h
                    JOIN disponibilidade d ON h.horario_id = d.atividade_id
                    JOIN candidatos c ON d.candidato_id = c.candidato_id
                    LEFT JOIN imagens i ON c.imagem_id = i.imagem_id
                    WHERE h.evento_id = {$evento_selecionado_id} AND d.escalado = 0
                    ORDER BY h.data_inicio, c.rodizio ASC
                ";

                $result = mysqli_query($conexao, $query_horarios);

                $horarios = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $horarios[$row['horario_id']]['detalhes'] = [
                        'data_inicio' => $row['data_inicio'],
                        'data_final' => $row['data_final'],
                        'vagas' => $row['vagas'],
                        'tipo' => $row['tipo']
                    ];
                    $horarios[$row['horario_id']]['atendentes'][] = [
                        'candidato_id' => $row['candidato_id'],
                        'disponibilidade_id' => $row['disponibilidade_id'],
                        'nome' => $row['nome'],
                        'rodizio' => $row['rodizio'],
                        'perfil' => $row['perfil'] ?? '/img/profile.png'
                    ];
                }

                if (empty($horarios)) {
                    echo "<div class='alert alert-warning mt-3'>Nenhum atendente disponível para este evento ou todos já foram escalados.</div>";
                } else {
                    echo '<form method="POST" action="' . $GLOBALS['app_web_root'] . 'escala">';
                    echo '<input type="hidden" name="evento_id" value="' . $evento_selecionado_id . '">';
                    echo '<input type="hidden" name="acao" value="salvar_escala">';

                    foreach ($horarios as $horario_id => $data) {
                        $detalhes = $data['detalhes'];
                        $atendentes = $data['atendentes'];
                        $vagas_disponiveis = $detalhes['vagas'];

                        echo "<div class='card mb-4'>";
                        echo "<div class='card-header bg-info text-white'>";
                        echo "<h5>Horário: " . date('d/m/Y H:i', strtotime($detalhes['data_inicio'])) . " às " . date('H:i', strtotime($detalhes['data_final'])) . "</h5>";
                        echo "<strong>Vagas:</strong> {$vagas_disponiveis}";
                        echo "</div>";
                        echo "<div class='card-body'>";
                        echo "<table class='table table-sm table-hover'>";
                        echo "<thead><tr><th>Escalar</th><th>Foto</th><th>Atendente</th><th>Prioridade (Rodízio)</th></tr></thead>";
                        echo "<tbody>";

                        foreach ($atendentes as $atendente) {
                            echo "<tr>";
                            echo "<td><input class='form-check-input' type='checkbox' name='escala[{$horario_id}][{$atendente['disponibilidade_id']}]' value='{$atendente['candidato_id']}'></td >";
                            echo "<td><img src='" . $GLOBALS['app_web_root'] . $atendente['perfil'] . "' height='40' class='img-thumbnail rounded-circle'></td>";
                            echo "<td>{$atendente['nome']}</td>";
                            echo "<td>{$atendente['rodizio']}</td>";
                            echo "</tr>";
                        }

                        echo "</tbody></table>";
                        echo "</div></div>";
                    }

                    echo '<button type="submit" class="btn btn-success btn-lg w-100">Salvar Escala</button>';
                    echo '</form>';
                }
			}
			?>

		</div>
	<?php
	include_once($base_path . 'include/footer-database.php');

/*
} else {
// Usuário não tem permissão, redirecione ou exiba uma mensagem de erro
	include_once($base_path . 'include/conexao.php');
	include_once($base_path . 'include/head.php');
	include_once($base_path . 'pages/restrito.php');
}
*/
	?>
	</body>
<?php include_once($base_path . 'include/end.php');
?>