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
if($_SERVER['REQUEST_METHOD']=="POST"){
	if (array_key_exists(1,$parametros)){
		$id = intval($parametros[1]);
	}
	if ($id>0){
		$query = "SELECT * FROM candidatos WHERE candidato_id = ".$id;
		$resp = mysqli_query($conexao,$query);
		if ($resp){
			$row = mysqli_fetch_assoc($resp);
			$nome = $row['nome'];
			$responsavel = $row['responsavel'];
			$Email = $row['Email'];
			$CPF = $row['CPF'];
			$CPF_RESP = $row['CPF_RESP'];
			$cidade = $row['cidade'];
			$UF = $row['UF'];
		}
		$query = "UPDATE `candidatos` 
		SET `nome`='".$nome."',
			`responsavel`='".$responsavel."',
			`Email`='".$Email."',
			`CPF`='".$CPF."',
			`CPF_RESP`='".$CPF_RESP."',
			`CEP`='".$CEP."',
			`endereco`='".$_POST['endereco']."',
			`complemento`='".$_POST['complemento']."',
			`cidade`='".$_POST['cidade']."',
			`UF`='".$_POST['UF']."',
			`mensagem`='".$_POST['mensagem']."',
			`imagem_id`='".$_POST['imagem_id']."',
			`anexo`='".$_POST['anexo']."',
			`anexo_id`='".$_POST['anexo_id']."',
			`certificado`='".$_POST['certificado']."',
			`rodizio`='".$_POST['rodizio']."',
			`data_inscricao`='".$_POST['data_inscricao']."',
			`WZ`='".$_POST['WZ']."',
			`telegram`='".$_POST['telegram']."',
			`boletim`='".$_POST['boletim']."',
			`grupo_capacitacao`='".$_POST['grupo_capacitacao']."',
			`grupo_voluntarios`='".$_POST['grupo_voluntarios']."',
			`IP`='".$_POST['IP']."' 
			WHERE candidato_id = ".$id;				
	} else {
		$query = "INSERT INTO `usuarios`(`login`, `email`, `telefone`, `nome`, `nivel`) VALUES ('" . $_POST['Email']."','" . $_POST['Email']."','" . formataWA($_POST['Telefone'])."','" . $_POST['nome']."','1')";
		$resp = mysqli_query($conexao,$query);
		$usuario_id = $conexao -> insert_id;

		$query = "INSERT INTO `candidatos`(`usuario_id`, `inscrito`, `nome`, `responsavel`, `Email`, `Telefone`, `genero`, `Nascimento`, `RG`, `CPF`, `CPF_RESP`, `PIX`, `camisa`, `calca`, `sapato`, `CEP`, `endereco`, `complemento`, `cidade`, `UF`, `mensagem`, `imagem_id`, `anexo`, `anexo_id`, `certificado`, `rodizio`, `data_inscricao`, `WZ`, `telegram`, `boletim`, `grupo_capacitacao`, `grupo_voluntarios`, `IP`) 
		VALUES ('". $usuario_id."','candidato','" . $_POST['nome']."','" . $_POST['responsavel']."','" . $_POST['Email']."','" . $_POST['Telefone']."','" . $_POST['genero']."','" . $_POST['Nascimento']."','" . $_POST['RG']."','" . $_POST['CPF']."','" . $_POST['CPF_RESP']."','" . $_POST['PIX']."','" . $_POST['camisa']."','" . $_POST['calca']."','" . $_POST['sapato']."','" . $_POST['CEP']."','" . $_POST['endereco']."','" . $_POST['complemento']."','" . $_POST['cidade']."','" . $_POST['UF']."','" . $_POST['mensagem']."','" . $_POST['imagem_id']."','" . $_POST['anexo']."','" . $_POST['anexo_id']."','" . $_POST['certificado']."','" . $_POST['rodizio']."','" . $_POST['data_inscricao']."','" . $_POST['WZ']."','" . $_POST['telegram']."', '" . $_POST['boletim'] . "','" . $_POST['grupo_capacitacao']."','" . $_POST['grupo_voluntarios']."' ,'" . $_POST['IP']."')";
}
//	echo $query . "<br>";
//	exit;
		$resp = mysqli_query($conexao,$query);	
}

