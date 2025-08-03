<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatórios - Contatos e Follow-ups</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr.pendente { background-color: #fff3cd; } /* Amarelo para pendentes */
        tr.atrasado { background-color: #f8d7da; } /* Vermelho para atrasados */
        select { padding: 5px; }
        button { padding: 5px 10px; cursor: pointer; }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Relatórios de Contatos e Follow-ups</h1>
    <table id="tabelaContatos">
        <thead>
            <tr>
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

    <script>
        // Função para carregar os contatos
        function carregarContatos() {
            $.get('/include/lista_contatos.php', function(data) {
                let tbody = $('#tabelaContatos tbody');
                tbody.empty();

                const hoje = new Date().toISOString().split('T')[0]; // Data atual no formato YYYY-MM-DD

                data.forEach(contato => {
                    let classeLinha = '';
                    if (contato.status === 'pendente' || contato.status === 'em andamento') {
                        if (contato.data_followup && contato.data_followup < hoje) {
                            classeLinha = 'atrasado'; // Follow-up atrasado
                        } else {
                            classeLinha = 'pendente'; // Follow-up pendente
                        }
                    }

                    let row = `
                        <tr class="${classeLinha}">
                            <td>${contato.expositor}</td>
                            <td>${contato.evento}</td>
                            <td>${contato.tipo_contato}</td>
                            <td>${contato.data_contato}</td>
                            <td>${contato.observacao || ''}</td>
                            <td>${contato.data_followup || 'N/A'}</td>
                            <td>
                                <select class="status" data-id="${contato.id}">
                                    <option value="pendente" ${contato.status === 'pendente' ? 'selected' : ''}>Pendente</option>
                                    <option value="em andamento" ${contato.status === 'em andamento' ? 'selected' : ''}>Em andamento</option>
                                    <option value="convertido" ${contato.status === 'convertido' ? 'selected' : ''}>Convertido</option>
                                    <option value="perdido" ${contato.status === 'perdido' ? 'selected' : ''}>Perdido</option>
                                </select>
                            </td>
                            <td><button class="atualizar" data-id="${contato.id}">Atualizar</button></td>
                        </tr>`;
                    tbody.append(row);
                });
            });
        }

        // Carregar contatos ao abrir a página
        $(document).ready(function() {
            carregarContatos();
        });

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
    </script>
</body>
</html>