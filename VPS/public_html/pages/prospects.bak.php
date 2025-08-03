<?php
$offset = 0;
$empresa_id = 0;
if (array_key_exists(1,$parametros)){
	$empresa_id = $parametros[1];
}
include_once('./include/conexao.php');
include_once('./include/funcoes.php');
$titulo = "Prospecção de novos trabalhos";
include_once('./include/head-datatable.php');
$query = "SELECT expositores2024.*, expositores_telefone.numero,expositores_telefone.celular FROM expositores2024
LEFT JOIN expositores_telefone ON expositores_telefone.expositor_id = expositores2024.expositor_id 
ORDER BY expositor_id DESC
LIMIT 100 OFFSET ".$offset.";" ;
$resp = mysqli_query($conexao,$query);
if ($_SERVER['REQUEST_METHOD']=="POST"){
	if(isset($_POST['offset'])){
		$offset = $_POST['offset'];
	}
}
?>
<body>
	<div class="container">
		<header>
			<h1 class="text-center">Prospecção</h1>
		</header>
		<div class="row mb-5">
			<div class="col vol-md-8">
				<?
				if ($empresa_id==0){
				?>
				<table class="table table-striped table-hover" id = "table">
					<thead>
						<th>#</th>
						<th>Empresa</th>
						<th>Contato</th>
						<th>Telefone</th>
						<th>E-mail</th>
						<th>Ação</th>
					</thead>
					<tbody>
						<?
						$i=1;
						while ($row = mysqli_fetch_assoc($resp)){
							echo "<tr><td>".$i."</td><td>". $row['empresa']."</td><td>".$row['nome']."</td><td>".$row['numero']."</td><td>".$row['email']."</td><td><a href='./prospects/".digitos($row['expositor_id'])."'>ver</a></td></tr>";	
							$i++;
						}
						?>
					</tbody>
				</table>
				<?
				} else {
					$query = "SELECT expositores2024.*, expositores_telefone.numero,expositores_telefone.celular FROM expositores2024
					LEFT JOIN expositores_telefone ON expositores_telefone.expositor_id = expositores2024.expositor_id 
					WHERE expositores2024.expositor_id = ".$empresa_id." 
					ORDER BY expositores_telefone.numero DESC;";
//					echo $query."<br>";
					$resp = mysqli_query($conexao,$query);
					while ($row = mysqli_fetch_assoc($resp)){
				?>
						<p>Empresa:<? echo $row['empresa']?></p>
						<p>Contato:<? echo $row['nome']?></p>
						<p>E-mail:<? echo $row['email']?></p>
				<table class="table table-striped table-hover" id = "table1">
					<thead>
						<th>#</th>
						<th>Telefone</th>
					</thead>
					<tbody>
						<?
						$i=1;
							echo "<tr><td>".$i."</td><td><a href='tel:" . $row['numero'] . "'>" . $row['numero'] . "</a><a href='https://api.whatsapp.com/send?phone=".formataWA($row['numero'])."&text=Ol%C3%A1'<i class='fab fa-whatsapp text-success ml-3'></i></td></tr>";	
							$i++;
						}
						?>
					</tbody>
				</table>
				<?
				}
				?>
				
			</div>
		</div>
		<h1> </h1>
	</div>
<?php
include_once('./include/footer-botton.php');
?>
</body>
<?php
include_once('./include/scripts-datatable.php');
?>
<?
include_once('./include/end.php');
?>
