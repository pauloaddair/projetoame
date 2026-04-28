<?php
// include_once('./include/conexao.php');
include_once('./include/funcoes.php');
include_once('./include/head-table.php');
$query = "SELECT * FROM expositores2024 ORDER BY empresa"
?>
<body>
	<div class="container">
	<table class="table" id="table">
		<thead>
			<th>#</th>
			<th>ID</th>
			<th>Empresa</th>
			<th>Slug</th>
		</thead>
		<tbody>
			
				<?php 
				$i=1;
				$resp = mysqli_query($conexao,$query);
				while ($row = mysqli_fetch_assoc($resp)){
					echo "<tr><td>".$i."</td><td>".$row['expositor_id']."<td>".$row['empresa']."</td><td>".slugify($row['empresa'],'')."</td></tr>";
					$i++;
				}
				?>
			
		</tbody>
	</table>
	</div>
<?php
include_once('./include/footer-database.php');
?>
</body>
<?php
include_once('./include/scripts.php');
?>
<?php 
include_once('./include/end.php');
?>
