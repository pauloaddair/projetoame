<?php
// Inclui a conexão com o banco de dados
// include('./include/conexao.php');
$ativ = "Backup";
$titulo = "Backup de dados";
if (isset($_SESSION['nivel']) && $_SESSION['nivel'] >= 3) {

// Diretório onde os backups serão salvos
$backup_dir = 'docs/';

// Função para gerar o backup
function backupDatabase($conexao, $database, $backup_dir) {
    $backup_file = $backup_dir . 'backup_' . $database . '_' . date("Y-m-d-H-i-s") . '.sql';
    $zip_file = $backup_dir . 'backup_' . $database . '_' . date("Y-m-d-H-i-s") . '.zip';
    
    $tables = array();
    $result = $conexao->query("SHOW TABLES");

    // Obter todas as tabelas
    while ($row = $result->fetch_row()) {
        $tables[] = $row[0];
    }

    $sqlScript = "";
    foreach ($tables as $table) {
        // Adicionar comando para excluir a tabela se já existir
        $sqlScript .= "DROP TABLE IF EXISTS `$table`;\n";

        // Obter estrutura da tabela
        $result = $conexao->query("SHOW CREATE TABLE $table");
        $row = $result->fetch_row();
        $sqlScript .= "\n\n" . $row[1] . ";\n\n";

        // Obter os dados da tabela
        $result = $conexao->query("SELECT * FROM $table");
        $columnCount = $result->field_count;

        for ($i = 0; $i < $columnCount; $i++) {
            while ($row = $result->fetch_row()) {
                $sqlScript .= "INSERT INTO $table VALUES(";
                for ($j = 0; $j < $columnCount; $j++) {
                    $row[$j] = $row[$j] ? "'".$conexao->real_escape_string($row[$j])."'" : "NULL";
                    $sqlScript .= $row[$j];
                    if ($j < ($columnCount - 1)) {
                        $sqlScript .= ', ';
                    }
                }
                $sqlScript .= ");\n";
            }
        }
        $sqlScript .= "\n";
    }

    // Salvar o arquivo de backup
    if (!empty($sqlScript)) {
        file_put_contents($backup_file, $sqlScript);
        
        // Compactar o arquivo em um arquivo .zip
        $zip = new ZipArchive();
        if ($zip->open($zip_file, ZipArchive::CREATE) === TRUE) {
            $zip->addFile($backup_file, basename($backup_file));
            $zip->close();
            
            // Apagar o arquivo .sql original após compactá-lo
            unlink($backup_file);
            
            $msg= "<p class='text-center bg-info rounded-pill p-1'>Backup da base de dados '$database' criado e compactado com sucesso em <strong>'$zip_file'</strong></p>";
        } else {
            $msg =  "Falha ao criar o arquivo .zip.<br>";
        }
    } else {
        $msg= "Erro ao criar o backup.<br>";
    }
	return($msg);
}
$msg = "";
// Verifica se o backup foi solicitado
if (isset($_POST['backup'])) {
    $msg = backupDatabase($conexao, 'projetoAME', $backup_dir);
}
if (isset($_POST['excluir'])) {
//	echo "<p>".$_POST['arquivo']."</p>";
	$file = $_POST['arquivo'];
	unlink($file);
	$msg = "<p class='text-center bg-success rounded-pill p-1'>Arquivo ".$file." excluído com sucesso!</p>";
}

// Função para listar os backups
function listarBackups($backup_dir) {
    if (is_dir($backup_dir)) {
        if ($dh = opendir($backup_dir)) {
            echo "<h2>Lista de Backups Disponíveis</h2>";
            echo "<table class='table' id='table'><thead><th>#</th><th>Data</th><th>Baixar</th><th>Excluir</th></thead><tbody>";
			$i=1;
            while (($file = readdir($dh)) !== false) {
                if ($file != "." && $file != ".." && substr($file,0,6)=="backup") {
                    echo "<tr><td>$i</td><td><a href='$backup_dir$file' download>$file</a></td><td><a href='$backup_dir$file' class='btn btn-sm btn-info rounded-pill'>Baixar</a></td><td><form method='post'><input name='arquivo' type='hidden' value='$backup_dir$file'><button type='submit' name='excluir' class='btn btn-sm btn-danger rounded-pill'>Excluir</button></form></for></td></tr>";
                }
				$i++;
            }
            echo "</tbody></table>";
            closedir($dh);
        } else {
            echo "Não foi possível abrir o diretório de backups.<br>";
        }
    } else {
        echo "O diretório de backups não existe.<br>";
    }
}
include('./include/head-table.php');
?>

<body>
	<div  class="container">
		<?php 
		include_once('./include/nav.php');
		if ($msg<>""){
			echo $msg;
		}
		?>
		<nav aria-label="breadcrumb">
		  <ol class="breadcrumb">
			  <li class="breadcrumb-item"><a href="/painel">Painel</a></li>
			<li class="breadcrumb-item active" aria-current="page">Backup</li>
			<li class="breadcrumb-item"><a href="/perfil">Perfil</a></li>
		  </ol>
		</nav>
		<div class="card">
    <form method="post">
        <button type="submit" name="backup" class="btn btn-primary rounded-pill">Novo Backup</button>
    </form>
		</div>

    <hr>

    <?php
    // Exibe a lista de backups após gerar o backup
    listarBackups($backup_dir);
    ?>
	</div>
</body>
<?php 
include('./include/footer-table.php');
include('./include/scripts.php');
} else {
// Usuário não tem permissão, redirecione ou exiba uma mensagem de erro
//	include_once('include/conexao.php');
//    echo "Você não tem permissão para acessar esta página.<a href='/login'>Login</a>";
// 	include_once('include/head.php');
	include_once('pages/restrito.php');
	include_once('include/scripts.php');
}
include('./include/end.php');
?>
