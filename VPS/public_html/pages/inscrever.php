<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');

$nomearquivo = "";
$campos = array(
	array(
		"nome",
		"Nome",
		"nome do inscrito",
		"text",
		"fas fa-user",
		"required"
	),
	array(
		"responsavel",
		"Responsável",
		"nome do responsável",
		"text",
		"fas fa-user",
		"required"
	),
	array(
		"Email",
		"E-mail",
		"e-mail do responsável",
		"text",
		"fas fa-envelope",
		"required"
	),
	array(
		"Telefone",
		"Telefone",
		"telefone de contato",
		"tel",
		"fas fa-phone",
		""
	),
	array(
		"Nascimento",
		"",
		"data de nascimento do inscrito",
		"date",
		"fas fa-calendar",
		""
	),
	array(
		"Curriculo",
		"Currículo ou foto",
		"curriculo ou foto (opcional)",
		"file",
		"fas fa-file",
		""
	),
	array(
		"genero",
		"Gênero",
		"sexo do inscrito (opcional)",
		"option",
		"fas fa-user",
		"",
		"masculino",
		"feminino",
	),
	array(
		"mensagem",
		"Observação",
		"deixe sua observação (opcional)",
		"textarea",
		""
	),
);
$dados['nome']="";
$dados['responsavel']="";
$dados['Email']="";
$dados['Telefone']="";
$dados['Nascimento']="";
$dados['Curriculo']="";
$dados['genero']="";
$dados['mensagem']="";

