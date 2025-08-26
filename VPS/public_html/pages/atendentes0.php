<?php
session_start();
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
include_once('./include/conexao.php');
include_once('./include/funcoes.php');
include_once('./include/head.php');
$body="";
$salvar = 0;
$queryeventos = "SELECT eventos_marcados.*,imagens.url
FROM eventos_marcados,imagens
WHERE eventos_marcados.imagem_id = imagens.imagem_id
AND final >= CURDATE() 
ORDER BY final ASC;";
$resp = mysqli_query($conexao,$queryeventos);
$msg ="";
$maxcampos=4;
$ncampos = 4;
$campos = array(
	"candidato_id"=>array("candidato_id",0,"ID:","Código do candidato",'hidden','','',"far fa-user",''),
	"usuario_id"=>array("usuario_id",0,"Usuário:","Código do usuário principal",'hidden','','',"far fa-user",''),
	"nome"=>array("nome",'',"Nome:","nome atendente",'text','','required',"far fa-user",''),
	"Email"=>array("Email",'',"E-mail:","forneça o e-mail",'email','','required',"far fa-envelope",''),
	"responsavel"=>array("responsavel",'',"Responsável:","Nome do responsável",'text','','',"far fa-user",''),
	"curatela"=>array("curatela",'',"Curatela:","forneça o tipo de curatela, se houver",'curatela','Escolha a opção (não informado);Curatela total;parcial;sem curatela','',"far fa-group",''),
	"genero"=>array("genero",'',"Gênero:","informe o gênero",'option','masculino;feminino;não informado','',"far fa-user",''),
	"Telefone"=>array("Telefone",'',"Celular:","telefone com DDD",'tel','','',"fa fa-phone",''),
	"WZ"=>array("WZ",0,"WhatsApp:","celular é WhatsApp?",'switch',';on','',"far fa-whatsapp",''),
	"telegram"=>array("telegram",0,"Telegram:","celular é Telegram?",'switch',';on','',"far fa-telegram",''),
	"Nascimento"=>array("Nascimento",'',"Data nascimento:","data de nascimento",'date','','',"far fa-calendar",''),
	"RG"=>array("RG",'',"RG:","RG do atendente",'text','','',"far fa-edit",''),
	"CPF"=>array("CPF",'',"CPF:","CPF do atendente",'text','','',"far fa-edit",''),
	"CPF_RESP"=>array("CPF_RESP",0,"CPF do responsável:","CPF do responsável",'text','','required',"far fa-edit",''),
	"PIX"=>array("PIX",'',"PIX:","chave PIX para pagamentos",'text','','',"fa fa-dollar-sign",''),
	"camisa"=>array("camisa",'',"Camisa:","tamanho da camisa",'text','','',"far fa-user",''),
	"calca"=>array("calca",'',"Calça:","tamanho da calça",'text','','',"far fa-user",''),
	"sapato"=>array("sapato",'',"Calçado:","número do calçado",'text','','',"far fa-user",''),
	"CEP"=>array("CEP",'',"CEP:","CEP do endereço residencial",'text','','',"far fa-map",''),
	"endereco"=>array("endereco",'',"Endereço:","forneça o endereço",'text','','',"far fa-map",''),
	"complemento"=>array("complemento",'',"Complemento:","número, casa, apartamento etc.",'text','','',"far fa-map",''),
	"cidade"=>array("cidade",'',"Cidade:","forneça a cidade",'number','','',"far fa-map",''),
	"UF"=>array("UF",'',"Estado:","estado",'number','','',"far fa-map",''),
	array("mensagem",'',"Mensagem:","",'textarea','','',"far fa-edit",''),
	"imagem_id"=>array("imagem_id",0,"Imagem:","código da imagem",'hidden','','',"far fa-image",''),
	"anexo"=>array("anexo",'',"Anexo:","último anexo enviado",'hidden','','',"far fa-file",''),
	"anexo_id"=>array("anexo_id",0,"ID do anexo:","identificação do anexo",'hidden','','',"far fa-file",''),
	"certificado"=>array("certificado",0,"Certificado:","é certificado?",'option','atendente certificado;em treinamento','',"far fa-file",'disabled'),
	"ativo"=>array("ativo",0,"Ativo","inscrição ativa",'option','cadastro ativo;inativo','',"far fa-user",'disabled'),
	"rodizio"=>array("rodizio",0,"Rodizio","ordem no rodizio",'number','','',"fa fa-users",'disabled'),
	"data_inscricao"=>array("data_inscricao",0,"Data:","data da inscrição",'hidden','','',"far fa-calendar",''),
	"boletim"=>array("boletim",0,"Boletim:","enviar boletim?",'switch','sim;não','',"far fa-map",''),
	"grupo_capacitacao"=>array("grupo_capacitacao",0,"Grupo Capacitação?","incluir no Grupo Capacitação?",'switch',';on','',"far fa-map",''),
	"grupo_voluntarios"=>array("grupo_voluntarios",0,"Grupo Voluntários?","incluir no Grupo Voluntários?",'switch',';on','',"far fa-map",''),
	"IP"=>array("IP",0,"IP","IP do usuário",'hidden','','','','')
	);
