<?php
session_start();
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
//include_once('./include/conexao.php');
//include_once('./include/funcoes.php');
// include_once('./include/head.php');
$body="";
$queryeventos = "SELECT eventos_marcados.*,imagens.url
FROM eventos_marcados,imagens
WHERE eventos_marcados.imagem_id = imagens.imagem_id
AND inicio > CURDATE() 
ORDER BY inicio ASC;";
$resp = mysqli_query($conexao,$queryeventos);
$msg ="";
if ($_SERVER['REQUEST_METHOD']=="POST"){
	$query = "SELECT candidatos.* FROM candidatos,usuarios WHERE usuarios.email LIKE '".$_POST['email']."' AND candidatos.usuario_id = usuarios.usuario_id";
	$r = mysqli_query($conexao,$query);
	$msg = "<h2 class='text-center'>Atendente não encontrado</h2><p class='text-center'><span class='mt-1 p-2 bg-danger rounded-pill text-white'>Por favor, verifique se digitou seu e-mail cadastrado corretamente e tente novamente</span></p>";
?>
	<?php 
	If (mysqli_num_rows($r)>0){
		$row=mysqli_fetch_assoc($r);
		$msg = "<h2 class='text-center'>Atendente: ".$row['nome']."</h2><p class='mt-1 p-2 text-center bg-success rounded-pill'>Disponibilidade registrada</p>";
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
	//    $headers[] = 'Cc: <'. $email.'>,<regina.rjr@hotmail.com>';
	//    $headers[] = 'Cc: <regina.rjr@hotmail.com>';
	//    $headers[] = "From:" . $from;

	//    $headers = "From:" . $from;

		$subject  = 'Dados do atendente '. $nome; // Assunto da mensagem
		$body = ' <strong>Nome: '.$dados['nome'].'</strong><br>'; // Nomes do atendente
		$body .= ' <strong>Responsavel: '.$row['responsavel'].'</strong><br>'; // Nomes dos noivos
			$body .="<br>Disponibilidade:<hr>";
	    foreach ($_POST as $campo => $valor) {
        // Exibir o nome do campo e seu valor
			$ev = "";
			if ($campo<>"nome" && $campo<>"email"){
				$inserir = "INSERT INTO `disponibilidade`(`candidato_id`, `atividade_id`) VALUES (".$row['candidato_id']."," . intval($campo) . ")";
				$insere = mysqli_query($conexao,$inserir);
				$query = "SELECT eventos_marcados.*,horarios.data_inicio,horarios.data_final FROM eventos_marcados,horarios WHERE eventos_marcados.id = horarios.evento_id AND horario_id = ".intval($campo)." ORDER BY eventos_marcados.nome,horarios.data_inicio;";
//				echo $query . "<br>";
				$resp = mysqli_query($conexao,$query);
				if(mysqli_num_rows($resp)>0){
					$ativ = mysqli_fetch_assoc($resp);
//					$dados['resp'] = iconv("UTF-8", "ISO-8859-1//TRANSLIT",$_POST['resp']);
					if ($ev<>$ativ['nome']){
						$body .= '<strong>Evento: '.$ativ['nome'].'</strong><br>';
					}
					$body .= 'Dia: '. iconv( "ISO-8859-1","UTF-8",strftime("%a, %d/%b das %H",strtotime($ativ['data_inicio']))) ." às ". Date("H:i",strtotime($ativ['data_final'])) . '<br>';
				}
					$ev = $ativ['nome'];
			}
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
			<div class="row mt-1">
				<div class='col-12'>
					<?php 
						echo $msg . "<br>" . $body;
					?>
				</div>
			</div>
			<div class="row mt-1">
			<?php 
$resp = mysqli_query($conexao,$queryeventos);
			while ($row0 = mysqli_fetch_assoc($resp)){
			?>
		<div class="col col-md-6 align-items-stretch d-flex mb-2">
			<div class="card">
				<div class="card-header">
					<img class="card-img-top" src="<?php echo $row0['url']?>">
					<h1 class="text-center"><?php echo $row0['nome']?></h1>
				</div>
				<div class="card-body">
			<?php 
				$id = $row0['id'];
				$query1 = "SELECT * FROM horarios WHERE evento_id = ".$id;
				$horarios = mysqli_query($conexao,$query1);
				while ($row0 = mysqli_fetch_assoc($horarios)){
					$checked = "";
					if (isset($_POST[digitos($row0['horario_id'])])){
						$checked = " checked";
					}
					?>
					<div class="custom-control custom-switch">
					<input type="checkbox" class="custom-control-input" name="<?php echo digitos($row0['horario_id'])?>" id="<?php echo digitos($row0['horario_id'])?>" <?php echo $checked?>>
					<label class="custom-control-label" for="<?php echo digitos($row0['horario_id'])?>"><?php echo date("d-M-Y",strtotime($row0['data_inicio']))?><br>
						<?php 
						echo "das ".date("H:i",strtotime($row0['data_inicio'])). " às ".date("H:i",strtotime($row0['data_final']));
						$vagas = "";
						If (intval($row0['vagas'])>0){
							$vagas = " (".intval($row0['vagas'])." vagas)";
						}
						echo $vagas;
						?>
						</label>
					</div>
					<?php 
					
				}
			?>
			</div>
			</div>
		</div>
			<?php 
			}
			?>
	</div><hr>
			<div class="row justify-content-center">
				<div class="col">
				<p class="text-center"><strong>Confirme seu nome, e-mail e telefone cadastrados, por favor</strong></p>
				<div class="md-form">
					<i class="far fa-map prefix grey-text"></i>
					<input type="text" id="nome" name="nome" class="form-control" placeholder="nome do atendente"
						   <?php 
						   if(isset($_POST['nome'])){
							   echo " value='".$_POST['nome']."'";
						   }
						   ?>>
					<label for="nome">digite o nome do atendente</label>
				</div>
				<div class="md-form">
					<i class="far fa-map prefix grey-text"></i>
					<input type="email" id="email" name="email" class="form-control" placeholder="seu e-mail cadastrado"
						   <?php 
						   if(isset($_POST['email'])){
							   echo " value='".$_POST['email']."'";
						   }
						   ?>>
					<label for="email">digite seu e-mail</label>
				</div>
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
<?php 
include_once('./include/end.php');
?>
