<?php
$titulo = "Modelo";
// include_once('./include/conexao.php');
include_once('./include/funcoes.php');
// include_once('./include/head.php');
$doc_id = -1;
$msg = "";
if (array_key_exists(1,$parametros)){
	$doc_id = intval($parametros[1]);
	$query = "SELECT * FROM documentos WHERE doc_id = ".$doc_id;
	$resp = mysqli_query($conexao,$query);
}
if ($_SERVER['REQUEST_METHOD']=="POST"){
	if(isset($_POST['descritivo'])){
		$query = "UPDATE `documentos` SET `descritivo`='".$_POST['descritivo']."' WHERE doc_id =".$doc_id;
//		echo $query . "<br>";
//		exit;
		$resp1 = mysqli_query($conexao,$query);
		if ($resp1) {
			$msg = "Documento atualizado";
		}
	}
}
?>
<body>
	<div class="container">
<?php
	if($resp){
//		$query = "SELECT * FROM documentos WHERE doc_id =".$doc_id;
//		$resp = mysqli_query($conexao,$query);
		$row = mysqli_fetch_assoc($resp);
		?>
		<nav aria-label="breadcrumb bg-transparent">
		  <ol class="breadcrumb bg-transparent">
			<li class="breadcrumb-item"><a href="/atendentes">Atividades</a></li>
			<li class="breadcrumb-item"><a href="/casting">Atendentes</a></li>
			<li class="breadcrumb-item"><a href="/editacandidato/<?php echo digitos($row['candidato_id'])?>">Candidato</a></li>
			<li class="breadcrumb-item active" aria-current="page">Editando candidato</li>
		  </ol>
		</nav>
		<div class="row justify-content-center">
			<div class="col">
		<h1 class="text-center">Descritivo do documento</h1>
				<div class="card p-2">
			<?php 
			if ($msg<>""){
				echo "<p class='text-center'>".$msg."</p>";
			}
				$extension = pathinfo("/".$row['url'], PATHINFO_EXTENSION);
				$filename = pathinfo("/".$row['url'], PATHINFO_BASENAME);
//				echo $extension."<br>";
				$icone = "img/DOCNA.png";
				if ($extension=="pdf"){
					$icone = "img/PDF.png";
				}
				if ($extension=="doc" || $extension=="docx"){
					$icone = "img/DOC.png";
				}
				if ($extension=="jpg" || $extension=="jpeg" || $extension=="gif" || $extension=="png"){
					$icone = $row['url'];
				}

							
			?>
<!--			<i class="far fa-file prefix grey-text"></i>-->
				<label for="descritivo"><?php echo $filename?></label>
		<form class="form-inline" method="post">
		<div class="form-group">
			<?php echo "<img src='/".$icone."' width=32 class='prefix mr-2'>"?>
			<input type="text" maxlength="64" class="form-control" id="descritivo" name="descritivo" placeholder="descrição" value="<?php echo $row['descritivo']?>">
			<button type="submit" class="btn btn-sm btn-primary mb-2">Salvar</button>
		</div>
		</form>
			</div>
			</div>
		</div>
		<?php 
	} else {
		?>
		<h1 class="text-center">Documento não encontrado!</h1>
		<div class="text-center"><button class="btn btn-sm btn-primary mb-2" onclick="history.back()">Voltar</button></div>
		<?php 
	}
include_once('./include/footerbt.php');
?>
	</div>
</body>
<?php
include_once('./include/scripts.php');
?>
<?php 
include_once('./include/end.php');
?>
