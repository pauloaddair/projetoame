<?php
// include_once('./include/conexao.php');
include_once('./include/funcoes.php');
$titulo = "Troca fot de perfil";
// include_once('./include/head.php');
$usuario_id = 0;
$candidato_id = 0;
	if (array_key_exists(1,$parametros)){
		$candidato_id = $parametros[1];
		$query = "SELECT candidatos.nome, candidatos.usuario_id,imagens.url FROM candidatos
		LEFT JOIN imagens
		ON candidatos.imagem_id = imagens.imagem_id
		WHERE candidatos.candidato_id = " . $candidato_id;
		$icone = "img/profile.png";
		$nome = "nenhum usuário definido";
		$usuario_id = 0;
		$resp = mysqli_query($conexao,$query);
		if(mysqli_num_rows($resp)>0){
			$row = mysqli_fetch_assoc($resp);
			$icone = $row['url'];
			$nome = $row['nome'];
			$usuario_id = $row['usuario_id'];
		}
	}
if ($_SERVER['REQUEST_METHOD']=="POST"){
	if (isset($_POST['usuario_id'])){
		$usuario_id = $_POST['usuario_id'];
		$tnpFile = $_FILES["file"]["tmp_name"];
		if (!empty($tnpFile)){
			$img_id = 0;
			$nomearquivo = "img/". $_FILES['file']['name'] ;
			$dir = before("pages",__DIR__);
	//		$file_parts = pathinfo($dir . $nomearquivo);
			$icone = $nomearquivo;

	/*
			echo $file_parts."<br>".$dir . "<br>".$_FILES['file']['tmp_name']. "<br>". $nomearquivo . "<br>" . $icone . "<br>";
			exit;
	*/
		//		echo $nomearquivo . "<br>";
		copy ( $_FILES['file']['tmp_name'], 
		 $dir . $nomearquivo ) 
		or die( "Não foi possível copiar o arquivo!" );
		}
		$query = "INSERT INTO `imagens`(`url`) VALUES ('".$nomearquivo."')";
		$resp = mysqli_query($conexao,$query);
		$imagem_id = $conexao -> insert_id;
		$query = "UPDATE `candidatos` SET `imagem_id`='".$imagem_id."' WHERE candidato_id = ".$candidato_id;
		$resp = mysqli_query($conexao,$query);
		if ($usuario_id>0){
			$query = "UPDATE `usuarios` SET `imagem_id`='".$imagem_id."' WHERE usuario_id = ".$usuario_id;
			$resp = mysqli_query($conexao,$query);
		}
	}
}

?>
<body>
	<div class="container">
	<header>
		<h1 class="text-center">Troca foto de perfil</h1>		
		<nav aria-label="breadcrumb">
		  <ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="/atendentes">Atividades</a></li>
			<li class="breadcrumb-item"><a href="/convertefone">Atendentes</a></li>
			<li class="breadcrumb-item active" aria-current="page">Editando foto de perfil</li>
		  </ol>
		</nav>
	</header>
		<div class="row justify-content-center">
			<div class="col col-md-8">
				<div class="card">
				<form method="post" enctype="multipart/form-data">
					<input class="btn rounded-pill" type="hidden" id="usuario_id" name="usuario_id" value="<?php echo $candidato_id?>">
					<div class="card-header">
						<h1><?php echo $nome?></h1>
					<img src="/<?php echo $icone?>" class="img-card-top img-thumbnail">
					</div>
					<div class="md-form">
						<!--  <label for="formFile" class="form-label">Default file input example</label>-->
						<input class="form-control btn-info p-1 rounded-pill" type="file" name="file" id="file" accept=".jpg,.jpeg,.png,.pdf" ><small>escolha o arquivo de imagem para este evento</small>
					</div>
					<div class="md-form">
						<button class="btn btn-block rounded-pill btn-primary" type="submit">Enviar</button>
					</div>
				</form>
				</div>
			</div>
		</div>
	</div>
	<div class="row mb-5">
	</div>
<?php
include_once('./include/footer.php');
?>
</body>
<?php
include_once('./include/scripts.php');
?>
<?php 
include_once('./include/end.php');
?>
