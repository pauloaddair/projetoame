<?php
// session_start();
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
include_once('./include/conexao.php');
include_once('./include/funcoes.php');
include_once('./include/head.php');
$nomearquivo = "";
$campos = array(
	array(
		"nome",
		"Nome",
		"nome do inscrito",
		"text",
		"fas fa-user",
		"required"
	),
	array(
		"responsavel",
		"Responsável",
		"nome do responsável",
		"text",
		"fas fa-user",
		"required"
	),
	array(
		"Email",
		"E-mail",
		"e-mail do responsável",
		"text",
		"fas fa-envelope",
		"required"
	),
	array(
		"Telefone",
		"Telefone",
		"telefone de contato",
		"tel",
		"fas fa-phone",
		""
	),
	array(
		"Nascimento",
		"",
		"data de nascimento do inscrito",
		"date",
		"fas fa-calendar",
		""
	),
	array(
		"Curriculo",
		"Currículo ou foto",
		"curriculo ou foto (opcional)",
		"file",
		"fas fa-file",
		""
	),
	array(
		"genero",
		"Gênero",
		"sexo do inscrito (opcional)",
		"option",
		"fas fa-user",
		"",
		"masculino",
		"feminino",
	),
	array(
		"mensagem",
		"Observação",
		"deixe sua observação (opcional)",
		"textarea",
		""
	),
);
$dados['nome']="";
$dados['responsavel']="";
$dados['Email']="";
$dados['Telefone']="";
$dados['Nascimento']="";
$dados['Curriculo']="";
$dados['genero']="";
$dados['mensagem']="";
/*
echo "<pre>";
print_r($campos);
echo "</pre>";
*/
//exit;
$body="";
$msg ="Novo inscrito";
if ($_SERVER['REQUEST_METHOD']=="POST"){
	$query = "SELECT candidatos.* FROM candidatos WHERE candidatos.Email LIKE '".$_POST['Email']."'";
	$nome = $_POST['nome'];
	$email = $_POST['Email'];
	$telefone = $_POST['Telefone'];
	$nome = iconv("UTF-8", "ISO-8859-1//TRANSLIT",$_POST['nome']);
//	echo $query . "<br>";
	$r = mysqli_query($conexao,$query);
	$cadastrar = 1;
?>
	<?php foreach ($_POST as $campo => $valor) {
			$dados[$campo] = $valor;
		}
	If (mysqli_num_rows($r)>0){
		$cadastrar = 0;
		$row = mysqli_fetch_assoc($r);
		$candidato_id = $row['candidato_id'];
		$nome = $_POST['nome'];
		$msg = "<h2 class='text-center'>Atendente: ".$nome."</h2><p class='mt-1 p-2 text-center bg-success rounded-pill'>Confirme seus dados abaixo</p>";
		$query = "UPDATE `candidatos` SET ";
	    foreach ($_POST as $campo => $valor) {
        // Exibir o nome do campo e seu valor
			$ev = "";
			if ($campo<>"nome" && $campo<>"email"&& $campo<>"Curriculo"){
				$query .="`".$campo."`='".$valor."', ";
			} 
		}
			$query = substr($query,0,-2);
			$query .=" WHERE candidato_id =".$candidato_id;
		} else {
			$query = "SELECT (MAX(rodizio)+1) AS max FROM candidatos;";
			$resp = mysqli_query($conexao,$query);
			$max = mysqli_fetch_array($resp);
			$rodizio = $max['max'];
			$cadastrar = 1;
			$msg = "<h2 class='text-center'>Atendente não encontrado</h2><p class='text-center'><span class='mt-1 p-2 bg-danger rounded-pill text-white'>Por favor, verifique se digitou seu e-mail cadastrado corretamente e tente novamente, ou então preencha os dados abaixo para se cadastrar pela primeira vez.</span></p>";
			$query = "INSERT INTO `candidatos` ";
			$insert_campos = "`inscrito`, `IP`, `rodizio`, ";
			$valores="'candidato', '".$_SERVER["REMOTE_ADDR"]."', ".$rodizio.", ";
			foreach ($_POST as $campo => $valor) {
			// Exibir o nome do campo e seu valor
				$ev = "";
				if ($campo<>"Curriculo"){
					$insert_campos .="`".$campo."`, ";
					$valores .="'".$valor."', ";
				}
			}
			$insert_campos = substr($insert_campos,0,-2);
			$valores = substr($valores,0,-2);
			$query .="(".$insert_campos.") VALUES (".$valores.")";
		}
		$resp = mysqli_query($conexao,$query);
		if ($cadastrar==1){
			$sobrenome = "";
			$candidato_id = $conexao -> insert_id;
			$temp = explode(" ",$_POST['responsavel']);
			$login = slugify($_POST['responsavel'],'',10);
			if (array_key_exists(1,$temp)){
				$sobrenome = trim(after($temp[0],$_POST['responsavel']));	
			}
			$query = "SELECT * FROM usuarios WHERE `email` LIKE `".$email."`";
			$resp = mysqli_query($conexao,$query);
			if (mysqli_num_rows($resp)>0){
				$query = "UPDATE `usuarios` SET `login`='".$login."',`telefone`='".$telefone."',`nome`='".$nome."',`sobrenome`='".$sobrenome."'";
			} else {
				$senha = "projetoame";
				$query = "INSERT INTO usuarios (`nome`, `login`, `senha`, `email`, `telefone`, `sobrenome`, `nivel`) VALUES ('".$temp[0]."','".$login."',MD5('".$senha."'),'".$email."','".$telefone."','".$sobrenome."',1)";			
			}
/*
			echo $query."<br>";
			exit;
*/
			$resp = mysqli_query($conexao,$query);
			$usuario_id = $conexao -> insert_id;
			$query = "UPDATE candidatos SET `usuario_id`=".$usuario_id." WHERE candidato_id =".$candidato_id;
			$resp = mysqli_query($conexao,$query);
		}
		$tnpFile = $_FILES["Curriculo"]["tmp_name"];
		if (!empty($tnpFile)){
			$img_id = 0;
			$nomearquivo = "docs/". $_FILES['Curriculo']['name'] ;
			$dir = before("pages",__DIR__);
	//		$file_parts = pathinfo($dir . $nomearquivo);
			$icone = $nomearquivo;

	/*
			echo $file_parts."<br>".$dir . "<br>".$_FILES['file']['tmp_name']. "<br>". $nomearquivo . "<br>" . $icone . "<br>";
			exit;
	*/
		//		echo $nomearquivo . "<br>";
		copy ( $_FILES['Curriculo']['tmp_name'], 
		 $dir . $nomearquivo ) 
		or die( "Não foi possível copiar o arquivo!" );
		}
		$query1 = "INSERT INTO `documentos` (`candidato_id`,`url`,`descritivo`) VALUES (".$candidato_id.",'".$nomearquivo."','Curriculo - ".$nome."')";
		$resp1 = mysqli_query($conexao,$query1);
		$doc_id = $conexao -> insert_id;
	
		$query1 = "UPDATE candidatos SET `anexo_id` = ".$doc_id." WHERE candidato_id =".$candidato_id;
		$resp1 = mysqli_query($conexao,$query1);

		ini_set( 'display_errors', 1 );
		error_reporting( E_ALL );
		require './include/autoload.php';
		$email = "pauloadd@hotmail.com";
		if (isset($_POST['email'])){
			$email = strtolower($_POST['email']);
			$dados['email'] = $email;
		}
		$from = "contato@projetoame.org";
		$to = "pauloadd@gmail.com";
	//    $to = "pauloadd@gmail.com";
		$message = "";
		// To send HTML mail, the Content-type header must be set
		$headers[] = 'MIME-Version: 1.0';
		$headers[] = 'Content-type: text/html; charset=iso-8859-1';
		$headers[] = 'From:Dados de Atendente via Site<' . $from . ">";
		$headers[] = 'Content-Type: text/html; charset=iso-8859-1';
	//    $headers[] = 'Content-Transfer-Encoding: 7bit';
	    $headers[] = 'Cc: <'. $_POST['Email'].'>,<regina.rjr@hotmail.com>';
	//    $headers[] = 'Cc: <regina.rjr@hotmail.com>';
	//    $headers[] = "From:" . $from;

	//    $headers = "From:" . $from;

		$subject  = 'Dados do atendente:'.$nome; // Assunto da mensagem
		$body = ' Nome: <strong>'.$nome.'</strong><br>'; // Nomes do atendente
		$body .= ' Responsavel: <strong>'.$_POST['responsavel'].'</strong><br>'; // Nomes dos noivos
			    foreach ($_POST as $campo => $valor) {
        // Exibir o nome do campo e seu valor
			$ev = "";
			if ($campo<>"nome" && $campo<>"Email"&& $campo<>"responsavel"){
				$body .= ' '. $campo .': '.$valor."<br>";
			}
				}
		$body .= " <a href='https://www.projetoame.org/".$nomearquivo."' target='_blank'>Curriculo</a><br>";
		$body .= '<br><hr> DADOS DE AUDITORIA:<br>IP: '.$_SERVER['SERVER_ADDR'].'<br>'; // IP do visitante
		$body .= ' Navegador: '.$_SERVER['HTTP_USER_AGENT'].'<br>'; // IP do visitante
		$body .= ' Enviado em: '. date('d/m/Y H:i').'<br>'; // Texto da mensagem
			$message = $body;
		$dados['server'] = $_SERVER['SERVER_ADDR'];
		$dados['agent'] = $_SERVER['HTTP_USER_AGENT'];
		$dados['data_atual'] = date('d/m/Y H:i');
		if (mail($to,$subject,$message, implode("\r\n", $headers))) {
	//    if (mail($to,$subject,$message, $headers)) {
		   $msg = "<p class='text-center'>Os dados de <strong><em>".$nome."</em></strong> foram enviados com sucesso!</p>"; // or use booleans here
		} else {
			$msg = "<p class='text-center'>Não conseguimos enviar sua mensagem!  Tente novamente mais tarde.</p>";;
		}
	}
