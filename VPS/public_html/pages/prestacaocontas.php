<?php
// include_once('./include/conexao.php');
include_once('./include/funcoes.php');
include_once('./include/head-table.php');
//$query = "SELECT * FROM contabil_movimento ORDER BY movimento_id DESC;";
/*
$query = "SELECT data_realizada,
  SUM(valor) AS total_diario,
  SUM(SUM(valor_realizada)) OVER (ORDER BY data_lancamento) AS saldo_acumulado
FROM
  contabil_movimento
GROUP BY
  valor_realizada
ORDER BY
  valor_realizada;";
*/
$query = "SELECT
  a.data_realizada,
  a.descricao,
  a.valor_realizado,
  (SELECT SUM(b.valor_realizado)
   FROM contabil_movimento b
   WHERE b.data_realizada <= a.data_realizada) AS saldo_acumulado
FROM
  contabil_movimento a
ORDER BY
  a.data_realizada;";
$contas = mysqli_query($conexao,$query);
?>
<body>
	<div class="container">
		<header><h1 class="display-1 text-center">Prestação de Contas</h1></header>
		<table class="table" id="table">
			<thead>
				<th>#</th>
				<th>Data</th>
				<th>Diario</th>
				<th>Valor</th>
				<th align="right">Saldo</th>
			</thead>	
			<tbody>
			<?php 
				$i=1;
				$saldo = 0;
				while ($conta = mysqli_fetch_assoc($contas)){
						$classe = "text-danger";
					if ($conta['valor_realizado']>=0){
						$classe = "text-primary";
					}
/*
					if ($saldo<>$conta['saldo_acumulado'] && $saldo<>0){
						echo "<tr><td>".$i."</td><td>".$conta['data_realizada']."</td><td><strong>Saldo do dia:</strong></td><td align='right'><strong>". number_format($saldo,2,',','.')."</strong></td></tr>";
					} else {
*/
					$saldo += $conta['valor_realizado'];
						$classe1 = "text-danger";
					if ($saldo>=0){
						$classe1 = "text-primary";
					}
					echo "<tr><td>".$i."</td><td>".$conta['data_realizada']."</td><td>".$conta['descricao']."</td><td class='".$classe."'>". number_format($conta['valor_realizado'],2,',','.')."</td><td align='right' class='".$classe1."'>". number_format($saldo,2,',','.')."</td></tr>";
					$data = $conta['data_realizada'];
					$i++;
//					}
//					$saldo=$conta['saldo_acumulado'];
					
//					echo "<tr><td>".$i."</td><td>".$conta['descricao']."</td><td>". strftime('%a, %e/%b/%y',strtotime($conta['data_realizada']))."</td><td align='right'>".number_format($conta['valor_realizado'],2,',','.')."</td></tr>";
					$i++;
				}	
						echo "<tr><td>".$i."</td><td>".$data."</td></td> <td><td> </td><td align='right'>". number_format($saldo,2,',','.')."</td></tr>";
			?>	
			</tbody>
		</table>
	</div>
<?php
include_once('./include/footer.php');
?>
</body>
<?php
include_once('./include/scripts-database.php');
?>
<?php 
include_once('./include/end.php');
?>
