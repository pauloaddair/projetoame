<?php
setlocale(LC_ALL, 'pt_BR');
// include_once('./include/conexao.php');
include_once('./include/funcoes.php');
include_once('./include/funcoes-fpdf.php');
$titulo = "Edição de candidato";
// include_once('./include/head.php');
$id = 0;
$perfil = "img/profile.png";
$candidato = "";
$cpf = "";
$resp ="";
$cpfresp = "";
$atendente = "";
$rg = "";
$email = "";
$cidade = "";
$estado = "";
if (array_key_exists(1,$parametros)){
	$id = intval($parametros[1]);
}
if($_SERVER['REQUEST_METHOD']=="POST"){
	$arquivo = "./docs/direito-de-imagem.pdf";
	if(isset($_POST['atendente'])){
		$atendente = $_POST['atendente'];		
		$arquivo = "./docs/direito-de-imagem-de-".slugify($atendente).".pdf";
	}
	if(isset($_POST['email'])){
		$email = $_POST['email'];
		$query = "SELECT candidato_id,nome,imagens.url as url 
		FROM candidatos
		LEFT JOIN imagens ON candidatos.imagem_id = imagens.imagem_id
        WHERE candidatos.email LIKE '".$email."'";
		$resp = mysqli_query($conexao,$query);
		if ($resp){
			$row = mysqli_fetch_assoc($resp);
			$candidato_id = $row['candidato_id'];
//			$atendente = $row['nome'];
			$perfil = $row['url'];
			$querydoc = "INSERT INTO `documentos`(`candidato_id`, `url`) VALUES ('".$candidato_id."','".$arquivo."')";
			$r = mysqli_query($conexao,$querydoc);
		}
			
	}
	if(isset($_POST['resp'])){
		$resp = $_POST['resp'];		
	}
	if(isset($_POST['cpfresp'])){
		$cpfresp = $_POST['cpfresp'];		
	}
	if(isset($_POST['RG'])){
		$rg = $_POST['RG'];		
	}
	if(isset($_POST['cpf'])){
		$cpf = $_POST['cpf'];		
	}
	if(isset($_POST['cidade'])){
		$cidade = $_POST['cidade'];		
	}
	if(isset($_POST['uf'])){
		$estado = $_POST['uf'];		
	}

	$pdf = new PDF();
	$title = iconv("UTF-8", "ISO-8859-1", 'TERMO DE AUTORIZAÇÃO DE USO DE IMAGEM, 
	<br>VOZ E RESPECTIVA CESSÃO DE DIREITOS
	<br>(LEI N. 9.610/98)');
	$pdf->SetTitle($title);
	$pdf->SetAuthor('A.B.I.A.T. - Atendentes Muito Especiais');
	$txt = "<br><br>Pelo presente instrumento, eu, <b>@responsavel</b>, portador do RG/RNE/Passaporte nº <b>@rg</b> e do CPF nº <b>@respcpf</b>, domiciliado na cidade <b>@cidade</b>, no estado de <b>@estado</b>, responsável pelo atendente especial <b>@atendente</b>, portador do CPF nº <b>@cpf</b>, <b>AUTORIZO</b>, de forma gratuita e sem qualquer ônus, a <i>Associação Brasileira de Inclusão Através do Trabalho (Atendentes Muito Especiais)</i>, a utilização da(s) imagem(ns) e/ou voz nos eventos em o atendente especial participar, e em sua divulgação, se houver, em todos os meios de divulgação possíveis, quer sejam na mídia impressa (livros, catálogos, revistas, jornais, entre outros), televisiva (propagandas para televisão aberta e/ou fechada, vídeos, filmes, entre outros), radiofônica (programas de rádio/podcasts), internet, banco de dados informatizados, multimídia, entre outros, e nos meios de comunicação interna, como jornais e periódicos em geral, na forma de impresso, voz e imagem.
	<br><br>A presente autorização e cessão são outorgadas livre e espontaneamente, em caráter gratuito, não incorrendo à autorizada qualquer custo ou ônus, seja a que título for, sendo que estas são firmadas em caráter irrevogável, irretratável, e por prazo indeterminado, obrigando, inclusive, eventuais herdeiros e sucessores outorgantes.";
	$campos = array("@atendente","@responsavel","@rg","@cpf","@respcpf","@cidade","@estado");
	$dados = array($atendente,$resp,$rg,$cpf,$cpfresp,$cidade,$estado);
	$txt = str_replace($campos,$dados,$txt);
	$pdf->PrintChapter(1,'AUTORIZAÇÃO',$txt);
	$pdf->Ln(30);
	$txt = iconv("UTF-8", "ISO-8859-1//IGNORE","São Paulo, ".strftime("%A, %e de %B de %G"));
	$pdf->Cell(0,5,$txt,0,1,"R");
	$pdf->Ln(30);
	$pdf->Line($pdf->GetPageWidth()/2,$pdf->GetY(),$pdf->GetPageWidth()-10,$pdf->GetY());
	$pdf->AliasNbPages();
	//$pdf->PrintChapter(2,'THE PROS AND CONS','./fpdf/tutorial/20k_c2.txt');
	$pdf->Output("F",$arquivo,1);
	ini_set( 'display_errors', 1 );
	error_reporting( E_ALL );
	require './include/autoload.php';
//	$emailto = "pauloadd@hotmail.com";
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
	$headers[] = 'Cc: <'.$email.'>,<regina.rjr@hotmail.com>';
//    $headers[] = 'Cc: <regina.rjr@hotmail.com>';
//    $headers[] = "From:" . $from;

//    $headers = "From:" . $from;

	$subject  = 'Autorização do uso de imagem '. $atendente; // Assunto da mensagem
	$body = ' <strong>Nome: '.$atendente.'</strong><br>'; // Nomes do atendente
	$body .= ' <strong>Responsavel: '.$resp.'</strong><br>'; // Nomes dos noivos
	$body .= "<a href='https://projetoame.org/".$arquivo."'>Autorização</a><br>";
	$body .= '<br><hr> DADOS DE AUDITORIA:<br>IP: '.$_SERVER['SERVER_ADDR'].'<br>'; // IP do visitante
	$body .= ' Navegador: '.$_SERVER['HTTP_USER_AGENT'].'<br>'; // IP do visitante
	$body .= ' Enviado em: '. date('d/m/Y H:i').'<br>'; // Texto da mensagem
		$message = $body;
	$dados['server'] = $_SERVER['SERVER_ADDR'];
	$dados['agent'] = $_SERVER['HTTP_USER_AGENT'];
	$dados['data_atual'] = date('d/m/Y H:i');
	if (mail($to,$subject,$message, implode("\r\n", $headers))) {
//    if (mail($to,$subject,$message, $headers)) {
	   $msg = "<p class='text-center'>Os dados de <strong><em>".$atendente."</em></strong> foram enviados com sucesso!</p>"; // or use booleans here
	} else {
		$msg = "<p class='text-center'>Não conseguimos enviar sua mensagem!  Tente novamente mais tarde.</p>";;
	}
}

?>
<body>
	<div class="container">
		<header class="mt-5 p-2 justify-content-md-center">
			<h1 class="text-center">Autorização de uso de imagem</h1>
			<div class="row justify-content-between bg-light p-1 rounded">
			<div class="col">
				<nav aria-label="breadcrumb bg-transparent">
			  <ol class="breadcrumb bg-transparent">
				<li class="breadcrumb-item"><a href="/atendentes">Atividades</a></li>
				<li class="breadcrumb-item"><a href="/convertefone">Atendentes</a></li>
				<li class="breadcrumb-item active" aria-current="page">Editando candidato</li>
			  </ol>
			</nav>
			</div>
			</div>
		</header>
		<div class="row justify-content-center">
			<div class="col col-md-8">
				<div class="card">
				<div class="card-header">
					<img class="card-img-top img-thumbnail" src="<?php echo $perfil?>">
					<h1 class="text-center"><?php echo $candidato?></h1>
					<?php 
					if ($_SERVER['REQUEST_METHOD']=="POST"){
					?>
					<p class="text-center"><a href="<?php echo $arquivo?>" target="_blank">Autorização</a></p>
					<?php 					
					}
					?>
				</div>
				<div class="card-body">
					<form method="post">
						<input type="hidden" id="candidato_id" name="candidato_id" value="<?php echo $id?>">
					<div class="md-form">
						<i class="far fa-user prefix grey-text"></i>
						<input type="text" id="atendente" name="atendente" class="form-control" placeholder="informe o nome do atendente" value="<?php echo $atendente?>" required>
						<label for="atendente">Nome do atendente</label>
					</div>
					<div class="md-form">
						<i class="far fa-address-card prefix grey-text"></i>
						<input type="text" id="cpf" name="cpf" class="form-control" placeholder="informe o CPF do atendente" value="<?php echo $cpf?>" required>
						<label for="cpf">CPF do atendente</label>
					</div>
					<div class="md-form">
						<i class="far fa-user prefix grey-text"></i>
						<input type="text" id="resp" name="resp" class="form-control" placeholder="informe o nome do responsável" value="<?php echo $resp?>" required>
						<label for="resp">Nome do Responsável</label>
					</div>
					<div class="md-form">
						<i class="far fa-address-card prefix grey-text"></i>
						<input type="text" id="RG" name="RG" class="form-control" placeholder="informe o RG do responsável" value="<?php echo $rg?>" required>
						<label for="RG">RG do responsável</label>
					</div>
					<div class="md-form">
						<i class="far fa-address-card prefix grey-text"></i>
						<input type="text" id="cpfresp" name="cpfresp" class="form-control" placeholder="informe o CPF do responsável" value="<?php echo $cpfresp?>" required>
						<label for="cpf">CPF do responsável</label>
					</div>
					<div class="md-form">
						<i class="far fa-address-card prefix grey-text"></i>
						<input type="email" id="email" name="email" class="form-control" placeholder="informe o e-mail cadastrado" value="<?php echo $email?>" required>
						<label for="cpf">e-mail cadastrado</label>
					</div>
					<div class="md-form">
						<i class="far fa-address-card prefix grey-text"></i>
						<input type="text" id="cidade" name="cidade" class="form-control" placeholder="informe a cidade" value="<?php echo $cidade?>" required>
						<label for="cidade">Cidade</label>
					</div>
					<div class="md-form">
						<i class="far fa-address-card prefix grey-text"></i>
						<input type="text" id="UF" name="uf" class="form-control" placeholder="sigla do estado" value="<?php echo $estado?>" required>
						<label for="uf">Estado</label>
					</div>
					<div class="md-form">
						<button class="btn btn-block btn-pill btn-primary" type="submit">Enviar</button>
					</div>
				</div>
					</form>
				</div>
			</div>
		</div>
	</div>
<?php 
	include_once('./include/footer.php');
	include_once('./include/scripts.php');
	include_once('./include/end.php');
?>

