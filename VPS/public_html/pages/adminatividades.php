<?php
include_once('./include/head-table.php');
?>
<body>
	<div class="container">
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
	include_once('./include/footer.php');
	include_once('./include/scripts.php');
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
                        return '<a href="' + AppWebRoot + 'admin/escala?evento_id=' + data + '" class="btn btn-primary btn-sm">Gerenciar Escala</a>';
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
	});
</script>
</body>
</html>