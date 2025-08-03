<?php
setlocale(LC_ALL, 'pt_BR');
include_once('./include/conexao.php');
include_once('./include/funcoes.php');
include_once('./include/funcoes-fpdf.php');
$titulo = "Edição de candidato";
include_once('./include/head.php');
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
	if(isset($_POST['rgresp'])){
		$rg = $_POST['rgresp'];		
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
	$title = iconv("UTF-8", "ISO-8859-1", 'INSTRUMENTO PARTICULAR DE PRESTAÇÃO DE <br>
	SERVIÇOS ESPECIALIZADOS PARA EVENTO CERTO E DETERMINADO');
	$pdf->SetTitle($title);
	$pdf->SetAuthor('A.B.I.A.T. - Atendentes Muito Especiais');
	$txt = "Pelo presente instrumento, as partes:

(i)	ASSOCIAÇÃO BRASILEIRA DE INCLUSÃO ATRAVÉS DO TRABALHO – ATENDENTES MUITO ESPECIAIS – AME LTDA, pessoa jurídica de direito privado sem fins lucrativos, estabelecida no Município de São Paulo, Estado de São Paulo, com sede situada na Avenida Paula Ferreira, nº 2571, Distrito Industrial, CEP: 02915-100, representada na forma de seus atos constitutivos, pelo Sr. Paulo Addair Daniel Filho, brasileiro, casado, empresário, portador da cédula de identidade, RG-SSP/SP, nº 6.999.001-3, inscrito no CPF/MF sob o nº 007.234.198-09, doravante designada simplesmente “CONTRATANTE”; e,

(ii)	<strong>@atendente</strong>, brasileiro, incapaz, portador da Cédula de Identidade, RG. nº @rg, inscrito no CPF/MF sob o nº @cpf, representado por seu/sua responsável @resp, brasileiro(a), portador(a) da Cédula de Identidade, RG. nº @rgresp, inscrito(a) no CPF/MF sob o nº @cpfresp, ambos residentes e domiciliados @cidade , @estado – CEP: @cep, doravante designado apenas “CONTRATADO”.

CONSIDERANDO QUE:
a)	A CONTRATANTE é uma entidade sem fins lucrativos que atua na triagem, capacitação e inclusão de pessoas com deficiência intelectual para a área de eventos, feiras de negócios e congressos.
b)	A CONTRATANTE irá participar, como contratado de um EXPOSITOR/PROMOTOR, no evento EVENTO CONTRATADO, voltado para o segmento exemplo, que será realizado entre os dias xx e xx de xxxx de xxxx no PAVILHÃO.
c)	No decorrer do ano de xxxx a CONTRATANTE, no desempenho de sua função social, tem trabalhado o tema “inclusão social”, adotando políticas especiais voltada para o tema.
d)	O CONTRATADO é portador de necessidades especiais em razão de síndrome de down, mas com capacidade laborativa.
e)	É interesse da CONTRATANTE contar com toda a experiência do CONTRATADO para a realização de serviços específicos durante a realização no espaço do EXPOSITOR/PROMOTOR durante o evento EVENTO. Sendo de interesse do CONTRATADO prestar seus serviços à CONTRATANTE.

Agora, as partes decidem firmar o presente Instrumento Particular de Prestação de Serviços Específicos Para Evento Certo e Determinado (CONTRATO) que se regerá pelas cláusulas e condições a seguir definidas:

Cláusula 1ª:	Por meio da presente contratação, o CONTRATADO prestará à CONTRATANTE os serviços de:
a)	Recepção de visitantes ao estande da CONTRATANTE;
b)	Distribuição de brindes, bebidas e petiscos aos participantes do evento;
c)	Apoio e suporte à equipe da CONTRATANTE e visitantes do estande, durante todo o evento.
 
Cláusula 2ª:	A presente contratação vigerá desde a data da assinatura deste instrumento até a conclusão e encerramento do evento. Contudo os serviços serão prestados entre os dias 21  de 24 de maio, no horário das 12:00 h às 18:00 h.
Parágrafo primeiro: Durante a prestação dos serviços, o CONTRATADO terá direito ao gozo de 60 minutos para refeição e descanso, por dia de evento. Sendo que a alimentação será fornecida pela CONTRATANTE.

Parágrafo segundo: Em razão das necessidades especiais do CONTRATADO, durante a execução dos serviços ora contratados, ele deverá estar acompanhado de um monitor designado pelo CONTRATANTE que o assistirá em todas as suas necessidades, responsabilizando-se integralmente por sua saúde e capacidade laborativa.

Parágrafo terceiro: Havendo qualquer intercorrência com o CONTRATADO, seu monitor deverá comunicar imediatamente ao preposto da CONTRATANTE que estiver presente no local do evento para a adoção das devidas medidas a fim de garantir assistência às necessidades do CONTRATADO.

Cláusula 3ª:	Em remuneração aos serviços ora contratados, a CONTRATANTE pagará ao CONTRATADO o valor de R$ 200,00 por dia. Os quais serão pagos até o dia 24/junho, por meio de crédito em conta bancária de titularidade do próprio ATENDENTE ou do representante legal do CONTRATADO.

Cláusula 4ª:	Eventual inadimplemento do disposto na cláusula 3ª, sujeitará a CONTRATANTE à incidência de juros legais 1% ao mês e correção monetária segundo o INPC-IBGE, tudo calculado sobre o valor do débito apurado.

Cláusula 5ª:	Toda e qualquer alteração aos serviços de que trata este CONTRATO, deverá ser efetuado mediante expressa, formal e escrita autorização da outra PARTE, sob pena de ser tida por nula, ensejando a PARTE responsável às penalidades cabíveis, conforme disposto na cláusula 6ª, bem como à imediata rescisão do presente CONTRATO.

