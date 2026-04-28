<?php
// include_once('./include/conexao.php');
include_once('./include/funcoes.php');
include_once('./include/head-table.php');
$query = "SELECT candidatos.*, documentos.*
FROM candidatos
LEFT JOIN documentos ON candidatos.candidato_id = documentos.candidato_id
ORDER BY candidatos.nome;";
$resp = mysqli_query($conexao,$query);
?>
<body>
	<div class="container">
	<table class="table table-striped" id="table">
		<thead>
		<th>Atendente</th>
		<th>Autorização</th>
		</thead>
			<tbody>
			<?php 
			$arquivo = "";
			while ($row = mysqli_fetch_assoc($resp)){
				$url = "";
				$nome = "";
				if ($row['url']<>""){
					if ($nome <> $row['nome']){
						$arquivo .= $row['nome'] .";".$row['url']."\n";			
					}
					$nome = $row['nome'];
					$url = "<a href='".$row['url']."' target='_blank'>baixar documento</a>";
					echo "<tr><td>".$row['nome']."</td><td><i class='fas fa-download text-muted mr-1'></i>".$url."</td></tr>";
				} 
			}
			$myfile = fopen("./docs/autorizacoes.csv", "w") or die("Não foi possível abrir o arquivo!");
			fwrite($myfile,$arquivo);
			fclose($myfile);

			?>
			</tbody>
		</table>
		<p><a href="./docs/autorizacoes.csv" target="_blank">Autorizações</a></p>
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
