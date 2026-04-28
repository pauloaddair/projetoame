<?php
$id = 0;
if (array_key_exists(2,$parametros)){
	$id = intval($parametros[2]);
}
$query = "SELECT * FROM contabil_movimento WHERE id =".$id.";";
$result = mysqli_query($conexao,$query);
$msg="";
$query_planos = "SELECT codigo,plano_ID,plano FROM `contabil_plano_itens` WHERE planos_id =2 order by codigo;";
$planos = mysqli_query($conexao,$query_planos);
?>
<body>
		<?php 
		include_once('./include/nav.php');
		?>
	<div class="container mt-5">
		<header class="p-5">
				<?php 
				if (mysqli_num_rows($result)>0){
					$row = mysqli_fetch_array($result);
					$msg = $row['descricao'];
					$date = date_create($row['data_realizada']);
					$ano = date_format($date,"Y");
					$mes = date_format($date,"m");
					$anterior = $id-1;
					$proximo = $id+1;
					?>
			<h1><?php echo $msg?></h1>
			<span class="badge badge-light m-1 rounded-pill"><a href='/admin'>Balanço</a></span>
			<span class="badge badge-light m-1 rounded-pill"><a href='/admin/extrato/<?php echo $ano?>/<?php echo digitos($mes,2)?>'><?php echo $ano?>/<?php echo digitos($mes,2)?></a></span>|
			<span class="badge badge-light m-1 rounded-pill"><a href='/admin/movimento/<?php echo $anterior?>'>Anterior</a></span>
			<span class="badge badge-light m-1 rounded-pill"><a href='/admin/movimento/<?php echo $proximo?>'>Próximo</a></span>
<!--
<?php echo $query?>
			<span class="badge badge-info m-1 rounded-pill"><a href='/admin/extrato/<?php echo $anoantes?>/<?php echo $mesantes?>'><?php echo $mesantes?>/<?php echo $anoantes?></a></span>
			<span class="badge badge-success m-1 rounded-pill"><?php echo $mes?>/<?php echo $ano?></span>
			<span class="badge badge-info m-1 rounded-pill"><a href='/admin/extrato/<?php echo $anoantes?>/<?php echo $mesdepois?>'><?php echo $mesdepois?>/<?php echo $anodepois?></a></span>
-->
		</header>
		<div class="row">
			<div class="col">
				<div class="card p-1">
				<form method="post">
					<input type="hidden" value="<?php echo $id?>">
				<div class="md-form">
					<i class="far fa-edit prefix grey-text"></i>
					<input type="text" name="descricao" id="descricao" value="<?php echo $row['descricao']?>">
					<label for="descricao">descrição</label>
				</div>
				<div class="md-form">
					<i class="far fa-calendar prefix grey-text"></i>
					<input type="text" name="data_inclusao" id="data_inclusao" value="<?php echo $row['data_inclusao']?>">
					<label for="data_inclusao">Data inclusão</label>
				</div>
				<div class="md-form">
					<i class="far fa-calendar prefix grey-text"></i>
					<input type="text" name="data_prevista" id="data_prevista" value="<?php echo $row['data_prevista']?>">
					<label for="data_prevista">Data prevista</label>
				</div>
				<div class="md-form">
					<i class="far fa-calendar prefix grey-text"></i>
					<input type="text" name="data_realizada" id="data_realizada" value="<?php echo $row['data_realizada']?>">
					<label for="data_realizada">Data realizada</label>
				</div>
				<div class="md-form">
					<i class="far fa-calendar prefix grey-text"></i>
					<input type="text" name="data_realizada" id="data_realizada" value="<?php echo $row['data_realizada']?>">
					<label for="data_realizada">Data realizada</label>
				</div>
				<div class="md-form">
					<i class="fa fa-dollar-sign prefix grey-text"></i>
					<input type="text" name="valor_previsto" id="valor_previsto" value="<?php echo $row['valor_previsto']?>">
					<label for="valor_previsto">Valor previsto</label>
				</div>
				<div class="md-form">
					<i class="fa fa-dollar-sign prefix grey-text"></i>
					<input type="text" name="valor_realizado" id="valor_realizado" value="<?php echo $row['valor_realizado']?>">
					<label for="valor_realizado">Valor realizado</label>
				</div>
					<label for="plano">Plano de contas: </label>
					<select id="plano" name="plano" list="listaplanos"/>
					<datalist id="listaplanos">
					<?php 
						while($item = mysqli_fetch_array($planos)){
						
					?>
					<option value="<?php echo $item['plano_ID']?>"
							<?php 
							if ($item['plano_ID']==$row['plano_ID']){
								echo "selected";
							}
							?>><?php echo $item['codigo']." - ".$item['plano']?></option>
						<?php 
							
						}
						?>
					</datalist>
					</select></br>
					<label for="contra"> Contrapartida:</label>
					<select id="contra" name="contra" list="listacontra"/>
					<datalist id="listaconstra">
					<?php 
						$planos2 = mysqli_query($conexao,$query_planos);
						while($item = mysqli_fetch_array($planos2)){
						
					?>
					<option value="<?php echo $item['plano_ID']?>"
							<?php 
							if ($item['plano_ID']==$row['contrapartida_ID']){
								echo "selected";
							}
							?>><?php echo $item['codigo']." - ".$item['plano']?></option>
						<?php 
							
						}
						?>
					</datalist>
					</select>
				<div class="md-form">
					<i class="far fa-edit prefix grey-text"></i>
					<input type="text" name="obs" id="obs" value="<?php echo $row['obs']?>">
					<label for="Obs">Observação</label>
				</div>
<!--
					<ul>
						<li>id:<?php echo $row['movimento_id']?></li>
						<li>empresa: <?php echo $row['empresa_ID']?></li>
						<li>usuário: <?php echo $row['usuario_ID']?></li>
						<li>descrição: <?php echo $row['descricao']?></li>
						<li>Data inclusão: <?php echo $row['data_inclusao']?></li>
						<li>Data prevista: <?php echo $row['data_prevista']?></li>
						<li>Data realizada: <?php echo $row['data_realizada']?></li>
						<li>Valor previsto: <?php echo $row['valor_previsto']?></li>
						<li>Valor realizado: <?php echo $row['valor_realizado']?></li>
						<li>Plano ID:<?php echo $row['plano_ID']?></li>
						<li>Contrapartida ID: <?php echo $row['contrapartida_ID']?></li>
						<li>Plano: <?php echo $row['plano']?></li>
						<li>Obs: <?php echo $row['obs']?></li>
					</ul>	
-->
				<div class="md-form">
					<button class="btn btn-sm btn-block rounded-pill btn-primary" type="submit">Enviar</button>
				</div>
				</form>
				</div>
			</div>
					
		</div>
				<?php 
				} else {
				?>
		<header class="p-5">
		<h1 class="text-center">Movimento não localizado!</1>	
		</header>
			<?php 
				}
				?>
	
	</div>
<?php
include_once('./include/footerbt.php');
?>
</body>
<?php
include_once('./include/scripts.php');
?>
