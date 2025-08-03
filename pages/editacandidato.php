<?php
include_once('./include/conexao.php');
include_once('./include/funcoes.php');
$titulo = "Edição de candidato";
$id = 0;
$usuario_id = 0;
$campos = array(
	array("candidato_id",0,"ID:","Código do candidato",'hidden','','',"far fa-user"),
	array("usuario_id",0,"Usuário:","Código do usuário principal",'hidden','','',"far fa-user"),
	array("nome",'',"Nome:","nome do usuário principal",'text','','required',"far fa-user"),
	array("inscrito",'',"Tipo inscrição:","Candidato ou voluntário",'option','atendente;voluntário','',"far fa-edit"),
	array("responsavel",'',"Responsável:","Nome do responsável",'text','','',"far fa-user"),
	array("curatela",'',"Curatela:","forneça o tipo de curatela, se houver",'option','Escolha a opção (não informado);Curatela total;parcial;sem curatela','',"far fa-group"),
	array("genero",'',"Gênero:","informe o gênero",'option','masculino;feminino;não informado','',"far fa-user"),
	array("Email",'',"E-mail:","forneça o e-mail",'email','','required',"far fa-envelope"),
	array("Telefone",'',"Celular:","telefone com DDD",'tel','','required',"fa fa-phone"),
	array("WZ",0,"WhatsApp:","celular é WhatsApp?",'switch',';on','',"far fa-whatsapp"),
	array("telegram",0,"Telegram:","celular é Telegram?",'switch',';on','',"far fa-telegram"),
	array("Nascimento",'',"Data nascimento:","data de nascimento",'date','','required',"far fa-calendar"),
	array("RG",'',"RG:","RG do atendente",'text','','',"far fa-edit"),
	array("CPF",'',"CPF:","CPF do atendente",'text','','required',"far fa-edit"),
	array("CPF_RESP",0,"CPF do responsável:","CPF do responsável",'text','','required',"far fa-edit"),
	array("PIX",'',"PIX:","chave PIX para pagamentos",'text','','',"fa fa-dollar-sign"),
	array("camisa",'',"Camisa:","tamanho da camisa",'text','','',"far fa-user"),
	array("calca",'',"Calça:","tamanho da calça",'text','','',"far fa-user"),
	array("sapato",'',"Calçado:","número do calçado",'text','','',"far fa-user"),
	array("CEP",'',"CEP:","CEP do endereço residencial",'text','','',"far fa-map"),
	array("endereco",'',"Endereço:","forneça o endereço",'text','','',"far fa-map"),
	array("complemento",'',"Complemento:","número, casa, apartamento etc.",'text','','',"far fa-map"),
	array("cidade",'',"Cidade:","forneça a cidade",'number','','',"far fa-map"),
	array("UF",'',"Estado:","estado",'number','','',"far fa-map"),
	array("mensagem",'',"Mensagem:","se quiser, deixe uma mensagem",'textarea','','',"far fa-edit"),
	array("imagem_id",0,"Imagem:","código da imagem",'number','','',"far fa-image"),
	array("anexo",'',"Anexo:","último anexo enviado",'number','','',"far fa-file"),
	array("anexo_id",0,"ID do anexo:","identificação do anexo",'hidden','','',"far fa-file"),
	array("certificado",0,"Certificado:","é certificado?",'option','atendente certificado;em treinamento','',"far fa-file"),
	array("ativo",0,"Ativo","inscrição ativa",'option','cadastro ativo;inativo','',"far fa-user"),
	array("rodizio",0,"Rodizio","ordem no rodizio",'number','','',"fa fa-users"),
	array("data_inscricao",0,"Data:","data da inscrição",'number','','',"far fa-calendar"),
	array("boletim",0,"Boletim:","enviar boletim?",'switch','sim;não','',"far fa-map"),
	array("grupo_capacitacao",0,"Grupo Capacitação?","incluir no Grupo Capacitação?",'switch',';on','',"far fa-map"),
	array("grupo_voluntarios",0,"Grupo Voluntários?","incluir no Grupo Voluntários?",'switch',';on','',"far fa-map"),
	array("IP",0,"IP","IP do usuário",'hidden','','')
	);
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

