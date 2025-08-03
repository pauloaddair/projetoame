<?php
$titulo = "Converte telefones";
include_once('./include/conexao.php');
include_once('./include/funcoes.php');
include_once('./include/head-table.php');
if (isset($_SESSION['nivel']) && $_SESSION['nivel'] >= 3) {
	$query = "SELECT candidatos.*,imagens.url AS perfil 
	FROM candidatos
	LEFT JOIN imagens ON candidatos.imagem_id = imagens.imagem_id 
	ORDER BY nome,data_inscricao;";
	$resp = mysqli_query($conexao,$query);
	?>
	<body>
<?php
	include_once('./include/nav.php');
?>
		<div class="container">
			<header class="mt-5 p-2 justify-content-md-center">
				<h1 class="text-center">Lista de candidatos cadastrados</h1>
				<nav aria-label="breadcrumb">
				  <ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="/atendentes">Atividades</a></li>
					<li class="breadcrumb-item"><a href="/rodizio">Rodízio</a></li>
					<li class="breadcrumb-item active" aria-current="page">Candidatos cadastrados</li>
				  </ol>
				</nav>
			</header>
		<table class="table table-striped table-hover" id="table">
			<thead><th>#</th><th>ID</th><th>Perfil</th><th>Atendente</th><th>E-mail</th><th>tel</th><th>tel novo</th><th>Ações</th></thead>
				<?php
			$i=1;
				while ($row = mysqli_fetch_array($resp)){
	/*
					$query = "SELECT usuario_id FROM usuarios WHERE email LIKE '".$row['Email']."'";
					echo $query . "<br>";
					exit;
					$resp1 = mysqli_query($conexao,$query);

					print_r($resp1) . "<br>";
					exit;
	*/
	/*
					if (mysqli_num_rows($resp1)>0){
						$row1 = mysqli_fetch_array($resp1);
						$id = $row1['usuario_id'];
						$query = "UPDATE `usuarios` SET `login`='".$row['Email']."',`email`='".$row['Email']."',`telefone`='".formataWA($row['Telefone'])."',`nome`='".$row['nome']."',`imagem_id`=0,`nivel`=1 WHERE usuario_id=".$id;
					} else {
						$query = "INSERT INTO `usuarios`(`login`, `email`, `telefone`, `nome`, `imagem_id`, `nivel`) VALUES ('" . $row['Email'] . "','".$row['Email']."','".formataWA($row['Telefone'])."','".$row['nome']."',0,1)";
					}
	*/
	/*
					echo $query . "<br>";
					exit;
	*/
	//				$resp2 = mysqli_query($conexao,$query);
					$perfil = "/img/profile.png";
					if (!is_null($row['perfil'])){
						$perfil = "/".$row['perfil'];
					}
				?>
			<tr>
			<td><?php echo $i?></td>
			<td><?php echo $row['candidato_id']?>/<?php echo $row['usuario_id']?></td>
				<td><a href="trocafoto/<?php echo digitos($row['candidato_id'])?>"><img src="<?php echo $perfil?>" height="32" class="img-thumbnail"></a></td>
			<td><?php echo $row['nome']?></td>
			<td><?php echo $row['Email']?></td>
			<td><?php echo $row['Telefone']?></td>
			<td><?php echo formataWA($row['Telefone'])?></td>
				<td><small><a href="/excluircandidato/<?php echo digitos($row['candidato_id'])?>">Excluir</a> | <a href="/editacandidato/<?php echo digitos($row['candidato_id'])?>">Editar</a></small></td>
		</tr>
			<?php $i++;
				}
			?>
		</table>
		</div>
	<?php
	include_once('./include/footer-database.php');
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
<?php include_once('./include/end.php');
?>
