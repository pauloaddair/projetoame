<?php
	session_start();
	setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');
	date_default_timezone_set('America/Sao_Paulo');
	include_once('./include/conexao.php');
	include_once('./include/funcoes.php');
	$titulo = "Editando candidato";
	include_once('./include/head.php');
	$candidato_id = 0;
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
	$salvar = 0;
	$body="";
	$nome = "";
	$responsavel = "";
	$email = "";
	$telefone = "";
	$grupo_capacita = 1;
	$grupo_voluntarios = 1;
	$boletim = 1;
	$queryeventos = "SELECT eventos_marcados.*,imagens.url
	FROM eventos_marcados,imagens
	WHERE eventos_marcados.imagem_id = imagens.imagem_id
	AND inicio > CURDATE() 
	ORDER BY inicio ASC;";
	$resp = mysqli_query($conexao,$queryeventos);
	$msg = "<h2 class='text-center'>Informe seu nome e e-mail cadastrados</h2><p class='text-center'></p>";
	if ($_SERVER['REQUEST_METHOD']=="POST"){
		if (isset($_POST['salvar'])){
			$salvar = intval($_POST['salvar']);	
			$salvar++;
		}
		if (isset($_POST['id']) && $_POST['id']>0){
			$candidato_id = $_POST['id'];
		}
		$query = "SELECT candidatos.* FROM candidatos,usuarios WHERE candidatos.Email LIKE '".$_POST['email']."'";
		echo $query . "<br>";
		$r = mysqli_query($conexao,$query);
		$msg = "<h2 class='text-center'>Atendente não encontrado</h2><p class='text-center'><span class='mt-1 p-2 bg-danger rounded-pill text-white'>Por favor, verifique se digitou seu e-mail cadastrado corretamente e tente novamente</span></p><p class='text-center bg-warning rounded-pill'>Caso queira se cadastrar, preencha o formulário abaixo</p>";
	?>
		<?
		If (mysqli_num_rows($r)>0){
			$row=mysqli_fetch_assoc($r);
			$candidato_id = $row['candidato_id'];
			$msg = "<h2 class='text-center'>Atendente: ".$row['nome']."</h2>";
			if ($salvar==1){
				$deletar ="DELETE disponibilidade FROM disponibilidade
				JOIN horarios ON disponibilidade.atividade_id = horarios.horario_id
				JOIN eventos_marcados ON horarios.evento_id = eventos_marcados.id
				WHERE eventos_marcados.inicio > CURDATE()
				AND disponibilidade.candidato_id =".$row['candidato_id'].";";
				$del = mysqli_query($conexao,$deletar);
				$nome = iconv("UTF-8", "ISO-8859-1//TRANSLIT",$_POST['nome']);
				$dados['nome']=$nome;
				ini_set( 'display_errors', 1 );
				error_reporting( E_ALL );
				require './include/autoload.php';
				$email = "pauloadd@hotmail.com";
				if (isset($_POST['email'])){
					$email = strtolower($_POST['email']);
					$dados['email'] = $email;
				}
				$from = "pauloadd@projetoame.org";
				$to = "pauloadd@gmail.com";
			//    $to = "pauloadd@gmail.com";
				$message = "";
				// To send HTML mail, the Content-type header must be set
				$headers[] = 'MIME-Version: 1.0';
				$headers[] = 'Content-type: text/html; charset=iso-8859-1';
				$headers[] = 'From:Dados de Atendente via Site<' . $from . ">";
				$headers[] = 'Content-Type: text/html; charset=iso-8859-1';
			//    $headers[] = 'Content-Transfer-Encoding: 7bit';
				$headers[] = 'Cc: <'. $_POST['email'].'>,<regina.rjr@hotmail.com>';
			//    $headers[] = 'Cc: <regina.rjr@hotmail.com>';
			//    $headers[] = "From:" . $from;

			//    $headers = "From:" . $from;

				$subject  = 'Dados do atendente '. $nome; // Assunto da mensagem
				$body = ' <strong>Nome: '.$dados['nome'].'</strong><br>'; // Nomes do atendente
				$body .= ' <strong>Responsavel: '.$row['responsavel'].'</strong><br>'; // Nomes dos noivos
					$body .="<br>Disponibilidade:<hr>";
				$i=0;
				foreach ($_POST as $campo => $valor) {
				// Exibir o nome do campo e seu valor
				$ev = "";
				if ($campo<>"nome" && $campo<>"email" && $salvar<>""){
					$inserir = "INSERT INTO `disponibilidade`(`candidato_id`, `atividade_id`) VALUES (".$row['candidato_id']."," . intval($campo) . ")";
					$insere = mysqli_query($conexao,$inserir);
					$msg .="<p class='mt-1 p-2 text-center bg-success rounded-pill'>Disponibilidade registrada</p>";
					$query = "SELECT eventos_marcados.*,horarios.data_inicio,horarios.data_final FROM eventos_marcados,horarios WHERE eventos_marcados.id = horarios.evento_id AND horario_id = ".intval($campo)." ORDER BY eventos_marcados.nome,horarios.data_inicio;";
					echo $query . "<br>";
					exit;
					$resp = mysqli_query($conexao,$query);
					if(mysqli_num_rows($resp)>0){
						$ativ = mysqli_fetch_assoc($resp);
	//					$dados['resp'] = iconv("UTF-8", "ISO-8859-1//TRANSLIT",$_POST['resp']);
						if ($ev<>$ativ['nome']){
							$body .= '<strong>Evento: '.$ativ['nome'].'</strong><br>';
						}
						$body .= 'Dia: '. iconv( "ISO-8859-1","UTF-8",Date("d/M, D",strtotime($ativ['data_inicio']))) . " das " .Date("H:i, D",strtotime($ativ['data_inicio'])) ." às ". Date("H:i",strtotime($ativ['data_final'])) . '<br>';
					}
						$ev = $ativ['nome'];
				}
				$i++;
			}	
			if ($i==0){
				$body .= "Nenhuma disponibilidade informada!<br>";
			}
		$body .= '<br><hr> DADOS DE AUDITORIA:<br>IP: '.$_SERVER['SERVER_ADDR'].'<br>'; // IP do visitante
		$body .= ' Navegador: '.$_SERVER['HTTP_USER_AGENT'].'<br>'; // IP do visitante
		$body .= ' Enviado em: '. date('d/m/Y H:i').'<br>'; // Texto da mensagem
			$message = $body;
		$dados['server'] = $_SERVER['SERVER_ADDR'];
		$dados['agent'] = $_SERVER['HTTP_USER_AGENT'];
		$dados['data_atual'] = date('d/m/Y H:i');
		if (mail($to,$subject,$message, implode("\r\n", $headers))) {
	//    if (mail($to,$subject,$message, $headers)) {
		   $msg = "<p class='text-center'>Os dados de <strong><em>".$nome."</em></strong> foram enviados com sucesso!</p>"; // or use booleans here
		} else {
			$msg = "<p class='text-center'>Não conseguimos enviar sua mensagem!  Tente novamente mais tarde.</p>";;
		}
			}
		}
	}
