<?php
$titulo = "Modelo";
// include_once('./include/conexao.php');
include_once('./include/funcoes.php');
include_once('./include/head-table.php');
if ($_SERVER['REQUEST_METHOD']=="POST"){
	if (isset($_POST['excluir'])){
		$id = intval($_POST['excluir']);
		echo "Excluir =". $id. "<br>";	
		$query1 = "DELETE FROM `disponibilidade` WHERE disponibilidade.id=".$id; 
		$action = mysqli_query($conexao,$query1);
		if(mysqli_affected_rows($conexao)>0){
			echo "Item excluído com sucesso!";
		} else {
			echo "Não foi encontrado o item!";
		}		
	}
	if (isset($_POST['editar'])){
		$id = intval($_POST['editar']);
		echo "Editando =". $id. "<br>";	
		$query1 = "DELETE FROM `disponibilidade` WHERE disponibilidade.id=".$id; 
		$action = mysqli_query($conexao,$query1);
		if(mysqli_affected_rows($conexao)>0){
			echo "Item excluído com sucesso!";
		} else {
			echo "Não foi encontrado o item!";
		}		
	}
}
$query = "SELECT h.data_inicio AS inicio,h.data_final AS final,em.nome AS evento, em.local,i.url,c.nome,c.candidato_id,d.escalado,d.id 
FROM disponibilidade d
LEFT JOIN candidatos c ON d.candidato_id = c.candidato_id
LEFT JOIN horarios h ON d.atividade_id = h.horario_id
LEFT JOIN eventos_marcados em ON h.evento_id = em.id
LEFT JOIN imagens i ON em.imagem_id = i.imagem_id 
ORDER BY `h`.`data_inicio` DESC;";
$disponivel = mysqli_query($conexao,$query);

?>
<body>
	<div class="container">
		<table class="table" id="table">
			<thead>
				<td>#</td>
				<td>Evento</td>
				<td>Local</td>
				<td>Atendente</td>
				<td>Data</td>
				<td>Escala</td>
				<td>Ação</td>
			</thead>
			<tbody>
			<?php
			$semana = ["DOM","SEG","TER","QUA","QUI","SEX","SÁB"];
			$i = 1;
			while ($row = mysqli_fetch_array($disponivel)){
				$badge = "<span class='badge badge-danger ml-1 p-1 rounded-pill'>escalado</span>";
				if ($row['escalado']==0){
				$badge = "<span class='badge badge-success ml-1 p-1 rounded-pill'>disponível</span>";
				}
				?>
				<tr>
					<td><?php echo $i?></td>
					<td><?php echo $row['evento']?></td>
					<td><?php echo $row['local']?></td>
					<td><?php echo $row['nome'].$badge?></td>
					<?php
					$date = $row['inicio'];
					?>
					<td>
						<?php if (!is_null($date)) {
							$dia = $semana[intval(date_format(date_create($date),"w"))];
							echo $dia . " " . date_format(date_create($date),"d/m"); 
					}
						?>
					</td>
					<td><?php echo $row['escalado']?></td>
					<td><a href="/admin/excluidisponibilidade/<?php echo $row['id']?>" class="btn btn-danger btn-sm rounded-pill m-1">Excluir</a><a href="/admin/editadisponibilidade/<?php echo $row['id']?>" class="btn btn-info btn-sm rounded-pill">Editar</a></td>
				</tr>
				<?php
					$i++;
			}
			?>
			</tbody>
		</table>
	</div>
<?php
include_once('./include/footer-table.php');
?>
</body>
<?php
include_once('./include/scripts.php');
?>
<?php
include_once('./include/end.php');
?>
