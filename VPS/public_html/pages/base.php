<?php
include_once('include/conexao_vb.php');
// include_once('include/head.php');
include_once('include/nav.php');
$query = "SELECT table_name
FROM information_schema.tables
WHERE table_type='BASE TABLE'
      AND table_schema = 'virtualb_ag'";
$r = mysqli_query($conexao,$query);
?>
<div class="header mt-5">
<h1 class="text-center">Banco de dados</h1>
</div>
<div class="container">
	<h6><?php echo $url?></h6>
<?php 
	echo "<p>".mysqli_num_rows($r)."</p>";
	while($row = mysqli_fetch_array($r)){
		$query_qtd = "SELECT count(*) AS qtd FROM ".$row[0];
		$r1 = mysqli_query($conexao,$query_qtd);
		$row1 = mysqli_fetch_array($r1);
		echo "<div class='card my-1'><h6>".$row[0] . " - ".$row1['qtd']." itens</h6>";
		$query_itens = "SELECT * FROM ".$row[0] . " LIMIT 5;";
		$r2 = mysqli_query($conexao,$query_itens);
//		echo $query_itens."</br>";
		echo "<table>";
		while($row2 = mysqli_fetch_array($r2)){
			echo "<tr><td>&nbsp;</td><td>".$row2[1] . "</td><td>";
			if (isset($row2[2])){
				echo $row2[1] . "</td></tr>";
		}
		}
		echo "</table></div>";
	};
?>
</div>
<?php 
include_once('include/footer.php');
?>
