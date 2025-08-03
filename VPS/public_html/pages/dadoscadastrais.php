<?php
require_once ('./include/conexao.php');
require_once ('./include/head.php');
$id = 0;
if (array_key_exists(2,$parametros)){
	$id = intval($parametros[2]);
}
$query = "SELECT * FROM contabil_dados_cadastrais WHERE empresa_id =4;";
$result = mysqli_query($conexao,$query);
$msg="";
$dados = array(
	array('Razão social: ','razao'),
	array('Nome fantasia: ','fantasia'),
	array('CNPJ: ','cnpj'),
	array('CEP: ','CEP'),
	array('Endereço: ','endereco'),
	array('Complemento: ','complemento'),
	array('Cidade: ','cidade'),
	array('UF: ','UF'),
	array('Pais: ','pais'),
	array('Telefone: ','telefone'),
	array('Celular: ','celular'),
	array('E-mail: ','email'),
	array('Site: ','site'),
	array('Banco: ','banco'),
	array('Conta: ','conta'),
	array('Agencia: ','agencia'),
	array('Chave PIX: ','PIX')
);
?>
<body>
		<?
		include_once('./include/nav.php');
		?>
	<div class="container mt-5">
		<header class="p-5">
				<?
				if (mysqli_num_rows($result)>0){
					$row = mysqli_fetch_array($result);
					$msg = $row['fantasia'];
					?>
			<h1><?echo $msg?></h1>
			<span class="badge badge-light m-1 rounded-pill"><a href='/admin'>Balanço</a></span>
<!--
<? echo $query?>
			<span class="badge badge-info m-1 rounded-pill"><a href='/admin/extrato/<? echo $anoantes?>/<?echo $mesantes?>'><?echo $mesantes?>/<?echo $anoantes?></a></span>
			<span class="badge badge-success m-1 rounded-pill"><?echo $mes?>/<?echo $ano?></span>
			<span class="badge badge-info m-1 rounded-pill"><a href='/admin/extrato/<? echo $anoantes?>/<?echo $mesdepois?>'><?echo $mesdepois?>/<?echo $anodepois?></a></span>
-->
		</header>
		<div class="row">
			<div class="col col-lg-8 justify-content-center">
				<div class="card p-2">
					<div class="card-header">
					<img src="/<? echo $row['capa_url']?>" class="img-fluid">
						<h1>Dados cadastrais</h1>
					</div>
				<form method="post">
<!--					<img src="/<? echo $row['imagem_url']?>" class="img-fluid">-->
					<ul>
						<?
						$texto = "DADOS CADASTRAIS:\n";
						foreach ($dados as $item){
							$texto .= $item[0].$row[$item[1]]."\n";
						?>
						<li><? echo $item[0]?><strong><?  echo  $row[$item[1]]?></strong></li>
						<?							
						}
						?>
<!--
						<li>id:<? echo $row['empresa_ID']?></li>
						<li>Razão social: <strong><? echo $row['razao']?></strong></li>
						<li>Nome fantasia: <em><? echo $row['fantasia']?></em></li>
						<li>slug: <? echo $row['slug']?></li>
						<li>Plano de conta: <? echo $row['plano_ID']?></li>
						<li>CNPJ: <? echo $row['cnpj']?></li>
						<li>CEP: <? echo $row['CEP']?></li>
						<li>Endereço: <? echo $row['endereco']?></li>
						<li>Complemento: <? echo $row['complemento']?></li>
						<li>Cidade: <? echo $row['cidade']?></li>
						<li>UF: <? echo $row['UF']?></li>
						<li>Pais: <? echo $row['pais']?></li>
						<li>Telefone: <? echo $row['telefone']?></li>
						<li>Celular: <? echo $row['celular']?></li>
						<li>E-mail: <? echo $row['email']?></li>
						<li>Site: <? echo $row['site']?></li>
						<li>Banco: <? echo $row['banco']?></li>
						<li>Agencia: <? echo $row['agencia']?></li>
						<li>Conta: <? echo $row['conta']?></li>
						<li>Chave PIX: <? echo $row['PIX']?></li>
-->
					</ul>						
				</form>
				<div class="card-footer">
					<div class="mb-3">
<!--						<label for="textToCopy" class="form-label">Texto para copiar:</label>-->
						<textarea type="text" id="textToCopy" class="form-control" readonly style="display:none;"> <? echo $texto?></textarea> 
        <button id="copyButton" class="btn btn-primary">Copiar</button>
					</div>       				
				</div>
				</div>
			</div>
					
		</div>
				<?
				} else {
				?>
		<header class="p-5">
		<h1 class="text-center">Movimento não localizado!</1>	
		</header>
			<?
				}
				?>
	
	</div>
<?php
include_once('./include/footer.php');
?>
<script>
	document.getElementById('copyButton').addEventListener('click', function() {
		// Seleciona o input de texto
		const textToCopy = document.getElementById('textToCopy');
		// Seleciona o texto do input
		textToCopy.select();
		textToCopy.setSelectionRange(0, 99999); // Para dispositivos móveis
		// Copia o texto para o clipboard
		navigator.clipboard.writeText(textToCopy.value)
			.then(() => {
				alert('Texto copiado para o clipboard!');
			})
			.catch(err => {
				alert('Falha ao copiar o texto: ' + err);
			});
	});
</script>
</body>
<?php
require_once ('./include/footer.php');
include_once('./include/scripts.php');
require_once ('./include/end.php');
?>
