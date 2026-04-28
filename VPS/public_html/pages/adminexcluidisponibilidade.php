<?php
$titulo = "Modelo";
// include_once('./include/conexao.php');
include_once('./include/funcoes.php');
include_once('./include/head-table.php');
$id = 0;
if (array_key_exists(2,$parametros)){
	$id = intval($parametros[2]);
}
$query = "SELECT em.inicio,em.final,em.nome AS evento, em.local,i.url,c.nome,c.candidato_id,d.escalado,d.id 
FROM disponibilidade d
LEFT JOIN candidatos c ON d.candidato_id = c.candidato_id
LEFT JOIN horarios h ON d.atividade_id = h.horario_id
LEFT JOIN eventos_marcados em ON h.evento_id = em.id
LEFT JOIN imagens i ON em.imagem_id = i.imagem_id
WHERE d.id = ".$id . " 
ORDER BY em.inicio;";
$disponivel = mysqli_query($conexao,$query);
?>
<body>
	<div class="container">
		<table class="table" id="table">
			<thead>
				<td>Evento</td>
				<td>Local</td>
				<td>Atendente</td>
				<td>Data</td>
				<td>Escala</td>
				<td>Ação</td>
			</thead>
			<tbody>
			<?php
			while ($row = mysqli_fetch_array($disponivel)){
				$badge = "<span class='badge badge-danger ml-1 p-1 rounded-pill'>escalado</span>";
				if ($row['escalado']==0){
				$badge = "<span class='badge badge-success ml-1 p-1 rounded-pill'>disponível</span>";
				}
				?>
				<tr>
					<td><?php echo $row['evento']?></td>
					<td><?php echo $row['local']?></td>
					<td><?php echo $row['nome'].$badge?></td>
					<td><?php echo $row['inicio']?></td>
					<td><?php echo $row['escalado']?></td>
					<td><form method="post" action="/admin/disponibilidade">
						<input type="hidden"  name="excluir" value="<?php echo $row['id']?>">
							<button type="submit" href="/admin/excluidisponibilidade/<?php echo $row['id']?>" class="btn btn-danger btn-sm rounded-pill m-1">Confirmar?</button>
						</form>
					</td>
				</tr>
				<?php
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