if (isset($_SESSION['nivel']) && $_SESSION['nivel'] >= 3) {

if (isset($_POST['buscar'])){
	$query = "SELECT candidato_id FROM candidatos WHERE nome LIKE '".$_POST['buscar']."'";
	$resp = mysqli_query($conexao,$query);
	if (mysqli_num_rows($resp)>0){
		$row = mysqli_fetch_assoc($resp);
		header('Location: /editacandidato/'.digitos($row['candidato_id']));
	}
}
include_once('./include/head.php');
$msg="";
$id = 0;
if (array_key_exists(1,$parametros)){
	$id = intval($parametros[1]);
}
$imgperfil = "/img/profile.png";
	if ($id>0){
		$query = "SELECT candidatos.*,imagens.url AS perfil 
		FROM candidatos
		LEFT JOIN imagens ON candidatos.imagem_id = imagens.imagem_id 
		WHERE candidato_id = ".$id;
		echo $query."<br>";
		$msg .= "<br>Candidato não encontrado";
		$resp = mysqli_query($conexao,$query);
		$row = mysqli_fetch_assoc($resp);
		if ($row) {
			foreach ($row as $c=>$v){
				$valores[$c] = $v;
			}
			if (!is_null($row['perfil'])){
				$imgperfil = "/".$row['perfil'];
			}
			$telefone = formataWA($row['Telefone']);
			$query = "SELECT usuario_id FROM usuarios WHERE telefone = '".$telefone."'";
			$t = mysqli_query($conexao,$query);
			$row1 = mysqli_fetch_assoc($t);
			$usuario_id=0;
			if($row1){
				$usuario_id = $row1['usuario_id'];
			}
		}
	}
if($_SERVER['REQUEST_METHOD']=="POST"){
/*
	echo "<pre>";
	print_r($_POST);
	echo "</pre>";
*/
	foreach ($campos as $campo) {
		if (isset($_POST[$campo[0]])) {
			if ($campo[4]=="switch"){
				$valores[$campo[0]]=0;
				if ($valores[$campo[0]]=="on"){
					$valores[$campo[0]]=1;
				}
//				echo "valor=".$campo[0]."=".$valores[$campo[0]]."<br>";
			}
			$valores[$campo[0]]= $_POST[$campo[0]];	
		}		
	}
/*
	echo "<pre>";
	print_r($valores);
	echo "</pre>";
*/
	$id = $valores['candidato_id'];
	$target_dir = "./docs/";
	$target_url = "docs/";
	if(basename($_FILES["arquivo"]["name"])){
		$target_file = $target_dir . basename($_FILES["arquivo"]["name"]);
		$target_url .= basename($_FILES["arquivo"]["name"]);
		$uploadOk = 1;
		$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));			
		if (file_exists($target_file)) {
		//  echo "Sorry, file already exists.";
			chmod($target_file,0755); //Change the file permissions if allowed
			unlink($target_file); //remove the file
		//  $uploadOk = 0;
		}

		// Check file size
		if ($_FILES["arquivo"]["size"] > 5000000) {
			$msg .= "Infelizmente seu arquivo é muito grande.";
			$uploadOk = 0;
		}

		// Allow certain file formats
			if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
			&& $imageFileType != "gif"  && $imageFileType != "pdf" && $imageFileType != "doc" && $imageFileType != "docx") {
			$msg .= "Apenas JPG, JPEG, PNG, GIF e PDF podem ser enviados.";
			$uploadOk = 0;
		}

		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			$msg .= "Infelizmente, o arquivo não foi enviado.";
			// if everything is ok, try to upload file
			} else {
			if (move_uploaded_file($_FILES["arquivo"]["tmp_name"], $target_file)) {
			$msg .= "O arquivo ". htmlspecialchars( basename( $_FILES["arquivo"]["name"])). " foi enviado com sucesso.";
				$nome_arquivo ="";
				if(isset($_POST['nome_arquivo'])){
					$nome_arquivo = $_POST['nome_arquivo'];
				}
				$querydoc = "INSERT INTO `documentos`(`candidato_id`, `url`,`descritivo`) VALUES ('".$id."','".$target_url."','".$nome_arquivo."')";
				$resp = mysqli_query($conexao,$querydoc);
			} else {
			$msg .= "Infelizmente houve um erro no envio do seu arquivo. Tente novamente mais tarde.";
			}
		}
	}
	if ($id>0){
		$query = "UPDATE `candidatos`
		SET "; 
		foreach ($campos as $campo) {
			$query .=  "`". $campo[0]."`='".$valores[$campo[0]]."',";				
		};
		$query = substr($query,0,-1);
		$query .= " WHERE candidato_id = ".$id;	
	} else {
		$query = "INSERT INTO `candidatos` ("; 
		foreach ($campos as $campo) {
				$query .=  "`". $campo[0]."`='"."',";	
		};
		$query = substr($query,0,-1);
		$query .= ")
		VALUES (";
		foreach ($campos as $campo) {
			$query .= "'". $valores[$campo[0]]."',";	
		};
		$query = substr($query,0,-1);

		$query1 = "INSERT INTO `usuarios`(`login`, `email`, `telefone`, `nome`, `nivel`) VALUES ('" . $valores['Email']."','" . $valores['Email']."','" . formataWA($valores['Telefone'])."','" . $valores['nome']."','1')";
		echo $query1 ."<br>";
//		$resp = mysqli_query($conexao,$query1);
//		$usuario_id = $conexao -> insert_id;
		$usuario_id = 1;

/*
		$query = "INSERT INTO `candidatos`(`usuario_id`, `inscrito`, `nome`, `responsavel`, `curatela`, `Email`, `Telefone`, `genero`, `Nascimento`, `RG`, `CPF`, `CPF_RESP`, `PIX`, `camisa`, `calca`, `sapato`, `CEP`, `endereco`, `complemento`, `cidade`, `UF`, `mensagem`, `imagem_id`, `anexo`, `anexo_id`, `certificado`, `rodizio`, `data_inscricao`, `WZ`, `telegram`, `boletim`, `grupo_capacitacao`, `grupo_voluntarios`, `IP`) 
		VALUES ('". $usuario_id."','candidato','" . $valores['nome']."','" . $valores['responsavel']."','" . $valores['curatela']."','" . $valores['Email']."','" . $valores['Telefone']."','" . $valores['genero']."','" . $valores['Nascimento']."','" . $valores['RG']."','" . $valores['CPF']."','" . $valores['CPF_RESP']."','" . $valores['PIX']."','" . $valores['camisa']."','" . $valores['calca']."','" . $valores['sapato']."','" . $valores['CEP']."','" . $valores['endereco']."','" . $valores['complemento']."','" . $valores['cidade']."','" . $valores['UF']."','" . $valores['mensagem']."','" . $valores['imagem_id']."','" . $valores['anexo']."','" . $valores['anexo_id']."','" . $valores['certificado']."','" . $valores['rodizio']."','" . $valores['data_inscricao']."','" . $valores['WZ']."','" . $valores['telegram']."', '" . $valores['boletim'] . "','" . $valores['grupo_capacitacao']."','" . $valores['grupo_voluntarios']."' ,'" . $valores['IP']."')";
*/
	}
/*
		echo $query . "<br>";
		exit;
*/
		$resp = mysqli_query($conexao,$query);	

}
?>
	<style>
		.hidden {
			display: none;
		}
	</style>
    <style>
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
<?php include_once('./include/nav.php');
?>
	<div class="container">
		<header class="mt-5 p-2 justify-content-md-center">
			<h1 class="text-center">Edição de candidato</h1>
			<p  class="text-center"><small><?php echo $msg?></small></p>
			<div class="row justify-content-between bg-light p-1 rounded">
				<div class="col">
					<nav aria-label="breadcrumb bg-transparent">
				  <ol class="breadcrumb bg-transparent">
					<li class="breadcrumb-item"><a href="/atendentes">Atividades</a></li>
					<li class="breadcrumb-item"><a href="/casting">Atendentes</a></li>
					<li class="breadcrumb-item active" aria-current="page">Editando candidato</li>
				  </ol>
				</nav>
				</div>
				<div class="col  col-auto">
					<form class="form-inline" method="post">
						<label for="exampleDataList" class="form-label">Buscar atendente</label>
						<input class="form-control" list="datalistOptions" id="exampleDataList" name="buscar" placeholder="Digite para buscar...">
						<datalist id="datalistOptions">
							<?php $querynomes = "SELECT candidato_id,nome FROM candidatos ORDER BY nome;";
							$respnome = mysqli_query($conexao,$querynomes);
							while ($row = mysqli_fetch_assoc($respnome)){
								?>
								<option value="<?php echo $row['nome']?>">
								<?php }
							?>
						</datalist>
						<button class="btn btn-sm rounded-pill" type="submit">buscar</button>
					</form>
				</div>
			</div>
		</header>
		<div class="row justify-content-center">
			<div class="col">
				<div class="card">
				<div class="card-header">
					<h1 class="text-center"><?php echo $valores['nome']?></h1>
					<div class="image-container">
						<a href="/trocafoto/<?php echo digitos($id)?>">
						<img class="card-img-top object-fit-scale img-thumbnail" src="<?php echo $imgperfil?>" height="100vw"></a></div>
				</div>
					<div class="card-body">
						<form method="post" enctype="multipart/form-data">
							<input type="hidden" id="candidato_id" name="candidato_id" value="<?php echo $id?>">
							<input type="hidden" id="usuario_id" name="usuario_id" value="<?php echo $usuario_id?>">
