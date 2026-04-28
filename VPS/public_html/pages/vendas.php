<?php
//include_once('./include/conexao.php');
//include_once('./include/funcoes.php');
// include_once('./include/head.php');
if (isset($_SESSION['id'])){
	$usuario_id = $_SESSION['id'];
$query = "SELECT * FROM produtos ORDER BY produto;";
$produto_id = 0;
$qtd = 0;
$total=0;
$custo = 0;
$produtos = mysqli_query($conexao,$query);
/*
echo "<pre>";
print_r($_POST);
echo "</pre>";
*/
if ($_SERVER['REQUEST_METHOD']=="POST"){
	$produto_id = $_POST['produto_id'];
	$query = "SELECT * FROM produtos WHERE produto_id = ".$produto_id." ORDER BY produto;";
	if (isset($_POST['registrar'])){
		$produto_id = $_POST['produto_id'];
		$descricao = $_POST['descricao'];
		$unitario = $_POST['valor'];
		$qtd = $_POST['qtd'];
		$custo = $_POST['custo'];
		$valor = floatval($_POST['valor']);
		$total = $valor * $qtd;
		$custo_total = $custo * $qtd;
		$queryvendas = "INSERT INTO `vendas`(`origem`, `produto_id`, `usuario_id`, `descricao`, `unitario`, `qtd`, `total`, `total_custo`) VALUES ('FESPA2024','".$produto_id."','".$usuario_id."','".$descricao."','".$unitario."','".$qtd."','".$total."','".$custo_total."')";
//		echo $queryvendas . "<br>";
		$resp = mysqli_query($conexao,$queryvendas);
		
	}
}
$queryvendas = "SELECT COUNT(venda_id) as qtd,SUM(total) AS total, SUM(total_custo) AS custo FROM vendas;";
$totais = mysqli_query($conexao,$queryvendas);
if (mysqli_num_rows($totais)>0){
	$totais = mysqli_fetch_assoc($totais);
	$db_qtd = $totais['qtd'];
	$db_total = $totais['total'];
	$db_custo = $totais['custo'];
}
?>
<body>
	<div class="container">
	<header>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="/vendas">Vendas</a></li>
					<li class="breadcrumb-item active" aria-current="page">Venda realizada</li>
				</ol>
			</nav>
			<h1>Vendas</h1>
		</header>
		<div class="row justify-content-center">
			<div class="row no-gutters align-items-center mr-2 mb-2 border shadow rounded p-1">
				<div class="col ">
					<div class="text-xs font-weight-bold text-primary text-uppercase mb-1 mr-1"><a href="/admin/pendencias">Vendas</a>
					</div>
					<div class="h5 mb-0 font-weight-bold text-gray-800">
					<?php echo number_format($db_qtd,0,",",".")?>
					</div>
				</div>
				<div class="col-auto">
				<a href="/admin/pendencias">
				<i class="fas fa-coins fa-2x text-gray-300"></i>
				</a>
				</div>
			</div>
			<div class="row no-gutters align-items-center mr-2 mb-2 border shadow rounded p-1">
				<div class="col ">
					<div class="text-xs font-weight-bold text-primary text-uppercase mb-1 mr-1">Total
					</div>
					<div class="h5 mb-0 font-weight-bold text-gray-800">
					<?php echo number_format($db_total,2,",",".")?>
					</div>
				</div>
				<div class="col-auto">
				
				<i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
				
				</div>
			</div>
			<div class="row no-gutters align-items-center mr-2 mb-2 border shadow rounded p-1">
				<div class="col ">
					<div class="text-xs font-weight-bold text-danger text-uppercase mb-1 mr-1">Custo
					</div>
					<div class="h5 mb-0 font-weight-bold text-gray-800">
					<?php echo number_format($db_custo,2,",",".")?>
					</div>
				</div>
				<div class="col-auto">
				<a href="/admin/pendencias">
				<i class="fas fa-dollar-sign fa-2x text-danger"></i>
				</a>
				</div>
			</div>

		</div>
		<div class="row">
			<form method="post";
			<div class="col">
			<div class="card border shadow-1 p-2">
				<?php 
					if ($produto_id==0){
						$botao = "Selecionar";
					?>
						<select name="produto_id" required>
						<option value=0>Escolha o produto</option>
					<?php 
						while ($produto = mysqli_fetch_assoc($produtos)){
							echo "<option value='".$produto['produto_id']."'>".$produto['produto']."</option>";
						}
						?>
						</select>
						<?php 
					} else {
						$botao = "Registar";
						$produtos = mysqli_query($conexao,$query);
						$produto = mysqli_fetch_assoc($produtos);
						?>
						<input type="hidden" name="produto_id" id="produto_id" value="<?php echo $produto_id?>">
						<input type="hidden" name="registrar" id="registrar" value="1">
						<input type="hidden" name="custo" id="custo" value="<?php echo $produto['custo']?>">
						<div class="md-form">
						<i class="far fa-map prefix grey-text"></i>
						<input type="text" id="produto" name="produto" class="form-control" placeholder="produto" value="<?php echo $produto['produto']?>">
						<label for="produto">produto vendido</label>
						</div>
						<div class="md-form">
						<i class="far fa-map prefix grey-text"></i>
						<input type="text" id="descricao" name="descricao" class="form-control" placeholder="produto" value="<?php echo $produto['descricao']?>">
						<label for="descricao">anotações sobre a venda</label>
						</div>
						<div class="md-form">
						<i class="far fa-map prefix grey-text"></i>
						<input type="text" id="valor" name="valor" class="form-control" placeholder="valor" value="<?php echo number_format($produto['valor'],2,",",".")?>">
						<label for="produto">valor</label>
						</div>
						<div class="md-form">
						<i class="far fa-map prefix grey-text"></i>
						<input type="number" id="qtd" name="qtd" class="form-control" placeholder="quantidade" value="1" min="1">
						<label for="qtd">quantidade</label>
						</div>
						<?php 

					}
				?>

				<button class="btn btn-primary btn-sm"><?php echo $botao?></button>	
			</div>
			</form>
		</div>
		</div>
	</div>
	
<?php
	
} else {
	include_once('restrito.php');
}
include_once('./include/footer.php');
?>
</body>
<?php
include_once('./include/scripts.php');
?>
<?php 
include_once('./include/end.php');
?>
