<?php
$titulo = "A.B.I.A.T. - Ass. Bras. Inclusão Através do Trabalho";
include_once("include/conexao.php");
include_once("include/funcoes.php");
include_once("include/head-table.php");
// Atividades
/*
$query = "SELECT atividades.atividade,locais.local,expositores.NomeFantasia,expositores.Empresa 
FROM atividades, locais, expositores
WHERE atividades.expositor_id = expositores.expositor_id
AND data_final > NOW() ORDER BY atividade;";
*/
$query = "SELECT * FROM eventos WHERE  Inicio > CURDATE() ORDER BY Inicio;";
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
	<div class="container mt-5">
		<h1>Próximos eventos</h1>
		<div class="row wow fadeIn animated">
			<div class="col-sm">
				<div class="card border-left-success shadow h-100 py-2">
					<div class="card-body">
						<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Pendências</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">
								<? echo '0'?>
							</div>
						</div>
						<div class="col-auto">
							<i class="fas fa-pen-square fa-2x text-gray-300"></i></div>
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
								<? echo number_format($qtd_atendentes,0,",",".")?>
							</div>
						</div>
						<div class="col-auto">
							<i class="fas fa-user fa-2x text-gray-300"></i></div>
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
								<? echo number_format($qtd_atividades,0,",",".")?>
							</div>
						</div>
						<div class="col-auto">
							<i class="fas fa-building fa-2x text-gray-300"></i></div>
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
								<? echo number_format($qtd_expositores,0,",",".")?>
							</div>
						</div>
						<div class="col-auto">
							<i class="fas fa-users fa-2x text-gray-300"></i></div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm">
				<div class="card border-left-success shadow h-100 py-2">
					<div class="card-body">
						<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Prospects</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">
								<? echo number_format($qtd_prospects,0,",",".")?>
							</div>
						</div>
						<div class="col-auto">
							<i class="fas fa-users fa-2x text-gray-300"></i></div>
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
			<th>
			Evento</th>
			<th>
			Local</th>
			<th>
			Data</th>
			<th>
			Dias</th>
		</thead>
			<?
		if (mysqli_num_rows($atividades)>0){
				$i = 1;
				while($atividade = mysqli_fetch_array($atividades)){
					    $data_inicio = new DateTime();
    $data_fim = new DateTime($atividade['Inicio']);

    // Resgata diferença entre as datas
    $dateInterval = $data_fim->diff($data_inicio);
    

			?>
			<tr>
				<td class="">
				<? echo $atividade['Evento']?>
				</td>
				<td>
				<? echo $atividade['Local']?>
				</td>
				<td>
				<? echo $atividade['Inicio']?>
				</td>
				<td>
				<? echo $dateInterval->days?>
				</td>
			</tr>
			<?		
				}
		} else {
			?>
			<tr><td colspan=3><? echo $mensagem?></td></tr>
			<?
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
			<?
		if (mysqli_num_rows($atendentes)>0){
				$i = 1;
				while($atividade = mysqli_fetch_array($atendentes)){
			?>
			<tr>
				<td>
				<? echo $atividade['nome']?>
				</td>
				<td>
				<? echo $atividade['responsavel']?>
				</td>
				<td>
				<? echo $atividade['Telefone']?>
				</td>
			</tr>
			<?		
				}
		} else {
			?>
			<tr><td colspan=3><? echo $mensagem?></td></tr>
			<?
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
<?
include_once("include/footer-database.php");
include_once("include/scripts.php");
include_once("include/end.php");
?>