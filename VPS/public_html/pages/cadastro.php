<?PHP
$titulo = "Cadastro";
include_once("include/head.php");
include_once("include/conexao.php");
	$id = 0;
	$nome = "visitante";
	$boletim = 0;
/*
	$candidato = array (
	"inscrito" => '',
	"nome" =>  '',
	"responsavel" =>  '',
	"imagem" =>  '',
	"nascimento" =>  '',
	"email" =>  '',
	"telefone" =>  '',
	"genero" =>  '',
	"mensagem" =>  '',
	"anexo" =>  '',
	"inscricao" =>  '',
	"IP" =>  ''				
	);
*/
	$inscrito = '';
	$nome = '';
	$responsavel = '';
	$imagem = '';
	$nascimento = '';
	$email = '';
	$telefone = '';
	$genero = '';
	$mensagem = '';
	$anexo = '';
	$inscricao = '';
	$IP = '';				

if($_SERVER['REQUEST_METHOD']=="POST"){
		if (issset($_POST['id'])){			
			$id = $_GET['id'];
		}
		$query = "SELECT candidatos.*,imagens.url FROM `candidatos`,`imagens` WHERE candidatos.imagem_id = imagens.imagem_id AND candidato_id=".$id.";";
		$candidatos = mysqli_query($conexao, $query);
	//	echo($query);
		while($dados = mysqli_fetch_array($candidatos)){
/*
			$candidato = array (
			"inscrito" => "". $dados['Seu Nome'],
			"nome" => "". $dados['Seu Nome'],
			"responsavel" => "". $dados['responsavel'],
			"imagem" => "". $dados['url'],
			"nascimento" => "". $dados['Nascimento'],
			"email" => "". $dados['Email'],
			"telefone" => "". $dados['Telefone'],
			"genero" => "". $dados['genero'],
			"mensagem" => "". $dados['Mensagem'],
			"anexo" => "". $dados['certificado'],
			"inscricao" => "". $dados['data_inscricao'],
			"IP" => "". $dados['IP']				
			);
*/
			$inscrito = $dados['Seu Nome'];
			$nome = $dados['Seu Nome'];
			$responsavel = $dados['responsavel'];
			$imagem = $dados['url'];
			$nascimento = $dados['Nascimento'];
			$email = $dados['Email'];
			$telefone = $dados['Telefone'];
			$genero = $dados['genero'];
			$mensagem = $dados['Mensagem'];
			$anexo = $dados['certificado'];
			$inscricao = $dados['data_inscricao'];
			$IP = $dados['IP'];				
		}
	} else {
/*
			$candidato = [
				"inscrito" => "Novo inscrito",
				"imagem" => "img/ms-icon-310x310.png"
				];
*/
			$inscrito = "Novo inscrito";
			$imagem = "img/ms-icon-310x310.png";
	}
?>
<body>
<div class="edge-header blue-gradient p-5 mb-2">
	<img src="../atendentes/img/ProjetoAME-LOGO-2022.jpg">
	<h1 class="p-2">Atendente do <strong>Projeto A.M.E.</strong></h1>
	<nav aria-label="breadcrumb">
	  <ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="/">Inicio</a></li>
		<li class="breadcrumb-item"><a href="../atendentes/atendentes.php">Atendentes</a></li>
		<li class="breadcrumb-item active" aria-current="page"><? echo $inscrito?></li>
	  </ol>
	</nav>
