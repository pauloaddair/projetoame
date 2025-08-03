<?php
$titulo = "Usuários cadastrados";
include_once('./include/conexao.php');
include_once('./include/funcoes.php');
include_once('./include/head-table.php');
if (isset($_SESSION['nivel']) && $_SESSION['nivel'] >= 3) {
	$query = "SELECT usuarios.*,imagens.url AS perfil 
	FROM usuarios
	LEFT JOIN imagens ON usuarios.imagem_id = imagens.imagem_id 
	ORDER BY nome;";
	$resp = mysqli_query($conexao,$query);
	?>
	<body>
<?
	include_once('./include/nav.php');
?>
		<div class="container">
			<header class="mt-5 p-2 justify-content-md-center">
				<h1 class="text-center">Lista de candidatos cadastrados</h1>
				<nav aria-label="breadcrumb">
				  <ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="/atendentes">Atividades</a></li>
					<li class="breadcrumb-item"><a href="/rodizio">Rodízio</a></li>
					<li class="breadcrumb-item active" aria-current="page">Usuários</li>
				  </ol>
				</nav>
			</header>
		<table class="table table-striped table-hover" id="table">
			<thead><th>#</th><th>ID</th><th>Perfil</th><th>Atendente</th><th>E-mail</th><th>Ações</th></thead>
				<?
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
			<td><? echo $i?></td>
			<td><? echo $row['usuario_id']?></td>
				<td><a href="trocafotou/<? echo digitos($row['usuario_id'])?>"><img src="<? echo $perfil?>" height="32" class="img-thumbnail"></a></td>
			<td><? echo $row['nome']?></td>
			<td><? echo $row['email']?></td>
<!--
			<td><? echo $row['Telefone']?></td>
			<td><? echo formataWA($row['Telefone'])?></td>
-->
				<td><small><a href="/excluirusuario/<? echo digitos($row['usuario_id'])?>">Excluir</a> | <a href="/editausuario/<? echo digitos($row['usuario_id'])?>">Editar</a></small></td>
		</tr>
			<?
					$i++;
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
<?
include_once('./include/end.php');
?>