?>
<body>
	<div class="container">
		<header>
		<h1 class="text-center"><?php echo $msg?></h1>
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="/atendentes">Atividades</a></li>
				<li class="breadcrumb-item"><a href="/rodizio">Rodizio</a></li>
				<li class="breadcrumb-item active" aria-current="page">Atividades confirmadas</li>
			  </ol>
			</nav>
		</header>
		<form method="post" enctype="multipart/form-data">
<!--
			<div class="row mt-1">
				<div class='col-12'>
					<?php echo $msg;
					?>
				</div>
			</div>
-->
			<div class="row mt-1">
	</div><hr>
			<div class="row justify-content-center">
				<div class="col">
					<div class="card p-1 rounded shadow">
				<p class="text-center"><strong>Confirme seus dados, por favor</strong></p>
					<?php foreach ($campos as $item){
/*
						echo $item[0]."<br>";
						echo $item[1]."<br>";
						echo $item[2]."<br><hr>";
*/
						?>
						<div class="md-form">
							<?php switch ($item[3]){
								case "textarea":
									?>
							<p><?php echo $item[0]?></p>
<!--								<label for="<?php echo $item[0]?>"><?php echo $item[1]?></label>-->
							<textarea class="form-control" id="<?php echo $item[0]?>" name="<?php echo $item[0]?>"><?php echo $dados[$item[0]]?></textarea>
									<?php break;
								case "option":
									?>
							<p><?php echo $item[0]?></p>
<!--								<label for="<?php echo $item[0]?>"><?php echo $item[1]?></label>-->
<!--								<i class="<?php echo $item[4]?> prefix grey-text"></i>-->
								<select class="form-select" id="<?php echo $item[0]?>" name="<?php echo $item[0]?>">
									<option value="<?php echo $item[6]?>"><?php echo $item[6]?></option>
									<option value="<?php echo $item[7]?>"><?php echo $item[7]?></option>
								</select>
							<?php break;
								case "file":
									?>
								<i class="<?php echo $item[4]?> prefix grey-text"></i>
								<input class="form-control"  type="<?php echo $item[3]?>" id="<?php echo $item[0]?>" name="<?php echo $item[0]?>" class="form-control" <?php echo $item[5]?>>
							<?php break;
								default:
									?>
								<i class="<?php echo $item[4]?> prefix grey-text"></i>
								<label for="<?php echo $item[0]?>"><?php echo $item[1]?></label>
								<input type="<?php echo $item[3]?>" id="<?php echo $item[0]?>" name="<?php echo $item[0]?>" class="form-control" placeholder="<?php echo $item[2]?>" <?php echo $item[5]?> value="<?php echo $dados[$item[0]]?>">
							<?php }
							?>
						</div>
						<?php }
					?>
				<div class="md-form">
					<button class="btn btn-sm btn-block rounded-pill btn-primary" type="submit">Enviar</button>
				</div>
				</div>
			</div>
			</div>
			</form>
	</div>
<?php
include_once('./include/footer.php');
?>
</body>
<?php
include_once('./include/scripts.php');
?>
<?php include_once('./include/end.php');
?>
