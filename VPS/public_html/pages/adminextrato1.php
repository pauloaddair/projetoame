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
    data_realizada,
    descricao,
    valor_previsto,
    @saldo := @saldo + valor_previsto AS saldo_acumulado
FROM (
    SELECT 
    	id,
        'Saldo Anterior' AS descricao,
        NULL AS data_realizada,
        IFNULL(SUM(valor_previsto), 0) AS valor_previsto
    FROM 
        contabil_movimento2
    WHERE 
        data_realizada < '".$ano."-".$mes."-1'    
    UNION ALL
    
    SELECT 
		id,
        descricao,
        data_realizada,
        valor_previsto
    FROM 
        contabil_movimento2
    WHERE 
        YEAR(data_realizada) = ".$ano." 
        AND MONTH(data_realizada) = ".$mes."
    ORDER BY 
        data_realizada
) AS extrato,
(SELECT @saldo := 0) AS inicializador;";
$result = mysqli_query($conexao,$query);
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
			<h1>Extrato de <?echo $mes?>/<?echo $ano?></h1>
			<span class="badge badge-light m-1 rounded-pill"><a href='/admin'>Balanço</a></span>
			<span class="badge badge-info m-1 rounded-pill"><a href='/admin/extrato/<? echo $anoantes?>/<?echo $mesantes?>'><?echo $mesantes?>/<?echo $anoantes?></a></span>
			<span class="badge badge-success m-1 rounded-pill"><?echo $mes?>/<?echo $ano?></span>
			<span class="badge badge-info m-1 rounded-pill"><a href='/admin/extrato/<? echo $anoantes?>/<?echo $mesdepois?>'><?echo $mesdepois?>/<?echo $anodepois?></a></span>
		</header>
	
		<?
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
			<th><strong>Valor</strong></th>
			<th><strong>Saldo</strong></th>
		</thead>
			<tbody>
			<?
				$i=1;
			while ($row=mysqli_fetch_array($result)){
				$link = "<td>#</td><td></td>";
				if ($i>1){
					$link = "<td><a href='/admin/movimento/".digitos($row['id'])."'>".$i."<i class='fa fa-arrow-right ml-1 text-info'></i></a></td><td>".date("Y/m/d",strtotime($row['data_realizada']))."</td>";
				}
				echo "<tr>".$link."<td>".$row['descricao']."</td><td align='right'>".number_format($row['valor_previsto'],2,",",".")."</td><td align='right'>".number_format($row['saldo_acumulado'],2,",",".")."</td></tr>";
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
