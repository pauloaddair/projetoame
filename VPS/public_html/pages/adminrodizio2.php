<?php
include_once('./include/conexao.php');
include_once('./include/funcoes.php');
include_once('./include/head.php');
require 'vendor/autoload.php'; // Carrega o autoload do Composer para usar o PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Verifica a conexão
if ($conexao->connect_error) {
    die("Falha na conexão: " . $mysqli->connect_error);
}

// Consulta para obter as colunas dinâmicas (eventos futuros)
$sqlColumns = "
    SELECT GROUP_CONCAT(DISTINCT
        CONCAT(
            'MAX(CASE WHEN horarios.horario_id = ', horarios.horario_id, 
            ' THEN \"X\" ELSE \"\" END) AS `',
            eventos_marcados.nome, ' (', 
            DATE_FORMAT(horarios.data_inicio, '%Y-%m-%d'), ')` ')
        ORDER BY horarios.data_inicio ASC
    ) AS dynamic_columns
    FROM horarios
    LEFT JOIN eventos_marcados ON horarios.evento_id = eventos_marcados.id
    LEFT JOIN imagens ON eventos_marcados.imagem_id = imagens.imagem_id
    WHERE horarios.data_inicio >= CURDATE()
";

$resultColumns = $conexao->query($sqlColumns);
if (!$resultColumns) {
    die("Erro ao buscar colunas dinâmicas: " . $mysqli->error);
}

$row = $resultColumns->fetch_assoc();
$dynamicColumns = $row['dynamic_columns'];

// Consulta principal para montar a tabela
$sqlMain = "
    SELECT candidatos.nome, $dynamicColumns
    FROM candidatos
    LEFT JOIN disponibilidade ON candidatos.candidato_id = disponibilidade.candidato_id
    LEFT JOIN horarios ON disponibilidade.atividade_id = horarios.horario_id
    LEFT JOIN eventos_marcados ON horarios.evento_id = eventos_marcados.id
    LEFT JOIN imagens ON eventos_marcados.imagem_id = imagens.imagem_id
    WHERE candidatos.ativo>-1 AND (horarios.data_inicio >= CURDATE() OR horarios.horario_id IS NULL)
    GROUP BY candidatos.candidato_id, candidatos.nome
    ORDER BY candidatos.rodizio
";
//echo $sqlMain."<br>";
//exit;

$resultMain = $conexao->query($sqlMain);
if (!$resultMain) {
    die("Erro ao buscar dados: " . $mysqli->error);
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
    $titulo = "Título da Planilha";
    $sheet->setCellValue('A1', $titulo);

    // Define o número de colunas com base no resultado da consulta
    $colCount = $resultMain->field_count;
    $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);
    
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
// Montagem da tabela em HTML
echo "<body><div class='container'><header><h1 class='text-center'>Disponibilidade</h1></header><table border='1' cellspacing='0' cellpadding='5'>";
echo "<thead><tr>";

// Adiciona cabeçalhos das colunas (nome dos candidatos + colunas dinâmicas)
echo "<th>ID</th><th>Nome</th>";
foreach ($resultMain->fetch_fields() as $field) {
	if ($field->name<>"nome" && $field->name<>"candidato_id"){
		$cel = $d.chr($columnIndex+64)."4";
        $sheet->setCellValue($cel, ucfirst($field->name));
  //      $sheet->setCellValueByColumnAndRow($columnIndex, 2, $fieldInfo->name);
        $columnIndex++;
		if ($columnIndex>26){
        	$columnIndex=1;
			$d = "A";
		}
    	echo "<th>{$field->name}</th>";		
	}
}
$sheet->setCellValue("A4", "#");
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
	$columnIndex = 1;
    echo "<tr><td>$i</td>";
	$sheet->setCellValue("A".$rowNumber, $i);				
    foreach ($row as $value) {
		$cel = chr($columnIndex+64).$rowNumber;
        echo "<td align='center'>$value</td>";
		$sheet->setCellValue($cel, $value);				
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

echo "</tbody></table>";
	// Nome do arquivo Excel a ser salvo
		$fileName = slugify($titulo).Date("YmdHi").'.xlsx';
		$writer = new Xlsx($spreadsheet);

	// Salva a planilha no servidor
		$writer->save("./docs/".$fileName);
	
?>
			<div class="row">
				<a href="/docs/<? echo $fileName?>" class="btn btn-sm btn-primary rounded-pill">Baixar relatório</a></div>
			</div>
<?
	// Nome do arquivo Excel a ser salvo
	$fileName = slugify($titulo).Date("YmdHi").'.xlsx';
	$writer = new Xlsx($spreadsheet);

// Salva a planilha no servidor
	$writer->save("./docs/".$fileName);
	$msg = "Arquivo <strong><em>$fileName</em></strong> salvo com sucesso!";

// Fecha a conexão com o banco de dados
$conexao->close();
include_once('./include/footer.php');
echo "</body>";
include_once('./include/scripts.php');
include_once('./include/end.php');
?>


