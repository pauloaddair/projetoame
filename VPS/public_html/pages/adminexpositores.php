<?php
$qtd_atendentes =0;
$qtd_prospects = 0;
$qtd_atividades = 0;
$qtd_eventos = 0;
$qtd_expositores = 0;
$query = "SELECT empresas.*,empresas_telefones.numero,empresas_telefones.celular FROM empresas,empresas_telefones WHERE empresas.empresa_id = empresas_telefones.empresa_id ORDER BY empresa;";
$mensagem = "Nenhuma empresa encontrada!";
$atividades = mysqli_query($conexao,$query);
// Atendentes
$query = "SELECT * FROM empresas;";
$mensagem_candidatos = "Nenhum atendente encontrado!";
$atendentes = mysqli_query($conexao,$query);
$query = "SELECT count(*) AS qtd FROM candidatos;";
$resp = mysqli_query($conexao,$query);
if (mysqli_num_rows($resp)){
	$row = mysqli_fetch_array($resp);
	$qtd_atendentes = $row['qtd'];
}
$query = "SELECT count(*) AS qtd FROM empresas;";
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
$query = "SELECT count(*) AS qtd FROM empresas;";
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
		<header class="mt-5">
			<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="/atendentes">Atendentes</a></li>
				<li class="breadcrumb-item active" aria-current="page">Empresas</li>
			</ol>
			</nav>
			<h1 class="text-center">Empresas</h1>
		</header>
		<div class="row wow fadeIn animated">
			<div class="col-sm">
				<div class="card border-left-success shadow h-100 py-2">
					<div class="card-body">
						<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-primary text-uppercase mb-1"><a href="/admin/pendencias">Pendências</a></div>
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
							<div class="text-xs font-weight-bold text-primary text-uppercase mb-1"><a href="/admin/atendentes">Atendentes</a></div>
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
							<div class="text-xs font-weight-bold text-primary text-uppercase mb-1"><a href="/admin/atividades">Atividades</a></div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">
								<?php echo number_format($qtd_atividades,0,",",".")?>
							</div>
						</div>
						<div class="col-auto">
							<a href="/admin/atividades">
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
								<div class="text-xs font-weight-bold text-primary text-uppercase mb-1"><a href="/admin/empresas">Empresas</a></div>
								<div class="h5 mb-0 font-weight-bold text-gray-800">
									<?php echo number_format($qtd_expositores,0,",",".")?>
								</div>
							</div>
							<div class="col-auto">
								<a href="/admin/empresas">
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
		<h3 class="text-center">Empresas</h3>
		<table class="table" id="table">
		<thead>
			<th>
			Empresa</th>
			<th>
			Responsável</th>
			<th>
			Telefone</th>
			<th>
			E-mail</th>
		</thead>
			<?php
		if (mysqli_num_rows($atividades)>0){
				$i = 1;
				while($atividade = mysqli_fetch_array($atividades)){
/*
					    $data_inicio = new DateTime();
    $data_fim = new DateTime($atividade['Inicio']);

    // Resgata diferença entre as datas
    $dateInterval = $data_fim->diff($data_inicio);
    

*/
			?>
			<tr>
				<td><a href="<?php echo '/admin/contato/'.digitos($atividade['empresa_id'])?>/empresas">
				<?php 
				echo $atividade['empresa'];
				?>
					</a>
				</td>
				</a><td><a href="<?php echo '/admin/contato/'.digitos($atividade['empresa_id'])?>/empresas">
				<?php echo $atividade['nome']?>
				</a></td>
				<td><a href="<?php echo '/admin/contato/'.digitos($atividade['empresa_id'])?>/empresas">
					   <?php echo $atividade['numero']?>
				</a></td>
				<td><a href="<?php echo '/admin/contato/'.digitos($atividade['empresa_id'])?>/empresas">
						<?php echo $atividade['email']?>
				</a></td>
			</tr>
			<?php		
				}
		} else {
			?>
					
			<tr><td colspan=3><?php echo $mensagem?></td></tr>
			<?php
		}	
			?>
		</table>
	</div>
	</div>
	</div>
</main>
</body>
<?php
include_once("include/footer-database.php");
include_once("include/scripts.php");
?>
