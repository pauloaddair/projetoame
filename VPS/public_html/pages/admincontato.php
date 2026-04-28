<body>
	<div class="container mt-5">
<?php
	$t="";
	$titulo = "";
	$tabela = "eventos";
	$usuario_id = $_SESSION['id'];
	$id = 0;
	if(array_key_exists(2,$parametros)){
		$id = intval($parametros[2]);
	}		
	$msg = "ID=".$id;
	$query ="SELECT Empresa,NomeFantasia,Resp1,Cargo1,Cidade,DDD+Fone1 AS tel,Email FROM expositores WHERE expositor_ID = ".$id;
	if(array_key_exists(3,$parametros)){
		$msg .= "<br>Da tabela ".$parametros[3];
		$t= $parametros[3];
		switch ($t) {
			case "prospects":
				$tabela ="expositores";
				$titulo = "Prospect";
				$query ="SELECT Empresa,NomeFantasia,Resp1,Cargo1,Cidade,DDD+Fone1 AS tel,Email FROM expositores WHERE expositor_ID = ".$id;
				$campos = array("Empresa","NomeFantasia","Resp1","Cargo1","Cidade","tel","Email");
				$labels = array(
					"Empresa"=>"Empresa",
					"NomeFantasia"=>"Nome fantasia",
					"Resp1" => "Responsável",
					"Cargo1" => "Cargo",
					"Cidade" => "Cidade",
					"tel" => "Telefone",
					"Email" => "E-mail");
				break;				
			case "eventos":
				$tabela ="eventos";
				$titulo = "Evento";
				$query ="SELECT Evento,Local,Promotor,Cidade,Inicio,Final,SITE FROM ".$tabela . " WHERE evento_id = ". $id;
				$campos = array("Evento","Local","Promotor","Cidade","Inicio","Final","SITE");
				$labels = array(
					"Evento"=>"Evento",
					"Local"=>"Local",
					"Promotor" => "Promotor",
					"Cidade" => "Cidade",
					"Inicio" => "Início",
					"Final" => "Final",
					"SITE" => "Site");
				break;				
			case "expositores":
				$tabela ="expositores2024";
				$titulo = "Expositor";
				$query ="SELECT nome,email,empresa,expositores_telefone.numero,expositores_telefone.celular FROM expositores2024,expositores_telefone WHERE expositores2024.expositor_id = expositores_telefone.expositor_id AND expositores2024.expositor_id = " . $id;
				$campos = array("nome","empresa","email","numero");
				$labels = array(
					"nome"=>"Contato",
					"email"=>"E-mail",
					"empresa" => "Empresa",
					"numero" => "Telefone");
				break;				
			case "atendente":
				$tabela ="candidatos";
				$titulo = "Atendente";
				break;				
			default:
				$tabela ="pendencia";
				$titulo = "Pendência";
				break;				
		}
	}
		if($_SERVER['REQUEST_METHOD']=="POST"){
			if (isset($_POST['anotar'])){
				$comentario = "";
				if(isset($_POST['comentario'])){
					$comentario = $_POST['comentario'];
				}
				$queryinsert = "INSERT INTO `comentarios`(`usuario_id`, `tabela`, `id`, `comentario`) VALUES (".$usuario_id.",'".$tabela."',".$id.",'".$comentario."')";
				$anotando = mysqli_query($conexao,$queryinsert);
			}
			if (isset($_POST['enviar'])){
				$comentario = "";
				if(isset($_POST['comentario'])){
					$comentario = $_POST['comentario'];
				}
				$queryinsert = "INSERT INTO `comentarios`(`usuario_id`, `tabela`, `id`, `comentario`) VALUES (".$usuario_id.",'".$tabela."',".$id.",'".$comentario."')";
				$anotando = mysqli_query($conexao,$queryinsert);
			}
			if (isset($_POST['salvar'])){
				$comentario = "";
				if(isset($_POST['comentario'])){
					$comentario = $_POST['comentario'];
				}
				$queryinsert = "INSERT INTO `comentarios`(`usuario_id`, `tabela`, `id`, `comentario`) VALUES (".$usuario_id.",'".$tabela."',".$id.",'".$comentario."')";
				$anotando = mysqli_query($conexao,$queryinsert);
			}
		}
		$querycomments = "SELECT * FROM comentarios WHERE id=".$id. " AND tabela = '".$tabela."'";
		echo $msg."<br>";
		echo "<br>".$query."<br>".$tabela . "<br>";
		$resp= mysqli_query($conexao,$query);