</div>
<div class="container mt-2 p-3">
<?
include_once("include/header.php");
?>
	<div class="card mt-5">
				<?
				if ($id>0){
				?>
		<img src="<? echo $imagem?>" class="card-img-top" alt="<? echo $nome?>">
				<?
					}
				?>
		<div class="card-body">
			<div class="row">
				<div class="col-12">
				<?
				if ($id>0){
				?>
				<form class="card m-1">
					<div class="col-auto">
						<label for="formFile" class="form-label">Imagem de perfil <small>(mínimo 310x310px)</small></label>
						<input class="form-control form-control" id="formFile" type="file">
					</div>
					<div class="col-auto">
						<button type="submit" class="btn btn-primary mb-3">Enviar imagem</button>
					</div>
				</form>
				</div>
				<div class="col-12">
				<form class="card m-1">
					<div class="col-auto">
						<label for="formFile" class="form-label">Anexo <small>(arquivo PDF, DOC ou DOCX)</small></label>
						<input class="form-control form-control" id="formFile" type="file">
					</div>
					<div class="col-auto">
						<button type="submit" class="btn btn-primary mb-3">Enviar documento</button>
					</div>
				</form>
				<?
				} else {
				?>
				<div class="alert alert-warning" role="alert">
  <strong>ATENÇÃO: </strong>Após preencher e enviar o formulário abaixo, esta página se atualizará com as informações fornecidas e você poderá enviar uma foto do inscrito <small>(de preferência quadrada, com pelo menos 310x310 pixels, formato PNG ou JPG)</small> e um arquivo com o curriculo <small>(formato PDF ou DOC/DOCX)</small>.  Esses dados são opcionais. Confira as informações e, caso não seja necessária nenhuma correção, pode sair desta página. <strong>Obrigado!</strong> 
</div>
				<?
					}
/*
echo "<pre>";
print_r($candidato);
echo "</pre>";
*/
				?>
				</div>
				<div class="col-12">
<form class="border border-light p-5" action="../atendentes/card1.php" method="post">

	<div class="md-form">
	<i class="fas fa-user prefix grey-text"></i>
	<input type="text" id="nome" name="nome" class="form-control" value="<?echo $nome?>">
	<label for="nome">Nome</label>
	</div>
	<div class="md-form">
	<i class="fas fa-user prefix grey-text"></i>
	<input type="text" id="responsavel" name="responsavel" class="form-control" value="<?echo $responsavel?>">
	<label for="empresa">Responsável</label>
	</div>
	<div class="md-form">
	<i class="fas  fa-calendar prefix grey-text"></i>
<!--		<i class="fas fa-building prefix grey-text"></i>-->
	<input type="date" id="depto" name="depto" class="form-control" value="<?echo $nascimento?>">
	<label for="depto">Nascimento (dd/mm/aaaa)</label>
	</div>
<!--
	<div class="md-form">
	<i class="fas  fa-calendar prefix grey-text"></i>
	<input type="text" id="desde" name="desde" class="form-control" value="<?echo $inscricao?>">
	<label for="cargo">Inscrito desde</label>
	</div>
