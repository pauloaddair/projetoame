<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
include_once('./include/funcoes.php');

require before('/public_html',__DIR__) . '/vendor/autoload.php';
//session_start();
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');

// Consulta eventos ativos com data final futura ou presente
$queryeventos = "SELECT eventos_marcados.*,imagens.url
FROM eventos_marcados,imagens
WHERE eventos_marcados.imagem_id = imagens.imagem_id
AND final >= CURDATE() 
ORDER BY final ASC;";
$respeventos = mysqli_query($conexao, $queryeventos);
$total_eventos = ($respeventos) ? mysqli_num_rows($respeventos) : 0;

$msg = "";
$dados = array(
	array("nome","nome do atendente","digite o nome do atendente","text","fas fa-user"),
	array("Email","seu e-mail de contato","e-mail cadastrado","text","fas fa-envelope"),
	array("Telefone","telefone de contato","digite o telefone de contato","text","fas fa-phone"),
	array("WZ","WhatsApp","","switch",""),
	array("telegram","Telegram","","switch",""),
	array("Nascimento","Data de nascimento","digite a data de nascimento","date","fas fa-calendar"),
	array("RG","RG do atendente","digite o RG do atendente","text","fas fa-address-card"),
	array("CPF","CPF do atendente","digite o CPF do atendente","text","fas fa-address-card"),
	array("responsavel","digite o nome do responsável","nome do responsável","text","fas fa-user"),
	array("CPF_RESP","CPF do responsável","digite o CPF do responsável","text","fas fa-address-card","far fa-map"),
	array("CEP","CEP","digite o CEP do endereço","text","far fa-map"),
	array("endereco","endereço","digite o endereço do atendente","text","far fa-map"),
	array("complemento","complemento (nº, andar, apto etc.)","digite o complemento do endereço","text","far fa-map"),
	array("cidade","cidade","digite a cidade","text","far fa-map"),
	array("UF","Estado","forneça o estado","text","far fa-map"),
	array("boletim","Boletim","","switch",""),
	array("grupo_capacitação","Incluir no grupo de capacitação","","switch",""),
	array("grupo_voluntarios","Quero ser voluntário","","switch","")		
);

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	if ($total_eventos == 0) {
		$msg = "<div class='alert alert-warning text-center'>Nenhum evento programado no momento. Formulário desativado.</div>";
	} else {
		// Contar se há pelo menos um horário/evento marcado
		$horarios_selecionados = 0;
		foreach ($_POST as $campo => $valor) {
			if ($campo !== "nome" && $campo !== "email" && intval($campo) > 0) {
				$horarios_selecionados++;
			}
		}

		if ($horarios_selecionados === 0) {
			$msg = "<h2 class='text-center'>Nenhuma disponibilidade selecionada</h2><p class='text-center'><span class='mt-1 p-2 bg-warning rounded-pill text-dark'>Por favor, selecione ao menos um horário de disponibilidade nos eventos acima.</span></p>";
		} else {
			$email_post = isset($_POST['email']) ? trim($_POST['email']) : '';
			$email_safe = mysqli_real_escape_string($conexao, $email_post);
			$query = "SELECT candidatos.* FROM candidatos WHERE candidatos.Email LIKE '" . $email_safe . "' LIMIT 1";
			$r = mysqli_query($conexao, $query);

			if (!$r || mysqli_num_rows($r) == 0) {
				$msg = "<h2 class='text-center'>Atendente não encontrado</h2><p class='text-center'><span class='mt-1 p-2 bg-danger rounded-pill text-white'>Por favor, verifique se digitou seu e-mail cadastrado corretamente e tente novamente</span></p><p class='text-center'>Se ainda não é inscrito, clique <a href='https://projetoame.org/inscrever' class='btn btn-sm btn-primary'>AQUI</a></p>";
			} else {
				$row = mysqli_fetch_assoc($r);
				$nome = isset($_POST['nome']) && trim($_POST['nome']) !== '' ? trim($_POST['nome']) : $row['nome'];
				$msg = "<h2 class='text-center'>Atendente: " . htmlspecialchars($row['nome']) . "</h2><p class='mt-1 p-2 text-center bg-success rounded-pill text-white'>Disponibilidade registrada com sucesso!</p>";

				$deletar = "DELETE disponibilidade FROM disponibilidade
				JOIN horarios ON disponibilidade.atividade_id = horarios.horario_id
				JOIN eventos_marcados ON horarios.evento_id = eventos_marcados.id
				WHERE eventos_marcados.inicio > CURDATE()
				AND eventos_marcados.escala_fechada = 0 
				AND disponibilidade.candidato_id = " . intval($row['candidato_id']) . ";";
				mysqli_query($conexao, $deletar);

				$to = "pauloadd@novaeratec.com.br";
				$subject = '[Projeto AME] Comprovante de Disponibilidade: ' . $nome;

				$body = '<div style="background-color: #f8f9fa; border-left: 4px solid #28a745; padding: 12px; margin-bottom: 20px; font-family: sans-serif;">';
				$body .= '<strong style="color: #155724; font-size: 15px;">Comprovante de envio pelo site</strong><br>';
				$body .= '<span style="color: #555; font-size: 13px;">Recebemos através do site <a href="https://projetoame.org" target="_blank" style="color: #007bff;">projetoame.org</a> o registro de disponibilidade para o atendente abaixo.</span>';
				$body .= '</div>';
				$body .= '<div style="font-family: sans-serif; font-size: 14px; line-height: 1.6;">';
				$body .= '<strong>Nome:</strong> ' . htmlspecialchars($nome) . '<br>';
				$body .= '<strong>Responsável:</strong> ' . htmlspecialchars($row['responsavel']) . '<br>';
				$body .= '<br><strong>Disponibilidade informada:</strong><hr style="border: 0; border-top: 1px solid #eee; margin: 10px 0;">';

				$ev = "";
				foreach ($_POST as $campo => $valor) {
					if ($campo !== "nome" && $campo !== "email" && intval($campo) > 0) {
						$horario_id = intval($campo);
						$inserir = "INSERT INTO `disponibilidade`(`candidato_id`, `atividade_id`) VALUES (" . intval($row['candidato_id']) . ", " . $horario_id . ")";
						mysqli_query($conexao, $inserir);

						$query_h = "SELECT eventos_marcados.*, horarios.data_inicio, horarios.data_final FROM eventos_marcados, horarios WHERE eventos_marcados.id = horarios.evento_id AND horario_id = " . $horario_id . " ORDER BY eventos_marcados.nome, horarios.data_inicio;";
						$resp_h = mysqli_query($conexao, $query_h);
						if ($resp_h && mysqli_num_rows($resp_h) > 0) {
							$ativ = mysqli_fetch_assoc($resp_h);
							if ($ev !== $ativ['nome']) {
								$body .= '<strong style="color: #333;">Evento: ' . htmlspecialchars($ativ['nome']) . '</strong><br>';
							}
							$body .= 'Dia: ' . date("d/m/Y, D", strtotime($ativ['data_inicio'])) . ' das ' . date("H:i", strtotime($ativ['data_inicio'])) . ' às ' . date("H:i", strtotime($ativ['data_final'])) . '<br>';
							$ev = $ativ['nome'];
						}
					}
				}

				// Capturar IP real do visitante (respeitando Cloudflare / proxies reversos)
				$ip_visitante = function_exists('obter_ip_visitante') 
					? obter_ip_visitante() 
					: ($_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'IP não identificado');

				$body .= '<br><hr style="border: 0; border-top: 1px solid #ddd; margin: 20px 0;">';
				$body .= '<p style="color: #666; font-size: 12px; margin-bottom: 4px;"><strong>DADOS DE AUDITORIA:</strong></p>';
				$body .= '<p style="color: #666; font-size: 12px; line-height: 1.5; margin: 0;">';
				$body .= 'IP: ' . htmlspecialchars($ip_visitante) . '<br>';
				$body .= 'Navegador: ' . htmlspecialchars($_SERVER['HTTP_USER_AGENT'] ?? 'Não informado') . '<br>';
				$body .= 'Enviado em: ' . date('d/m/Y H:i') . '<br>';
				$body .= '</p></div>';

				$mail = new PHPMailer(true);
				try {
					$mail->isSMTP();
					$mail->Host       = 'mail.projetoame.org';
					$mail->SMTPAuth   = true;
					$mail->Username   = 'noreply@projetoame.org';
					$mail->Password   = 'PittJusto@3802';
					$mail->SMTPSecure = 'tls';
					$mail->Port       = 587;

					$mail->SMTPOptions = array(
						'ssl' => array(
							'verify_peer' => false,
							'verify_peer_name' => false,
							'allow_self_signed' => true
						)
					);

					$mail->setFrom('noreply@projetoame.org', 'Projeto AME - Disponibilidade');
					$mail->addAddress($to);
					$mail->addAddress($email_post);
					$mail->addCC('regina.rjr@hotmail.com');

					$mail->isHTML(true);
					$mail->Subject = $subject;
					$mail->Body    = $body;
					$mail->CharSet = 'UTF-8';

					$mail->send();
					$msg = "<p class='text-center'>Os dados de <strong><em>" . htmlspecialchars($nome) . "</em></strong> foram enviados com sucesso!</p>";
				} catch (Exception $e) {
					$msg = "<p class='text-center'>Não conseguimos enviar o e-mail de confirmação! Erro: {$mail->ErrorInfo}</p>";
				}
			}
		}
	}
}
?>
	<?php include_once('./include/nav.php'); ?>
	<div class="container mt-5 pt-4">
		<header>
		<h1 class="text-center">Atividades confirmadas</h1>
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="/atendentes">Atividades</a></li>
				<li class="breadcrumb-item"><a href="/rodizio">Rodizio</a></li>
				<li class="breadcrumb-item active" aria-current="page">Atividades confirmadas</li>
			  </ol>
			</nav>
		</header>

		<?php if (!empty($msg)): ?>
			<div class="row mt-1">
				<div class='col-12'>
					<?php echo $msg; ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ($total_eventos == 0): ?>
			<div class="row justify-content-center my-5">
				<div class="col-md-8 col-lg-6 text-center">
					<div class="card shadow-sm border-0 py-5 px-4 bg-light">
						<div class="card-body">
							<i class="far fa-calendar-times fa-4x text-muted mb-3 d-block"></i>
							<h3 class="h4 font-weight-bold text-dark">Nenhum evento programado</h3>
							<p class="text-muted mb-0">Aguarde nova programação.</p>
						</div>
					</div>
				</div>
			</div>
		<?php else: ?>
			<form method="post" id="formDisponibilidade" onsubmit="return validarEnvio(event)">
				<div class="row mt-1">
				<?php
				$resp = mysqli_query($conexao, $queryeventos);
				while ($row0 = mysqli_fetch_assoc($resp)){
				?>
				<div class="col col-md-6 align-items-stretch d-flex mb-2">
					<div class="card shadow-sm w-100">
						<div class="card-header bg-light">
							<img class="card-img-top rounded mb-2" src="<?php echo $GLOBALS['app_web_root'] . ltrim($row0['url'], '/'); ?>" style="max-height: 220px; object-fit: cover;">
							<h2 class="text-center h4 font-weight-bold"><?php echo htmlspecialchars($row0['nome']); ?></h2>
							<p class="text-center text-muted mb-2">De <?php echo formataDataEventoBR($row0['inicio']); ?> a <?php echo formataDataEventoBR($row0['final']); ?></p>
							<p class="mb-1"><a href="<?php echo htmlspecialchars($row0['maps']); ?>" target="_blank"><i class="far fa-map text-primary mr-1"></i><strong><?php echo htmlspecialchars($row0['local']); ?></strong></a>&nbsp;<?php echo htmlspecialchars($row0['endereco']); ?></p>
							<?php
							if (!empty($row0['obs'])){
								echo "<p class='mb-0'><small><strong class='badge badge-warning text-dark p-1 mr-1'>Obs.:</strong> ".htmlspecialchars($row0['obs'])."</small></p>";
							}
							?>
						</div>
						<div class="card-body">
					<?php
						$id = $row0['id'];
						$query1 = "SELECT * FROM horarios WHERE evento_id = ".$id. " ORDER BY data_inicio;";
						$horarios = mysqli_query($conexao, $query1);
						while ($row_horario = mysqli_fetch_assoc($horarios)){
							$checked = "";
							$disable = "";
							if (isset($_POST[digitos($row_horario['horario_id'])])){
								$checked = " checked";
							}
							if ($row_horario['preenchido'] == '1'){
								$disable = " disabled";
							}
							?>
							<div class="custom-control custom-switch mb-2">
							<input type="checkbox" class="custom-control-input item-horario" name="<?php echo digitos($row_horario['horario_id'])?>" id="<?php echo digitos($row_horario['horario_id'])?>" <?php echo $checked?> <?php echo $disable?>>
							<label class="custom-control-label" for="<?php echo digitos($row_horario['horario_id'])?>">
								<strong><?php echo formataDataEventoBR($row_horario['data_inicio']); ?></strong> &bull; <?php echo htmlspecialchars($row_horario['tipo']); ?><br>
								<small class="text-muted"><?php echo "das ".date("H:i",strtotime($row_horario['data_inicio'])). " às ".date("H:i",strtotime($row_horario['data_final']));
								$vagas = "";
								if (intval($row_horario['vagas']) > 0){
									$vagas = " (".intval($row_horario['vagas'])." vagas)";
								}
								echo $vagas;
								?>
								</small>
								</label>
							</div>
							<?php }
					?>
					</div>
					</div>
				</div>
					<?php }
					?>
				</div><hr>
				<div class="row justify-content-center">
					<div class="col col-md-8 col-lg-6">
						<p class="text-center"><strong>Confirme seu nome e e-mail, por favor</strong></p>
						<div class="md-form">
							<i class="far fa-user prefix grey-text"></i>
							<input type="text" id="nome" name="nome" class="form-control" placeholder="nome do atendente" required
								   <?php if(isset($_POST['nome'])){
									   echo " value='".htmlspecialchars($_POST['nome'], ENT_QUOTES)."'";
								   }
								   ?>>
							<label for="nome" class="sr-only">digite o nome do atendente</label>
						</div>
						<div class="md-form">
							<i class="far fa-envelope prefix grey-text"></i>
							<input type="email" id="email" name="email" class="form-control" placeholder="seu e-mail cadastrado" required
								   <?php if(isset($_POST['email'])){
									   echo " value='".htmlspecialchars($_POST['email'], ENT_QUOTES)."'";
								   }
								   ?>>
							<label for="email" class="sr-only">digite seu e-mail</label>
						</div>
						<div class="md-form mt-4">
							<button class="btn btn-sm btn-block rounded-pill btn-primary" id="btnEnviar" type="submit">Enviar</button>
						</div>
						<div class="text-center mt-3">
							<p class="small text-muted">Mudou de e-mail? <a href="/atualizar_email">Clique aqui para atualizar seu cadastro</a>.</p>
						</div>
					</div>
				</div>
			</form>
		<?php endif; ?>
	</div>
<?php
include_once('./include/footer.php');
?>
<script>
function validarEnvio(e) {
	var nome = document.getElementById('nome');
	var email = document.getElementById('email');
	if (!nome || !nome.value.trim() || !email || !email.value.trim()) {
		alert('Por favor, informe seu nome e o e-mail cadastrado.');
		return false;
	}
	var selecionados = document.querySelectorAll('input.item-horario:checked');
	if (selecionados.length === 0) {
		alert('Por favor, selecione ao menos um dia/horário de disponibilidade nos eventos acima.');
		return false;
	}
	var btn = document.getElementById('btnEnviar');
	if (btn) {
		btn.disabled = true;
		btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando...';
	}
	return true;
}
</script>
</body>
<?php
include_once('./include/scripts.php');
?>
<?php include_once('./include/end.php');
?>