<?php
include_once('./include/head-table.php');
include_once('./include/nav.php');
include_once('./include/admin_sidebar.php');
?>
<div class="container mt-4">
		<header>
			<h1 class="text-center">Gestão de Atividades</h1>
			<hr>
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="/admin/relatorios">Relatórios</a></li>
				<li class="breadcrumb-item"><a href="/admin/resumo">Resumo</a></li>
				<li class="breadcrumb-item active" aria-current="page">Atividades</li>
			  </ol>
			</nav>
		</header>

        <div class="card mb-4">
            <div class="card-header">
                <h2>Lista de Atividades Confirmadas</h2>
            </div>
            <div class="card-body">
                <table id="eventos-datatable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Início</th>
                            <th>Fim</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

	</div>
<?php
	include_once('./include/footer-database-noorder.php');
?>
<script>
	$(document).ready(function() {
        var AppWebRoot = '<?php echo $GLOBALS["app_web_root"]; ?>';
        
        // Inicializa a DataTable
        $('#eventos-datatable').DataTable({
            "ajax": AppWebRoot + 'include/lista_todos_eventos.php',
            "columns": [
                { "data": "id" },
                { "data": "nome" },
                { "data": "inicio" },
                { "data": "final" },
                { "data": "status_evento" },
                {
                    "data": "id",
                    "render": function (data, type, row) {
                        var html = '<a href="' + AppWebRoot + 'admin/escala?evento_id=' + data + '" class="btn btn-primary btn-sm mr-1">Gerenciar Escala</a>';
                        
                        // Verifica se o evento já iniciou
                        var inicioDate = new Date(row.inicio.replace(/-/g, "/"));
                        var now = new Date();
                        
                        if (inicioDate <= now) {
                            if (row.uuid) {
                                var evalUrl = window.location.origin + AppWebRoot + 'avaliacao/' + row.uuid;
                                html += '<button class="btn btn-outline-success btn-sm btn-copy-eval ml-1" data-url="' + evalUrl + '" title="Copiar Ficha de Avaliação"><i class="fas fa-copy mr-1"></i> Ficha Avaliação</button>';
                            } else {
                                html += '<button class="btn btn-outline-secondary btn-sm ml-1" disabled title="UUID não gerado"><i class="fas fa-exclamation-circle mr-1"></i> Ficha Indisponível</button>';
                            }
                        } else {
                            html += '<button class="btn btn-outline-secondary btn-sm ml-1" disabled title="O evento ainda não iniciou"><i class="fas fa-clock mr-1"></i> Não Iniciado</button>';
                        }
                        
                        return html;
                    },
                    "orderable": false
                }
            ],
            "responsive": true,
            "order": [[ 2, "desc" ]],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.1/i18n/pt-BR.json"
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
<?php include_once('./include/admin_sidebar_footer.php'); ?>