<?php
include_once('./include/conexao.php');
include_once('./include/funcoes.php');
include_once('./include/head.php');
$query = "SELECT candidatos.nome AS atendente,candidatos.rodizio, candidatos.certificado,eventos_marcados.nome AS evento, horarios.data_inicio, horarios.data_final,horarios.tipo 
FROM candidatos,disponibilidade,eventos_marcados,horarios 
WHERE candidatos.candidato_id = disponibilidade.candidato_id 
AND disponibilidade.atividade_id = horarios.horario_id
AND eventos_marcados.id = horarios.evento_id
AND candidatos.rodizio >0
ORDER BY horarios.data_inicio ASC,rodizio ASC, atendente ASC";
$resp = mysqli_query($conexao,$query);
?>
<body>
	<div class="container">
	<header>
	<h1 class="text-center">Disponibilidade próximos eventos</h1></header>
	<table class="table table-hover">
	<?
		$feira = "";
		$hora = "";
		$i=1;
		while ($row = mysqli_fetch_assoc($resp)){
			$cert = "treinamento";
			if ($row['certificado']==1){
				$cert = "atendimento";
			}
			if ($row['tipo']=="treinamento"){
				$cert = "treinamento";
			}
			if($feira<>$row['evento']){
				echo "<tr class='bg-info'><td colspan=3>".$row['evento']."</td></tr>";
			}
			if($hora<>$row['data_inicio']){
				echo "<tr class='bg-light'><td>#</td><td colspan=2>Dia: ".Date('d/m',strtotime($row['data_inicio']))."</td></tr>";
			}
		?>	
	<tr><td><? echo $i?></td><td><? echo $row['atendente'] ?></td><td><? echo $cert?></td></tr>
	<?
		$i++;
			$feira =$row['evento'];
			$hora =$row['data_inicio'];
		}
	?>
	</table>
	</div>
<?
include_once('./include/footer.php');
?>
</body>
<?
include_once('./include/scripts.php');
?>
<?
include_once('./include/end.php');
?>
