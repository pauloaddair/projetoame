<?php
include_once('include/funcoes.php');
include_once('include/head-table.php');
$expositor_id = 0;
if (array_key_exists(2,$parametros)){
	$expositor_id = $parametros[2];
}
?>
<meta charset="UTF-8">
    <title>Relatórios - Contatos e Follow-ups</title>
    <style>
body { font-family: Arial, sans-serif; }
    table { width: 100%; border-collapse: collapse; margin: 20px 0; }
    th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
    th { background-color: #f2f2f2; }
    tr:nth-child(even) { background-color: #f9f9f9; }
    tr.pendente { background-color: #fff3cd; }
    tr.atrasado { background-color: #f8d7da; }
    select { padding: 5px; }
    button { padding: 5px 10px; cursor: pointer; }
    .alerta-vencendo { color: #ff9800; font-weight: bold; margin-left: 5px; } /* Amarelo para vencendo */
    .alerta-vencido { color: #dc3545; font-weight: bold; margin-left: 5px; } /* Vermelho para vencido */
	body { font-family: Arial, sans-serif; }
/*        button { padding: 5px 10px; cursor: pointer; }*/
    </style>
<!--    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>-->
</head>
<body>
	<div class="container">
	<head>
		<h1>Relatórios de Contatos e Follow-ups</h1>
		<hr>
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
				<li class="breadcrumb-item" aria-current="page">Relatórios</li>
				<li class="breadcrumb-item"><a href="/admin/resumo">Resumo</a></li>
				<li class="breadcrumb-item active"><a href="/admin/atividades">Contatos</a></li>
			  </ol>
			</nav>		
	</head>
    <table id="tabelaContatos">
        <thead>
            <tr>
				<th>#</th>
                <th>Expositor</th>
                <th>Evento</th>
                <th>Tipo de Contato</th>
                <th>Data do Contato</th>
                <th>Observação</th>
                <th>Data de Follow-up</th>
                <th>Status</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            <!-- Preenchido via AJAX -->
        </tbody>
    </table>
	</div>
<?php
include_once('include/scripts.php');
include_once('include/footer-table.php');
?>
    <script>
        $(document).ready(function() {
            // Inicializar o DataTable vazio
            let table = $('#tabelaContatos').DataTable({
					"scrollX": true,
					"language": {
						"url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json" // Traduzir para português
					},
					"order": [[5, "asc"]], // Ordenar por Data de Follow-up por padrão
					"pageLength": 10 // Mostrar 10 linhas por página
				});

            // Carregar os contatos
            carregarContatos();

            function carregarContatos() {
                $.get('/include/lista_contatos.php', function(data) {
                    table.clear(); // Limpar tabela antes de adicionar novos dados

                    const hoje = new Date().toISOString().split('T')[0];
					let i = 1;

                    data.forEach(contato => {
                        let classeLinha = '';
                        if (contato.status === 'pendente' || contato.status === 'em andamento') {
                            if (contato.data_followup && contato.data_followup < hoje) {
                                classeLinha = 'atrasado';
                            } else {
                                classeLinha = 'pendente';
                            }
                        }

                        let expositorLink = `<a href="/admin/atividade/${contato.expositor_id}">${contato.expositor}</a>`;
                        // Adicionar linha ao DataTable
                        table.row.add([
							i,
                            expositorLink,
                            contato.evento,
                            contato.tipo_contato,
                            contato.data_contato,
                            contato.observacao || '',
                            contato.data_followup || 'N/A',
                            `<select class="status" data-id="${contato.id}">
                                <option value="pendente" ${contato.status === 'pendente' ? 'selected' : ''}>Pendente</option>
                                <option value="em andamento" ${contato.status === 'em andamento' ? 'selected' : ''}>Em andamento</option>
                                <option value="convertido" ${contato.status === 'convertido' ? 'selected' : ''}>Convertido</option>
                                <option value="perdido" ${contato.status === 'perdido' ? 'selected' : ''}>Perdido</option>
                            </select>`,
                            `<button class="btn btn-sm btn-primary rounded-pill atualizar" data-id="${contato.id}">Atualizar</button>`
                        ]).node().className = classeLinha; // Aplicar classe à linha
						i++;
                    });

                    table.draw(); // Redesenhar a tabela com os novos dados
                });
            }

            // Atualizar status ao clicar no botão
            $(document).on('click', '.atualizar', function() {
                let contato_id = $(this).data('id');
                let status = $(this).closest('tr').find('.status').val();

                $.ajax({
                    url: '/include/atualiza_status.php',
                    type: 'POST',
                    data: { contato_id: contato_id, status: status },
                    dataType: 'json',
                    success: function(response) {
                        alert(response.message);
                        if (response.success) {
                            carregarContatos(); // Recarregar a tabela
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