$pdf = new PDF();
$title = iconv("UTF-8", "ISO-8859-1", 'TERMO DE AUTORIZAÇÃO DE USO DE IMAGEM, 
<br>VOZ E RESPECTIVA CESSÃO DE DIREITOS
<br>(LEI N. 9.610/98)');
$pdf->SetTitle($title);
$pdf->SetAuthor('A.B.I.A.T. - Atendentes Muito Especiais');
$txt = "<br><br>Pelo presente instrumento, eu,<b>@resp</b>, portador do RG/RNE/Passaporte nº <b>@rg</b> e do CPF nº <b>@cpfresp</b>, domiciliado na cidade <b>@cidade</b>, no estado de <b>@estado</b>, responsável pelo atendente especial <b>@atendente</b>, portador do CPF nº <b>@cpf</b>, <b>AUTORIZO</b>, de forma gratuita e sem qualquer ônus, a <i>Associação Brasileira de Inclusão Através do Trabalho (Atendentes Muito Especiais)</i>, a utilização da(s) imagem(ns) e/ou voz nos eventos em o atendente especial participar, e em sua divulgação, se houver, em todos os meios de divulgação possíveis, quer sejam na mídia impressa (livros, catálogos, revistas, jornais, entre outros), televisiva (propagandas para televisão aberta e/ou fechada, vídeos, filmes, entre outros), radiofônica (programas de rádio/podcasts), internet, banco de dados informatizados, multimídia, entre outros, e nos meios de comunicação interna, como jornais e periódicos em geral, na forma de impresso, voz e imagem.
<br><br><br><br>A presente autorização e cessão são outorgadas livre e espontaneamente, em caráter gratuito, não incorrendo à autorizada qualquer custo ou ônus, seja a que título for, sendo que estas são firmadas em caráter irrevogável, irretratável, e por prazo indeterminado, obrigando, inclusive, eventuais herdeiros e sucessores outorgantes.";
$pdf->PrintChapter(1,'AUTORIZAÇÃO',$txt);
$pdf->Ln(30);
$txt = iconv("UTF-8", "ISO-8859-1//IGNORE","São Paulo, ");
$pdf->Cell(0,5,$txt.strftime("%A, %e de %B de %G"),0,1,"R");
$pdf->Ln(30);
$pdf->Line($pdf->GetPageWidth()/2,$pdf->GetY(),$pdf->GetPageWidth()-10,$pdf->GetY());
$pdf->AliasNbPages();
//$pdf->PrintChapter(2,'THE PROS AND CONS','./fpdf/tutorial/20k_c2.txt');
$pdf->Output("F","./docs/autorização.pdf",1);
?>
<body>
	<div class="container">
		<header class="mt-5 p-2 justify-content-md-center">
			<h1 class="text-center">Edição de candidato</h1>
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
			<div class="col  col-auto">
			<form class="form-inline">
				<input class="form-control mr-sm-2 rounded-pill bg-transparent" type="search" placeholder="nome" aria-label="busca" id="busca" name="busca">
				<button class="btn btn-sm rounded-pill my-2 my-sm-0" type="submit">buscar</button>
			</form>
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
					<p class="text-center"><a href="./docs/autorização.pdf" target="_blank">Autorização</a></p>
					<?						
					}
					?>
				</div>
				<div class="card-body">
					<form method="post">
						<input type="hidden" id="candidato_id" name="candidato_id" value="<? echo $id?>">
					<div class="md-form">
						<i class="far fa-user prefix grey-text"></i>
						<input type="text" id="atendente" name="atendente" class="form-control" placeholder="informe o nome do atendente" value="<? echo $candidato?>">
						<label for="atendente">Nome do atendente</label>
					</div>
					<div class="md-form">
						<i class="far fa-address-card prefix grey-text"></i>
						<input type="text" id="cpf" name="cpf" class="form-control" placeholder="informe o CPF do atendente" value="<? echo $cpf?>">
						<label for="cpf">CPF do atendente</label>
					</div>
					<div class="md-form">
						<i class="far fa-user prefix grey-text"></i>
						<input type="text" id="resp" name="resp" class="form-control" placeholder="informe o nome do responsável" value="<? echo $resp?>">
						<label for="resp">Nome do Responsável</label>
					</div>
					<div class="md-form">
						<i class="far fa-address-card prefix grey-text"></i>
						<input type="text" id="CEP" name="cep" class="form-control" placeholder="CEP do endereço" value="<? echo $cpfresp?>">
						<label for="cep">CEP</label>
					</div>
					<div class="md-form">
						<i class="far fa-address-card prefix grey-text"></i>
						<input type="text" id="endereco" name="endereco" class="form-control" placeholder="informe o endereço" value="<? echo $cpfresp?>">
						<label for="endereco">Endereço</label>
					</div>
					<div class="md-form">
						<i class="far fa-address-card prefix grey-text"></i>
						<input type="text" id="compl" name="cpfresp" class="form-control" placeholder="cpf do responsável" value="<? echo $cpfresp?>">
						<label for="cpf">CPF do responsável</label>
					</div>
					<div class="md-form">
						<i class="far fa-address-card prefix grey-text"></i>
						<input type="text" id="complemento" name="compl" class="form-control" placeholder="Complemento (nº, apto, bloco etc.)" value="<? echo $cpfresp?>">
						<label for="compl">Complemento do endereço</label>
					</div>
					<div class="md-form">
						<i class="far fa-address-card prefix grey-text"></i>
						<input type="text" id="cidade" name="cidade" class="form-control" placeholder="informe a cidade" value="<? echo $cpfresp?>">
						<label for="cidade">Cidade</label>
					</div>
					<div class="md-form">
						<i class="far fa-address-card prefix grey-text"></i>
						<input type="text" id="UF" name="uf" class="form-control" placeholder="sigla do estado" value="<? echo $cpfresp?>">
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
?>
		<script>

        $(document).ready(function() {

            function limpa_formulário_cep() {
                // Limpa valores do formulário de cep.
                $("#endereco").val("");
//                $("#bairro").val("");
                $("#complemento").val("");
                $("#cidade").val("");
                $("#UF").val("");
//                $("#ibge").val("");
            }
            
			//Quando o campo cep perde o foco.
            $("#CEP").blur(function() {

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
                        $("#UF").val("...");
//                        $("#ibge").val("...");

                        //Consulta o webservice viacep.com.br/
                        $.getJSON("https://viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {

                            if (!("erro" in dados)) {
                                //Atualiza os campos com os valores da consulta.
                                $("#endereco").val(dados.logradouro);
//                                $("#bairro").val(dados.bairro);
                                $("#cidade").val(dados.localidade);
                                $("#UF").val(dados.uf);
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

<?
	include_once('./include/end.php');
?>

