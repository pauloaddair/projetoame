<?php
// pages/casting.php - Gestão e Listagem de Candidatos/Atendentes do Projeto AME
$app_web_root = $GLOBALS['app_web_root'] ?? '/';
$titulo = "Casting & Gestão de Candidatos";
include_once('./include/funcoes.php');
include_once('./include/head-table.php');

if (isset($_SESSION['nivel']) && $_SESSION['nivel'] >= 3) {
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
					<div class="mt-2 mt-md-0">
						<a href="<?php echo $app_web_root; ?>inscrever" class="btn btn-success btn-sm rounded-pill shadow-sm">
							<i class="fas fa-user-plus mr-1"></i> Nova Inscrição
						</a>
						<a href="<?php echo $app_web_root; ?>admin/rodizio" class="btn btn-outline-primary btn-sm rounded-pill ml-2">
							<i class="fas fa-sync-alt mr-1"></i> Ver Fila do Rodízio
						</a>
					</div>
				</div>
				<nav aria-label="breadcrumb" class="mt-3">
				  <ol class="breadcrumb bg-light p-2 rounded shadow-sm small">
					<li class="breadcrumb-item"><a href="<?php echo $app_web_root; ?>admin">Painel</a></li>
					<li class="breadcrumb-item"><a href="<?php echo $app_web_root; ?>admin/atividades">Atividades</a></li>
					<li class="breadcrumb-item active" aria-current="page">Candidatos Cadastrados</li>
				  </ol>
				</nav>
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
								$statusAtivo = (isset($row['ativo']) && $row['ativo'] == 1) ? '<span class="badge badge-success">Ativo</span>' : '<span class="badge badge-secondary">Inativo</span>';
								$tipoInscrito = !empty($row['inscrito']) ? ucfirst($row['inscrito']) : 'Atendente';
							?>
							<tr>
								<td class="align-middle text-muted small"><?php echo $i; ?></td>
								<td class="align-middle text-center" style="width: 50px;">
									<a href="<?php echo $app_web_root; ?>trocafoto/<?php echo digitos($row['candidato_id']); ?>" title="Alterar foto">
										<img src="<?php echo $perfil; ?>" height="42" width="42" class="rounded-circle shadow-sm" style="object-fit: cover;">
									</a>
								</td>
								<td class="align-middle">
									<strong class="text-dark"><?php echo htmlspecialchars($row['nome']); ?></strong>
									<br><small class="text-muted"><i class="fas fa-id-badge mr-1"></i>ID: <?php echo $row['candidato_id']; ?> | <?php echo $tipoInscrito; ?></small>
								</td>
								<td class="align-middle small">
									<div><i class="far fa-envelope text-muted mr-1"></i><?php echo htmlspecialchars($row['Email'] ?? ''); ?></div>
									<div>
										<i class="fas fa-phone text-muted mr-1"></i><?php echo $telFormatado; ?>
										<?php if ($waLink): ?>
											<a href="<?php echo $waLink; ?>" target="_blank" class="text-success ml-1" title="Chamar no WhatsApp"><i class="fab fa-whatsapp"></i></a>
										<?php endif; ?>
									</div>
								</td>
								<td class="align-middle small">
									<?php echo $statusAtivo; ?>
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
</body>
<?php
include_once('./include/scripts.php');
include_once('./include/end.php');
?>