/*
echo "<pre>";
print_r($campos);
echo "</pre>";
exit;
*/
$valores = array(
	"candidato_id"=>0,
	"usuario_id"=>0,
	"inscrito"=>'',
	"nome"=>'',
	"responsavel"=>0,
	"curatela"=>0,
	"Email"=>'',
	"Telefone"=>'',
	"genero"=>'',
	"Nascimento"=>'',
	"RG"=>'',
	"CPF"=>'',
	"CPF_RESP"=>'',
	"PIX"=>'',
	"camisa"=>'',
	"calca"=>'',
	"sapato"=>'',
	"CEP"=>'',
	"endereco"=>'',
	"complemento"=>'',
	"cidade"=>'',
	"UF"=>'',
	"mensagem"=>'',
	"imagem_id"=>0,
	"anexo"=>'',
	"anexo_id"=>0,
	"certificado"=>0,
	"ativo"=>0,
	"rodizio"=>0,
	"data_inscricao"=>'',
	"WZ"=>0,
	"telegram"=>0,
	"boletim"=>0,
	"grupo_capacitacao"=>0,
	"grupo_voluntarios"=>0,
	"IP"=>$_SERVER["REMOTE_ADDR"]
	);
if ($_SERVER['REQUEST_METHOD']=="POST"){
	$ncampos = 12;
	$maxcampos = 36;
	$salvar++;
	echo "<pre>";
	print_r($_POST);
	echo "</pre>";
	if (isset($_POST['salvar'])){
		$salvar = $_POST['salvar'];
		$query = "SELECT candidatos.* FROM candidatos WHERE candidatos.Email LIKE '".$_POST['Email']."'";
	//	echo $query . "<br>";
		$r = mysqli_query($conexao,$query);
		$msg = "<h2 class='text-center'>Atendente não encontrado</h2><p class='text-center'><span class='mt-1 p-2 bg-danger rounded-pill text-white'>Por favor, verifique se digitou seu e-mail cadastrado corretamente e tente novamente</span></p>";
		$cadastrar = 1;
		If (mysqli_num_rows($r)>0){
			$salvar++;
			$cadastrar = 0;
			$row=mysqli_fetch_assoc($r);
			$msg = "<h2 class='text-center'>Atendente: ".iconv("UTF-8", "ISO-8859-1", $row['nome'])."</h2><p class='mt-1 p-2 text-center bg-success rounded-pill'>Disponibilidade registrada</p>";
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
			if ($salvar>0){
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
					if (is_numeric($campo)){
						$inserir = "INSERT INTO `disponibilidade`(`candidato_id`, `atividade_id`) VALUES (".$row['candidato_id']."," . intval($campo) . ")";
						$insere = mysqli_query($conexao,$inserir);
						$query = "SELECT eventos_marcados.*,horarios.data_inicio,horarios.data_final FROM eventos_marcados,horarios WHERE eventos_marcados.id = horarios.evento_id AND horario_id = ".intval($campo)." ORDER BY eventos_marcados.nome,horarios.data_inicio;";
						echo $query . "<br>";
						$resp = mysqli_query($conexao,$query);
						if(mysqli_num_rows($resp)>0){
							$ativ = mysqli_fetch_assoc($resp);
		//					$dados['resp'] = iconv("UTF-8", "ISO-8859-1//TRANSLIT",$_POST['resp']);
							if ($ev<>$ativ['nome']){
								$body .= '<strong>Evento: '.$ativ['nome'].'</strong><br>';
							}
							$body .= 'Dia: '. iconv( "ISO-8859-1","UTF-8",Date("d/M, D",strtotime($ativ['data_inicio']))) . " das " .Date("H:i, D",strtotime($ativ['data_inicio'])) ." às ". Date("H:i",strtotime($ativ['data_final'])) . '<br>';
						}
						echo "<pre>";
						print_r($ativ);
						echo "</pre>";
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
}
?>
	<style>
		.hidden {
			display: none;
		}
	</style>
<body>
	<div class="container">
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
		<form method="post">
		<input type="hidden" id = "salvar" name="salvar" value="<? echo $salvar?>">
			<div class="row mt-1">
				<div class='col-12'>
					<?
						echo $msg;
					?>
				</div>
			</div>
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
					<label class="custom-control-label" for="<? echo digitos($row0['horario_id'])?>"><? echo Date("d/M, D",strtotime($row0['data_inicio'])). " - ". $row0['tipo']?><br>
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
	</div><hr>
	<div class="row justify-content-center">
		<div class="col">
		<p class="text-center"><strong>Confirme seu nome e e-mail, por favor</strong></p>
<!--
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
-->
			<?
/*
			echo "<pre>";
			print_r($dados);
			echo "</pre>";
*/
			$j=0;
			echo "<div id='section1' class='p-2 border'>";
			foreach ($campos as $linha){
				switch ($linha[4]) {
					case "option":
						$itens = explode(";",$linha[5]);
						$i=0;
						$selected = "selected";
					?>
					<i class="far fa-map prefix grey-text"></i>
						  <label class="form-check-label" for="<? echo $linha[0] ?>"><? echo $linha[2] ?></label>
						<select class="form-select" name="<? echo $linha[0] ?>" id="<? echo $linha[0] ?>" aria-label="<? echo $linha[0] ?>" <? echo $linha[8] ?>>
							<?
							foreach($itens as $item){
							?>
						  <option <? echo $selected?> value="<? echo $i?>"><? echo $item?></option>
							<?
								$i++;
								$selected="";
							}
							?>
						</select>
					<hr>
					<?
					break;
					case "curatela":
						$itens = explode(";",$linha[5]);
						$i=0;
						$selected = "selected";
					?>
					<i class="far fa-map prefix grey-text"></i>
						  <label class="form-check-label" for="<? echo $linha[0] ?>"><? echo $linha[2] ?></label>
						<select class="form-select" name="<? echo $linha[0] ?>" id="<? echo $linha[0] ?>" aria-label="<? echo $linha[0] ?>" <? echo $linha[8] ?>>
							<?
							foreach($itens as $item){
							?>
						  <option <? echo $selected?> value="<? echo $i?>"><? echo $item?></option>
							<?
								$i++;
								$selected="";
							}
							?>
						</select>
					<hr>
					<?
					break;
					case 'hidden':
						?>
						  <input class="custom-control-input" type="hidden" id="<? echo $linha[0] ?>" name="<? echo $linha[0] ?>" value="<? echo $linha[1] ?>"> 
						<?
						break;
					case 'CTPS':
						echo $linha[0]."-"."Carteira de trabalho<br>";
						break;
					case 'switch':
							?>
						<div class="custom-control custom-switch">
<!--							<i class="far fa-map prefix grey-text"></i>-->
						  <input class="custom-control-input" type="checkbox" role="switch" id="<? echo $linha[0] ?>" name="<? echo $linha[0] ?>" 
								 <?
								if ($linha[1]==1){
									echo "value='on' ";
								}
								echo $linha[2];
								 ?> <? echo $linha[8] ?>>
						  <label class="custom-control-label" for="<? echo $linha[0] ?>"><? echo $linha[2] ?></label>
						</div>
							<?
						break;
					default:
//						if($valor[4]<>"hidden"){
						?>
							<div class="md-form">
								<i class="<? echo $linha[7] ?> prefix grey-text"></i>
								<input type="<? echo $linha[4] ?>" maxlength="64" class="form-control" id="<? echo $linha[0] ?>" name="<? echo $linha[0] ?>" placeholder="<? echo $linha[3] ?>" value="<? echo $linha[1] ?>" <? echo $linha[6] ?> <? echo $linha[8] ?>>
							  <label class="form-label" for="<? echo $linha[0] ?>"><? echo $linha[2] ?></label>
							</div>
						<?									
						}
//				}				
						$j++;
						if ($j==$ncampos){
							echo "</div><div id='section2' class='p-2 border hidden'>";
						}
						if ($j==$maxcampos){
							break;
						}
				}
			?>
		</div>
<!--
		<?
		if ($ncampos>4){
//			echo $ncampos."<br>";
		?>
-->
			<button class="btn btn-sm btn-pill btn-success rounded-pill" type="button" id="toggleButton">MAIS</button>
			<script>
//	 JavaScript Document
//			$(document).ready(function () {
				// Lógica JavaScript para exibir/ocultar a segunda divisão
				const toggleButton = document.getElementById('toggleButton');
				const section2 = document.getElementById('section2');
				const RG = document.getElementById('RG');
				const nome = document.getElementById('nome');

				toggleButton.addEventListener('click', () => {
					if (section2.classList.contains('hidden')) {
						section2.classList.remove('hidden');
						toggleButton.textContent = 'MENOS';
						RG.focus();
					} else {
						section2.classList.add('hidden');
						toggleButton.textContent = 'MAIS';
						nome.focus();
					}
				});
			</script>
		<?			
		}
		?>
<!--
				<div class="md-form">
					<i class="far fa-map prefix grey-text"></i>
					<input type="tel" id="tel" name="tel" class="form-control" placeholder="telefone cadastrado">
					<label for="tel">digite seu telefone</label>
				</div>
-->
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
