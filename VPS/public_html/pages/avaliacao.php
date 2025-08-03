<?php
//	session_start();
	setlocale (LC_ALL, 'pt_BR.utf-8');
    date_default_timezone_set('America/Sao_Paulo');
	include_once('./include/head.php');
	include_once('./include/conexao.php');
	include_once('./include/funcoes.php');

	if(empty($_GET['ref'])) {
		$ref = "./";
	} else {
	  $ref = $_GET['ref'];
	}
if ($_SERVER['REQUEST_METHOD']=="POST"){
	$nome = iconv("UTF-8","ISO-8859-1",$_POST['nome']);
//require_once("../atendentes/include/autoload.php");
	// Inicia a classe PHPMailer
//	$mail = new PHPMailer();
    ini_set( 'display_errors', 1 );
    error_reporting( E_ALL );
/*
    $from = "mauro@rapaper.com.br";
    $to = "pauloadd@gmail.com";
    $subject = "Checking PHP mail";
    $message = "PHP mail works just fine";
    $headers = "From:" . $from;
    mail($to,$subject,$message, $headers);
*/

//use PHPMailer\PHPMailer\PHPMailer;
require './include/autoload.php';
	$email = "pauloadd@hotmail.com";
	if (isset($_POST['email'])){
		$email = $_POST['email'];
	}
	$arquivo = "arquivo.html";
    $from = "pauloadd@projetoame.org";
    $to = "pauloadd@gmail.com";
//    $to = "pauloadd@gmail.com";
    $message = "";
	// To send HTML mail, the Content-type header must be set
	$headers[] = 'MIME-Version: 1.0';
	$headers[] = 'Content-type: text/html; charset=iso-8859-1';
	$headers[] = 'From:Site projeto AME<' . $from . ">";
	$headers[] = 'Content-Type: text/html; charset=iso-8859-1';
//    $headers[] = 'Content-Transfer-Encoding: 7bit';
//    $headers[] = 'Cc: <'. $email.'>';
//    $headers[] = 'Cc: pauloadd@hotmail.com';
//    $headers = "From:" . $from;
 
//    $headers = "From:" . $from;

	$subject  = iconv("UTF-8","ISO-8859-1",'Avaliação do Atendente: '). $nome; // Assunto da mensagem
	if (isset($_POST['nome'])){
		$arquivo = slugify($nome).".html";
		$dados['nome'] = $_POST['nome'];
		$body = '<hr><h2>'.$_POST['nome'].'</h2>';
		$body .= '<h4>Nome: '.$nome.'</h4><hr><strong>AVALIAÇÃO:</strong><br>'; // Nomes dos noivos
		$body .= ' Dia: '.$_POST['data'].'</h4><hr>';
	}

	if (isset($_POST['pontualidade'])){
		$body .= '  Pontualidade: '. $_POST['pontualidade'].'<br>';
	}

	if (isset($_POST['asseio'])){
		$body .= ' Asseio: '. $_POST['asseio'].'<br>';
	}

	if (isset($_POST['socializacao'])){
		$body .= ' Socialização: '. $_POST['socializacao'].'<br>';
	}

	if (isset($_POST['simpatia'])){
		$body .= ' Simpatia: '. $_POST['simpatia'].'<br>';
	}

	if (isset($_POST['compreencao'])){
		$body .= ' Compreensão da instruções: '. $_POST['compreencao'].'<br>';
	}

	if (isset($_POST['facilidade'])){
		$body .= ' Facilidade em cumprir orientações: '. $_POST['facilidade'].'<br>';
	}

	if (isset($_POST['foco'])){
		$body .= ' Foco nas atividades: '. $_POST['foco'].'<br>';
	}

	if (isset($_POST['comportamento'])){
		$body .= ' Comportamento geral: '. $_POST['comportamento'].'<br>';
	}

	if (isset($_POST['obs'])){
		$body .= ' Observações/ocorrências: '. iconv("UTF-8","ISO-8859-1",$_POST['obs']).'<br><hr>';
	}

	if (isset($_POST['avaliador'])){
		$body .= ' Avaliador(a): '. iconv("UTF-8","ISO-8859-1",$_POST['avaliador']).'<br>';
	}

	if (isset($_POST['avaliadoremail'])){
		$body .= ' E-mail do avaliador: '. $_POST['avaliadoremail'].'<br>';
	}

	$body .= '<br><hr> DADOS DE AUDITORIA:<br>IP: '.$_SERVER['SERVER_ADDR'].'<br>'; // IP do visitante

	$body .= ' Navegador: '.$_SERVER['HTTP_USER_AGENT'].'<br>'; // IP do visitante
	$body .= ' Enviado em: '. date('d/m/Y H:i').'<br>'; // Texto da mensagem
	$dados['server'] = $_SERVER['SERVER_ADDR'];
	$dados['agent'] = $_SERVER['HTTP_USER_AGENT'];
	$dados['data_atual'] = date('d/m/Y H:i');

	$message = $body;
	$mensagem="";
	if (mail($to,$subject,$message, implode("\r\n", $headers))) {
//    if (mail($to,$subject,$message, $headers)) {
       $mensagem = "Os dados da avaliação de <strong><em>".iconv("ISO-8859-1","UTF-8",$nome)."</em></strong> foram enviados com sucesso!"; // or use booleans here
    } else {
        $mensagem = "Não conseguimos enviar sua mensagem!  Tente novamente mais tarde.";;
    }

}