-->
	<div class="md-form">
	<i class="fas fa-envelope prefix grey-text"></i>
	<input type="email" id="email" name="email" class="form-control" value="<?echo $email?>">
	<label for="email">E-mail de contato</label>
	</div>

	<div class="md-form">
	<i class="fas fa-phone prefix grey-text"></i>
	<input type="tel" id="telefone" name="telefone" class="form-control" value="<? echo $telefone?>">
	<label for="telefone">Seu telefone</label>
	</div>
	<!-- Default switch -->
	<div class="custom-control custom-switch">
		<input type="checkbox" class="custom-control-input" name="whatsapp" id="whatsapp" checked>
		<label class="custom-control-label" for="whatsapp">WhatsApp</label>
	</div>
	<!-- Default checked -->
	<div class="custom-control custom-switch">
	  <input type="checkbox" class="custom-control-input" name="telegram" id="telegram">
	  <label class="custom-control-label" for="telegram">Telegram</label>
	</div>
	<div class="md-form">
	<i class="far fa-map prefix grey-text"></i>
	<input type="text" id="cep" name="cep" class="form-control" placeholder="informe seu CEP">
	<label for="cep">CEP</label>
	</div>
	<div class="md-form">
	<i class="far fa-map prefix grey-text"></i>
	<input type="text" id="endereco" name="endereco" class="form-control" placeholder="endereço" >
	<label for="endereco">Endereço <small>(1ª linha)</small></label>
	</div>
	<div class="md-form">
	<i class="far fa-map prefix grey-text"></i>
	<input type="text" id="complemento" name="complemento" class="form-control" placeholder="número, apto, casa etc." >
	<label for="complemento">Complemento - <small>(número, apto, bloco,casa etc.)</small></label>
	</div>
	<div class="md-form">
	<i class="far fa-map prefix grey-text"></i>
	<input type="text" id="cidade" name="cidade" class="form-control" placeholder="cidade" >
	<label for="cidade">Cidade</label>
	</div>
	<div class="md-form">
	<i class="far fa-map prefix grey-text"></i>
	<input type="text" id="uf" name="uf" class="form-control" placeholder="UF" >
	<label for="uf">Estado</label>
	</div>
	<hr>
	<fieldset class="form-check">
	  <div class="form-check">
		  <input type="checkbox" class="form-check-input" name="newsletter" id="newsletter"></input>
	  <label for="newsletter" class="form-check-label dark-grey-text">Quero saber das novidades</label>
		</div>
	  <div class="form-check">
	  <input type="checkbox" class="form-check-input" name="grupo_capacitacao" id="grupo_capacitacao"<? echo $boletim?>></input>
	  <label for="grupo_capacitacao" class="form-check-label dark-grey-text">Quero ser incluído(a) no grupo de capacitação para acompanhar as atividades</label>
		</div>
	  <div class="form-check">
	  <input type="checkbox" class="form-check-input" name="grupo_voluntarios" id="grupo_voluntarios"<? echo $boletim?>></input>
	  <label for="grupo_voluntarios" class="form-check-label dark-grey-text mb-3">Quero ser incluído(a) no grupo de voluntários para colaborar</label>
		</div>
	</fieldset>
    <button class="btn btn-info btn-block" type="submit">Salvar</button>
	</div>

    <div class="text-center">
        <p>ou identifique-se com:</p>

        <a type="button" class="light-blue-text mx-2">
            <i class="fab fa-facebook-f"></i>
        </a>
        <a type="button" class="light-blue-text mx-2">
            <i class="fab fa-twitter"></i>
        </a>
        <a type="button" class="light-blue-text mx-2">
            <i class="fab fa-google"></i>
        </a>
        <hr>

        <p>Clicando em 
            <strong><em>Salvar</em></strong>  acima, você estará concordando com nossos 
            <a href="../atendentes/servicos.php" target="_blank">Termos de Serviço</a> e com nossa
            <a href="../atendentes/privacidade.php" target="_blank">Política de Privacidade</a>.
        </p>
    </div>
</form>

				</div>
			</div>
		</div>
	</div>
<hr>
		</div>
	<!-- JQuery -->
	<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>

	<script type="text/javascript" src="../atendentes/js/bootstrap.min.js"></script>

	<!-- Bootstrap tooltips -->
	<script type="text/javascript" src="../atendentes/js/popper.min.js"></script>

	<!-- Bootstrap core JavaScript -->
	<script type="text/javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap4.min.js"></script>

	<!-- MDB core JavaScript -->
	<script type="text/javascript" src="../atendentes/js/mdb.min.js"></script>
    <!-- Adicionando Javascript -->
    <script>

        $(document).ready(function() {

            function limpa_formulário_cep() {
                // Limpa valores do formulário de cep.
                $("#endereco").val("");
//                $("#bairro").val("");
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
	<footer class="page-footer text-center font-small primary-color-dark darken-2 mt-4 wow fadeIn">


    <hr class="my-4">

    <!-- Social icons -->
    <div class="pb-4">
      <a href="https://www.facebook.com/projetoatendentesmuitoespeciais" target="_blank">
        <i class="fab fa-facebook-f mr-3"></i>
      </a>

      <a href="https://twitter.com/projetoameorg" target="_blank">
        <i class="fab fa-twitter mr-3"></i>
      </a>

      <a href="https://www.youtube.com/channel/UCqTfVItS3lDOJhCeQVPkB3w" target="_blank">
        <i class="fab fa-youtube mr-3"></i>
      </a>

    </div>
    <!-- Social icons -->

    <!--Copyright-->
    <div class="footer-copyright py-3">
      © 2016-2023 Copyright:
      <a href="https://projetoame.org" target="_blank"> Projeto Atendentes Muito Especiais - A.M.E. </a>
    </div>
    <!--/.Copyright-->

  </footer>
  <!--/.Footer-->
	</body>
</html>