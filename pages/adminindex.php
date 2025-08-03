<?php
include_once('./include/conexao.php');
include_once('./include/funcoes.php');
include_once('./include/head-table.php');
/*
$query = "SELECT 
    data_realizada,
    descricao,
    valor_realizado,
    @saldo := @saldo + valor_realizado AS saldo_acumulado
FROM (
    SELECT 
        'Saldo Anterior' AS descricao,
        NULL AS data_realizada,
        IFNULL(SUM(valor_realizado), 0) AS valor_realizado
    FROM 
        contabil_movimento2
    WHERE 
        data_realizada < '2024-10-01'
    
    UNION ALL
    
    SELECT 
        descricao,
        data_realizada,
        valor_realizado
    FROM 
        contabil_movimento2
    WHERE 
        YEAR(data_realizada) = 2024 
        AND MONTH(data_realizada) = 10
    ORDER BY 
        data_realizada
) AS extrato,
(SELECT @saldo := 0) AS inicializador;";
*/
$query = "SELECT 
    mes_ano,
    total_valor,
	qtd_itens,
    @saldo_acumulado := @saldo_acumulado + total_valor AS saldo_final
FROM (
    SELECT 
        DATE_FORMAT(data_prevista, '%Y-%m') AS mes_ano,
        SUM(valor_previsto) AS total_valor,
        COUNT(valor_previsto) AS qtd_itens
    FROM 
        contabil_movimento2
    GROUP BY 
        YEAR(data_prevista), 
        MONTH(data_prevista)
    ORDER BY 
        data_prevista ASC
) AS movimentos,
(SELECT @saldo_acumulado := 0) AS inicializador;";
$result = mysqli_query($conexao,$query);
$query = 'SELECT sum(valor_previsto) AS saldo 
FROM `contabil_movimento2`;';
$saldo_total = mysqli_query($conexao,$query);
$saldo = mysqli_fetch_array($saldo_total);
$total = $saldo['saldo'];
/*
$query = 'SELECT sum(valor_realizado) AS saldo 
FROM contabil_movimento2;';
$saldo_atual = mysqli_query($conexao,$query);
$saldo = mysqli_fetch_array($saldo_atual);
$atual = $saldo['saldo'];
*/
$query = 'SELECT count(id) AS pendencias 
FROM `contabil_movimento2` WHERE valor_realizado IS Null;';
$resultado = mysqli_query($conexao,$query);
$pendencias = mysqli_fetch_array($resultado);
$pendente = $pendencias['pendencias'];

// A PAGAR
$query = "SELECT SUM(valor_previsto) AS pagar, COUNT(valor_previsto) AS qtd
FROM contabil_movimento2 
WHERE valor_previsto <= 0 
AND data_prevista > NOW();";
$apagar = mysqli_query($conexao,$query);
$pagar = mysqli_fetch_array($apagar);

