<?php
$titulo = "Modelo";
// include_once('./include/conexao.php');
// include_once('./include/funcoes.php');
include_once('./include/head-table.php');
if ($_SESSION['id']<>""){
	// A PAGAR
	$query = "SELECT *
	FROM contabil_movimento2 
	WHERE valor_previsto <= 0 
	AND data_prevista > NOW();";
	$apagar = mysqli_query($conexao,$query);
?>
<body>
	<div class="container mt-5">
		<header class="p-5">
			<h1>Contas a pagar</h1>
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="/admin">Geral</a></li>
				  <li class="breadcrumb-item"><a href="/admin/saldo/atual">Saldo atual</a></li>
				<li class="breadcrumb-item"><a href="/admin/receber">A receber</a></li>
				<li class="breadcrumb-item active" aria-current="page">A pagar</li>
				<li class="breadcrumb-item"><a href="/admin/saldo/final">Saldo final</a></li>
			  </ol>
			</nav>
		</header>
		<?php 
			include_once('./include/nav.php');
		?>
		<div class="row wow fadeIn animated mb-5">
			<div class="col-sm">
				<table class="table" id="table">
				<thead>
					<th>#</th>
					<th>Lançamento</th>
					<th>Data prevista</th>
					<th>Valor</th>
					<th>Comprovante</th>
					<th>Ação</th>
				</thead>
				<tbody>
					<?php 
						$i=1;
						while($pagar = mysqli_fetch_array($apagar)){
					?>
					<tr><td><?php echo $i?></td><td><?php echo $pagar['descricao']?></td><td><?php echo $pagar['data_prevista']?></td><td><?php echo $pagar['valor_previsto']?></td><td>comprovante</td><td>ação</td></tr>
					<?php 
							
						}
						$i++;
					?>
				</tbody>
				</table>
				
			</div>
	</div>
	</div>
		<?php 
		include_once('./include/footer-table.php');
		?>
</body>
<?php
			include_once('./include/scripts.php');
		} else {
			include_once('./include/restrito.php');
		}
?>
<?php 
include_once('./include/end.php');
?>
