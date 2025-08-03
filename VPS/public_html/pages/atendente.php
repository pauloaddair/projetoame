<?php
include_once('./include/conexao.php');
include_once('./include/funcoes.php');
$titulo = "Edição de candidato";
include_once('./include/head.php');
$id = 0;
$campos_editar = array (
	'nome'=>'nome do atendente',
	'responsavel'=>'nome do responsável',
	'Email'=>'e-mail do responsável',
	'Telefone'=>'telefone de contato',
	'Nascimento'=>'data de nascimento',
	'RG'=>'RG do atendente',
	'CPF'=>'CPF do atendente',
	'CPF_RESP'=>'CPF do responsável',
	'CEP'=>'CEP do endereço',
	'endereco'=>'endereço',
	'complementto'=>'número, bloco, apto etc.',
	'cidade'=>'Cidade',
	'UF'=>'Estado'
);
if (array_key_exists(1,$parametros)){
	$id = intval($parametros[1]);
}
if($_SERVER['REQUEST_METHOD']=="POST"){
	if ($id>0){
		$query = "UPDATE `candidatos` 
		SET `usuario_id`='".$_POST['usuario_id']."',
			`nome`='".$_POST['nome']."',
			`responsavel`='".$_POST['responsavel']."',
			`Email`='".$_POST['Email']."',
			`Telefone`='".$_POST['Telefone']."',
			`Nascimento`='".$_POST['Nascimento']."',
			`RG`='".$_POST['RG']."',
			`CPF`='".$_POST['CPF']."',
			`CPF_RESP`='".$_POST['CPF_RESP']."',
			`CEP`='".$_POST['CEP']."',
			`endereco`='".$_POST['endereco']."',
			`complemento`='".$_POST['complemento']."',
			`cidade`='".$_POST['cidade']."',
			`UF`='".$_POST['UF']."',
			`imagem_id`='".$_POST['imagem_id']."',
			`data_inscricao`='".$_POST['data_inscricao']."',
			WHERE candidato_id = ".$id;				
	} else {
		$query = "INSERT INTO `usuarios`(`login`, `email`, `telefone`, `nome`, `nivel`) VALUES (".slugify($_POST['nome']).",'" . $_POST['Email'] . "','" . telephone($_POST['Telefone']) ."','" . $_POST['nome']."',1)";
		$resp = mysqli_query($conexao,$query);
		$usuario_id = $conexao -> insert_id;

		$query = "INSERT INTO `candidatos`(`usuario_id`, `nome`, `responsavel`, `Email`, `Telefone`, `Nascimento`, `RG`, `CPF`, `CPF_RESP`, `CEP`, `endereco`, `complemento`, `cidade`, `UF`, `imagem_id`, `data_inscricao`, `IP`) 
		VALUES ('". $usuario_id."','" . $_POST['nome']."','" . $_POST['responsavel']."','" . $_POST['Email']."','" . $_POST['Telefone']."','" . $_POST['Nascimento']."','" . $_POST['RG']."','" . $_POST['CPF']."','" . $_POST['CPF_RESP']."','". $_POST['CEP']."','" . $_POST['endereco']."','" . $_POST['complemento']."','" . $_POST['cidade']."','" . $_POST['UF']. $_POST['imagem_id']."','" . $_POST['IP']."')";
}
//	echo $query . "<br>";
//	exit;
		$resp = mysqli_query($conexao,$query);	
}
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
<?php
			$query = "SELECT candidatos.*,imagens.url AS perfil 
			FROM candidatos, imagens 
			WHERE candidatos.imagem_id = imagens.imagem_id 
			AND candidato_id = ".$id;
			$msg = "Candidato não encontrado";
			$resp = mysqli_query($conexao,$query);
				$row = mysqli_fetch_assoc($resp);
				if ($row) {
					$perfil = "/".$row['perfil'];
					$telefone = formataWA($row['Telefone']);
					$query = "SELECT usuario_id FROM usuarios WHERE telefone = '".$telefone."'";
					$t = mysqli_query($conexao,$query);
					$row1 = mysqli_fetch_assoc($t);
					$usuario_id=0;
					if($row1){
						$usuario_id = $row1['usuario_id'];
					}
?>
				<div class="card">
				<div class="card-header">
					<a href="/trocafoto/<? echo digitos($id)?>"><img class="card-img-top img-thumbnail" src="<? echo $perfil?>"></a>
					<h1 class="text-center"><? echo $row['nome']?></h1>
				</div>
					<div class="card-body">
						<form method="post">
							<input type="hidden" id="candidato_id" name="candidato_id" value="<? echo $id?>">
							<input type="hidden" id="usuario_id" name="usuario_id" value="<? echo $usuario_id?>">
						<div class="md-form">
							<i class="far fa-map prefix grey-text"></i>
							<input type="text" id="usuario_id0" name="usuario_id0" class="form-control" placeholder="código usuário" value="<? echo $usuario_id?>" disabled>
							<label for="usuario_id">Usuário ID</label>
						</div>
					<?
					// Loop através de todas as colunas e seus valores
					foreach ($row as $campo => $valor) {
						if ($campo <>"perfil"  && $campo <> "candidato_id" && $campo <> "usuario_id"){
						?>
						<div class="md-form">
							<i class="far fa-map prefix grey-text"></i>
							<input type="text" id="<? echo $campo?>" name="<? echo $campo?>" class="form-control" placeholder="<? echo $campo?>" value="<? echo $valor?>">
							<label for="cep"><? echo $campo?></label>
						</div>
						<?
						}
					}
					?>
						<div class="md-form">
						<button class="btn btn-block btn-pill btn-primary" type="submit">Enviar</button>
						</div>
						</form>
					<?		   
} else {
//    echo "Nenhum registro encontrado.";
// Nome da tabela
$tabela = 'candidatos';

// Consulta SQL para obter a estrutura da tabela
$sql = "SHOW COLUMNS FROM $tabela";
$resultado = mysqli_query($conexao,$sql);

if (mysqli_num_rows($resultado) > 0) {
	$perfil = "/img/profile.png";
?>
	<div class="card">
		<div class="card-header">
			<img class="card-img-top img-thumbnail" src="<? echo $perfil?>">
			<h1 class="text-center">Novo candidato</h1>
		</div>
		<div class="card-body">
			<form method="post">
			<input type="hidden" id="candidato_id" name="candidato_id" value="0">
<?
    while ($row = $resultado->fetch_assoc()) {
				$campo = $row['Field'];
				$valor = "";
		echo $campo . "<br><pre>";
		print_r($campos_editar);
		echo "</pre>";
		exit;
				if (array_key_exists($campo,$campos_editar)){
				if ($campo =="IP"){
					$valor = $_SERVER['REMOTE_ADDR'];
				}
				if ($campo =="data_inscricao"){
					$valor = date("Y/m/d H:i");
				}
				if ($campo <> "candidato_id" && $campo <> "usuario_id"){
		?>
					<div class="md-form">
						<i class="far fa-map prefix grey-text"></i>
						<input type="text" id="<? echo $campo?>" name="<? echo $campo?>" class="form-control" placeholder="<? echo $campos_editar($campo)?>" value="<? echo $valor?>">
						<label for="cep"><? echo $campo?></label>
					</div>
		<?
		   }
			
		}
	}
			?>
				<div class="md-form">
				<button class="btn btn-block btn-pill btn-primary" type="submit">Enviar</button>
				</div>
				</form>
			<?		   
} else {
    echo "Nenhum campo encontrado na tabela.";
}
}
/*
echo $url . "<br>";
echo "<pre>";
print_r($parametros) . "<br>";
echo "<pre>";
*/
?>
					</div>
				</div>
			</div>
		</div>
			 
	</div>
<?	
include_once('./include/footer.php');
?>
	
</body>
<?php
include_once('./include/scripts.php');
?>
	<script>
	// JavaScript Document
	$(document).ready(function () {
	  // Send Search Text to the server
	  $("#busca").keyup(function () {
		let searchText = $(this).val();
		if (searchText != "") {
		  $.ajax({
			url: "/pages/action.php",
			method: "post",
			data: {
			  query: searchText,
			},
			success: function (response) {
			  $("#show-list").html(response);
			},
		  });
		} else {
		  $("#show-list").html("");
		}
	  });
	  // Set searched text in input field on click of search button
	  $(document).on("click", "a", function () {
		$("#search").val($(this).text());
		$("#show-list").html("");
	  });
	});
	</script>
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
