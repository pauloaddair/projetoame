<?php
$titulo = "Documentos";
include_once('./include/conexao.php');
include_once('./include/funcoes.php');
$titulo = "Documentos";

include_once('./include/head-datatable.php');
if (isset($_SESSION['nivel']) && $_SESSION['nivel'] >= 3) {
$query = "SELECT candidatos.candidato_id,candidatos.nome,documentos.descritivo,documentos.url 
			FROM candidatos
			LEFT JOIN documentos ON candidatos.candidato_id = documentos.candidato_id
			ORDER BY candidatos.nome;";
$resp = mysqli_query($conexao,$query);
?>
<body>
	<?
	include_once('./include/nav.php');
	?>
	<style>
	a:link {
/*
	  color: yellow;
	  background-color: transparent;
*/
	  text-decoration: underline;
	}
	</style>
	<div class="container mt-5">
		<header class="p-5">
		<h1 class="text-center mt-1">Documentos</h1>
		</header>
	<table id="table" class="table table-hover table-stripe">
		<thead><th>#</th><th>Documento</th></thead>
		<tbody>
		<?
			$i=1;
		while ($row = mysqli_fetch_array($resp)){
			echo "<tr><td>".$i."</td><td><a href='/editacandidato/".digitos($row['candidato_id'])."'>".$row['nome'] . "</a>" . " - <a href='/".$row['url']. "' target='_blank'>".$row['descritivo']."</a></td></tr>\n";
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
<?
} else {
include_once('./pages/restrito.php');
include_once('./include/scripts.php');
}
include_once('./include/end.php');
?>