?>
<body>
	<div class="container">
		<header>
		<h1 class="text-center">Atividades confirmadas</h1>
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="/atendentes">Atividades</a></li>
				<li class="breadcrumb-item"><a href="/convertefone">Atendentes</a></li>
				<li class="breadcrumb-item active" aria-current="page">Atividades confirmadas</li>
			  </ol>
			</nav>
		</header>
		<form method="post">
			<input type="text" id="salvar" name="salvar" value="<? echo $salvar?>" disabled>
			<input type="hidden" id="id" name="id" value="<? echo $candidato_id?>">
			<div class="row mt-1">
				<div class='col-12'>
					<?
						echo $msg;
					?>
				</div>
			</div>
			<?
				if ($salvar>0){
			?>
			<div class="row mt-1">
			<?
			$resp = mysqli_query($conexao,$queryeventos);
			while ($row0 = mysqli_fetch_assoc($resp)){
			?>
		<div class="col col-md-6 align-items-stretch d-flex mb-2">
			<div class="card">
				<div class="card-header">
					<img class="card-img-top" src="/<? echo $row0['url']?>">
					<h1 class="text-center"><? echo $row0['nome']?></h1>
					<p><a href="<? echo $row0['maps']?>" target="_blank"><i class="far fa-map grey-text mr-1"></i><strong><? echo $row0['local']?></strong></a>&nbsp;<? echo $row0['endereco']?></p>
					<?
					if ($row0['obs']<>""){
						echo "<p><small><strong class='bg-warning p-1 rounded-pill'>Obs.:</strong> ".$row0['obs']."</small></p>";
					}
					?>
				</div>
				<div class="card-body">
			<?
				$id = $row0['id'];
				$query1 = "SELECT * FROM horarios WHERE evento_id = ".$id. " ORDER BY data_inicio;";
				$horarios = mysqli_query($conexao,$query1);
				while ($row0 = mysqli_fetch_assoc($horarios)){
					$checked = "";
					if (isset($_POST[digitos($row0['horario_id'])])){
						$checked = " checked";
					}
					?>
					<div class="custom-control custom-switch">
					<input type="checkbox" class="custom-control-input" name="<? echo digitos($row0['horario_id'])?>" id="<? echo digitos($row0['horario_id'])?>" <? echo $checked?>>
					<label class="custom-control-label" for="<? echo digitos($row0['horario_id'])?>"><? echo date("d-M-Y",strtotime($row0['data_inicio'])). " - ". $row0['tipo']?><br>
						<? 
						echo "das ".date("H:i",strtotime($row0['data_inicio'])). " às ".date("H:i",strtotime($row0['data_final']));
						$vagas = "";
						If (intval($row0['vagas'])>0){
							$vagas = " (".intval($row0['vagas'])." vagas)";
						}
						echo $vagas;
						?>
						</label>
					</div>
					<?
					
				}
			?>
			</div>
			</div>
			</div>
			<?
				}
				?>
			</div>
				<?					
				}
			?>
			<div class="row justify-content-center">
				<div class="col">
				<p class="text-center"><strong>Confirme seu nome e e-mail, por favor</strong></p>
				<div class="md-form">
					<i class="far fa-map prefix grey-text"></i>
					<input type="text" id="nome" name="nome" class="form-control" placeholder="nome do atendente"
						   <? 
						   if(isset($_POST['nome'])){
							   echo " value='".$_POST['nome']."'";
						   }
						   ?>>
					<label for="nome">digite o nome do atendente</label>
				</div>
					<?
					if ($candidato_id>0){
					?>
						<div class="md-form">
							<i class="<? echo $dados[$i][4]?> prefix grey-text"></i>
							<input type="<? echo $dados[$i][3]?>" id="<? echo $dados[$i][0]?>" name="<? echo $dados[$i][0]?>" class="form-control" placeholder="<? echo $dados[$i][2]?>">
							<label for="nome"><? echo $dados[$i][1]?></label>
						</div>
					<?
					}
					?>
					?>
				<div class="md-form">
					<i class="far fa-map prefix grey-text"></i>
					<input type="email" id="email" name="email" class="form-control" placeholder="seu e-mail cadastrado"
						   <? 
						   if(isset($_POST['email'])){
							   echo " value='".$_POST['email']."'";
						   }
						   ?>>
					<label for="email">digite seu e-mail</label>
				</div>
					<?
					echo "Candidado ID = ".$candidato_id."<br>";
					if($salvar<>0 && $candidato_id ==0){
					for ($i=2;$i<count($dados);$i++){
						if ($dados[$i][3]=="switch"){
					?>
							<!-- Default switch -->
							<div class="custom-control custom-switch">
								<input type="checkbox" class="custom-control-input" name="<? echo $dados[$i][0]?>" id="<? echo $dados[$i][0]?>" checked>
								<label class="custom-control-label" for="<? echo $dados[$i][0]?>"><? echo $dados[$i][1]?></label>
							</div>
					<?
						} else {
					?>
							<div class="md-form">
								<i class="<? echo $dados[$i][4]?> prefix grey-text"></i>
								<input type="<? echo $dados[$i][3]?>" id="<? echo $dados[$i][0]?>" name="<? echo $dados[$i][0]?>" class="form-control" placeholder="<? echo $dados[$i][2]?>">
								<label for="nome"><? echo $dados[$i][1]?></label>
							</div>
					<?
							
						}
					}
					}
					?>
				<div class="md-form">
					<button class="btn btn-sm btn-block rounded-pill btn-primary" type="submit">Enviar</button>
				</div>
				</div>
			</div>
			</form>
	</div>
<?php
include_once('./include/footer.php');
?>
</body>
<?php
include_once('./include/scripts.php');
?>
<?
include_once('./include/end.php');
?>
