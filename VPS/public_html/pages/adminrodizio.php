<?php
// include_once('./include/conexao.php');
// include_once('./include/funcoes.php');
// include_once('./include/head.php');
require 'vendor/autoload.php'; // Carrega o autoload do Composer para usar o PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$foto = "/img/ms-icon-310x310.png";
$perfil = "";
if (isset($_SESSION['usuario'])){
		$nome = $_SESSION['usuario'];
		$perfil = $_SESSION['perfil'];
		$logado = "visible";
		$login = "none";
	}

// Verifica a conexão
if ($conexao->connect_error) {
    die("Falha na conexão: " . $mysqli->connect_error);
}
$msg="";
$c_id=0;
$a_id=0;
if($_SERVER['REQUEST_METHOD']=="GET"){
	if (isset($_GET['c'])){
		$c_id = $_GET['c'];
	}
	if (isset($_GET['a'])){
		$a_id = $_GET['a'];
	}
	$query = "SELECT candidatos.nome,eventos_marcados.nome AS evento,horarios.data_inicio,horarios.data_final
				FROM candidatos,horarios
				LEFT JOIN eventos_marcados ON horarios.evento_id = eventos_marcados.id
				LEFT JOIN imagens ON eventos_marcados.imagem_id = imagens.imagem_id
				WHERE candidatos.candidato_id =".$c_id." AND horarios.horario_id =".$a_id;
	$resp = mysqli_query($conexao,$query);
	if (mysqli_num_rows($resp)>0){
		$query = "UPDATE `disponibilidade` SET escalado = NOT escalado WHERE candidato_id =".$c_id." AND atividade_id =".$a_id;
		echo $query."<br>";
		$up = mysqli_query($conexao,$query);
		$row = mysqli_fetch_array($resp);
		$msg = $row['nome']." - ".$row['evento']. " - das ".$row['data_inicio']. "às ".$row['data_final'];
	}
	sleep(1);
}

// Consulta para obter as colunas dinâmicas (eventos futuros)
$sqlColumns = "
    SELECT GROUP_CONCAT(DISTINCT
        CONCAT(
            'MAX(CASE WHEN horarios.horario_id = ', horarios.horario_id, 
            ' THEN \"X\" ELSE \"\" END) AS `', horarios.horario_id,';',
            eventos_marcados.nome, ' (', 
            DATE_FORMAT(horarios.data_inicio, '%d-%m-%Y'), ')` ')
        ORDER BY horarios.data_inicio ASC
    ) AS dynamic_columns
    FROM horarios
    LEFT JOIN eventos_marcados ON horarios.evento_id = eventos_marcados.id
    LEFT JOIN imagens ON eventos_marcados.imagem_id = imagens.imagem_id
    WHERE horarios.data_inicio >= CURDATE()
";

$resultColumns = $conexao->query($sqlColumns);
if (!$resultColumns) {
    die("Erro ao buscar colunas dinâmicas: " . $conexao->error);
}

$row = $resultColumns->fetch_assoc();
$dynamicColumns = $row['dynamic_columns'];

$dynamicColumnsPart = "";
if (!empty($dynamicColumns)) {
    $dynamicColumnsPart = ", " . $dynamicColumns;
}

// Consulta principal para montar a tabela
$sqlMain = "
    SELECT candidatos.candidato_id, candidatos.nome, candidatos.ativo" . $dynamicColumnsPart . "
    FROM candidatos
    LEFT JOIN disponibilidade ON candidatos.candidato_id = disponibilidade.candidato_id
    LEFT JOIN horarios ON disponibilidade.atividade_id = horarios.horario_id
    LEFT JOIN eventos_marcados ON horarios.evento_id = eventos_marcados.id
    LEFT JOIN imagens ON eventos_marcados.imagem_id = imagens.imagem_id
    WHERE candidatos.ativo>-1 
    GROUP BY candidatos.candidato_id, candidatos.nome
    ORDER BY candidatos.rodizio
";
/*
echo $sqlMain."<br>";
exit;
*/

$resultMain = $conexao->query($sqlMain);
if (!$resultMain) {
    die("Erro ao buscar dados: " . $conexao->error);
}

	$nplanilha = 1;
    // Cria uma nova planilha
    $spreadsheet = new Spreadsheet();
	$spreadsheet->getProperties()->setCreator("Projeto AME");
	$spreadsheet->getProperties()->setLastModifiedBy("Sistema de Relatório");
	$spreadsheet->getProperties()->setTitle("Relatório de disponibilidades");
	$spreadsheet->getProperties()->setSubject("Atendentes Muito Especiais");
	$relatorio = "Relatório de evento gerado automaticamente em ".Date("d/m/Y \à\s H:i");
	$spreadsheet->getProperties()->setDescription($relatorio);
	$spreadsheet->getProperties()->setKeywords("AME excel relatório planilha");
	$spreadsheet->getProperties()->setCategory("Arquivo de relatório");	
    $sheet = $spreadsheet->getActiveSheet();
