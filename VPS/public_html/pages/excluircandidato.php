<?php
// include_once('./include/conexao.php');
include_once('./include/funcoes.php');
// include_once('./include/head.php');
$id = 0;
$msg = "Nenhum candidato a excluir";
if (array_key_exists(1,$parametros)){
	$id = intval($parametros[1]);
}
if ($id>0){
	$query = "SELECT nome, candidato_id FROM candidatos WHERE candidato_id = ".$id;
	$resp = mysqli_query($conexao,$query);
	if (mysqli_num_rows($resp) > 0){
		$row = mysqli_fetch_assoc($resp);
		$candidato = $row['nome'];
		$msg = "Excluir candidato ". $candidato."?";
	}
}
if($_SERVER['REQUEST_METHOD']=="POST"){
	if ($id>0){
		$query = "DELETE FROM candidatos WHERE candidato_id =".$id;
		$resp = mysqli_query($conexao,$query);
		$msg = "Candidato não encontrado!";
		if($resp){
			$msg = "Candidato ".digitos($id)." excluido com sucesso";
		}		
	}
}
?>
<body>
	<div class="view">
		<header class="mt-5 p-2 justify-content-md-center">
			<h1 class="text-center">Exclusão de candidato</h1>
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="/atendentes">Atividades</a></li>
				<li class="breadcrumb-item"><a href="/convertefone">Atendentes</a></li>
				<li class="breadcrumb-item active" aria-current="page">Exclusão de candidato</li>
			  </ol>
			</nav>
		</header>
		<div class="row">
			<div class="col col-md-8">
				<form method="post">
					<input type="hidden" id="cnadidato_id" name="candidato_id" value="<?php echo $id?>">
				<div class="card">
					<div class="card-header">
						<h2 class="text-center">
						<?php echo $msg?>
						</h2>
					</div>
					<div class="card-body">
						<div class="md-form">
						<button class="btn btn-block btn-pill btn-primary" type="submit">Enviar</button>
						</div>
					</div>
				</div>
				</form>
			</div>
		</div>
	</div>
<?php
include_once('./include/footes.php');
?>
</body>
<?php
include_once('./include/scripts.php');
?>
<?php 
include_once('./include/end.php');
?>