//	include('pages/head.php');
$evento='';
if (array_key_exists(1,$parametros) && intval($parametros[1])>0){
	$evento = " AND evento_id = ".intval($parametros[1])." ";
}
$convite = "AVALIAÇÃO DE ATENDENTE";
//$mensagem = "";
$dados[]="";
$query = "SELECT 
    c.candidato_id, 
    c.rodizio, 
    c.ativo, 
    c.nome, 
	h.data_inicio,
	h.data_final,
    i.url, 
    i2.url AS evento_img, 
    em.nome AS evento_nome,
	em.id AS evento_id
FROM candidatos c
LEFT JOIN imagens i ON c.imagem_id = i.imagem_id
LEFT JOIN disponibilidade d ON d.candidato_id = c.candidato_id
LEFT JOIN horarios h ON d.atividade_id = h.horario_id
LEFT JOIN eventos_marcados em ON h.evento_id = em.id
LEFT JOIN imagens i2 ON em.imagem_id = i2.imagem_id
WHERE d.escalado = 1 ".$evento." 
ORDER BY h.data_inicio,c.rodizio;";
	?>
<style>
	.bg-rodizio{
		background-color:antiquewhite;
	}
	.bg-treinamento{
		background-color:aquamarine;
	}
</style>
    <style>
        .image-container-48 {
            width: 100%; /* Ajusta para a largura do contêiner pai */
            max-width: 48px;  Define um tamanho máximo 
            aspect-ratio: 3 / 4; /* Mantém a proporção desejada */
            overflow: hidden; /* Garante que partes excedentes sejam cortadas */
/*            border: 2px solid #333;*/
        }

        .image-container-48 img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Garante que a imagem preencha o contêiner */
            object-position: top; /* Centraliza a imagem */
        }
        .image-container {
            width: 100%; /* Ajusta para a largura do contêiner pai */
/*            max-width: 400px;  Define um tamanho máximo */
            aspect-ratio: 3 / 4; /* Mantém a proporção desejada */
            overflow: hidden; /* Garante que partes excedentes sejam cortadas */
            border: 2px solid #333;
        }

        .image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Garante que a imagem preencha o contêiner */
            object-position: top; /* Centraliza a imagem */
        }
    </style>
<body>
<div class="container p-1">
	<header class="mt-5 p-2 justify-content-md-center">