//	$worksheet1 = $spreadsheet->createSheet();
	$sheet->setTitle('Relatório');

    // Título da planilha
    $titulo = "Disponibilidade para eventos";
    $sheet->setCellValue('A1', $titulo);

    // Define o número de colunas com base no resultado da consulta
    $colCount = $resultMain->field_count;
    $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);
	$lastColumn++;
    
    // Mescla as células para o título
    $sheet->mergeCells("A1:$lastColumn" . '1');
    
    // Formatação do título
    $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(16);
    $sheet->getStyle("A1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("A1")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    
    // Mescla as células para o sub-titulo
    $sheet->mergeCells("A2:$lastColumn" . '2');
    
    // Formatação do título
    $sheet->getStyle("A2")->getFont()->setBold(true)->setSize(12);
    $sheet->getStyle("A2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("A2")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
	$subtitulo = "Relatório emitido em ".Date("d/m/Y \à\s H:i");
	$sheet->setCellValue("A2", $subtitulo);
	// Cabeçalho das colunas
    $columnIndex = 1;
	$d = "";
?>
<style>
	.bg-rodizio{
		background-color:antiquewhite;
	}
	.bg-treinamento{
		background-color:aquamarine;
	}
</style>
<?php
include_once('./include/nav.php');
include_once('./include/admin_sidebar.php');
?>
<div class="container mt-4">
    <header>
        <h1 class="text-center">Fila & Rodízio</h1>
        <p class="text-center"><?php echo $msg; ?></p>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">Painel</a></li>
                <li class="breadcrumb-item active">Fila & Rodízio</li>
            </ol>
        </nav>
    </header>
<?php 
// Montagem da tabela em HTML
echo "<table border='1' cellspacing='0' cellpadding='5'>";
echo "<thead class='bg-light'>";

// Adiciona cabeçalhos das colunas (nome dos candidatos + colunas dinâmicas)
echo "<th>ID</th><th>Nome</th>";
$temp[]="";
$evento[] = "";
foreach ($resultMain->fetch_fields() as $field) {
	if ($field->name<>"nome" && $field->name<>"candidato_id"&& $field->name<>"ativo"){
		$temp = explode(";",$field->name);
		$query = "SELECT imagens.url 
FROM imagens
LEFT JOIN eventos_marcados ON eventos_marcados.imagem_id = imagens.imagem_id
LEFT JOIN horarios ON eventos_marcados.id = horarios.evento_id
WHERE horarios.horario_id = ".$temp[0].";";
		$img = mysqli_query($conexao,$query);
		$url = mysqli_fetch_array($img);
		$evento[$columnIndex+2]=$temp[0];
		$cel = $d.chr($columnIndex+66)."4";
        $sheet->setCellValue($cel, ucfirst($temp[1]));
  //      $sheet->setCellValueByColumnAndRow($columnIndex, 2, $fieldInfo->name);
        $columnIndex++;
		if ($columnIndex>26){
        	$columnIndex=1;
			$d = "A";
		}
    	echo "<th><img src='/".$url['url']."' class='img-fluid'><br><h1 class='text-center'>{$temp[1]}</h1></th>";		
	}
}
/*
echo "<pre>";
print_r($evento);
echo "</pre>";
exit;		
*/
$sheet->setCellValue("A4", "#");
$sheet->setCellValue("B4", "Nome");
echo "</tr></thead><tbody>";

    // Estilo do cabeçalho
    $sheet->getStyle("A4:$lastColumn" . '4')->getFont()->setBold(true);
    $sheet->getStyle("A4:$lastColumn" . '4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCCCCC');
    $sheet->getStyle("A4:$lastColumn" . '4')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    
    // Preenchendo os dados
    $rowNumber = 5;
	$f = 0;
	$d = "";

$i = 1;
// Adiciona os dados da tabela
while ($row = $resultMain->fetch_assoc()) {
	$bg = "bg-treinamento";
	if ($row['ativo']=='1'){
		$bg = "bg-rodizio";
	}
	$columnIndex = 1;
    echo "<tr class='$bg'><td>$i</td>";
	$sheet->setCellValue("A".$rowNumber, $i);
	$j=1;
    foreach ($row as $value) {
		if ($columnIndex>1){
			if ($j<>3){
				$cel = chr($columnIndex+65).$rowNumber;
				if ($value =='' or $columnIndex==2){
					echo "<td align='center'>$value</td>";			
				} else {
					if(array_key_exists($columnIndex,$evento)){
					echo "<td align='center'>".atividade($conexao,$row['candidato_id'],$evento[$columnIndex])."</td>";			
						
					}
				}
				$sheet->setCellValue($cel, $value);							
				
			}
		if ($columnIndex>2){
			$spreadsheet->getActiveSheet()->getStyle($cel)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			}
		}
		$j++;
		$columnIndex++;
		if ($columnIndex>26){
			$columnIndex=1;
			$d = "A";
		}
    }
	$rowNumber++;
    echo "</tr>";
	$i++;
}
    // Ajusta a largura das colunas automaticamente
    for ($col = 1; $col <= $colCount; $col++) {
        $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
    }
/*
    for ($col = 3; $col <= $colCount; $col++) {
        $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
    }
*/

echo "</tbody></table>";
	// Nome do arquivo Excel a ser salvo
		$fileName = slugify($titulo).Date("YmdHi").'.xlsx';
		$writer = new Xlsx($spreadsheet);

	// Salva a planilha no servidor
		$writer->save("./docs/".$fileName);
	
?>
			<div class="row">
				<a href="/docs/<?php echo $fileName?>" class="btn btn-sm btn-primary rounded-pill">Baixar relatório</a></div>
			</div>
<?php 
	// Nome do arquivo Excel a ser salvo
	$fileName = slugify($titulo).Date("YmdHi").'.xlsx';
	$writer = new Xlsx($spreadsheet);

// Salva a planilha no servidor
	$writer->save("./docs/".$fileName);
	$msg = "Arquivo <strong><em>$fileName</em></strong> salvo com sucesso!";

$conexao->close();
include_once('./include/scripts.php');
include_once('./include/admin_sidebar_footer.php');
?>


