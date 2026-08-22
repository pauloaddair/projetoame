<?php
// pages/casting.php - Gestão e Listagem de Candidatos/Atendentes do Projeto AME
$app_web_root = $GLOBALS['app_web_root'] ?? '/';
$titulo = "Casting & Gestão de Candidatos";
include_once('./include/funcoes.php');
include_once('./include/head-table.php');

if (isset($_SESSION['nivel']) && $_SESSION['nivel'] >= 3) {
	// Contagem de totais
	$q_totais = "SELECT 
	                SUM(CASE WHEN ativo = 1 THEN 1 ELSE 0 END) AS total_ativos,
	                SUM(CASE WHEN ativo = 0 OR ativo IS NULL THEN 1 ELSE 0 END) AS total_inativos,
	                COUNT(*) AS total_geral 
	             FROM candidatos";
	$r_totais = mysqli_query($conexao, $q_totais);
	$totais = mysqli_fetch_assoc($r_totais);
	$totAtivos = $totais['total_ativos'] ?? 0;
	$totInativos = $totais['total_inativos'] ?? 0;
	$totGeral = $totais['total_geral'] ?? 0;

	$query = "SELECT c.*, i.url AS perfil 
	          FROM candidatos c
	          LEFT JOIN imagens i ON c.imagem_id = i.imagem_id 
	          ORDER BY c.ativo DESC, c.nome ASC, c.data_inscricao DESC;";
	$resp = mysqli_query($conexao, $query);
	?>
	<body>
	<?php
		include_once('./include/nav.php');
		include_once('./include/admin_sidebar.php');
	?>
		<div class="container-fluid mt-3">
			<header class="mb-4">
				<div class="d-flex justify-content-between align-items-center flex-wrap">
					<div>
						<h1 class="h3 font-weight-bold text-dark mb-1">
							<i class="fas fa-users text-primary mr-2"></i>Casting & Candidatos Cadastrados
						</h1>
						<p class="text-muted small mb-0">Gestão de associados, atendentes capacitados e treinandos do Projeto AME.</p>
					</div>
					<div class="mt-2 mt-md-0 d-flex flex-wrap align-items-center">
						<a href="<?php echo $app_web_root; ?>inscrever" class="btn btn-success btn-sm rounded-pill shadow-sm">
							<i class="fas fa-user-plus mr-1"></i> Nova Inscrição
						</a>
						<a href="<?php echo $app_web_root; ?>admin/rodizio" class="btn btn-outline-primary btn-sm rounded-pill ml-2">
							<i class="fas fa-sync-alt mr-1"></i> Ver Fila do Rodízio
						</a>
					</div>
				</div>

				<!-- Barra de Filtro de Status -->
				<div class="d-flex justify-content-between align-items-center flex-wrap mt-3 pt-2 border-top">
					<div class="btn-group btn-group-toggle shadow-sm rounded-pill p-1 bg-white border mb-2" data-toggle="buttons" id="filtrosStatusContainer">
						<label class="btn btn-sm btn-outline-success active rounded-pill px-3 py-1 font-weight-bold" id="lbl_filtro_ativos" style="cursor: pointer;">
							<input type="radio" name="filtro_status" id="filtro_ativos" value="ativo" autocomplete="off" checked> 
							<i class="fas fa-check-circle mr-1"></i> Apenas Ativos <span class="badge badge-success ml-1"><?php echo $totAtivos; ?></span>
						</label>
						<label class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 font-weight-bold ml-1" id="lbl_filtro_todos" style="cursor: pointer;">
							<input type="radio" name="filtro_status" id="filtro_todos" value="todos" autocomplete="off"> 
							<i class="fas fa-users mr-1"></i> Mostrar Todos <span class="badge badge-secondary ml-1"><?php echo $totGeral; ?></span>
						</label>
						<label class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 font-weight-bold ml-1" id="lbl_filtro_inativos" style="cursor: pointer;">
							<input type="radio" name="filtro_status" id="filtro_inativos" value="inativo" autocomplete="off"> 
							<i class="fas fa-user-slash mr-1"></i> Inativos <span class="badge badge-danger ml-1"><?php echo $totInativos; ?></span>
						</label>
					</div>

					<nav aria-label="breadcrumb" class="mb-2">
					  <ol class="breadcrumb bg-light p-2 rounded shadow-sm small mb-0">
						<li class="breadcrumb-item"><a href="<?php echo $app_web_root; ?>admin">Painel</a></li>
						<li class="breadcrumb-item"><a href="<?php echo $app_web_root; ?>admin/atividades">Atividades</a></li>
						<li class="breadcrumb-item active" aria-current="page">Candidatos</li>
					  </ol>
					</nav>
				</div>
			</header>

			<div class="card shadow-sm border-0 mb-4">
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-hover table-striped w-100" id="table">
							<thead class="thead-dark">
								<tr>
									<th>#</th>
									<th>Foto</th>
									<th>Atendente / Nome</th>
									<th>Contato</th>
									<th>Status / Rodízio</th>
									<th class="text-center">Ações</th>
								</tr>
							</thead>
							<tbody>
							<?php
							$i = 1;
							while ($row = mysqli_fetch_array($resp)) {
								$perfil = $app_web_root . "img/profile.png";
								if (!empty($row['perfil'])) {
									$perfil = $app_web_root . ltrim($row['perfil'], '/');
								}
								$telFormatado = !empty($row['Telefone']) ? telephone($row['Telefone']) : 'Não inf.';
								$waLink = !empty($row['Telefone']) ? 'https://wa.me/' . formataWA($row['Telefone']) : '';
								
								$isAtivo = (isset($row['ativo']) && $row['ativo'] == 1);
								$statusAttr = $isAtivo ? 'ativo' : 'inativo';
								
								$statusBadge = $isAtivo 
									? '<span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i>Ativo</span>' 
									: '<span class="badge badge-danger px-2 py-1" style="background-color: #dc3545; font-size: 80%;"><i class="fas fa-ban mr-1"></i>Inativo</span>';
								
								$imgStyle = $isAtivo 
									? 'object-fit: cover;' 
									: 'object-fit: cover; opacity: 0.5; filter: grayscale(30%);';
								
								$rowClass = $isAtivo ? '' : 'table-light text-muted';
								$tipoInscrito = !empty($row['inscrito']) ? ucfirst($row['inscrito']) : 'Atendente';
							?>
							<tr class="<?php echo $rowClass; ?>" data-status="<?php echo $statusAttr; ?>">
								<td class="align-middle text-muted small"><?php echo $i; ?></td>
								<td class="align-middle text-center" style="width: 50px;">
									<a href="<?php echo $app_web_root; ?>trocafoto/<?php echo digitos($row['candidato_id']); ?>" title="Alterar foto">
										<img src="<?php echo $perfil; ?>" height="42" width="42" class="rounded-circle shadow-sm" style="<?php echo $imgStyle; ?>">
									</a>
								</td>
								<td class="align-middle">
									<strong class="<?php echo $isAtivo ? 'text-dark' : 'text-muted'; ?>"><?php echo htmlspecialchars($row['nome']); ?></strong>
									<br><small class="text-muted"><i class="fas fa-id-badge mr-1"></i>ID: <?php echo $row['candidato_id']; ?> | <?php echo $tipoInscrito; ?></small>
								</td>
								<td class="align-middle small">
									<div>
										<?php if (!empty($row['Email'])): ?>
											<a href="mailto:<?php echo htmlspecialchars($row['Email']); ?>" class="text-dark text-decoration-none" title="Enviar E-mail para <?php echo htmlspecialchars($row['nome']); ?>">
												<i class="far fa-envelope text-primary mr-1"></i><?php echo htmlspecialchars($row['Email']); ?>
											</a>
										<?php else: ?>
											<span class="text-muted"><i class="far fa-envelope text-muted mr-1"></i>Não inf.</span>
										<?php endif; ?>
									</div>
									<div class="mt-1">
										<?php if (!empty($row['Telefone'])): 
											$numLimpo = preg_replace('/\D/', '', $row['Telefone']);
											$telHref = (strlen($numLimpo) <= 11) ? '+55' . $numLimpo : '+' . $numLimpo;
										?>
											<a href="tel:<?php echo $telHref; ?>" class="text-dark text-decoration-none mr-2" title="Ligar para <?php echo $telFormatado; ?>">
												<i class="fas fa-phone-alt text-info mr-1"></i><?php echo $telFormatado; ?>
											</a>
											<?php if ($waLink): ?>
												<a href="<?php echo $waLink; ?>" target="_blank" class="text-success font-weight-bold" title="Chamar no WhatsApp" style="font-size: 1.15em;">
													<i class="fab fa-whatsapp"></i>
												</a>
											<?php endif; ?>
										<?php else: ?>
											<span class="text-muted"><i class="fas fa-phone text-muted mr-1"></i>Não inf.</span>
										<?php endif; ?>
									</div>
								</td>
								<td class="align-middle small">
									<?php echo $statusBadge; ?>
									<span class="badge badge-light border ml-1">Rodízio: #<?php echo $row['rodizio'] ?? 0; ?></span>
								</td>
								<td class="align-middle text-center" style="white-space: nowrap;">
									<a href="<?php echo $app_web_root; ?>curriculo/<?php echo digitos($row['candidato_id']); ?>" class="btn btn-sm btn-outline-info rounded-pill py-1 px-2" title="Ver Currículo Inclusivo">
										<i class="fas fa-file-pdf mr-1"></i>CV
									</a>
									<a href="<?php echo $app_web_root; ?>editacandidato/<?php echo digitos($row['candidato_id']); ?>" class="btn btn-sm btn-outline-primary rounded-pill py-1 px-2 ml-1" title="Editar Ficha">
										<i class="fas fa-edit mr-1"></i>Editar
									</a>
								</td>
							</tr>
							<?php 
								$i++;
							}
							?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	<?php
	include_once('./include/admin_sidebar_footer.php');
	include_once('./include/footer-database.php');
} else {
	include_once('pages/restrito.php');
}
?>
<script type="text/javascript">
$(document).ready(function () {
	// Filtro personalizado do DataTables por status (Ativo / Todos / Inativo)
	if ($.fn.dataTable && $.fn.dataTable.ext) {
		$.fn.dataTable.ext.search.push(
			function (settings, data, dataIndex) {
				var filtroSelecionado = $('input[name="filtro_status"]:checked').val();
				if (!filtroSelecionado || filtroSelecionado === 'todos') {
					return true;
				}
				var rowNode = settings.aoData[dataIndex].nTr;
				var statusLinha = $(rowNode).attr('data-status');
				return statusLinha === filtroSelecionado;
			}
		);

		$('input[name="filtro_status"]').on('change', function () {
			if ($.fn.DataTable.isDataTable('#table')) {
				$('#table').DataTable().draw();
			}
		});

		// Aplica o filtro padrão (Apenas Ativos)
		setTimeout(function () {
			if ($.fn.DataTable.isDataTable('#table')) {
				$('#table').DataTable().draw();
			}
		}, 100);
	}
});
</script>
</body>
<?php
include_once('./include/scripts.php');
include_once('./include/end.php');
?>

