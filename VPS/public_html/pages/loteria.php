<?php
$titulo = "Loteria FEDERAL";
// include_once('./include/conexao.php');
include_once('./include/funcoes.php');
// include_once('./include/head.php');
$content = file_get_contents("https://servicebus2.caixa.gov.br/portaldeloterias/api/federal/");
$jsonobj = json_decode($content);

?>
<body>
	<div class="container">
	<div class="row">
	<div class="col col-md-8 col-lg-6">
		<header>
			<h1><?php echo $titulo?></h1>
		</header>
		<ul>
		<li>Número do sorteio: 
		<?php echo $jsonobj->numero?>		
		</li>
		<li>Data apuração: 
		<?php echo $jsonobj->dataApuracao?>		
		</li>
		<li>1º PRÊMIO: <strong>
		<?php echo $jsonobj->dezenasSorteadasOrdemSorteio[0]?></strong>		
		</li>
		<li>2º PRÊMIO: 
		<?php echo $jsonobj->dezenasSorteadasOrdemSorteio[1]?>		
		</li>
		<li>3º PRÊMIO: 
		<?php echo $jsonobj->dezenasSorteadasOrdemSorteio[2]?>		
		</li>
		<li>4º PRÊMIO: 
		<?php echo $jsonobj->dezenasSorteadasOrdemSorteio[3]?>		
		</li>
		<li>5º PRÊMIO: 
		<?php echo $jsonobj->dezenasSorteadasOrdemSorteio[4]?>		
		</li>
		</ul>
	</div>
<?php 
//echo "</br><pre>".$content."</pre>";
	?>
		</div>
	</div>
	<?php 
include_once('./include/footerbt.php');
?>
</body>
<?php
include_once('./include/scripts.php');
?>
<?php 
include_once('./include/end.php');
?>