Cláusula 6ª:	Dada a natureza e urgência com que os serviços, objeto desta parceria, serão executados, qualquer rescisão importará na incidência de multa equivalente ao valor integral da remuneração de que trata a Cláusula 3ª, sem prejuízo da devida reparação dos danos e prejuízos causados.

Cláusula 7ª:	Os termos deste CONTRATO não implicam qualquer tipo de associação ou sociedade entre as PARTES, nem em vínculo empregatício entre a CONTRATADA e a CONTRATANTE.

Cláusula 8ª:	Fica expressamente convencionado que não constituirá novação a abstenção, por quaisquer das PARTES, do exercício de qualquer direito, poder, recurso ou faculdade assegurados por lei ou pelo CONTRATO, nem a eventual tolerância a atraso no cumprimento de quaisquer obrigações por quaisquer das PARTES, o que não impedirá que a outra PARTE, a seu exclusivo critério, exerça, a qualquer momento, esses direitos, poderes, recursos ou faculdades, os quais são cumulativos e não excludentes em relação aos previstos em lei.

Cláusula 9ª:	Todos os termos, multas e condições do CONTRATO estarão vinculando as PARTES a ele, bem como seus sucessores. 

Cláusula 10ª:	O CONTRATO constitui o acordo integral entre as PARTES contratantes e substitui todos os acordos prévios escritos ou orais referentes ao seu objeto, tenham sido eles firmados pela ora CONTRATANTE ou por qualquer outra empresa que ela tenha sucedido.

Cláusula 11ª:	Ambas as PARTES cumprirão todas as exigências legais e regulamentares, inclusive de natureza jurídica, fiscal, bem como as formalidades e determinações dos órgãos públicos, decorrentes do CONTRATO.

Cláusula 12ª:	Com exceção das condições de rescisão, o CONTRATO e suas obrigações são estabelecidos em caráter incondicional, irrevogável e irretratável.

Cláusula 13ª:	As PARTES elegem o foro da Comarca de São Paulo/SP para dirimir eventuais pendências decorrentes do CONTRATO, renunciando, expressamente, a qualquer outro, por mais privilegiado que seja ou venha a ser.

E por estarem justas e contratadas, assinam digitalmente o presente contrato em 02 (duas) vias de igual teor, na presença de duas testemunhas identificadas abaixo. 
";
	$campos = array("@atendente","@responsavel","@rg","@cpf","@respcpf","@cidade","@estado");
	$dados = array($atendente,$resp,$rg,$cpf,$rgresp,$cpfresp,$cidade,$estado);
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
					<img class="card-img-top img-thumbnail" src="<? echo $perfil?>">
					<h1 class="text-center"><? echo $candidato?></h1>
					<?
					if ($_SERVER['REQUEST_METHOD']=="POST"){
					?>
					<p class="text-center"><a href="<? echo $arquivo?>" target="_blank">Autorização</a></p>
					<?						
					}
					?>
				</div>
				<div class="card-body">
					<form method="post">
						<input type="hidden" id="candidato_id" name="candidato_id" value="<? echo $id?>">
					<div class="md-form">
						<i class="far fa-user prefix grey-text"></i>
						<input type="text" id="atendente" name="atendente" class="form-control" placeholder="informe o nome do atendente" value="<? echo $atendente?>" required>
						<label for="atendente">Nome do atendente</label>
					</div>
					<div class="md-form">
						<i class="far fa-address-card prefix grey-text"></i>
						<input type="text" id="cpf" name="cpf" class="form-control" placeholder="informe o CPF do atendente" value="<? echo $cpf?>" required>
						<label for="cpf">CPF do atendente</label>
					</div>
					<div class="md-form">
						<i class="far fa-user prefix grey-text"></i>
						<input type="text" id="resp" name="resp" class="form-control" placeholder="informe o nome do responsável" value="<? echo $resp?>" required>
						<label for="resp">Nome do Responsável</label>
					</div>
					<div class="md-form">
						<i class="far fa-address-card prefix grey-text"></i>
						<input type="text" id="RG" name="RG" class="form-control" placeholder="informe o RG do responsável" value="<? echo $rg?>" required>
						<label for="RG">RG do responsável</label>
					</div>
					<div class="md-form">
						<i class="far fa-address-card prefix grey-text"></i>
						<input type="text" id="rgresp" name="rgresp" class="form-control" placeholder="informe o CPF do responsável" value="<? echo $cpfresp?>" required>
						<label for="rgresp">RG do responsável</label>
					</div>
					<div class="md-form">
						<i class="far fa-address-card prefix grey-text"></i>
						<input type="text" id="cpfresp" name="cpfresp" class="form-control" placeholder="informe o CPF do responsável" value="<? echo $cpfresp?>" required>
						<label for="cpf">CPF do responsável</label>
					</div>
					<div class="md-form">
						<i class="far fa-address-card prefix grey-text"></i>
						<input type="email" id="email" name="email" class="form-control" placeholder="informe o e-mail cadastrado" value="<? echo $email?>" required>
						<label for="cpf">e-mail cadastrado</label>
					</div>
					<div class="md-form">
						<i class="far fa-address-card prefix grey-text"></i>
						<input type="text" id="cidade" name="cidade" class="form-control" placeholder="informe a cidade" value="<? echo $cidade?>" required>
						<label for="cidade">Cidade</label>
					</div>
					<div class="md-form">
						<i class="far fa-address-card prefix grey-text"></i>
						<input type="text" id="UF" name="uf" class="form-control" placeholder="sigla do estado" value="<? echo $estado?>" required>
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
<?
	include_once('./include/footer.php');
	include_once('./include/scripts.php');
	include_once('./include/end.php');
?>

