<?php
setlocale(LC_ALL, 'pt_BR');
// include_once('include/conexao.php');
mysqli_set_charset($conexao, "utf8mb4");
// include_once('include/funcoes.php');
include_once('include/funcoes-fpdf.php');
$titulo = "Edição de candidato";
// include_once('include/head.php');
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
	$arquivo = "docs/direito-de-imagem.pdf";
	if(isset($_POST['atendente'])){
		$atendente = $_POST['atendente'];		
		$arquivo = "docs/direito-de-imagem-de-".slugify($atendente).".pdf";
	}
	
	// Processamento da Assinatura Digital
	$caminho_assinatura = "";
	if(isset($_POST['assinatura']) && !empty($_POST['assinatura'])){
		$data_uri = $_POST['assinatura'];
		$encoded_image = explode(",", $data_uri)[1];
		$decoded_image = base64_decode($encoded_image);
		
		// Criar pasta de assinaturas se não existir
		$diretorio_assinaturas = "docs/assinaturas/";
		if (!file_exists($diretorio_assinaturas)) {
			mkdir($diretorio_assinaturas, 0755, true);
		}
		
		$nome_assinatura = "assinatura_".slugify($atendente)."_".time().".png";
		$caminho_assinatura = $diretorio_assinaturas . $nome_assinatura;
		file_put_contents($caminho_assinatura, $decoded_image);
	}

	if(isset($_POST['email'])){
		$email = strtolower($_POST['email']);
		$query = "SELECT candidato_id,nome,imagens.url as url 
		FROM candidatos
		LEFT JOIN imagens ON candidatos.imagem_id = imagens.imagem_id
        WHERE candidatos.email LIKE '".$email."'";
		$resp = mysqli_query($conexao,$query);
		if ($resp && mysqli_num_rows($resp) > 0){
			$row = mysqli_fetch_assoc($resp);
			$candidato_id = $row['candidato_id'];
			$perfil = $row['url'];
			
			// Atualizar o cadastro do candidato com os dados fornecidos no formulário
			$update_query = "UPDATE candidatos SET 
							 nome = '".mysqli_real_escape_string($conexao, $_POST['atendente'])."',
							 RG = '".mysqli_real_escape_string($conexao, $_POST['RG'])."',
							 CPF = '".mysqli_real_escape_string($conexao, $_POST['cpf'])."',
							 cidade = '".mysqli_real_escape_string($conexao, $_POST['cidade'])."',
							 UF = '".mysqli_real_escape_string($conexao, $_POST['uf'])."',
							 responsavel = '".mysqli_real_escape_string($conexao, $_POST['resp'])."',
							 CPF_RESP = '".mysqli_real_escape_string($conexao, $_POST['cpfresp'])."'
							 WHERE candidato_id = $candidato_id";
			mysqli_query($conexao, $update_query);

			// Registrar o documento PDF - CORREÇÃO DEFINITIVA DE CODIFICAÇÃO
			$querydoc = "INSERT INTO `documentos`(`candidato_id`, `url`, `descritivo`) VALUES ('".$candidato_id."','".$arquivo."', 'Autorização de Uso de Imagem')";
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
	
	// Adicionar Assinatura e Dados de Auditoria ao PDF
	if($caminho_assinatura != "" && file_exists($caminho_assinatura)){
		$pdf->Ln(10);
		$pdf->Cell(0,10, iconv("UTF-8", "ISO-8859-1", "Assinatura do Responsável:"), 0, 1, "C");
		$pdf->Image($caminho_assinatura, $pdf->GetPageWidth()/2 - 30, $pdf->GetY(), 60);
		$pdf->Ln(20);
	} else {
		$pdf->Ln(30);
		$pdf->Line($pdf->GetPageWidth()/2,$pdf->GetY(),$pdf->GetPageWidth()-10,$pdf->GetY());
	}
	
	$txt_data = iconv("UTF-8", "ISO-8859-1//IGNORE","Assinado digitalmente em: ".date('d/m/Y H:i:s'));
	$pdf->SetFont('Arial','I',8);
	$pdf->Cell(0,5,$txt_data,0,1,"C");
	$txt_ip = iconv("UTF-8", "ISO-8859-1//IGNORE","IP de Auditoria: ".$_SERVER['REMOTE_ADDR']);
	$pdf->Cell(0,5,$txt_ip,0,1,"C");
	
	$pdf->Ln(10);
	$txt_local = iconv("UTF-8", "ISO-8859-1//IGNORE","Cidade/UF: ".$cidade."/".$estado);
	$pdf->Cell(0,5,$txt_local,0,1,"R");
	
	$pdf->AliasNbPages();
	//$pdf->PrintChapter(2,'THE PROS AND CONS','./fpdf/tutorial/20k_c2.txt');
	$pdf->Output("F",$arquivo,1);
	ini_set( 'display_errors', 1 );
	error_reporting( E_ALL );
	require 'include/autoload.php';
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
	$body .= '<br><hr> DADOS DE AUDITORIA:<br>IP: '.$_SERVER['REMOTE_ADDR'].'<br>'; // IP do visitante
	$body .= ' Navegador: '.$_SERVER['HTTP_USER_AGENT'].'<br>'; // IP do visitante
	$body .= ' Enviado em: '. date('d/m/Y H:i').'<br>'; // Texto da mensagem
		$message = $body;
	$dados['server'] = $_SERVER['REMOTE_ADDR'];
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
				<div class="card-header text-center">
					<a id="link-trocafoto" href="#" title="Clique para trocar a foto de perfil">
						<img id="perfil-img" class="card-img-top img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;" src="<?php echo $perfil?>">
					</a>
					<h1 class="text-center" id="atendente-header"><?php echo $candidato?></h1>
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
						<i class="far fa-envelope prefix grey-text"></i>
						<input type="email" id="email" name="email" class="form-control" placeholder="informe o e-mail cadastrado" value="<?php echo $email?>" required>
						<label for="email">E-mail cadastrado (Preencha para buscar seus dados)</label>
					</div>

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
						<input type="text" id="RG" name="RG" class="form-control" placeholder="informe o RG do responsável" value="<?php echo $rg?>">
						<label for="RG">RG do responsável (Opcional)</label>
					</div>
					<div class="md-form">
						<i class="far fa-address-card prefix grey-text"></i>
						<input type="text" id="cpfresp" name="cpfresp" class="form-control" placeholder="informe o CPF do responsável" value="<?php echo $cpfresp?>" required>
						<label for="cpfresp">CPF do responsável</label>
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

					<!-- Área de Assinatura Digital -->
					<div class="form-group mt-4">
						<label>Assinatura do Responsável (Assine no quadro abaixo):</label>
						<div id="signature-pad" class="signature-pad" style="border: 1px solid #ccc; background-color: #f9f9f9; border-radius: 4px;">
							<div class="signature-pad--body">
								<canvas id="signature-canvas" style="width: 100%; height: 200px; touch-action: none;"></canvas>
							</div>
							<div class="signature-pad--footer text-right p-2">
								<button type="button" class="btn btn-sm btn-outline-secondary" id="clear-signature">Limpar Assinatura</button>
							</div>
						</div>
						<input type="hidden" name="assinatura" id="assinatura-data">
					</div>

					<div class="md-form">
						<button class="btn btn-block btn-pill btn-primary" type="submit" id="btn-submit" disabled>Enviar e Assinar</button>
					</div>
					<div id="cadastro-msg" class="text-center mt-2" style="display:none;">
						<p class="text-danger small">E-mail não localizado. Para assinar o termo, você precisa estar cadastrado.</p>
						<a href="/inscrever" class="btn btn-sm btn-outline-primary rounded-pill">Fazer Inscrição Agora</a>
					</div>
				</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	
	<!-- Scripts para Assinatura Digital e Auto-Busca -->
	<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
	<script>
		document.addEventListener("DOMContentLoaded", function() {
			// Lógica do Signature Pad
			const canvas = document.getElementById('signature-canvas');
			const signaturePad = new SignaturePad(canvas, {
				backgroundColor: 'rgba(255, 255, 255, 0)',
				penColor: 'rgb(0, 0, 0)'
			});

			function resizeCanvas() {
				const ratio = Math.max(window.devicePixelRatio || 1, 1);
				canvas.width = canvas.offsetWidth * ratio;
				canvas.height = canvas.offsetHeight * ratio;
				canvas.getContext("2d").scale(ratio, ratio);
				signaturePad.clear();
			}

			window.onresize = resizeCanvas;
			resizeCanvas();

			document.getElementById('clear-signature').addEventListener('click', function() {
				signaturePad.clear();
			});

			// Lógica de Busca por E-mail
			const emailInput = document.getElementById('email');
			const btnSubmit = document.getElementById('btn-submit');
			const cadastroMsg = document.getElementById('cadastro-msg');

			emailInput.addEventListener('blur', function() {
				const email = this.value;
				if (email.length > 5 && email.includes('@')) {
					fetch('api_get_candidato?email=' + encodeURIComponent(email))
						.then(response => response.json())
						.then(result => {
							if (result.success) {
								const data = result.data;
								document.getElementById('atendente').value = data.nome || '';
								document.getElementById('atendente-header').innerText = data.nome || '';
								document.getElementById('cpf').value = data.CPF || '';
								document.getElementById('RG').value = data.RG || '';
								document.getElementById('resp').value = data.responsavel || '';
								document.getElementById('cpfresp').value = data.CPF_RESP || '';
								document.getElementById('cidade').value = data.cidade || '';
								document.getElementById('UF').value = data.UF || '';
								document.getElementById('candidato_id').value = data.candidato_id;
								
								// Ativar Botão e Esconder Mensagem de Erro
								btnSubmit.disabled = false;
								cadastroMsg.style.display = 'none';

								// Atualizar Link e Foto
								if (data.candidato_id) {
									document.getElementById('link-trocafoto').href = '/trocafoto/' + data.candidato_id;
									if (data.url) {
										document.getElementById('perfil-img').src = '/' + data.url;
									}
								}
								
								// Ativar labels do MDB
								document.querySelectorAll('.md-form label').forEach(label => {
									label.classList.add('active');
								});
							} else {
								// Caso o e-mail não seja encontrado
								btnSubmit.disabled = true;
								cadastroMsg.style.display = 'block';
								
								// Limpar campos
								document.getElementById('atendente').value = '';
								document.getElementById('atendente-header').innerText = 'Atendente não cadastrado';
								document.getElementById('cpf').value = '';
								document.getElementById('RG').value = '';
								document.getElementById('resp').value = '';
								document.getElementById('cpfresp').value = '';
								document.getElementById('cidade').value = '';
								document.getElementById('UF').value = '';
								document.getElementById('candidato_id').value = '0';
								document.getElementById('perfil-img').src = '/img/profile.png';
								document.getElementById('link-trocafoto').href = '#';
							}
						})
						.catch(error => console.error('Erro na busca:', error));
				}
			});

			// No submit do formulário
			const form = document.querySelector('form');
			form.addEventListener('submit', function(event) {
				if (signaturePad.isEmpty()) {
					alert("Por favor, forneça sua assinatura antes de enviar.");
					event.preventDefault();
				} else {
					const dataURL = signaturePad.toDataURL('image/png');
					document.getElementById('assinatura-data').value = dataURL;
				}
			});
		});
	</script>
<?php
	include_once('include/footer.php');
	include_once('include/scripts.php');
	include_once('include/end.php');
?>



