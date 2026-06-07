<?php
// include_once('./include/conexao.php');
include_once('./include/funcoes.php');
$titulo = "Edição de candidato";
// include_once('./include/head.php');
$id = 0;
$campos = array (
	'id' => 0,
	'nome'=>'',
	'cargo'=>'',
	'whatsapp'=>'',
	'telefone'=>'',
	'empresa'=>'',
	'email'=>'',
	'CNPJ'=>'',
	'CEP'=>'',
	'endereco'=>'',
	'complemento'=>'',
	'bairro'=>'',
	'cidade'=>'',
	'UF'=>'',
	'pais'=>'',
	'email_empresa'=>''
);

$campos_label = array (
	'nome'=>array('Nome:','nome do contato','text','required','far fa-user'),
	'cargo'=>array('Cargo:','cargo do contato','text','','far fa-user'),
	'email'=>array('E-mail:','e-mail do contato','email','','far fa-envelope'),
	'telefone'=>array('Telefone:','telefone da empresa','tel','required','fas fa-phone'),
	'empresa'=>array('Empresa:','forneça a empresa','text','required','far fa-address-card'),
	'CNPJ'=>array('CNPJ','CNPJ da Empresa','number','','far fa-address-card'),
	'whatsapp'=>array('WhatsApp:','whatsapp do contato','tel','','fas fa-phone'),
	'CEP'=>array('CEP:','CEP do endereço','number','','far fa-map'),
	'endereco'=>array('Endereço:','endereço','text','','far fa-map'),
	'complemento'=>array('Complemento:','número, bloco, apto etc.','text','','far fa-map'),
	'bairro'=>array('Bairro:','informe o bairro','text','','far fa-map'),
	'cidade'=>array('Cidade:','informe a cidade','text','','far fa-map'),
	'UF'=>array('Estado:','forneça o estado','text','','far fa-map'),
	'pais'=>array('País','digite o país','text','','far fa-map'),
	'email_empresa'=>array('E-mail','forneça o e-mail da empresa','email','','far fa-email')
);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['id'])) {
    header("Location: /login");
    exit;
}

if ($_SESSION['nivel'] != 5) {
    // Busca o empresa_id associado ao usuario_id logado
    $q_emp_link = "SELECT empresa_id FROM empresas WHERE usuario_id = " . intval($_SESSION['id']) . " LIMIT 1";
    $r_emp_link = mysqli_query($conexao, $q_emp_link);
    if ($r_emp_link && mysqli_num_rows($r_emp_link) > 0) {
        $row_emp_link = mysqli_fetch_assoc($r_emp_link);
        $id = (int)$row_emp_link['empresa_id'];
    } else {
        // Se o usuário não tiver empresa associada, exibe erro ou bloqueia
        die("Acesso restrito: sua conta de usuário não possui uma empresa associada.");
    }
} else {
    // Admin (nível 5) pode acessar via parâmetro
    if (array_key_exists(1, $parametros)){
        $id = intval($parametros[1]);
    }
}