<!--<img src="img/logo-brown.png" class="img-thumbnail">-->
<h1 class="mt-5 text-center">ATENDENTES MUITO ESPECIAIS</h1>
		<?php
		if (array_key_exists(1,$parametros)){
		?>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="/avaliacao/">Atividades</a></li>
	<li class="breadcrumb-item"><a href="/avaliacao/<?php echo (digitos($parametros[1]) !== "000") ? digitos($parametros[1]) : ''?>">Evento</a></li>
	<li class="breadcrumb-item active" aria-current="page">Avaliação</li>
  </ol>
</nav>		
		<?php }
		?>
	</header>
<main class="container">
	<div class="row">
		<div class="col-12">
			<?php if (array_key_exists(2,$parametros) && intval($parametros[2])>0){
	$c_id = intval($parametros[2]);
	$e_id = intval($parametros[1]);
	$query = "SELECT 
		c.candidato_id, 
		c.rodizio, 
		c.ativo, 
		c.nome, 
		h.data_inicio,
		h.data_final,
		i.url, 
		i2.url AS evento_img, 
		em.nome AS evento_nome,
		em.local,
		em.id AS evento_id
		FROM candidatos c
		LEFT JOIN imagens i ON c.imagem_id = i.imagem_id
		LEFT JOIN disponibilidade d ON d.candidato_id = c.candidato_id
		LEFT JOIN horarios h ON d.atividade_id = h.horario_id
		LEFT JOIN eventos_marcados em ON h.evento_id = em.id
		LEFT JOIN imagens i2 ON em.imagem_id = i2.imagem_id
		WHERE em.id = ".$e_id." 
		AND c.candidato_id = ".$c_id. " 
		ORDER BY h.data_inicio,c.rodizio;";
	$resp = mysqli_query($conexao,$query);
	$row = mysqli_fetch_array($resp);
			?>
<form class="border border-light p-2" method="post">
	<input type="hidden" id="nome" name="nome" value="<?php echo $row['nome']?>">
	<input type="hidden" id="data" name="data" value="<?php echo $row['data_inicio']?>">
	<input type="hidden" id="evento" name="evento" value="<?php echo $row['evento_nome']?>">
	<div class="row">
		<div class="col-12">
			<div class="card mb-2 p-1">
				<div class="card-header"><h1>FICHA DE AVALIAÇÃO:</h1>
					<h2>LOCAL:<?php echo $row['evento_nome']?><br><small><?php echo $row['local']?></small></h2><br>
				<small>por favor, responda de acordo com suas observações sobre o atendente abaixo</small></h1>
			</div>
		<?php if($mensagem<>""){
		echo "<div class='alert'><h2 class='text-center'>". $mensagem . "</h2></div>";
		}
			?>
		</div>
		</div>
		<div class="col-12">
			<div class="card mb-2 p-1">
				<div class="card-header"><div class="image-container"><img class="card-img-top" src="/<?php echo $row['url']?>" alt="Card image cap"></div><h1><?php echo $row['nome']?></div>
				<div class="card-body">
<ul class="list-group list-group-flush">
    <li class="list-group-item">
		<div class="custom-control custom-switch">
			<strong>PONTUALIDADE</strong><br>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="pontualidade" id="pontualidade" value="1">
				<label class="form-check-label" for="pontualidade"><img src="/img/aval_1.svg" class="img" width="32"></label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="pontualidade" id="pontualidade" value="2">
				<label class="form-check-label" for="pontualidade"><img src="/img/aval_2.svg" class="img" width="32"></label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="pontualidade" id="pontualidade" value="3">
				<label class="form-check-label" for="pontualidade"><img src="/img/aval_3.svg" class="img" width="32"></label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="pontualidade" id="pontualidade" value="4">
				<label class="form-check-label" for="pontualidade"><img src="/img/aval_4.svg" class="img" width="32"></label>
			</div>
				<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="pontualidade" id="pontualidade" value="5">
				<label class="form-check-label" for="pontualidade"><img src="/img/aval_5.svg" class="img" width="32"></label>
			</div>
		</div>
	</li>
    <li class="list-group-item">
		<div class="custom-control custom-switch">
			<strong>ASSEIO</strong><br>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="asseio" id="asseio" value="1">
				<label class="form-check-label" for="asseio"><img src="/img/aval_1.svg" class="img" width="32"></label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="asseio" id="asseio" value="2">
				<label class="form-check-label" for="asseio"><img src="/img/aval_2.svg" class="img" width="32"></label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="asseio" id="asseio" value="3">
				<label class="form-check-label" for="asseio"><img src="/img/aval_3.svg" class="img" width="32"></label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="asseio" id="asseio" value="4">
				<label class="form-check-label" for="asseio"><img src="/img/aval_4.svg" class="img" width="32"></label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="asseio" id="asseio" value="5">
				<label class="form-check-label" for="asseio"><img src="/img/aval_5.svg" class="img" width="32"></label>
			</div>
		</div>
	</li>
   <li class="list-group-item">
		<div class="custom-control custom-switch">
			<strong>SOCIALIZAÇÃO</strong><br>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="socializacao" id="socializacao" value="1">
				<label class="form-check-label" for="socializacao"><img src="/img/aval_1.svg" class="img" width="32"></label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="socializacao" id="socializacao" value="2">
				<label class="form-check-label" for="socializacao"><img src="/img/aval_2.svg" class="img" width="32"></label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="socializacao" id="socializacao" value="3">
				<label class="form-check-label" for="socializacao"><img src="/img/aval_3.svg" class="img" width="32"></label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="socializacao" id="socializacao" value="4">
				<label class="form-check-label" for="socializacao"><img src="/img/aval_4.svg" class="img" width="32"></label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="socializacao" id="socializacao" value="5">
				<label class="form-check-label" for="socializacao"><img src="/img/aval_5.svg" class="img" width="32"></label>
			</div>
		</div>
	  </li>
	  <li class="list-group-item">
		<div class="custom-control custom-switch">
			<strong>SIMPATIA</strong><br>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="simpatia" id="simpatia" value="1">
				<label class="form-check-label" for="simpatia"><img src="/img/aval_1.svg" class="img" width="32"></label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="simpatia" id="simpatia" value="2">
				<label class="form-check-label" for="simpatia"><img src="/img/aval_2.svg" class="img" width="32"></label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="simpatia" id="simpatia" value="3">
				<label class="form-check-label" for="simpatia"><img src="/img/aval_3.svg" class="img" width="32"></label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="simpatia" id="simpatia" value="4">
				<label class="form-check-label" for="simpatia"><img src="/img/aval_4.svg" class="img" width="32"></label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="simpatia" id="simpatia" value="5">
				<label class="form-check-label" for="simpatia"><img src="/img/aval_5.svg" class="img" width="32"></label>
			</div>
		</div>
	  </li>
	  <li class="list-group-item">
		<div class="custom-control custom-switch">
			<strong>COMPREENSÃO DAS INSTRUÇÕES</strong><br>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="compreencao" id="compreencao" value="1">
				<label class="form-check-label" for="compreencao"><img src="/img/aval_1.svg" class="img" width="32"></label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="compreencao" id="compreencao" value="2">
				<label class="form-check-label" for="compreencao"><img src="/img/aval_2.svg" class="img" width="32"></label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="compreencao" id="compreencao" value="3">
				<label class="form-check-label" for="compreencao"><img src="/img/aval_3.svg" class="img" width="32"></label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="compreencao" id="compreencao" value="4">
				<label class="form-check-label" for="compreencao"><img src="/img/aval_4.svg" class="img" width="32"></label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="compreencao" id="compreencao" value="5">
				<label class="form-check-label" for="compreencao"><img src="/img/aval_5.svg" class="img" width="32"></label>
			</div>
		</div>
	  </li>
	  <li class="list-group-item">
		<div class="custom-control custom-switch">
			<strong>FACILIDADE EM CUMPRIR ORIENTAÇÕES</strong><br>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="facilidade" id="facilidade" value="1">
				<label class="form-check-label" for="facilidade"><img src="/img/aval_1.svg" class="img" width="32"></label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="facilidade" id="facilidade" value="2">
				<label class="form-check-label" for="facilidade"><img src="/img/aval_2.svg" class="img" width="32"></label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="facilidade" id="facilidade" value="3">
				<label class="form-check-label" for="facilidade"><img src="/img/aval_3.svg" class="img" width="32"></label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="facilidade" id="facilidade" value="4">
				<label class="form-check-label" for="facilidade"><img src="/img/aval_4.svg" class="img" width="32"></label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="facilidade" id="facilidade" value="5">
				<label class="form-check-label" for="facilidade"><img src="/img/aval_5.svg" class="img" width="32"></label>
			</div>
		</div>
	  </li>
	  <li class="list-group-item">
		<div class="custom-control custom-switch">
			<strong>FOCO NAS ATIVIDADES</strong><br>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="foco" id="foco" value="1">
				<label class="form-check-label" for="foco"><img src="/img/aval_1.svg" class="img" width="32"></label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="foco" id="foco" value="2">
				<label class="form-check-label" for="foco"><img src="/img/aval_2.svg" class="img" width="32"></label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="foco" id="foco" value="3">
				<label class="form-check-label" for="foco"><img src="/img/aval_3.svg" class="img" width="32"></label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="foco" id="foco" value="4">
				<label class="form-check-label" for="foco"><img src="/img/aval_4.svg" class="img" width="32"></label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="foco" id="foco" value="5">
				<label class="form-check-label" for="foco"><img src="/img/aval_5.svg" class="img" width="32"></label>
			</div>
		</div>
	  </li>
	  <li class="list-group-item">
		<div class="custom-control custom-switch">
			<strong>COMPORTAMENTO GERAL</strong><br>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="comportamento" id="comportamento" value="1">
				<label class="form-check-label" for="comportamento"><img src="/img/aval_1.svg" class="img" width="32"></label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="comportamento" id="comportamento" value="2">
				<label class="form-check-label" for="comportamento"><img src="/img/aval_2.svg" class="img" width="32"></label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="comportamento" id="comportamento" value="3">
				<label class="form-check-label" for="comportamento"><img src="/img/aval_3.svg" class="img" width="32"></label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="comportamento" id="comportamento" value="4">
				<label class="form-check-label" for="comportamento"><img src="/img/aval_4.svg" class="img" width="32"></label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="comportamento" id="comportamento" value="5">
				<label class="form-check-label" for="comportamento"><img src="/img/aval_5.svg" class="img" width="32"></label>
			</div>
		</div>
	  </li>
	  <li class="list-group-item">
		<div class="custom-control custom-switch">
			<strong>Observações finais/ocorrências</strong><br>
		<div class="md-form">
			<i class="far fa-comment prefix grey-text"></i>
			<input type="textarea" id="obs" name="obs" class="form-control" placeholder="escreva aqui seus comentários ou ocorrências relevante com o atendente">
<!--
			<label for="obs">descreva aqui suas observações finais ou ocorrências com o atendente</label>
-->
		</div>
		</div>
	</li>
 	  <li class="list-group-item">
		  <h3>AVALIADOR(A)</h3>
		<div class="md-form">
			<i class="fas fa-user prefix grey-text"></i>
			<input type="text" id="avaliador" name="avaliador" class="form-control" value="" required>
			<label for="avaliador">Nome do avaliador(a)</label>
		</div>
		<div class="md-form">
			<i class="fas fa-envelope prefix grey-text"></i>
			<input type="text" id="avaliadoremail" name="avaliadoremail" class="form-control" value="" required>
			<label for="avaliadoremail">E-mail</label>
		</div>
	  </li>
  </ul>
			</div>
	<hr>
    <button class="btn bg-info rounded-pill m-3 p-1" type="submit">Enviar</button>
		</div>
		</div>
	</div>
	</form>
			<?php } else {
	?>
		<div class="col-12">
			<div class="card mb-2 p-1">
		<div class="card-header"><h1>AVALIAÇÕES</h1><br>
				<small>por favor, escolha o atendente a avaliar abaixo</small></h1></div>
			<div class="card-body">

				<table class="table">
			<thead>
				<th>Evento</th>
				<th>Dia</th>
				<th>Atendente</th>
			</thead>
			<tbody>
				<?php $resp = mysqli_query($conexao,$query);
				while ($row = mysqli_fetch_array($resp)){
					$bg = "bg-treinamento";
					if ($row['ativo']=='1'){
						$bg = "bg-rodizio";
					}
					?>
				<tr class="<?php echo $bg?>">
				<td><a href="/avaliacao/<?php echo digitos($row['evento_id'])?>"><img src="/<?php echo $row['evento_img']?>" class="img-fluid rounded m-1" width=48><br><strong><?php echo $row['evento_nome']?></strong></a></td>
				<td><?php echo $row['data_inicio']?></td>
				<td><a href="/avaliacao/<?php echo digitos($row['evento_id'])?>/<?php echo digitos($row['candidato_id'])?>"><div class="image-container-48"><img src="/<?php echo $row['url']?>" class="img-thumbnail rounded-2 m-1"></div><br><strong><?php echo $row['nome']?></strong></a></td>				
				</tr>
					<?php }
				?>
			</tbody>
			</table>
			</div>
		</div>
		</div>
	<?php }
			?>
		</div>
			<hr>
		</div>
	</div>
 <!-- JQuery -->
  <script type="text/javascript" src="../atendentes/js/jquery-3.4.1.min.js"></script>
  <!-- Bootstrap core JavaScript -->
  <script type="text/javascript" src="../atendentes/js/bootstrap.min.js"></script>
  <!-- MDB core JavaScript -->
  <script type="text/javascript" src="../atendentes/js/mdb.min.js"></script>
  <!-- Bootstrap tooltips -->
  <script type="text/javascript" src="../atendentes/js/popper.min.js"></script>
  <!-- SCRIPTS -->
  <script src="https://kit.fontawesome.com/d067a28273.js" crossorigin="18592B3C-6385-48D7-8605-5E63E600000B"></script>
    <!-- Adicionando Javascript -->
    <script>

        $(document).ready(function() {

            function limpa_formulário_cep() {
                // Limpa valores do formulário de cep.
                $("#endereco").val("");
//                $("#bairro").val("");
                $("#complemento").val("");
                $("#cidade").val("");
                $("#uf").val("");
//                $("#ibge").val("");
            }
            
			//Quando o campo cep perde o foco.
            $("#cep").blur(function() {

                //Nova variável "cep" somente com dígitos.
                var cep = $(this).val().replace(/\D/g, '');

                //Verifica se campo cep possui valor informado.
                if (cep != "") {

                    //Expressão regular para validar o CEP.
                    var validacep = /^[0-9]{8}$/;

                    //Valida o formato do CEP.
                    if(validacep.test(cep)) {

                        //Preenche os campos com "..." enquanto consulta webservice.
                        $("#endereco").val("...");
//                        $("#bairro").val("...");
                        $("#cidade").val("...");
                        $("#uf").val("...");
//                        $("#ibge").val("...");

                        //Consulta o webservice viacep.com.br/
                        $.getJSON("https://viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {

                            if (!("erro" in dados)) {
                                //Atualiza os campos com os valores da consulta.
                                $("#endereco").val(dados.logradouro);
//                                $("#bairro").val(dados.bairro);
                                $("#cidade").val(dados.localidade);
                                $("#uf").val(dados.uf);
                                $("#complemento").focus();
//                                $("#ibge").val(dados.ibge);
                            } //end if.
                            else {
                                //CEP pesquisado não foi encontrado.
                                limpa_formulário_cep();
                                alert("CEP não encontrado.");
                            }
                        });
                    } //end if.
                    else {
                        //cep é inválido.
                        limpa_formulário_cep();
                        alert("Formato de CEP inválido.");
                    }
                } //end if.
                else {
                    //cep sem valor, limpa formulário.
                    limpa_formulário_cep();
                }
            });
			
		});
		
    </script>
</body>

</html>