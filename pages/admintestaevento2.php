<?php
include_once('./include/head.php');
?>
<body>
	<div class="container">
		<header>
			<h1 class="text-center">Busca por contratos</h1>
			<hr>
		</header>
	<div class="row justify-content-center">
		<div class="col col-md-8 col-lg-4  d-flex align-items-stretch">
				<form method="post" action="/include/cadastra_evento.php" id="formEvento">
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
					<div class="card-footer">
					<button class="btn btn-primary rounded-pill" type="submit">Cadastrar</button></div>
			</div>
			</div>
				</form>
		</div>

		<div class="col col-md-8 col-lg-4 d-flex align-items-stretch">
			<div class="card d-flex">
				<div class="card-header">
					<h2>Cadastrar Expositor</h2>
				</div>
				<div class="card-body">
					<form id="formExpositor">
					<div class="md-form">
						<label for="eventoSelect">Evento:</label>
						<select name="evento_id" id="eventoSelect" required>
							<!-- Preenchido via AJAX -->
						</select>
					</div>
					<div class="md-form">
						<label for="nome">Nome:</label>
						<input type="text" id="nome" name="nome" required>
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
						<label for="whatsapp">WhatsApp:</label>
						<input type="text" id="whatsapp" name="whatsapp">
					</div>
					<div class="md-form">
						<label for="instagram">Instagram:</label>
						<input type="text" id="instagram" name="instagram">
					</div>
						<button class="btn btn-primary rounded-pill" type="submit">Cadastrar</button>
					</form>
				</div>
			</div>
		</div>
		<div class="col col-md-8 col-lg-4 d-flex align-items-stretch">
			<div class="card d-flex">
				<div class="card-header">
			<h2>Registrar Contato</h2>
				</div>
				<div class="card-body">
					<form id="formContato">
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
						<button type="submit">Registrar</button>
					</form>
				</div>
			</div>
		</div>
	</div>
	</div>
<?
	include_once('./include/footerbt.php');
	include_once('./include/scripts.php');
?>
<!--
	<script>
        // Carregar eventos no select
        $.get('./include/lista_eventos.php', function(data) {
            $('#eventoSelect').html(data);
        });

        // Carregar expositores no select
        $.get('./include/lista_expositores.php', function(data) {
            $('#expositorSelect').html(data);
        });

        // Cadastrar evento
        $('#formEvento').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: './include/cadastra_evento.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    alert(response.message);
                    if (response.success) {
                        $('#formEvento')[0].reset();
                        $.get('lista_eventos.php', function(data) {
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
                url: './include/cadastra_expositor.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    alert(response.message);
                    if (response.success) {
                        $('#formExpositor')[0].reset();
                        $.get('lista_expositores.php', function(data) {
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
                url: './include/registra_contato.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    alert(response.message);
                    if (response.success) $('#formContato')[0].reset();
                }
            });
        });
    </script>
-->
</body>
</html>