if($_SERVER['REQUEST_METHOD']=="POST"){
	if (isset($_POST['nome'])){
		$campos['nome'] = $_POST['nome'];
	}
	if (isset($_POST['cargo'])){
		$campos['cargo'] = $_POST['cargo'];
	}
	if (isset($_POST['email'])){
		$campos['email'] = $_POST['email'];
	}
	if (isset($_POST['whatsapp'])){
		$campos['whatsapp'] = $_POST['whatsapp'];
	}
	if (isset($_POST['telefone'])){
		$campos['telefone'] = $_POST['telefone'];
	}
	if (isset($_POST['empresa'])){
		$campos['empresa'] = $_POST['empresa'];
	}
	if (isset($_POST['CNPJ'])){
		$campos['CNPJ'] = $_POST['CNPJ'];
	}
	if (isset($_POST['CEP'])){
		$campos['CEP'] = $_POST['CEP'];
	}
	if (isset($_POST['endereco'])){
		$campos['endereco'] = $_POST['endereco'];
	}
	if (isset($_POST['complemento'])){
		$campos['complemento'] = $_POST['complemento'];
	}
	if (isset($_POST['bairro'])){
		$campos['bairro'] = $_POST['bairro'];
	}
	if (isset($_POST['cidade'])){
		$campos['cidade'] = $_POST['cidade'];
	}
	if (isset($_POST['UF'])){
		$campos['UF'] = $_POST['UF'];
	}
	if (isset($_POST['pais'])){
		$campos['pais'] = $_POST['pais'];
	}
	if (isset($_POST['email_empresa'])){
		$campos['email_empresa'] = $_POST['email_empresa'];
	}
	
	if ($id>0){
		$query = "UPDATE `empresas` 
		SET `nome`='".$campos['nome']."',
			`cargo`='".$campos['cargo']."',
			`email`='".$campos['email']."',
			`telefone`='".$campos['telefone']."',
			`whatsapp`='".$campos['whatsapp']."',
			`empresa`='".$campos['empresa']."',
			`CNPJ`='".$campos['CNPJ']."',
			`CEP`='".$campos['CEP']."',
			`endereco`='".$campos['endereco']."',
			`complemento`='".$campos['complemento']."',
			`cidade`='".$campos['cidade']."',
			`UF`='".$campos['UF']."',
			`pais`='".$campos['pais']."',
			`email_empresa`='".$campos['email_empresa']."'";
			
		if ($_SESSION['nivel'] == 5) {
			$is_expositor = isset($_POST['is_expositor']) ? 1 : 0;
			$is_promotor = isset($_POST['is_promotor']) ? 1 : 0;
			$is_parceiro = isset($_POST['is_parceiro']) ? 1 : 0;
			$is_contratante = isset($_POST['is_contratante']) ? 1 : 0;
			$query .= ", `is_expositor` = $is_expositor,
					   `is_promotor` = $is_promotor,
					   `is_parceiro` = $is_parceiro,
					   `is_contratante` = $is_contratante";
		}
		
		$query .= " WHERE empresa_id = ".$id;
	} else {
		$is_expositor = isset($_POST['is_expositor']) ? 1 : 0;
		$is_promotor = isset($_POST['is_promotor']) ? 1 : 0;
		$is_parceiro = isset($_POST['is_parceiro']) ? 1 : 0;
		$is_contratante = isset($_POST['is_contratante']) ? 1 : 0;
		$query = "INSERT INTO `empresas`(`nome`, `cargo`,`email`, `telefone`, `whatsapp`, `empresa`, `CNPJ`, `CEP`, `endereco`, `complemento`, `cidade`, `UF`, `pais`, `email_empresa`, `is_expositor`, `is_promotor`, `is_parceiro`, `is_contratante`) VALUES ('".$campos['nome']."','" . $campos['cargo'] . "','" . $campos['email'] . "','" . telephone($campos['telefone']). "','" . telephone($campos['whatsapp']) ."','" . $campos['empresa']."','" . $campos['CNPJ']."','" . $campos['CEP']."','" . $campos['endereco']."','" . $campos['complemento']."','" . $campos['cidade']."','" . $campos['UF']."','" . $campos['pais']."','" . $campos['email_empresa']."', $is_expositor, $is_promotor, $is_parceiro, $is_contratante)";
	}
	$resp = mysqli_query($conexao,$query);	
}
?>
<!--** INDEX **-->
<style>
	.hidden {
		display: none;
	}
</style>
<body>
	<div class="container">
		<header class="mt-5 p-2 justify-content-md-center">
			<h1 class="text-center">Empresa</h1>
			<div class="row justify-content-between bg-light p-1 rounded">
			<div class="col">
				<nav aria-label="breadcrumb bg-transparent">
			  <ol class="breadcrumb bg-transparent">
				<li class="breadcrumb-item"><a href="/empresas">Empresas</a></li>
				<li class="breadcrumb-item active" aria-current="page">Empresa</li>
			  </ol>
			</nav>
			</div>
			<div class="col  col-auto">
			<form class="form-inline" method="post">
				<label for="exampleDataList" class="form-label">Datalist example</label>
				<input class="form-control" list="datalistOptions" id="empresa" name="empresa" placeholder="empresa...">
				<datalist id="datalistOptions">
					<?php 
					$querynomes = "SELECT empresa_id,empresa FROM empresas ORDER BY empresa;";
					$respnome = mysqli_query($conexao,$querynomes);
					while ($row = mysqli_fetch_assoc($respnome)){
						?>
						<option value="<?php echo $row['empresa']?>">
						<?php 
					}
					?>
						<option value="Nova Empresa">
				</datalist>
