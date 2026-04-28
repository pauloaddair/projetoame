<?php
$msg="";
if ($_SERVER['REQUEST_METHOD']=="POST"){
	// Verifica se o arquivo foi enviado
	if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
		// Caminho temporário do arquivo
		$inputFileName = $_FILES['file']['tmp_name'];

		// Abre o arquivo CSV
		if (($handle = fopen($inputFileName, 'r')) !== FALSE) {
			// Ignora a primeira linha se for o cabeçalho
			fgetcsv($handle);
			$i=0;
			// Processa cada linha do CSV
			while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
				// Verifica se os dados estão completos para inserção
				if (count($data) >= 3) {
					$descricao = $conexao->real_escape_string($data[5]); // Ajuste o índice conforme a coluna
					$data_inclusao = date('Y-m-d H:i'); // Data atual
					$data_realizada = substr($data[0],6,4)."/".substr($data[0],3,2)."/".substr($data[0],0,2); // Ajuste o formato da data
					$data_dia = substr($data_realizada,0,2);
					$valor_realizado = (float) $data[6]; // Valor numérico
					$valor_realizado += (float) $data[7]; // Valor numérico
					$valor_realizado += (float) $data[8]; // Valor numérico
					$obs = $data[2];
					$empresa_id = 2;
					$usuario_id = intval($_SESSION['id']);
					// Insere no banco de dados
					$sql = "INSERT INTO contabil_movimento (descricao, empresa_id, usuario_id, data_inclusao, data_realizada, valor_realizado, obs)
							VALUES ('$descricao', '$empresa_id', '$usuario_id', '$data_inclusao', '$data_realizada', '$valor_realizado','$obs')";
/*
					echo $sql."<br>";
					exit;
*/
					if ($conexao->query($sql) === TRUE) {
//						echo "Registro inserido com sucesso: $descricao, $data_realizada,$data[0], $valor_realizado<br>";
					} else {
						$msg = "Erro ao inserir o registro: " . $conn->error . "<br>";
					}
					$i++;
				}
			}
			$msg = "Importados ".$i. " lançamentos";
			fclose($handle);
		} else {
			$msg = "Erro ao abrir o arquivo CSV.";
		}
	} else {
		$msg = "Erro ao carregar o arquivo.";
	}
	
}
$titulo = "Importar extrato";
// include_once('./include/head.php');
?>
<body>
    <div class="container mt-5">
		<header>
			<h2 class="mb-4">Importar Lançamentos - CSV</h2>
			<?php
			if ($msg){
				echo "<h2>".$msg."</h2>";
			}
			?>
		</header>
        
        <!-- Formulário para upload do arquivo CSV -->
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="file" class="form-label">Selecione o arquivo CSV:</label>
                <input type="file" class="form-control" id="file" name="file" accept=".csv" required>
            </div>
            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>
    </div>
<?php
	include_once('./include/footer.php');
	include_once('./include/scripts.php');
	include_once('./include/end.php');
?>

