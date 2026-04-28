<?PHP
//	session_start();
	setlocale (LC_ALL, 'pt_BR.utf-8');
    date_default_timezone_set('America/Sao_Paulo');
//	include_once("./include/conexao.php");
//	include_once("./include/funcoes.php");
//     include_once('./include/head.php');
	$nome = "";
	$mensagem = "";
	$logado = "none";
	$login = "visible";
	$foto = "/img/ms-icon-310x310.png";
	$perfil = "";
	if (isset($_GET['r'])){
		$query = "SELECT * FROM candidatos WHERE candidato_id = ".$_GET['r'];
		$resp = mysqli_query($conexao,$query);
		$row2 = mysqli_fetch_array($resp);
		$atendente = $row2['nome'];
		$mensagem = "Atendente: ".$atendente;
		$querymax = "SELECT max(rodizio) AS max FROM candidatos";
		$resp=mysqli_query($conexao,$querymax);
		$row3 = mysqli_fetch_array($resp);
		$max = $row3['max'];
		$mensagem .= " de ".$max. " para ". ($max+1);
		$max++;
		$querymax = "UPDATE candidatos SET rodizio = ".$max. " WHERE candidato_id = ".$_GET['r'];
		$mensagem .= "<br>".$querymax;
		$resp = mysqli_query($conexao,$querymax);
	}
	if (isset($_GET['a'])){
		$query = "UPDATE candidatos SET ativo = true WHERE candidato_id = ".$_GET['a'];
		$resp = mysqli_query($conexao,$query);
		$query = "SELECT * FROM candidatos WHERE candidato_id = ".$_GET['a'];
		$resp = mysqli_query($conexao,$query);
		$row2 = mysqli_fetch_array($resp);
		$atendente = $row2['nome'];
		$mensagem = "Atendente: ".$atendente . " APROVADO!";
	}
	if (isset($_GET['t'])){
		$query = "UPDATE candidatos SET ativo = false WHERE candidato_id = ".$_GET['t'];
		echo $query;
		$resp = mysqli_query($conexao,$query);
		$query = "SELECT * FROM candidatos WHERE candidato_id = ".$_GET['t'];
		$resp = mysqli_query($conexao,$query);
		$row2 = mysqli_fetch_array($resp);
		$atendente = $row2['nome'];
		$mensagem = "Atendente: ".$atendente . " EM TREINAMENTO!";
	}
if (isset($_SESSION['usuario'])){
		$nome = $_SESSION['usuario'];
		$perfil = $_SESSION['perfil'];
		$logado = "visible";
		$login = "none";
	}
/*
	if (isset($_SESSION['perfil'])){
		$foto = $_SESSION['perfil'];
	}
*/
$query = "SELECT * FROM candidatos WHERE ativo = 1 ORDER BY rodizio";
$rodizio = mysqli_query($conexao,$query);
$query = "SELECT * FROM candidatos WHERE ativo = 0 ORDER BY rodizio";
$treinamento = mysqli_query($conexao,$query);
?>
<!DOCTYPE html>
<html lang="pt-br">
<?php
//include_once("./include/header.php");
?>
<body>
<div class="container p-1">
	<header class="mt-5 p-2 justify-content-md-center">
		<!--<img src="img/logo-brown.png" class="img-thumbnail">-->
<h1 class="mt-5 text-center">ATENDENTES MUITO ESPECIAIS</h1>
  <nav class="navbar navbar-expand-lg navbar-light bg-light rounded">
<!--    <a class="navbar-brand" href="#">Navbar</a>-->
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample09" aria-controls="navbarsExample09" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarsExample09">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item">
          <a class="nav-link" href="/home">Home <span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/atendentes">Próximos eventos</a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="#" tabindex="-1" aria-disabled="true">Rodizio</a>
        </li>
<!--
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="dropdown09" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dropdown</a>
          <div class="dropdown-menu" aria-labelledby="dropdown09">
            <a class="dropdown-item" href="#">Action</a>
            <a class="dropdown-item" href="#">Another action</a>
            <a class="dropdown-item" href="#">Something else here</a>
          </div>
        </li>