</div>
				<button class="btn btn-sm rounded-pill" type="submit">buscar</button>
			</form>
				</div>
			</div>
		</header>
		<div class="row justify-content-center">
			<div class="col col-md-8">
<?php
			$perfil = "/img/ms-icon-310x310.png";
			if ($id>0){
				$query = "SELECT empresas.*,imagens.url AS perfil 
				FROM empresas
				LEFT JOIN imagens ON empresas.imagem_id = imagens.imagem_id 
				WHERE empresas.empresa_id = ".$id;
				$msg = "Empresa não encontrada";
				$resp = mysqli_query($conexao,$query);
				if ($resp) {
					$row = mysqli_fetch_assoc($resp);
					if ($row['perfil']<>""){
						$perfil = "/".$row['perfil'];					
					}
					$campos['nome']=$row['nome'];
					$campos['cargo']=$row['cargo'];
					$campos['email']=$row['email'];
					$campos['telefone']=$row['telefone'];
					$campos['whatsapp']=$row['whatsapp'];
					$campos['empresa']=$row['empresa'];
					$campos['CNPJ']=$row['CNPJ'];
					$campos['CEP']=$row['CEP'];
					$campos['endereco']=$row['endereco'];
					$campos['complemento']=$row['complemento'];
					$campos['cidade']=$row['cidade'];
					$campos['UF']=$row['UF'];
					$campos['pais']=$row['pais'];
					$campos['email_empresa']=$row['email_empresa']; 
				}				
			}
			?>
				<div class="card">
				<div class="card-header">
				<?php 
				$troca = "";
				if ($perfil<>""){
					$troca = "/trocafoto/".digitos($id);
					?>
					<a href="<?php $troca?>"><img class="card-img-top img-thumbnail" src="<?php echo $perfil?>"></a>
					<?php 
				} else {
					?>
					<img class="card-img-top img-thumbnail" src="<?php echo $perfil?>">
					<?php 
				}
				?>
					<h1 class="text-center"><?php echo $campos['nome']?></h1>
				</div>
					<div class="card-body">
						<form method="post" enctype="multipart/form-data">
							<input type="hidden" id="empresa_id" name="empresa_id" value="<?php echo $id?>">
						<p><small>Imagem:</small></p>
					<div class="md-form">
							<i class="far fa-image prefix grey-text"></i>
<!--					  <label for="formFile" class="form-label">Default file input example</label>-->
<input class="form-control" type="file" name="file" id="file" accept=".jpg,.jpeg,.png,.pdf" >
<!--						<small>escolha o arquivo de imagem para este expositor</small>-->
					</div>
					<?php 
					// Loop através de todas as colunas e seus valores
					$i = 0;
					echo "<div id='section1' class='p-2 border'>";
					foreach ($campos as $campo => $valor) {
						if ($campo <>"imagem_id"  && $campo <> "id"){
						?>
						<div class="md-form">
							<i class="<?php echo $campos_label[$campo][4]?> prefix grey-text"></i>
							<input type="<?php echo $campos_label[$campo][2]?>" id="<?php echo $campo?>" name="<?php echo $campo?>" class="form-control" placeholder="<?php echo $campos_label[$campo][1]?>" value="<?php echo $valor?>" <?php echo $campos_label[$campo][3]?>>
							<label class="grey-text" for="<?php echo $campo?>"><small><?php echo $campos_label[$campo][0]?></small></label>
						</div>
						<?php 
						}
						$i++;
						if ($i==6){
							echo "</div><div id='section2' class='p-2 border hidden'>";
						}
					}
					?>
							</div>
							
                        <!-- Flags de Tipo de Empresa -->
                        <div class="p-3 border mt-3 mb-3 bg-light rounded">
                            <h6>Atribuições da Empresa:</h6>
                            <?php
                            $disabled_flag = ($_SESSION['nivel'] != 5) ? 'disabled' : '';
                            ?>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="is_expositor" name="is_expositor" value="1" <?php echo (isset($row['is_expositor']) && $row['is_expositor']) ? 'checked' : ''; ?> <?php echo $disabled_flag; ?>>
                                <label class="form-check-label" for="is_expositor">Expositor</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="is_promotor" name="is_promotor" value="1" <?php echo (isset($row['is_promotor']) && $row['is_promotor']) ? 'checked' : ''; ?> <?php echo $disabled_flag; ?>>
                                <label class="form-check-label" for="is_promotor">Promotor</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="is_parceiro" name="is_parceiro" value="1" <?php echo (isset($row['is_parceiro']) && $row['is_parceiro']) ? 'checked' : ''; ?> <?php echo $disabled_flag; ?>>
                                <label class="form-check-label" for="is_parceiro">Parceiro</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="is_contratante" name="is_contratante" value="1" <?php echo (isset($row['is_contratante']) && $row['is_contratante']) ? 'checked' : ''; ?> <?php echo $disabled_flag; ?>>
                                <label class="form-check-label" for="is_contratante">Contratante</label>
                            </div>
                        </div>

						<button class="btn btn-sm btn-pill btn-success rounded-pill" type="button" id="toggleButton">MAIS</button>
						<div class="md-form">
						<button class="btn btn-block btn-pill btn-primary" type="submit">Enviar</button>
						</div>
						</form>
					</div>
				</div>
			</div>
		</div>
			 
	</div>
    <script>
        // Lógica JavaScript para exibir/ocultar a segunda divisão
        const toggleButton = document.getElementById('toggleButton');
        const section2 = document.getElementById('section2');
        const CNPJ = document.getElementById('CNPJ');
        const nome = document.getElementById('nome');

        toggleButton.addEventListener('click', () => {
            if (section2.classList.contains('hidden')) {
                section2.classList.remove('hidden');
                toggleButton.textContent = 'MENOS';
				CNPJ.focus();
            } else {
                section2.classList.add('hidden');
                toggleButton.textContent = 'MAIS';
				nome.focus();
            }
        });
    </script>