$body="";
$msg ="Novo inscrito";
if ($_SERVER['REQUEST_METHOD']=="POST"){
	$query = "SELECT candidatos.* FROM candidatos WHERE candidatos.Email LIKE '".$_POST['Email']."'";
	$nome = $_POST['nome'];
	$email = $_POST['Email'];
	$telefone = $_POST['Telefone'];
	$r = mysqli_query($conexao,$query);
	$cadastrar = 1;
?>
	<?php foreach ($_POST as $campo => $valor) {
			$dados[$campo] = $valor;
		}
	if (mysqli_num_rows($r)>0){
		$cadastrar = 0;
		$row = mysqli_fetch_assoc($r);
		$candidato_id = $row['candidato_id'];
		$nome = $_POST['nome'];
		$msg = "<h2 class='text-center'>Atendente: ".$nome."</h2><p class='mt-1 p-2 text-center bg-success rounded-pill'>Confirme seus dados abaixo</p>";
		$query = "UPDATE `candidatos` SET ";
	    foreach ($_POST as $campo => $valor) {
			if ($campo<>"nome" && $campo<>"email"&& $campo<>"Curriculo"){
				$query .="`".$campo."`='".$valor."', ";
			} 
		}
			$query = substr($query,0,-2);
			$query .=" WHERE candidato_id =".$candidato_id;
		} else {
			$query = "SELECT (MAX(rodizio)+1) AS max FROM candidatos;";
			$resp = mysqli_query($conexao,$query);
			$max = mysqli_fetch_array($resp);
			$rodizio = $max['max'];
			$cadastrar = 1;
			$msg = "<h2 class='text-center'>Atendente não encontrado</h2><p class='text-center'><span class='mt-1 p-2 bg-danger rounded-pill text-white'>Por favor, verifique se digitou seu e-mail cadastrado corretamente e tente novamente, ou então preencha os dados abaixo para se cadastrar pela primeira vez.</span></p>";
			
			// Novos inscritos entram como INATIVOS (ativo = -1) para moderação prévia
			$query = "INSERT INTO `candidatos` ";
			$insert_campos = "`inscrito`, `IP`, `rodizio`, `ativo`, ";
			$valores="'candidato', '".$_SERVER["REMOTE_ADDR"]."', ".$rodizio.", -1, ";
			foreach ($_POST as $campo => $valor) {
				if ($campo<>"Curriculo"){
					$insert_campos .="`".$campo."`, ";
					$valores .="'".$valor."', ";
				}
			}
			$insert_campos = substr($insert_campos,0,-2);
			$valores = substr($valores,0,-2);
			$query .="(".$insert_campos.") VALUES (".$valores.")";
		}
		$resp = mysqli_query($conexao,$query);
		if ($cadastrar==1){
			$sobrenome = "";
			$candidato_id = $conexao -> insert_id;
			$temp = explode(" ",$_POST['responsavel']);
			$login = slugify($_POST['responsavel'],'',10);
			if (array_key_exists(1,$temp)){
				$sobrenome = trim(after($temp[0],$_POST['responsavel']));	
			}
			$query = "SELECT * FROM usuarios WHERE `email` LIKE '".$email."'";
			$resp = mysqli_query($conexao,$query);
			$usuario_id = 0;
			if (mysqli_num_rows($resp)>0){
				$row_usr = mysqli_fetch_assoc($resp);
				$usuario_id = (int)$row_usr['usuario_ID'];
				$query = "UPDATE `usuarios` SET `login`='".$login."',`telefone`='".$telefone."',`nome`='".$temp[0]."',`sobrenome`='".$sobrenome."' WHERE usuario_ID = " . $usuario_id;
			} else {
				$query = "INSERT INTO usuarios (`nome`, `login`, `senha`, `email`, `telefone`, `sobrenome`, `nivel`) VALUES ('".$temp[0]."','".$login."', NULL ,'".$email."','".$telefone."','".$sobrenome."',1)";
			}
			$resp = mysqli_query($conexao,$query);
			if ($usuario_id === 0) {
				$usuario_id = $conexao -> insert_id;
			}
			$query = "UPDATE candidatos SET `usuario_id`=".$usuario_id." WHERE candidato_id =".$candidato_id;
			$resp = mysqli_query($conexao,$query);

			// Insere o vínculo na tabela de relacionamento candidatos_usuarios
			$q_vinculo = "INSERT INTO candidatos_usuarios (candidato_id, usuario_id, is_responsavel_principal) VALUES ($candidato_id, $usuario_id, 1)";
			mysqli_query($conexao, $q_vinculo);
		}
		$tnpFile = $_FILES["Curriculo"]["tmp_name"];
		if (!empty($tnpFile)){
			$img_id = 0;
			$nomearquivo = "docs/". $_FILES['Curriculo']['name'] ;
			$dir = before("pages",__DIR__);
			copy ($_FILES['Curriculo']['tmp_name'], $dir . $nomearquivo) or die( "Não foi possível copiar o arquivo!" );
		}
		$query1 = "INSERT INTO `documentos` (`candidato_id`,`url`,`descritivo`) VALUES (".$candidato_id.",'".$nomearquivo."','Curriculo - ".$nome."')";
		$resp1 = mysqli_query($conexao,$query1);
		$doc_id = $conexao -> insert_id;
	
		$query1 = "UPDATE candidatos SET `anexo_id` = ".$doc_id." WHERE candidato_id =".$candidato_id;
		$resp1 = mysqli_query($conexao,$query1);

		ini_set( 'display_errors', 1 );
		error_reporting( E_ALL );
		
		require before('/public_html',__DIR__) . '/vendor/autoload.php';

		if (isset($_POST['Email'])){
			$email_post = strtolower($_POST['Email']);
		}
		
		$to = "pauloadd@novaeratec.com.br";
		$subject  = 'Nova Inscrição de Atendente: '.$nome;
		$body = '<h3>Nova Inscrição Cadastrada (Pendente de Aprovação)</h3>';
		$body .= ' Nome: <strong>'.$nome.'</strong><br>'; 
		$body .= ' Responsável: <strong>'.$_POST['responsavel'].'</strong><br>'; 
		foreach ($_POST as $campo => $valor) {
			if ($campo<>"nome" && $campo<>"Email"&& $campo<>"responsavel" && $campo<>"criar_evento"){
				$body .= ' '. $campo .': '.$valor."<br>";
			}
		}
		if (!empty($nomearquivo)) {
			$body .= " <a href='https://projetoame.org/".$nomearquivo."' target='_blank'>Ver Currículo Enviado</a><br>";
		}
		$body .= '<br><hr> DADOS DE AUDITORIA:<br>IP: '.$_SERVER['REMOTE_ADDR'].'<br>'; 
		$body .= ' Navegador: '.$_SERVER['HTTP_USER_AGENT'].'<br>'; 
		$body .= ' Enviado em: '. date('d/m/Y H:i').'<br>'; 
		
		// Botões de Ação Direta para o Administrador
		$body .= '<br><hr><h3>⚡ Ações Rápidas de Moderação:</h3>';
		$body .= '<p style="margin-top:10px;">';
		$body .= '<a href="https://projetoame.org/admin/ativarcandidato/'.$candidato_id.'" style="background:#28a745; color:#ffffff; padding:10px 18px; border-radius:6px; text-decoration:none; font-weight:bold; display:inline-block; margin-right:10px;">✅ Aprovar e Ativar Candidato</a> ';
		$body .= '<a href="https://projetoame.org/excluircandidato/'.$candidato_id.'" style="background:#dc3545; color:#ffffff; padding:10px 18px; border-radius:6px; text-decoration:none; font-weight:bold; display:inline-block;">❌ Excluir Registro (Spam/Bot)</a>';
		$body .= '</p>';

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

			$mail->setFrom('noreply@projetoame.org', 'Projeto AME - Inscrição');
			$mail->addAddress($to);
			if(isset($email_post)) $mail->addAddress($email_post);
			$mail->addCC('regina.rjr@hotmail.com');

			$mail->isHTML(true);
			$mail->Subject = $subject;
			$mail->Body    = $body;
			$mail->CharSet = 'UTF-8';

			$mail->send();
			$msg = "<p class='text-center'>Os dados de <strong><em>".$nome."</em></strong> foram enviados com sucesso e estão em análise pela coordenação!</p>";
		} catch (Exception $e) {
			$msg = "<p class='text-center'>Não conseguimos enviar sua mensagem! Erro: {$mail->ErrorInfo}</p>";
		}
	}
?>
	<?php include_once('./include/nav.php'); ?>
	<div class="container mt-5 pt-4">
		<header>
		<h1 class="text-center"><?php echo $msg?></h1>
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="/">Início</a></li>
				<li class="breadcrumb-item"><a href="/atendentes">Atendentes</a></li>
				<li class="breadcrumb-item active" aria-current="page"><?php echo $inscrito?></li>
			  </ol>
			</nav>
		</header>
	</div>
