<?php
// pages/adminatividades.php - Gestão de Atividades e Eventos Confirmados
$app_web_root = $GLOBALS['app_web_root'] ?? '/';
$titulo = "Gestão de Atividades";
include_once('./include/head-table.php');
include_once('./include/nav.php');
include_once('./include/admin_sidebar.php');
?>
<div class="container-fluid mt-3">
	<header class="mb-4">
		<div class="d-flex justify-content-between align-items-center flex-wrap">
			<div>
				<h1 class="h3 font-weight-bold text-dark mb-1">
					<i class="fas fa-calendar-check text-primary mr-2"></i>Gestão de Atividades
				</h1>
				<p class="text-muted small mb-0">Listagem de eventos confirmados, escalas e links de avaliação.</p>
			</div>
			<div class="mt-2 mt-md-0">
				<a href="<?php echo $app_web_root; ?>admin/candidatos" class="btn btn-outline-primary btn-sm rounded-pill">
					<i class="fas fa-users mr-1"></i> Ver Casting
				</a>
			</div>
		</div>
		<nav aria-label="breadcrumb" class="mt-3">
		  <ol class="breadcrumb bg-light p-2 rounded shadow-sm small">
			<li class="breadcrumb-item"><a href="<?php echo $app_web_root; ?>admin">Painel</a></li>
			<li class="breadcrumb-item"><a href="<?php echo $app_web_root; ?>admin/resumo">Resumo</a></li>
			<li class="breadcrumb-item active" aria-current="page">Atividades</li>
		  </ol>
		</nav>
	</header>

	<div class="card shadow-sm border-0 mb-4">
		<div class="card-header bg-white py-3">
			<h5 class="mb-0 font-weight-bold text-dark">
				<i class="fas fa-list text-primary mr-2"></i>Eventos e Atividades Confirmadas
			</h5>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<table id="eventos-datatable" class="table table-hover table-striped w-100">
					<thead class="thead-dark">
						<tr>
							<th>ID</th>
							<th>Nome do Evento</th>
							<th>Início</th>
							<th>Fim</th>
							<th>Status</th>
							<th class="text-center">Ações</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
	</div>
</div>

<?php
include_once('./include/admin_sidebar_footer.php');
include_once('./include/footer-database-noorder.php');
?>
<script type="text/javascript">
	$(document).ready(function() {
        var AppWebRoot = '<?php echo $app_web_root; ?>';
        
        // Inicializa a DataTable
        $('#eventos-datatable').DataTable({
            "ajax": AppWebRoot + 'include/lista_todos_eventos.php',
            "columns": [
                { "data": "id", "className": "align-middle text-muted small" },
                { 
                    "data": "nome", 
                    "className": "align-middle font-weight-bold",
                    "render": function(data) {
                        return data ? data : '<span class="text-muted">Sem título</span>';
                    }
                },
                { "data": "inicio", "className": "align-middle small" },
                { "data": "final", "className": "align-middle small" },
                { 
                    "data": "status_evento", 
                    "className": "align-middle",
                    "render": function(data) {
                        var statusStr = (data || '').toLowerCase();
                        if (statusStr === 'confirmado') {
                            return '<span class="badge badge-success px-2 py-1">Confirmado</span>';
                        } else if (statusStr === 'realizado' || statusStr === 'concluído') {
                            return '<span class="badge badge-secondary px-2 py-1">Concluído</span>';
                        } else if (statusStr === 'cancelado') {
                            return '<span class="badge badge-danger px-2 py-1">Cancelado</span>';
                        }
                        return '<span class="badge badge-info px-2 py-1">' + (data || 'Agendado') + '</span>';
                    }
                },
                {
                    "data": "id",
                    "className": "align-middle text-center",
                    "render": function (data, type, row) {
                        var html = '<a href="' + AppWebRoot + 'admin/escala?evento_id=' + data + '" class="btn btn-primary btn-sm rounded-pill px-2 py-1 mr-1 shadow-sm"><i class="fas fa-user-clock mr-1"></i>Gerenciar Escala</a>';
                        
                        // Verifica se o evento já iniciou
                        var inicioDate = row.inicio ? new Date(row.inicio.replace(/-/g, "/")) : null;
                        var now = new Date();
                        
                        if (inicioDate && inicioDate <= now) {
                            if (row.uuid) {
                                var evalUrl = window.location.origin + AppWebRoot + 'avaliacao/' + row.uuid;
                                html += '<button class="btn btn-outline-success btn-sm rounded-pill px-2 py-1 btn-copy-eval ml-1" data-url="' + evalUrl + '" title="Copiar Ficha de Avaliação"><i class="fas fa-copy mr-1"></i> Ficha Avaliação</button>';
                            } else {
                                html += '<button class="btn btn-outline-secondary btn-sm rounded-pill px-2 py-1 ml-1" disabled title="UUID não gerado"><i class="fas fa-exclamation-circle mr-1"></i> Ficha Indisponível</button>';
                            }
                        } else {
                            html += '<button class="btn btn-outline-secondary btn-sm rounded-pill px-2 py-1 ml-1" disabled title="O evento ainda não iniciou"><i class="fas fa-clock mr-1"></i> Não Iniciado</button>';
                        }
                        
                        return html;
                    },
                    "orderable": false
                }
            ],
            "responsive": true,
            "order": [[ 2, "desc" ]],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
            }
        });

        // Evento de clique para copiar o link da avaliação
        $('#eventos-datatable').on('click', '.btn-copy-eval', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var url = $btn.data('url');
            
            navigator.clipboard.writeText(url).then(function() {
                var originalHtml = $btn.html();
                $btn.html('<i class="fas fa-check mr-1"></i> Copiado!');
                $btn.removeClass('btn-outline-success').addClass('btn-success');
                setTimeout(function() {
                    $btn.html(originalHtml);
                    $btn.removeClass('btn-success').addClass('btn-outline-success');
                }, 2000);
            }).catch(function(err) {
                alert('Erro ao copiar link: ' + err);
            });
        });
	});
</script>
</body>
<?php
include_once('./include/scripts.php');
include_once('./include/end.php');
?>