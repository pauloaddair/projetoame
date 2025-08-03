<?php
include_once('./include/conexao.php');
include_once('./include/funcoes.php');
$titulo = "Edição de candidato";
$campos = array(
	array("candidato_id",0,"ID:","Código do candidato",'number','',''),
	array("usuario_id",0,"Usuário:","Código do usuário principal",'number','',''),
	array("inscrito",0,"Tipo inscrição:","Candidato ou voluntário",'text','',''),
	array("nome",0,"Nome:","Código do usuário principal",'number','','required'),
	array("responsavel",0,"Responsável:","Nome do responsável",'number','',''),
	array("curatela",0,"Curatela:","forneça o tipo de curatela, se houver",'option','Escolha a opção (não informado);Curatela total;parcial;sem curatela',''),
	array("Email",0,"E-mail:","forneça o e-mail",'email','','required'),
	array("Telefone",0,"Celular:","telefone com DDD",'tel','','required'),
	array("genero",0,"Gênero:","informe o gênero",'number','option','masculino;feminino;não informado',''),
	array("Nascimento",0,"Data nascimento:","data de nascimento",'date','','required'),
	array("RG",0,"RG:","RG do atendente",'text','',''),
	array("CPF",0,"CPF:","CPF do atendente",'text','','required'),
	array("CPF_RESP",0,"CPF do responsável:","Código do usuário principal",'text','','required'),
	array("PIX",0,"PIX:","chave PIX para pagamentos",'text','',''),
	array("camisa",0,"Camisa:","tamanho da camisa",'text','',''),
	array("calca",0,"Calça:","tamanho da calça",'text','',''),
	array("sapato",0,"Calçado:","número do calçado",'text','',''),
	array("CEP",0,"CEP:","CEP do endereço residencial",'text','',''),
	array("endereco",0,"Endereço:","forneça o endereço",'text','',''),
	array("complemento",0,"Complemento:","número, casa, apartamento etc.",'number','',''),
	array("cidade",0,"Cidade:","forneça a cidade",'number','',''),
	array("UF",0,"Estado:","estado",'number','',''),
	array("mensagem",0,"Mensagem:","se quiser, deixe uma mensagem",'number','',''),
	array("imagem_id",0,"Imagem:","código da imagem",'number','',''),
	array("anexo",0,"Anexo:","anexo enviado",'number','',''),
	array("anexo_id",0,"ID do anexo:","identificação do anexo",'number','',''),
	array("certificado",0,"Certificado:","é certificado?",'number','',''),
	array("ativo",0,"Ativo","inscrição ativa",'number','',''),
	array("rodizio",0,"Rodizio","ordem no rodizio",'number','',''),
	array("data_inscricao",0,"Data:","data da inscrição",'number','',''),
	array("WZ",0,"WhatsApp:","celular é WhatsApp?",'switch','sim;não',''),
	array("telegram",0,"Telegram:","celular é Telegram?",'switch','sim;não',''),
	array("boletim",0,"Boletim:","enviar boletim?",'switch','sim;não',''),
	array("grupo_capacitacao",0,"Grupo Capacitação?","incluir no Grupo Capacitação?",'switch','sim;não',''),
	array("grupo_voluntarios",0,"Grupo Voluntários?","incluir no Grupo Voluntários?",'switch','sim;não',''),
	array("IP",0,"IP","IP do usuário",'text','','')
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
/*
$candidato [`inscrito`]=$_POST['inscrito'];
$candidato [`nome`]=$_POST['nome'];
$candidato [`responsavel`]=$_POST['responsavel'];
$candidato [`curatela`]=$_POST['curatela'];
$candidato [`Email`]=$_POST['Email'];
$candidato [`Telefone`]=$_POST['Telefone'];
$candidato [`genero`]=$_POST['genero'];
$candidato [`Nascimento`]=$_POST['Nascimento'];
$candidato [`RG`]=$_POST['RG'];
$candidato [`CPF`]=$_POST['CPF'];
$candidato [`CPF_RESP`]=$_POST['CPF_RESP'];
$candidato [`PIX`]=$_POST['PIX'];
$candidato [`camisa`]=$_POST['camisa'];
$candidato [`calca`]=$_POST['calca'];
$candidato [`sapato`]=$_POST['sapato'];
$candidato [`CEP`]=$_POST['CEP'];
$candidato [`endereco`]=$_POST['endereco'];
$candidato [`complemento`]=$_POST['complemento'];
$candidato [`cidade`]=$_POST['cidade'];
$candidato [`UF`]=$_POST['UF'];
$candidato [`mensagem`]=$_POST['mensagem'];
$candidato [`imagem_id`]=$_POST['imagem_id'];
$candidato [`anexo`]=$_POST['anexo'];
$candidato [`anexo_id`]=$_POST['anexo_id'];
$candidato [`certificado`]=$_POST['certificado'];
$candidato [`rodizio`]=$_POST['rodizio'];
$candidato [`data_inscricao`]=$_POST['data_inscricao'];
$candidato [`WZ`]=$_POST['WZ'];
$candidato [`telegram`]=$_POST['telegram'];
$candidato [`boletim`]=$_POST['boletim'];
$candidato [`grupo_capacitacao`]=$_POST['grupo_capacitacao'];
$candidato [`grupo_voluntarios`]=$_POST['grupo_voluntarios'];
*/

if (isset($_SESSION['nivel']) && $_SESSION['nivel'] >= 3) {

if (isset($_POST['buscar'])){
	$query = "SELECT candidato_id FROM candidatos WHERE nome LIKE '".$_POST['buscar']."'";
//	echo $query . "<br>";
//	exit;
	$resp = mysqli_query($conexao,$query);
	if (mysqli_num_rows($resp)>0){
		$row = mysqli_fetch_assoc($resp);
		header('Location: /editacandidato/'.digitos($row['candidato_id']));
	}
}
include_once('./include/head1.php');
$msg="";
$id = 0;
if (array_key_exists(1,$parametros)){
	$id = intval($parametros[1]);
}
if($_SERVER['REQUEST_METHOD']=="POST"){
	foreach ($campos as $campo) {
		if (isset($_POST[$campo[0]])) {
			$valores[$campo[0]]= $_POST[$campo[0]];	
		}		
	}
	$id = $valores['candidato_id'];
	echo "<pre>";
	var_dump($valores);
	echo "</pre>";
/*
if (isset($_POST['usuario_id'])) {
	$candidato [`candidato_id`]= $_POST['usuario_id'];	
}
*/
	if ($id>0){
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

		// Check if image file is a actual image or fake image
		/*
		if(isset($_POST["submit"])) {
		  $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
		  if($check !== false) {
			echo "File is an image - " . $check["mime"] . ".";
			$uploadOk = 1;
		  } else {
			echo "File is not an image.";
			$uploadOk = 0;
		  }
		}
		*/

		// Check if file already exists

		$query = "UPDATE `candidatos`
		SET "; 
	foreach ($campos as $campo) {
			$query .=  "`". $campo[0]."`='".$valores[$campo[0]]."',
			";	
	};
		$query = substr($query,0,-1);
		$query .= "WHERE candidato_id = ".$id;	

/*
		SET `candidato_id`='".$_POST['candidato_id']."',
			`usuario_id`='".$_POST['usuario_id']."',
			`inscrito`='".$_POST['inscrito']."',
			`nome`='".$_POST['nome']."',
			`responsavel`='".$_POST['responsavel']."',
			`curatela`='".$_POST['curatela']."',
			`Email`='".$_POST['Email']."',
			`Telefone`='".$_POST['Telefone']."',
			`genero`='".$_POST['genero']."',
			`Nascimento`='".$_POST['Nascimento']."',
			`RG`='".$_POST['RG']."',
			`CPF`='".$_POST['CPF']."',
			`CPF_RESP`='".$_POST['CPF_RESP']."',
			`PIX`='".$_POST['PIX']."',
			`camisa`='".$_POST['camisa']."',
			`calca`='".$_POST['calca']."',
			`sapato`='".$_POST['sapato']."',
			`CEP`='".$_POST['CEP']."',
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
			`grupo_voluntarios`='".$_POST['grupo_voluntarios']."'
			WHERE candidato_id = ".$id;	
		
*/
	} else {
		$query1 = "INSERT INTO `usuarios`(`login`, `email`, `telefone`, `nome`, `nivel`) VALUES ('" . $valores['Email']."','" . $valores['Email']."','" . formataWA($valores['Telefone'])."','" . $valores['nome']."','1')";
		echo $query1 ."<br>";
//		$resp = mysqli_query($conexao,$query1);
//		$usuario_id = $conexao -> insert_id;
		$usuario_id = 1;

		$query = "INSERT INTO `candidatos`(`usuario_id`, `inscrito`, `nome`, `responsavel`, `curatela`, `Email`, `Telefone`, `genero`, `Nascimento`, `RG`, `CPF`, `CPF_RESP`, `PIX`, `camisa`, `calca`, `sapato`, `CEP`, `endereco`, `complemento`, `cidade`, `UF`, `mensagem`, `imagem_id`, `anexo`, `anexo_id`, `certificado`, `rodizio`, `data_inscricao`, `WZ`, `telegram`, `boletim`, `grupo_capacitacao`, `grupo_voluntarios`, `IP`) 
		VALUES ('". $usuario_id."','candidato','" . $valores['nome']."','" . $valores['responsavel']."','" . $valores['curatela']."','" . $valores['Email']."','" . $valores['Telefone']."','" . $valores['genero']."','" . $valores['Nascimento']."','" . $valores['RG']."','" . $valores['CPF']."','" . $valores['CPF_RESP']."','" . $valores['PIX']."','" . $valores['camisa']."','" . $valores['calca']."','" . $valores['sapato']."','" . $valores['CEP']."','" . $valores['endereco']."','" . $valores['complemento']."','" . $valores['cidade']."','" . $valores['UF']."','" . $valores['mensagem']."','" . $valores['imagem_id']."','" . $valores['anexo']."','" . $valores['anexo_id']."','" . $valores['certificado']."','" . $valores['rodizio']."','" . $valores['data_inscricao']."','" . $valores['WZ']."','" . $valores['telegram']."', '" . $valores['boletim'] . "','" . $valores['grupo_capacitacao']."','" . $valores['grupo_voluntarios']."' ,'" . $valores['IP']."')";
	}
		echo $query . "<br>";
		exit;
		$resp = mysqli_query($conexao,$query);	
}
?>
<body>
<?
	include_once('./include/nav.php');
?>
	<div class="container">
		<header class="mt-5 p-2 justify-content-md-center">
			<h1 class="text-center">Edição de candidato</h1>
			<p  class="text-center"><small><? echo $msg?></small></p>
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
					<?
					$querynomes = "SELECT candidato_id,nome FROM candidatos ORDER BY nome;";
					$respnome = mysqli_query($conexao,$querynomes);
					while ($row = mysqli_fetch_assoc($respnome)){
						?>
						<option value="<? echo $row['nome']?>">
						<?
					}
					?>
				</datalist>
</div>
				<button class="btn btn-sm rounded-pill" type="submit">buscar</button>
			</form>
				</div>
			</div>
		</header>
		<div class="row justify-content-center">
			<div class="col col-md-8">
<?php
			$query = "SELECT candidatos.*,imagens.url AS perfil 
			FROM candidatos
			LEFT JOIN imagens ON candidatos.imagem_id = imagens.imagem_id 
			WHERE candidato_id = ".$id;
			$msg .= "<br>Candidato não encontrado";
			$resp = mysqli_query($conexao,$query);
				$row = mysqli_fetch_assoc($resp);
				if ($row) {
					$perfil = "/img/profile.png";
					if (!is_null($row['perfil'])){
						$perfil = "/".$row['perfil'];
					}
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
					<h1 class="text-center"><? echo $row['nome']?></h1>
					<a href="/trocafoto/<? echo digitos($id)?>"><img class="card-img-top img-thumbnail" src="<? echo $perfil?>"></a>
				</div>
					<div class="card-body">
						<form method="post" enctype="multipart/form-data">
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
						if ($campo <>"perfil"  && $campo <> "candidato_id" && $campo <> "usuario_id"&& $campo <> "IP"){
							if ($campo<>"curatela"){
						?>
						<div class="md-form">
							<i class="far fa-map prefix grey-text"></i>
							<input type="text" id="<? echo $campo?>" name="<? echo $campo?>" class="form-control" placeholder="<? echo $campo?>" value="<? echo $valor?>">
							<label for="<? echo $campo?>"><? echo $campo?></label>
						</div>
						<?
								
							} else {
							?>
							<label for="<? echo $campo?>" class=" grey-text"><? echo $campo?></label>
<select class="form-select" id="<? echo $campo?>" name="<? echo $campo?>">
  <option selected value="0"<? echo ($valor==0)? " selected": ''?>>Escolha uma opção (não informada)</option>
  <option value="1"<? echo ($valor==1)? " selected": ''?>>Curatela total</option>
  <option value="2"<? echo ($valor==2)? " selected": ''?>>Curatela parcial (provisória)</option>
  <option value="3"<? echo ($valor==3)? " selected": ''?>>Sem Curatela</option>
</select>
							<?
							}
						}
					}
					
					?>
							<p><small>Documento (opcional)</small></p>
						<div class="md-form">
							<i class="far fa-file prefix grey-text"></i>
							<input type="text" maxlength="64" class="form-control" id="nome_arquivo" name="nome_arquivo" placeholder="descrição">
							<input type="file" id="arquivo" name="arquivo" class="form-control">
						</div>
						<div class="md-form">
						<button class="btn btn-block btn-pill btn-primary" type="submit" id="submit" name="submit">Enviar</button>
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
				<input type="text" id="<? echo $campo?>" name="<? echo $campo?>" class="form-control" placeholder="<? echo $campo?>" value="<? echo $valor?>">
				<label for="<? echo $campo?>"><? echo $campo?></label>
			</div>
<?
   }
	}
			?>
