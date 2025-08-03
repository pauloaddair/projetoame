<?php
	$titulo = "Converte telefones";
	include_once('./include/conexao.php');
	include_once('./include/funcoes.php');
	include_once('./include/head-table.php');
	if (isset($_SESSION['nivel']) && $_SESSION['nivel'] >= 3) {
		$query = "SELECT * FROM eventos;";
		$resp = mysqli_query($conexao,$query);
		?>
		<body>
	<?php include_once('./include/nav.php');
	?>
		<div class="container">
			<header class="mt-5 p-2 justify-content-md-center">
				<h1 class="text-center">Lista de eventos cadastrados</h1>
				<nav aria-label="breadcrumb">
				  <ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="/atendentes">Atividades</a></li>
					<li class="breadcrumb-item"><a href="/rodizio">Rodízio</a></li>
					<li class="breadcrumb-item active" aria-current="page">Candidatos cadastrados</li>
				  </ol>
				</nav>
			</header>
		<table class="table table-striped table-hover" id="table">
			<thead><th>#</th><th>ID</th><th>Evento</th><th>Designação</th><th>Inicio</th><th>Final</th><th>Cidade</th><th>Estado</th><th>E-mail</th><th>site</th><th>Ações</th></thead>
				<?php $i=1;
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
	//				$resp2 = mysqli_query($conexao,$query);
					$perfil = "/img/profile.png";
					if (!is_null($row['perfil'])){
						$perfil = "/".$row['perfil'];
					}
	*/
				?>
			<tr>
			<td><?php echo $i?></td>
				<td><a href="evento/<?php echo digitos($row['codigo'])?>"><?php echo $row['codigo']?></a></td><td><?php echo $row['evento']?></td>
				<td><?php echo $row['feira_completo']?></td>
			<td><?php echo $row['data_inicio']?></td>
			<td><?php echo $row['data_final']?></td>
			<td><?php echo $row['cidade']?></td>
			<td><?php echo $row['estado']?></td>
			<td><?php echo $row['Email']?></td>
			<td><?php echo $row['site']?></td>
				<td><small><a href="/excluirevento/<?php echo digitos($row['codigo'])?>">Excluir</a> | <a href="/editaevento/<?php echo digitos($row['codigo'])?>">Editar</a></small></td>
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
//	include_once('include/conexao.php');
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