//	echo "<pre>";
//	print_r($resp);
//	echo "</pre>";
		$comments = mysqli_query($conexao,$querycomments);
		include_once('./include/nav.php');
		?>
		<header class="mt-5">
			<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="/atendentes">Atividades</a></li>
				<li class="breadcrumb-item"><a href="/admin/<?php echo $t?>"><?php echo $titulo?></a></li>
				<li class="breadcrumb-item active" aria-current="page">Contato</li>
			</ol>
			</nav>
			<h1 class="text-center"><?php echo $titulo?></h1>
		</header>
		<div class="row justify-content-center">
			<div class="col col-sm-6 mb-2">
				<div class="card rounded shadow">
					<div class="card-body">
						<form method="post">
							<input type="hidden" name="id" value="<?php echo $id?>">
							<input type="hidden" name="tabela" value="<?php echo $tabela?>">
							<input type="hidden" name="uid" value="<?php echo $usuario_id?>">
						<?php
						if (mysqli_num_rows($resp)>0){
							print_r($resp)."<br>";
							while($row = mysqli_fetch_array($resp)){
								foreach($campos as $item){
						?>
							<div class="md-form p-1">
								<i class="far fa-map prefix grey-text"></i>
								<input type="text" id="<?php echo $item?>" name="<?php echo $item?>" class="form-control" placeholder="<?php echo $item?>"
									   <?php 
									   if(isset($_POST[$item]) && !is_null($row)){
										   echo " value='".$_POST[$item]."'";
									   } else {
										   echo " value='".$row[$item]."'";
									   }
									   ?>>
								<label for="<?php echo $item?>"><?php echo $labels[$item]?></label>
							</div>
							<?php
								}
							}		
						} else {
							?>
							<div class="md-form p-1">
								<label>Nenhum <?php echo $titulo?> encontrado</label>
							</div>
							<?php
						}			
							?>
							<div class="md-form">
								<button class="btn btn-sm btn-block rounded-pill btn-primary" type="submit" name="enviar" name="enviar">Enviar</button>
							</div>
							<hr>
						</form>
					</div>
				</div>
			</div>
			<div class="row justify-content-center d-flex align-items-stretch">
				<div class="col-12 col-md-6 align-items-stretch  justify-content-center">
					<div class="card rounded shadow">
						<div class="card-header"><h1>Ações</h1></div>
						<div class="card-body">
								<input type="hidden" name="id" value="<?php echo $id?>">
								<input type="hidden" name="tabela" value="<?php echo $tabela?>">
								<input type="hidden" name="uid" value="<?php echo $usuario_id?>">
								<a hef="/whastapp/<?php digitos($id)?>" class="btn btn-sm btn-block rounded-pill btn-success mt-1" name="anotar"><i class="fa fa-whatsapp text-white p-1"></i>Mensagem</a>
								<a hef="/email/<?php digitos($id)?>" class="btn btn-sm btn-block rounded-pill btn-primary mt-1" name="anotar"><i class="far fa-envelope text-white p-1"></i>E-mail</a>
							<hr>
							<form method="post">
								<div class="custom-control custom-switch">
	<!--							<i class="far fa-map prefix grey-text"></i>-->
								  <input class="custom-control-input" type="checkbox" role="switch" id="prospect" name="prospect">
								  <label class="custom-control-label" for="prospect">Expositor</label>
								</div>
								<hr>
								<div class="md-form">
									<?php
									$dataFutura = date('Y-m-d', strtotime('+3 days'));
									?>
									<i class="fa fa-calendar prefix text-white p-1"></i>
									<input type="date" class="form-control" id="data" name="data" value="<?php echo $dataFutura ?>">
								  <label class="form-label" for="data">Próximo contato</label>
								</div>
								<div class="custom-control custom-switch">
	<!--							<i class="far fa-map prefix grey-text"></i>-->
								  <input class="custom-control-input" type="checkbox" role="switch" id="semretorno" name="semretorno" >
								  <label class="custom-control-label" for="semretorno">Não contactar</label>
								</div>
								<button type="submit" class="btn btn-sm btn-block rounded-pill btn-primary mt-1"  name="salvar" name="salvar">Salvar</button>
							</form>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-6 align-items-stretch justify-content-center">
					<div class="card rounded shadow">
						<div class="card-header"><h1>Anotações</h1></div>
						<div class="card-body">
							<form method="post">
								<input type="hidden" name="id" value="<?php echo $id?>">
								<input type="hidden" name="tabela" value="<?php echo $tabela?>">
								<input type="hidden" name="uid" value="<?php echo $usuario_id?>">
							<table class="table">
							<?php
							while ($row1 = mysqli_fetch_assoc($comments)){
								echo "<tr><td>".$row1['data']."</td><td>".$row1['comentario']."</td></tr>";
							}
							?>
							</table>
							<div class="md-form p-1">
								<i class="far fa-map prefix grey-text"></i>
								<textarea id="comentario" name="comentario" class="form-control" placeholder="faça sua anotação"></textarea>
								<label for="comentario">Anotação:</label>
							</div>
							<div class="md-form">
								<button class="btn btn-sm btn-block rounded-pill btn-primary" type="submit" id="anotar" name="anotar">Anotar</button>
							</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php
	include_once('./include/footer.php');
?>
</body>
<?php
include_once('./include/scripts.php');
?>
<?php
include_once('./include/end.php');
?>