<!--				<p><small>Curriculo (opcional)</small></p>-->
				<p><small>Documento (opcional)</small></p>
				<div class="md-form">
					<i class="far fa-file prefix grey-text"></i>
					<input type="text" maxlength="64" class="form-control" id="nome_arquivo" name="nome_arquivo" placeholder="descrição">
					<input type="file" id="arquivo" name="arquivo" class="form-control">
				</div>
				<div class="md-form">
				<button class="btn btn-block btn-pill btn-primary" type="submit" id="submit" name="submit">ENVIAR</button>
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
	$query = "SELECT c.candidato_id, c.nome, i.imagem_id, i.url, d.doc_id, d.url AS doc_url, d.descritivo AS descricao_documento
	FROM candidatos c
	LEFT JOIN imagens i ON c.imagem_id = i.imagem_id
	LEFT JOIN documentos d ON c.candidato_id = d.candidato_id
	WHERE c.candidato_id = ".$id.";";
	$resp = mysqli_query($conexao,$query);
if(mysqli_num_rows($resp)){
			?>
			<h2 class="text-center">Documentos</h2>
		<table class="table table-striped table-hover m-2 border">
			<?
			$i=0;
			while($row = mysqli_fetch_assoc($resp)){
				if (!is_null($row['doc_url']) || !is_null($row['url'])){
					$extension = pathinfo("/".$row['doc_url'], PATHINFO_EXTENSION);
	//				echo $extension."<br>";
					$icone = "img/DOCNA.png";
					if ($extension=="pdf"){
						$icone = "img/PDF.png";
					}
					if ($extension=="doc" || $extension=="docx"){
						$icone = "img/DOC.png";
					}
					if ($extension=="jpg" || $extension=="jpeg" || $extension=="gif" || $extension=="png"){
						$icone = $row['doc_url'];
					}

					echo "<tr><td><img src='/".$icone."' width=32></td><td><a href='/".$row['doc_url']."' target='_blank'>".$row['descricao_documento']."<i class='fa fa-arrow-right ml-1'></i></a></td><td><a href='/editardoc/".digitos($row['doc_id'])."'>Editar</a> | <a href='/excluirdoc/".digitos($row['doc_id'])."'>Excluir</a></td></tr>";
					$i++;
				}
			}
	if($i==0){
		echo "<tr><td class='text-center'>Nenhum documento encontrado</td></tr>";
	}
			?>
		</table>
			<?
				
}
			?>
					</div>
				</div>
			</div>
		</div>
	</div>
			<?	
include_once('./include/footer.php');
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
