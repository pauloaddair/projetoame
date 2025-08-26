<?php
include_once('./include/head.php');
?>
<body>
	<div class="container">
		<header>
			<h1 class="text-center">Cadastrar novo contato</h1>
			<hr>
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="/admin/relatorios">Relatórios</a></li>
				<li class="breadcrumb-item"><a href="/admin/resumo">Resumo</a></li>
				<li class="breadcrumb-item active" aria-current="page">Contatos</li>
			  </ol>
			</nav>
		</header>
	<div class="row justify-content-center">
			<div class="col col-md-8 col-lg-4  d-flex align-items-stretch">
			<form id="formEvento">
				<div class="card d-flex">
					<div class="card-header">
						<h2>Cadastrar Evento</h2>
					</div>
				<div class="card-body">
						<div class="md-form">
							<label for="nome">Nome do Evento:</label>
							<input type="text" id="nome" name="nome" required>
						</div>
						<div class="md-form">
							<label for="data">Data:</label>
							<input type="date" id="data" name="data" required>
						</div>
						<div class="md-form">
							<label for="local">Local:</label>
							<input type="text" id="local" name="local" required>
						</div>
						<div class="md-form">
							<label for="site">Site:</label>
							<input type="text" id="site" name="site">
						</div>
				</div>
				<div class="card-footer">
					<button class="btn btn-primary rounded-pill" type="submit">Cadastrar</button>
				</div>
				</div>
			</form>
		</div>

		<div class="col col-md-8 col-lg-4 d-flex align-items-stretch">
			<form id="formExpositor">
			<div class="card d-flex">
				<div class="card-header">
					<h2>Cadastrar Expositor</h2>
				</div>
				<div class="card-body">
					<div class="md-form">
						<label for="eventoSelect">Evento:</label>
						<select name="evento_id" id="eventoSelect" required>
							<!-- Preenchido via AJAX -->
						</select>
					</div>
					<div class="md-form">
						<label for="expositor">Expositor:</label>
						<input type="text" id="expositor" name="nome" required>
					</div>
					<div class="md-form">
						<label for="contato">Contato (Responsável):</label>
						<input type="text" id="contato" name="contato" required>
					</div>
					<div class="md-form">
						<label for="telefone">Telefone:</label>
						<input type="text" id="telefone" name="telefone">
					</div>
					<div class="md-form">
						<label for="email">Email:</label>
						<input type="email" id="email" name="email">
					</div>
						<div class="md-form">
							<label for="siteexp">Site:</label>
							<input type="text" id="siteexp" name="siteexp">
						</div>
					<div class="md-form">
						<label for="whatsapp">WhatsApp:</label>
						<input type="text" id="whatsapp" name="whatsapp">
					</div>
					<div class="md-form">
						<label for="instagram">Instagram:</label>
						<input type="text" id="instagram" name="instagram">
					</div>
				</div>
				<div class="card-footer">
					<button class="btn btn-primary rounded-pill" type="submit">Cadastrar</button>
				</div>
			</div>
			</form>
		</div>
		<div class="col col-md-8 col-lg-4 d-flex align-items-stretch">
			<form id="formContato">
			<div class="card d-flex">
				<div class="card-header">
			<h2>Registrar Contato</h2>
				</div>
				<div class="card-body">
						<label>Expositor:</label>
						<select name="expositor_id" id="expositorSelect" required>
							<!-- Preenchido via AJAX -->
						</select>
						<label>Tipo de Contato:</label>
						<select name="tipo_contato" required>
							<option value="telefone">Telefone</option>
							<option value="email">Email</option>
							<option value="whatsapp">WhatsApp</option>
							<option value="instagram">Instagram</option>
							<option value="outro">Outro</option>
						</select>
						<label>Data do Contato:</label>
						<input type="date" name="data_contato" required>
						<label>Observação:</label>
						<textarea name="observacao"></textarea>
						<label>Data de Follow-up:</label>
						<input type="date" name="data_followup">
				</div>
				<div class="card-footer">
					<button class="btn btn-primary rounded-pill" type="submit">Registrar</button>
				</div>
			</div>
			</form>
		</div>
	</div>
	</div>
<?
	include_once('./include/footer.php');
	include_once('./include/scripts.php');
?>
	<script>
		// Função para obter parâmetros da URL
		function getUrlParameter(name) {
			name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
			var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
			var results = regex.exec(location.search);
			return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
		}
		// Carregar eventos no select
        $.get('/include/lista_eventos.php', function(data) {
            $('#eventoSelect').html(data);
			var expositorId = getUrlParameter('expositor_id');
			if (expositorId) {
				$('#expositorSelect').val(expositorId);
				var nomeExpositor = getUrlParameter('nome');
				if ($('#expositorSelect option:selected').text() !== nomeExpositor) {
					// Se o expositor não estiver na lista, adicionar temporariamente
					$('#expositorSelect').append(`<option value="${expositorId}" selected>${nomeExpositor}</option>`);
				}
				// Opcional: rolar até o formulário de contato
				$('html, body').animate({
					scrollTop: $("#formContato").offset().top
				}, 500);
			}
		});

        // Carregar expositores no select
        $.get('/include/lista_expositores.php', function(data) {
            $('#expositorSelect').html(data);
        });

        // Cadastrar evento
        $('#formEvento').submit(function(e) {
			alert ('Submit');
            e.preventDefault();
            $.ajax({
                url: '/include/cadastra_evento.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    alert(response.message);
                    if (response.success) {
                        $('#formEvento')[0].reset();
                        $.get('/include/lista_eventos.php', function(data) {
                            $('#eventoSelect').html(data);
                        });
                    }
                }
            });
        });

        // Cadastrar expositor
        $('#formExpositor').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: '/include/cadastra_expositor.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    alert(response.message);
                    if (response.success) {
                        $('#formExpositor')[0].reset();
                        $.get('/include/lista_expositores.php', function(data) {
                            $('#expositorSelect').html(data);
                        });
                    }
                }
            });
        });

        // Registrar contato
        $('#formContato').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: '/include/registra_contato.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    alert(response.message);
                    if (response.success) $('#formContato')[0].reset();
                }
            });
        });
		// Função para carregar o histórico
		function carregarHistorico(expositorId) {
			if (!expositorId) {
				historicoTable.clear().draw();
				$('#contatoAtual').html('');
				return;
			}
			$.get('/include/historico_contatos.php', { expositor_id: expositorId }, function(data) {
				historicoTable.clear();
				if (data.contato) {
					$('#contatoAtual').html(`<p><strong>Contato Atual:</strong> ${data.contato}</p>`);
				} else {
					$('#contatoAtual').html('<p><strong>Contato Atual:</strong> Não informado</p>');
				}
				if (data.contatos && data.contatos.length > 0) {
					const hoje = new Date().toISOString().split('T')[0];
					data.contatos.forEach(contato => {
						let classeLinha = '';
						if (contato.status === 'pendente' || contato.status === 'em andamento') {
							if (contato.data_followup && contato.data_followup < hoje) {
								classeLinha = 'atrasado';
							} else {
								classeLinha = 'pendente';
							}
						}
						historicoTable.row.add([
							contato.tipo_contato,
							contato.data_contato,
							contato.observacao || '',
							contato.data_followup || 'N/A',
							contato.status
						]).node().className = classeLinha;
					});
				}
				historicoTable.draw();
			}, 'json');
		}
    </script>
</body>
</html>