<!--
						<div class="md-form">
							<i class="far fa-map prefix grey-text"></i>
							<input type="text" id="usuario_id0" name="usuario_id0" class="form-control" placeholder="código usuário" value="<?php echo $usuario_id?>" disabled>
							<label for="usuario_id">Usuário ID</label>
						</div>
-->
					<?php $j = 0;
					echo "<div id='section1' class='p-2 border'>";
					// Loop através de todas as colunas e seus valores
					foreach ($campos as $campo => $valor) {
						switch ($valor[4]){
							case "switch":
							?>
								<div class="custom-control custom-switch">
<!--							<i class="far fa-map prefix grey-text"></i>-->
								  <input class="custom-control-input" type="checkbox" role="switch" id="<?php echo $valor[0] ?>" name="<?php echo $valor[0] ?>" 
										 <?php if ($valores[$valor[0]]==1){
											echo "value='on' ";
										}
										echo $valor[6];
										 ?>>
								  <label class="custom-control-label" for="<?php echo $valor[0] ?>"><?php echo $valor[2] ?></label>
								</div>
							<?php break;
							case "option":
								$itens = explode(";",$valor[5]);
								$i=0;
								$selected = "selected";
							?>
							<i class="far fa-map prefix grey-text"></i>
								  <label class="form-check-label" for="<?php echo $valor[0] ?>"><?php echo $valor[2] ?></label>
								<select class="form-select" name="<?php echo $valor[0] ?>" id="<?php echo $valor[0] ?>" aria-label="<?php echo $valor[0] ?>">
									<?php foreach($itens as $item){
									?>
								  <option <?php echo $selected?> value="<?php echo $i?>"><?php echo $item?></option>
									<?php $i++;
										$selected="";
									}
									?>
								</select>
							<hr>
							<?php break;
							case "textarea":
							?>
								<div data-mdb-input-init class="form-outline">
								  <textarea class="form-control" name="<?php echo $valor[0] ?>" id="<?php echo $valor[0] ?>" rows="4" <?php echo $valor[6] ?>><?php echo $valores[$valor[0]] ?></textarea>
								  <label class="form-label" for="<?php echo $valor[0] ?>"><?php echo $valor[2] ?></label>
								</div>
							<?php break;
							default:
								if($valor[4]<>"hidden"){
							?>
								<div class="md-form">
									<i class="<?php echo $valor[7] ?> prefix grey-text"></i>
									<input type="<?php echo $valor[4] ?>" maxlength="64" class="form-control" id="<?php echo $valor[0] ?>" name="<?php echo $valor[0] ?>" placeholder="<?php echo $valor[3] ?>" value="<?php echo $valores[$valor[0]] ?>" <?php echo $valor[6] ?>>
								  <label class="form-label" for="<?php echo $valor[0] ?>"><?php echo $valor[2] ?></label>
								</div>
							<?php } else {
									?>
									<input type="<?php echo $valor[4] ?>" maxlength="64" class="form-control" id="<?php echo $valor[0] ?>" name="<?php echo $valor[0] ?>" placeholder="<?php echo $valor[3] ?>" value="<?php echo $valores[$valor[0]] ?>" <?php echo $valor[6] ?>>
							<?php }
						}
						$j++;
						if ($j==12){
							echo "</div><div id='section2' class='p-2 border hidden'>";
						}
						}
							?>
							</div>
						<button class="btn btn-sm btn-pill btn-success rounded-pill" type="button" id="toggleButton">MAIS</button>
							<p><small>Documento (opcional)</small></p>
						<div class="md-form">
							<i class="far fa-file prefix grey-text"></i>
							<input type="text" maxlength="64" class="form-control" id="nome_arquivo" name="nome_arquivo" placeholder="descrição do arquivo enviado">
							<input type="file" id="arquivo" name="arquivo" class="form-control">
						</div>
						<div class="md-form">
						<button class="btn btn-block btn-pill btn-primary" type="submit" id="submit" name="submit">Enviar</button>
						</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
					<?php include_once('./include/footer.php');
} else {
// Usuário não tem permissão, redirecione ou exiba uma mensagem de erro
	include_once('include/conexao.php');
//    echo "Você não tem permissão para acessar esta página.<a href='/login'>Login</a>";
	include_once('include/head.php');
	include_once('pages/restrito.php');
}
?>	
</body>
<?php
include_once('./include/scripts.php');
?>
    <script>
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
<?php include_once('./include/end.php');
?>