-->
      </ul>
		<?php
		if (isset($_SESSION['nivel']) && $_SESSION['nivel']>4){
		?>
		<a href="/logout"><div class="btn p-2 rounded" data-toggle="tooltip" data-placement="right" title="Clique para sair"><?php echo $nome?><img clas='img ms-4' src="/<?php echo $perfil?>" width="32" alt="<?php echo $nome?>"></div></a>
		
		<?php
		} else {
		?>
<a class="form-inline" href='/login'>Login</a>
		<?php
		}
		?>
<!--
      <form class="form-inline my-2 my-md-0">
        <input class="form-control" type="text" placeholder="Search" aria-label="Search">
      </form>
-->
    </div>
  </nav>
<!--
<nav class="nav-bar navbar-expand-lg" aria-label="breadcrumb">
  <ul class="breadcrumb">
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item"><a href="/atendentes">Próximos eventos</a></li>
    <li class="breadcrumb-item active" aria-current="page">Rodízio</li>
<a class="form-inline" href='/login'>Login</a>
  </ul>
</nav>
-->
	</header>
<main class="container">
<form class="border border-light p-2" method="post">
	<div class="row">
		<div class="col-12">
			<div class="card mb-2 p-1">
				<div class="card-header"><h1>RODÍZIO</h1>
		<?php
			if($mensagem<>""){
			echo "<span class='alert'><p>". $mensagem . "</p></span>";
			}
		?>
				</div>
				<div class="card-body">
<ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="home-tab" data-toggle="tab" data-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Atendentes</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="profile-tab" data-toggle="tab" data-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Treinamento</button>
  </li>
</ul>
<div class="tab-content" id="myTabContent">
	<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
		<table class="table table-hover">
			<thead class="blue-gradient text-white">
			<th>pos.#</th><th>Atendente</th>
				<?php
				if ($nome<>""){
				?>
				<th>Ação</th>
				<?php
				}
				?>
			</thead>
			</tbody>
		<?php
		$i = 1;
			while ($row1 = mysqli_fetch_array($rodizio)){
		?>
				<tr>
				<td><?php echo $i ?></td><td><?php echo $row1['nome'] ?>
				</td>
				<?php
				if ($nome<>""){
				?>
				<td><a href="./?r=<?php echo digitos($row1['candidato_id'])?>" class="btn btn-sm rounded-pill btn-primary">Rodar</a><a href="./?t=<?php echo digitos($row1['candidato_id'])?>" class="btn btn-sm rounded-pill btn-warning ml-1">Treinar</a></td>
				<?php
				}
				?>
				</tr>
		<?php
			$i++;
		}
		?>
		</tbody>
		</table>
	</div>
	<div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
		<table class="table table-hover">
			<thead class="blue-gradient text-white">
				<th>pos.#</th><th>Treinandos</th>
				<?php
				if ($nome<>""){
				?>
				<th>Ação</th>
				<?php
				}
				?>
			</thead>
			</tbody>
		<?php
			while ($row = mysqli_fetch_array($treinamento)){
		?>
				<tr>
				<td><?php echo $i ?></td><td><?php echo $row['nome'] ?>
				</td>
				<?php
				if ($nome<>""){
				?>
				<td><a href="./?r=<?php echo digitos($row['candidato_id'])?>" class="btn btn-sm rounded-pill btn-primary mr-1">Rodar</a><a href="./?a=<?php echo digitos($row['candidato_id'])?>" class="btn btn-sm rounded-pill btn-success ml-1">Aprovar</a></td>
				<?php
				}
				?>
				</tr>
		<?php
			$i++;
		}
		?>
		</tbody>
		</table>
	</div>
</div>
					

<!--				<small>marque sua disponibilidade abaixo e forneça seus dados</small></div>-->
		</div>
		</div>
		</div>
      <hr>
		</div>
	</form>
	</main>
	</div>
	<script src="https://kit.fontawesome.com/d067a28273.js" crossorigin="18592B3C-6385-48D7-8605-5E63E600000B"></script>
	<!-- JQuery -->
	<script type="text/javascript" src="./js/jquery-3.4.1.min.js"></script>
	<!-- Bootstrap tooltips -->
	<script type="text/javascript" src="./js/popper.min.js"></script>
	<!-- Bootstrap core JavaScript -->
	<script type="text/javascript" src="./js/bootstrap.min.js"></script>
	<!-- MDB core JavaScript -->
	<script type="text/javascript" src="./js/mdb.min.js"></script>
	<!-- SCRIPTS -->
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