<?php 
include_once('./include/footer.php');
?>
	
</body>
<?php
include_once('./include/scripts.php');
?>
	<script>
	// JavaScript Document
/*
	$(document).ready(function () {
	  // Send Search Text to the server
	  $("#busca").keyup(function () {
		let searchText = $(this).val();
		if (searchText != "") {
		  $.ajax({
			url: "/pages/action.php",
			method: "post",
			data: {
			  query: searchText,
			},
			success: function (response) {
			  $("#show-list").html(response);
			},
		  });
		} else {
		  $("#show-list").html("");
		}
	  });
	  // Set searched text in input field on click of search button
	  $(document).on("click", "a", function () {
		$("#busca").val($(this).text());
		$("#show-list").html("");
	  });
	});
*/
	</script>
	<script>

        $(document).ready(function() {

            function limpa_formulário_cep() {
                // Limpa valores do formulário de cep.
                $("#endereco").val("");
//                $("#bairro").val("");
                $("#complemento").val("");
                $("#cidade").val("");
                $("#UF").val("");
//                $("#ibge").val("");
            }
            
			//Quando o campo cep perde o foco.
            $("#CEP").blur(function() {

                //Nova variável "cep" somente com dígitos.
                var cep = $(this).val().replace(/\D/g, '');

                //Verifica se campo cep possui valor informado.
                if (cep != "") {

                    //Expressão regular para validar o CEP.
                    var validacep = /^[0-9]{8}$/;

                    //Valida o formato do CEP.
                    if(validacep.test(cep)) {

                        //Preenche os campos com "..." enquanto consulta webservice.
                        $("#endereco").val("...");
//                        $("#bairro").val("...");
                        $("#cidade").val("...");
                        $("#UF").val("...");
//                        $("#ibge").val("...");

                        //Consulta o webservice viacep.com.br/
                        $.getJSON("https://viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {

                            if (!("erro" in dados)) {
                                //Atualiza os campos com os valores da consulta.
                                $("#endereco").val(dados.logradouro);
//                                $("#bairro").val(dados.bairro);
                                $("#cidade").val(dados.localidade);
                                $("#UF").val(dados.uf);
                                $("#complemento").focus();
//                                $("#ibge").val(dados.ibge);
                            } //end if.
                            else {
                                //CEP pesquisado não foi encontrado.
                                limpa_formulário_cep();
                                alert("CEP não encontrado.");
                            }
                        });
                    } //end if.
                    else {
                        //cep é inválido.
                        limpa_formulário_cep();
                        alert("Formato de CEP inválido.");
                    }
                } //end if.
                else {
                    //cep sem valor, limpa formulário.
                    limpa_formulário_cep();
                }
            });
			
		});
		
    </script>
<?php 
include_once('./include/end.php');
?>
