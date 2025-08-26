<?php
$id = 0;
if (array_key_exists(2,$parametros)){
	$id = intval($parametros[2]);
}
$query = "SELECT * FROM contabil_movimento WHERE movimento_id =".$id.";";
$result = mysqli_query($conexao,$query);
$msg="";
$query_planos = "SELECT codigo,plano_ID,plano FROM `contabil_plano_itens` WHERE planos_id =2 order by codigo;";
$planos = mysqli_query($conexao,$query_planos);
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
					$msg = $row['descricao'];
					$date = date_create($row['data_realizada']);
					$ano = date_format($date,"Y");
					$mes = date_format($date,"m");
					$anterior = $id-1;
					$proximo = $id+1;
					?>
			<h1><?echo $msg?></h1>
			<span class="badge badge-light m-1 rounded-pill"><a href='/admin'>Balanço</a></span>
			<span class="badge badge-light m-1 rounded-pill"><a href='/admin/extrato/<? echo $ano?>/<? echo digitos($mes,2)?>'><? echo $ano?>/<? echo digitos($mes,2)?></a></span>|
			<span class="badge badge-light m-1 rounded-pill"><a href='/admin/movimento/<? echo $anterior?>'>Anterior</a></span>
			<span class="badge badge-light m-1 rounded-pill"><a href='/admin/movimento/<? echo $proximo?>'>Próximo</a></span>
<!--
<? echo $query?>
			<span class="badge badge-info m-1 rounded-pill"><a href='/admin/extrato/<? echo $anoantes?>/<?echo $mesantes?>'><?echo $mesantes?>/<?echo $anoantes?></a></span>
			<span class="badge badge-success m-1 rounded-pill"><?echo $mes?>/<?echo $ano?></span>
			<span class="badge badge-info m-1 rounded-pill"><a href='/admin/extrato/<? echo $anoantes?>/<?echo $mesdepois?>'><?echo $mesdepois?>/<?echo $anodepois?></a></span>
-->
		</header>
		<div class="row">
			<div class="col">
				<div class="card p-1">
				<form method="post">
					<input type="hidden" value="<? echo $id?>">
				<div class="md-form">
					<i class="far fa-edit prefix grey-text"></i>
					<input type="text" name="descricao" id="descricao" value="<? echo $row['descricao']?>">
					<label for="descricao">descrição</label>
				</div>
				<div class="md-form">
					<i class="far fa-calendar prefix grey-text"></i>
					<input type="text" name="data_inclusao" id="data_inclusao" value="<? echo $row['data_inclusao']?>">
					<label for="data_inclusao">Data inclusão</label>
				</div>
				<div class="md-form">
					<i class="far fa-calendar prefix grey-text"></i>
					<input type="text" name="data_prevista" id="data_prevista" value="<? echo $row['data_prevista']?>">
					<label for="data_prevista">Data prevista</label>
				</div>
				<div class="md-form">
					<i class="far fa-calendar prefix grey-text"></i>
					<input type="text" name="data_realizada" id="data_realizada" value="<? echo $row['data_realizada']?>">
					<label for="data_realizada">Data realizada</label>
				</div>
				<div class="md-form">
					<i class="far fa-calendar prefix grey-text"></i>
					<input type="text" name="data_realizada" id="data_realizada" value="<? echo $row['data_realizada']?>">
					<label for="data_realizada">Data realizada</label>
				</div>
				<div class="md-form">
					<i class="fa fa-dollar-sign prefix grey-text"></i>
					<input type="text" name="valor_previsto" id="valor_previsto" value="<? echo $row['valor_previsto']?>">
					<label for="valor_previsto">Valor previsto</label>
				</div>
				<div class="md-form">
					<i class="fa fa-dollar-sign prefix grey-text"></i>
					<input type="text" name="valor_realizado" id="valor_realizado" value="<? echo $row['valor_realizado']?>">
					<label for="valor_realizado">Valor realizado</label>
				</div>
					<label for="plano">Plano de contas: </label>
					<select id="plano" name="plano" list="listaplanos"/>
					<datalist id="listaplanos">
					<?
						while($item = mysqli_fetch_array($planos)){
						
					?>
					<option value="<? echo $item['plano_ID']?>"
							<?
							if ($item['plano_ID']==$row['plano_ID']){
								echo "selected";
							}
							?>><? echo $item['codigo']." - ".$item['plano']?></option>
						<?
							
						}
						?>
					</datalist>
					</select></br>
					<label for="contra"> Contrapartida:</label>
					<select id="contra" name="contra" list="listacontra"/>
					<datalist id="listaconstra">
					<?
						$planos2 = mysqli_query($conexao,$query_planos);
						while($item = mysqli_fetch_array($planos2)){
						
					?>
					<option value="<? echo $item['plano_ID']?>"
							<?
							if ($item['plano_ID']==$row['contrapartida_ID']){
								echo "selected";
							}
							?>><? echo $item['codigo']." - ".$item['plano']?></option>
						<?
							
						}
						?>
					</datalist>
					</select>
				<div class="md-form">
					<i class="far fa-edit prefix grey-text"></i>
					<input type="text" name="obs" id="obs" value="<? echo $row['obs']?>">
					<label for="Obs">Observação</label>
				</div>
<!--
					<ul>
						<li>id:<? echo $row['movimento_id']?></li>
						<li>empresa: <? echo $row['empresa_ID']?></li>
						<li>usuário: <? echo $row['usuario_ID']?></li>
						<li>descrição: <? echo $row['descricao']?></li>
						<li>Data inclusão: <? echo $row['data_inclusao']?></li>
						<li>Data prevista: <? echo $row['data_prevista']?></li>
						<li>Data realizada: <? echo $row['data_realizada']?></li>
						<li>Valor previsto: <? echo $row['valor_previsto']?></li>
						<li>Valor realizado: <? echo $row['valor_realizado']?></li>
						<li>Plano ID:<? echo $row['plano_ID']?></li>
						<li>Contrapartida ID: <? echo $row['contrapartida_ID']?></li>
						<li>Plano: <? echo $row['plano']?></li>
						<li>Obs: <? echo $row['obs']?></li>
					</ul>	
-->
				<div class="md-form">
					<button class="btn btn-sm btn-block rounded-pill btn-primary" type="submit">Enviar</button>
				</div>
				</form>
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
include_once('./include/footerbt.php');
?>
</body>
<?php
include_once('./include/scripts.php');
?>
