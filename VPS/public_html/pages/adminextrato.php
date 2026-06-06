<?php
$ano = intval(Date("Y"));
$mes = intval(Date("m"));
if (array_key_exists(2,$parametros)){
	$ano = $parametros[2];
}
if (array_key_exists(3,$parametros)){
	$mes = $parametros[3];
}
$anoantes = $ano;
$anodepois = $ano;
$mesantes = $mes -1;
$mesdepois = $mes +1;
if($mesantes<1){
//	$mes = 12;
	$mesantes = 12;	
	$anoantes = $ano-1;
}
if($mesdepois>12){
	$mesdepois = 1;	
	$anodepois = $ano+1;
//	$ano = $ano+1;
}
$query = "SELECT 
	id,
    data_prevista,
    descricao, plano_id,plano,
    valor_previsto, valor_realizado, 
    @saldo := @saldo + valor_previsto AS saldo_acumulado
FROM (
    SELECT 
    	id,
        'Saldo Anterior' AS descricao,
    	contabil_plano_itens.plano_ID AS plano_id,
    	contabil_plano_itens.plano AS plano,
        NULL AS data_prevista,
        IFNULL(SUM(valor_realizado), 0) AS valor_realizado,
        IFNULL(SUM(valor_previsto), 0) AS valor_previsto
    FROM 
        contabil_movimento,contabil_plano_itens
    WHERE 
    	contabil_movimento.plano_ID = contabil_plano_itens.plano_ID
        AND data_prevista <  '".$ano."-".$mes."-1'    
    UNION ALL
    
    SELECT 
		id,
        descricao,
    	contabil_plano_itens.plano_ID AS plano_id,
    	contabil_plano_itens.plano AS plano,
        data_prevista,
        valor_previsto,
        valor_realizado
    FROM 
        contabil_movimento,contabil_plano_itens
    WHERE 
   		contabil_movimento.plano_ID = contabil_plano_itens.plano_ID
        AND YEAR(data_prevista) = ".$ano." 
        AND MONTH(data_prevista) = ".$mes."
    ORDER BY 
        data_prevista
) AS extrato,
(SELECT @saldo := 0) AS inicializador;";
$result = mysqli_query($conexao,$query);
?>
<body>
		<?php 
		include_once('./include/nav.php');
		?>
	<div class="container mt-5">
		<?php 
		if ($_SESSION['id']<>""){
		?>
		<header class="p-5">
			<h1>Extrato de <?php echo $mes?>/<?php echo $ano?></h1>
			<span class="badge badge-light m-1 rounded-pill"><a href='/admin'>Balanço</a></span>
			<span class="badge badge-info m-1 rounded-pill"><a href='/admin/extrato/<?php echo $anoantes?>/<?php echo $mesantes?>'><?php echo $mesantes?>/<?php echo $anoantes?></a></span>
			<span class="badge badge-success m-1 rounded-pill"><?php echo $mes?>/<?php echo $ano?></span>
			<span class="badge badge-info m-1 rounded-pill"><a href='/admin/extrato/<?php echo $anoantes?>/<?php echo $mesdepois?>'><?php echo $mesdepois?>/<?php echo $anodepois?></a></span>
		</header>
	
		<?php 
/*
		echo "<pre>";
		print_r($parametros);
		echo "</pre>";
*/
		}
		?>
		<table id="table" class="table">
		<thead>
			<th><strong>Item</strong></th>
			<th><strong>Data</strong></th>
			<th><strong>Descrição</strong></th>
			<th><strong>Plano</strong></th>
			<th><strong>Valor</strong></th>
			<th><strong>Saldo</strong></th>
		</thead>
			<tbody>
			<?php 
				$i=1;
			while ($row=mysqli_fetch_array($result)){
				$link = "#";
				$data = "";
				if ($i>1){
					$data = date("Y/m/d",strtotime($row['data_prevista']));
					$link = "<a href='/admin/movimento/".digitos($row['id'])."'>".$i."<i class='fa fa-arrow-right ml-1 text-info'></i></a></td><td>".$data."</td>";
					echo "<tr><td>".$link."</td><td>".$row['descricao']."</td><td><a href='/admim/conta/".$ano."/".$mes."/".$row['plano_id']."'>".$row['plano']."</a></td><td align='right'>".number_format($row['valor_previsto'],2,",",".")."</td><td align='right'>".number_format($row['saldo_acumulado'],2,",",".")."</td></tr>";
				} else {
					echo "<tr><td>".$link."</td><td>".$data."<td>".$row['descricao']."</td><td></td><td align='right'>".number_format($row['valor_previsto'],2,",",".")."</td><td align='right'>".number_format($row['saldo_acumulado'],2,",",".")."</td></tr>";				
				}
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
