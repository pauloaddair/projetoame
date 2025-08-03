<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Leads - ONG</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .section { margin: 20px; }
        form { display: flex; flex-direction: column; width: 400px; }
        label { margin: 5px 0; }
        input, select, textarea { margin-bottom: 10px; padding: 5px; }
        button { padding: 10px; cursor: pointer; }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <!-- Seções existentes (Cadastrar Evento, Expositor, Contato) permanecem aqui -->

    <div class="section">
        <h2>Configurar Scraping de Expositores</h2>
        <form id="formScraping">
            <label>Evento:</label>
            <select name="evento_id" id="eventoSelectScraping" required>
                <!-- Preenchido via AJAX -->
            </select>
            <label>URL dos Expositores:</label>
            <input type="text" name="url" placeholder="Ex.: https://evento.com/expositores" required>
            <label>Seletor dos Itens (ex.: .exhibitor-item):</label>
            <input type="text" name="seletor_itens" placeholder="Ex.: .exhibitor-item" required>
            <label>Seletor do Nome (ex.: .name):</label>
            <input type="text" name="seletor_nome" placeholder="Ex.: .name" required>
            <button type="submit">Executar Scraping</button>
        </form>
    </div>

    <script>
        // Carregar eventos no select do scraping
        $.get('/include/lista_eventos.php', function(data) {
            $('#eventoSelectScraping').html(data);
        });

        // Carregar eventos no select do cadastro de expositores (se já existir)
        $.get('/include/lista_eventos.php', function(data) {
            $('#eventoSelect').html(data);
        });

        // Carregar expositores no select do cadastro de contatos (se já existir)
        $.get('/include/lista_expositores.php', function(data) {
            $('#expositorSelect').html(data);
        });

        // Submissão do formulário de scraping
        $('#formScraping').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: '/include/busca_expositores_config.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    alert(response.message);
                    if (response.success) {
                        $('#formScraping')[0].reset();
                        $.get('/include/lista_expositores.php', function(data) {
                            $('#expositorSelect').html(data); // Atualiza o select de expositores
                        });
                    }
                },
                error: function() {
                    alert('Erro ao executar o scraping. Verifique os dados e tente novamente.');
                }
            });
        });

        // Scripts existentes para os outros formulários (Evento, Expositor, Contato) permanecem aqui
    </script>
</body>
</html>