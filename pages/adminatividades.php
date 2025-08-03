<?php
$query = "SELECT eventos_marcados.*,imagens.url 
FROM eventos_marcados 
LEFT JOIN imagens ON eventos_marcados.imagem_id = imagens.imagem_id 
ORDER BY inicio DESC;";
$mensagem = "Nenhum evento encontrado!";
$atividades = mysqli_query($conexao,$query);
	$qtd_atendentes =0;
	$qtd_prospects = 0;
	$qtd_atividades = 0;
	$qtd_eventos = 0;
	$qtd_expositores = 0;
// Atendentes
$query = "SELECT * FROM candidatos WHERE rodizio>0 ORDER BY data_inscricao ASC, nome ASC LIMIT 10;";
$mensagem_candidatos = "Nenhum atendente encontrado!";
$atendentes = mysqli_query($conexao,$query);
$query = "SELECT count(*) AS qtd FROM candidatos;";
$resp = mysqli_query($conexao,$query);
if (mysqli_num_rows($resp)){
	$row = mysqli_fetch_array($resp);
	$qtd_atendentes = $row['qtd'];
}
$query = "SELECT count(*) AS qtd FROM expositores;";
$resp = mysqli_query($conexao,$query);
if (mysqli_num_rows($resp)){
	$row = mysqli_fetch_array($resp);
	$qtd_prospects = $row['qtd'];
}
$query = "SELECT count(*) AS qtd FROM eventos;";
$resp = mysqli_query($conexao,$query);
if (mysqli_num_rows($resp)){
	$row = mysqli_fetch_array($resp);
	$qtd_atividades = $row['qtd'];
}
$query = "SELECT count(*) AS qtd FROM eventos;";
$resp = mysqli_query($conexao,$query);
if (mysqli_num_rows($resp)){
	$row = mysqli_fetch_array($resp);
	$qtd_eventos = $row['qtd'];
}
$query = "SELECT count(*) AS qtd FROM expositores2024;";
$resp = mysqli_query($conexao,$query);
if (mysqli_num_rows($resp)){
	$row = mysqli_fetch_array($resp);
	$qtd_expositores = $row['qtd'];
}
?>
<body>
<main class="flex-shrink-0">
<?php
	include_once('./include/nav.php');
?>
	<div class="container mt-5">
		<h1>Atividades</h1>
		<div class="row wow fadeIn animated">
			<div class="col-sm">
				<div class="card border-left-success shadow h-100 py-2">
					<div class="card-body">
						<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Pendências</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">
								<?php echo '0'?>
							</div>
						</div>
						<div class="col-auto">
							<a href="/admin/pendencias">
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
							<div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Atendentes</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">
								<?php echo number_format($qtd_atendentes,0,",",".")?>
							</div>
						</div>
						<div class="col-auto">
							<a href="/admin/atendentes">
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
							<div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Eventos</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">
								<?php echo number_format($qtd_atividades,0,",",".")?>
							</div>
						</div>
						<div class="col-auto">
							<a href="/admin/eventos">
								<i class="fas fa-building fa-2x text-gray-300"></i>
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
								<div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Expositores</div>
								<div class="h5 mb-0 font-weight-bold text-gray-800">
									<?php echo number_format($qtd_expositores,0,",",".")?>
								</div>
							</div>
							<div class="col-auto">
								<a href="/admin/expositores">
									<i class="fas fa-users fa-2x text-gray-300"></i>
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
								<div class="text-xs font-weight-bold text-primary text-uppercase mb-1"><a href="/admin/prospects">Prospects</a></div>
								<div class="h5 mb-0 font-weight-bold text-gray-800">
									<?php echo number_format($qtd_prospects,0,",",".")?>
								</div>
							</div>
							<div class="col-auto">
								<a href="/admin/prospects">
									<i class="fas fa-users fa-2x text-gray-300"></i>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<div class="row justify-content-center text-center">
	<div class="col">
		<h3 class="text-center">Eventos</h3>
		<table class="table" id="table">
		<thead>
			<th>#</th>
			<th>
			Evento</th>
			<th>
			Local</th>
			<th>
			Data</th>
			<th>
			Dias</th>
		</thead>
			<?php
		if (mysqli_num_rows($atividades)>0){
		$i = 1;
		while($atividade = mysqli_fetch_array($atividades)){
	$data_inicio = new DateTime();
    $data_fim = new DateTime($atividade['inicio']);
					
    // Resgata diferença entre as datas
    $dateInterval = $data_fim->diff($data_inicio);
    
	$dis = false;
//	$evento_id = $row['evento_id'];
	$inicio = $atividade['inicio'];
	$final = strtotime($atividade['final']);
	$agora =strtotime(Date("Y-m-d"));
	//$agora = date_create($agora);
/*
	echo "Agora:<br><pre>";
	print_r($agora);
	echo "</pre><pre>";
	print_r($final);
	echo "</pre>";
*/
	$quando = "<i class='fa fa-check text-success' data-toggle='tooltip' data-placement='right' title='Vai acontecer'></i> faltam ";
	if ($agora>$final){
		$dis = true;
		$quando = "<i class='fa fa-check text-danger' data-toggle='tooltip' data-placement='right' title='Já aconteceu'></i> já passou ";
	}

			?>
			<tr>
				<td><?php echo $i?></td>
				<td class="">
					<div class="row"><div class="col"><img src="/<?php echo $atividade['url']?>" class="img-thumbnail"></div><div class="col"><h1><?php echo $atividade['nome']?></h1></div></div>
				
				
				</td>
				<td>
					<a href="<?php echo $atividade['maps']?>" target="_blank">
				<?php echo $atividade['local']?></a>
				</td>
				<td>
				<?php echo Date('d/m',strtotime($atividade['inicio']))?>
				</td>
				<td>
				<?php echo $quando . $dateInterval->days ." dias"?>
				</td>
			</tr>
			<?php		
			$i++;
				}
		} else {
			?>
			<tr><td colspan=3><?php echo $mensagem?></td></tr>
			<?php
		}	
			?>
<!--			<tr><td colspan="3"><a href="atividade.php" class=" btn btn-sm btn-primary rounded-pill">Nova atividade</a></td></tr>-->
		</table>
	</div>
<!--
	<div class="col">
		<h3 class="text-center">Atendentes</h3>
		<table class="table">
		<thead>
			<th>
			Atendente</th>
			<th>
			Responsável</th>
			<th>
			Telefone</th>
		</thead>
			<?php
		if (mysqli_num_rows($atendentes)>0){
				$i = 1;
				while($atividade = mysqli_fetch_array($atendentes)){
			?>
			<tr>
				<td>
				<?php echo $atividade['nome']?>
				</td>
				<td>
				<?php echo $atividade['responsavel']?>
				</td>
				<td>
				<?php echo $atividade['Telefone']?>
				</td>
			</tr>
			<?php		
				}
		} else {
			?>
			<tr><td colspan=3><?php echo $mensagem?></td></tr>
			<?php
		}	
			?>
			<tr><td colspan="3"><a href="atividade.php" class=" btn btn-sm btn-primary rounded-pill">Nova atividade</a></td></tr>
		</table>
	</div>
-->
	</div>
	</div>
</main>
</body>
<?php
include_once("include/footer-database.php");
include_once("include/scripts.php");
?>