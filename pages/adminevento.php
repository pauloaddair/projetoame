<?php
include_once('./include/conexao.php');
include_once('./include/head.php');
$id = 1;
if (array_key_exists(2,$parametros)){
	$id = intval($parametros[2]);
}
$sql = "SELECT lev.*,lex.* 
FROM leads_expositores lex
LEFT JOIN leads_eventos lev ON lev.id = lex.evento_id
WHERE lex.id = ".$id;
$resp = mysqli_query($conexao,$sql);
$row = mysqli_fetch_array($resp);
?>
<body>
	<div class="container">
		<header>
			<h1 class="text-center"><strong><? echo strtoupper($row[9])?></strong> - Novo contato</h1>
			<hr>
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="/admin/relatorios">Relatórios</a></li>
				<li class="breadcrumb-item"><a href="/admin/resumo">Resumo</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="/admin/eventos">Novo Expositor</a></li>
			  </ol>
			</nav>
		</header>
	<div class="row justify-content-center">
			<div class="col col-md-8 col-lg-4  d-flex align-items-stretch">
				<div class="card d-flex">
					<div class="card-header">
						<h2>Evento</h2>
					</div>
				<div class="card-body">
						<div class="md-form">
							<label for="nome">Nome do Evento:</label>
							<input type="text" id="nome" name="nome" value="<? echo $row[1]?>">
						</div>
						<div class="md-form">
							<label for="data">Data:</label>
							<input type="date" id="data" name="data" value="<? echo $row[2]?>">
						</div>
						<div class="md-form">
							<label for="local">Local:</label>
							<input type="text" id="local" name="local" value="<? echo $row[3]?>">
						</div>
						<div class="md-form">
							<label for="site">Site:</label>
							<input type="text" id="site" name="site" value="<? echo $row[4]?>">
						</div>
				</div>
				</div>
		</div>
		<div class="col col-md-8 col-lg-4 d-flex align-items-stretch">
			<div class="card d-flex">
				<div class="card-header">
					<h2>Empresa</h2>
				</div>
				<div class="card-body">
					<div class="md-form">
						<label for="nome">Nome:</label>
						<input type="text" id="nome" name="nome" value="<? echo $row[9]?>">
					</div>
					<div class="md-form">
						<label for="telefone">Telefone:</label>
						<input type="text" id="telefone" name="telefone" value="<? echo $row[10]?>">
					</div>
					<div class="md-form">
						<label for="email">Email:</label>
						<input type="email" id="email" name="email" value="<? echo $row[12]?>">
					</div>
						<div class="md-form">
							<label for="siteexp">Site:</label>
							<input type="text" id="siteexp" name="siteexp" value="<? echo $row[11]?>">
						</div>
					<div class="md-form">
						<label for="whatsapp">WhatsApp:</label>
						<input type="text" id="whatsapp" name="whatsapp" value="<? echo $row[13]?>">
					</div>
					<div class="md-form">
						<label for="instagram">Instagram:</label>
						<input type="text" id="instagram" name="instagram" value="<? echo $row[14]?>">
					</div>
				</div>
			</div>
		</div>
		<div class="col col-md-8 col-lg-4 d-flex align-items-stretch">
			<form id="formContato">
			<div class="card d-flex">
				<div class="card-header">
			<h2>Registrar Contato</h2>
				</div>
				<div class="card-body">
						<input type="hidden" name="expositor_id" value="<? echo $row[7] ?>"
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
    </script>
</body>
</html>