// A RECEBER
$query = "SELECT SUM(valor_previsto) AS receber, COUNT(valor_previsto) AS qtd
FROM contabil_movimento2 
WHERE valor_previsto > 0 
AND data_prevista > NOW();";
$areceber = mysqli_query($conexao,$query);
$receber = mysqli_fetch_array($areceber);
$atual = $total-$pagar['pagar']-$receber['receber'];
?>
<body>
		<?
		include_once('./include/nav.php');
		?>
	<div class="container mt-5">
		<?
		if ($_SESSION['id']<>""){
		?>
		<header class="p-5">
			<h1>Balanço Geral</h1>
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
				<li class="breadcrumb-item active" aria-current="page">Geral</li>
				<li class="breadcrumb-item"><a href="/admin/saldo/atual">Saldo atual</a></li>
				<li class="breadcrumb-item"><a href="/admin/receber">A receber</a></li>
				<li class="breadcrumb-item"><a href="/admin/pagar">A pagar</a></li>
				<li class="breadcrumb-item"><a href="/admin/saldo/final">Saldo final</a></li>
			  </ol>
			</nav>
		</header>
		<div class="row wow fadeIn animated mb-5">
			<div class="col-sm">
				<div class="card border-left-success shadow h-100 py-2">
					<div class="card-body">
						<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-primary text-uppercase mb-1"><a href="/admin/saldo/atual">Saldo Atual</a></div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">
								<? echo number_format($atual,2,",",".")?>
							</div>
						</div>
						<div class="col-auto">
							<a href="/admin/saldo/atual">
							<i class="fas fa-user fa-2x text-gray-300"></i></div>
							</a>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm">
				<div class="card border-left-success shadow h-100 py-2">
					<div class="card-body">
						<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-primary text-uppercase mb-1"><a href="/admin/pagar">A pagar</a></div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">
								<? echo number_format(abs($pagar['pagar']),2,",",".")?><small> (<? echo $pagar['qtd']?> lançamentos)</small>
							</div>
						</div>
						<div class="col-auto">
							<a href="/admin/pagar">
								<i class="fas fa-pen-square fa-2x text-gray-300"></i>
							</a>
						</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm">
				<div class="card border-left-success shadow h-100 py-2">
					<div class="card-body">
						<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-primary text-uppercase mb-1"><a href="/admin/receber">A receber</a></div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">
								<? echo number_format($receber['receber'],2,",",".")?><small> (<? echo $receber['qtd']?> lançamentos)</small>
							</div>
						</div>
						<div class="col-auto">
							<a href="/admin/receber">
							<i class="fas fa-user fa-2x text-gray-300"></i></div>
							</a>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm">
				<div class="card border-left-success shadow h-100 py-2">
					<div class="card-body">
						<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-primary text-uppercase mb-1"><a href="/admin/saldo/final">Saldo final</a></div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">
								<? echo number_format($total,2,",",".")?>
							</div>
						</div>
						<div class="col-auto">
							<a href="/admin/saldo/final">
								<i class="fas fa-building fa-2x text-gray-300"></i>
							</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row wow fadeIn animated">
       <canvas id="saldoChart"></canvas>
		<?
/*
		echo "<pre>";
		print_r($_SESSION);
		echo "</pre>";
*/
		}
		?>
			<table id="table" class="table">
				<thead>
					<th>#</th>
					<th>Período (ano/mês)</th>
					<th align='right'>Total</th>
					<th align='right'>Saldo</th>
				</thead>
				<tbody>
				<?
					$i=1;
				while ($row=mysqli_fetch_array($result)){
					echo "<tr><td>".$i."</td><td><a href='/admin/extrato/".before('-',$row['mes_ano'])."/".after('-',$row['mes_ano'])."'>".$row['mes_ano']." (".strtoupper(mes(intval(after('-',$row['mes_ano']))-1)).") "."<i class='fa fa-arrow-right ml-1 text-info'></i></a></td><td align='right'>".number_format($row['total_valor'],2,",",".")." (".$row['qtd_itens'].")</td><td align='right'>".number_format($row['saldo_final'],2,",",".")."</td></tr>";
					$i++;
				}	
				?>
				</tbody>
			</table>
		</div>
	</div>
	</div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            fetch('/include/get_saldo_mensal.php')
            .then(response => response.json())
            .then(data => {
                let labels = data.map(item => item.mes_ano);
                let totalMes = data.map(item => parseFloat(item.total_mes));
                let saldoAcumulado = data.map(item => parseFloat(item.saldo_acumulado));

                const ctx = document.getElementById('saldoChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Lançamento Mensal',
                                data: totalMes,
                                borderColor: 'blue',
                                backgroundColor: 'rgba(0, 0, 255, 0.2)',
                                fill: true
                            },
                            {
                                label: 'Saldo Acumulado',
                                data: saldoAcumulado,
                                borderColor: 'green',
                                backgroundColor: 'rgba(0, 128, 0, 0.2)',
                                fill: true
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'top' }
                        },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            })
            .catch(error => console.error('Erro ao buscar os dados:', error));
        });
    </script>
</body>
<?php
include_once('./include/footer-database-noorder.php');
?>
<script>
	$(document).ready(function () {
//		$('#example').DataTable();
		var table = new DataTable('#table', {
			language: {
				url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json',
			},
			order: [[1, 'desc']]
		});		
	});
</script>
<?
include_once('./include/scripts.php');
?>
