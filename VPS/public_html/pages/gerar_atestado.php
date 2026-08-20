<?php
// Inicia o buffer de saída para evitar que o roteador index.php envie HTML antes do PDF
ob_start();

// include_once('./include/conexao.php');
include_once('./include/funcoes.php');
include_once('./include/funcoes-fpdf.php');

setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');

if (!isset($_SESSION['nivel']) || $_SESSION['nivel'] < 3) {
    ob_end_clean();
    echo "Acesso restrito.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    echo "Requisição inválida.";
    exit;
}

// Função para substituir o strftime de forma compatível com PHP 8.1+
function data_extenso_pt() {
    $meses = array(
        '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março',
        '04' => 'Abril', '05' => 'Maio', '06' => 'Junho',
        '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro',
        '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
    );
    return date('d') . " de " . $meses[date('m')] . " de " . date('Y');
}

$candidato_id = intval($_POST['candidato_id']);
$evento_id = intval($_POST['evento_id']);
$tipo = $_POST['tipo']; 

// Busca dados do candidato
$sql_cand = "SELECT * FROM candidatos WHERE candidato_id = $candidato_id";
$res_cand = mysqli_query($conexao, $sql_cand);
$candidato = mysqli_fetch_assoc($res_cand);

// Busca dados do evento
$sql_ev = "SELECT * FROM eventos_marcados WHERE id = $evento_id";
$res_ev = mysqli_query($conexao, $sql_ev);
$evento = mysqli_fetch_assoc($res_ev);

if (!$candidato || !$evento) {
    ob_end_clean();
    echo "Dados não encontrados.";
    exit;
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->SetMargins(20, 20, 20);

$hoje_extenso = data_extenso_pt();

if ($tipo === 'matricula') {
    $title = "CONFIRMAÇÃO DE MATRÍCULA";
    $descritivo = "Confirmação de Matrícula - " . $evento['nome'];
    $arquivo_nome = "matricula_" . $candidato_id . "_" . $evento_id . "_" . time() . ".pdf";
    
    $texto = "Declaramos para os devidos fins que o(a) aluno(a) <b>" . $candidato['nome'] . "</b>, inscrito(a) sob o CPF nº <b>" . $candidato['CPF'] . "</b>, encontra-se devidamente matriculado(a) e frequenta regularmente as atividades de: <b>" . $evento['nome'] . "</b>, realizadas no período de " . date('d/m/Y', strtotime($evento['inicio'])) . " a " . date('d/m/Y', strtotime($evento['final'])) . ".
    <br><br>
    Por ser verdade, firmamos a presente.";
} else {
    $title = "ATESTADO DE PARTICIPAÇÃO";
    $descritivo = "Atestado de Participação - " . $evento['nome'];
    $arquivo_nome = "participacao_" . $candidato_id . "_" . $evento_id . "_" . time() . ".pdf";
    
    $sql_hor = "SELECT h.* FROM horarios h 
                JOIN disponibilidade d ON h.horario_id = d.atividade_id 
                WHERE d.candidato_id = $candidato_id AND h.evento_id = $evento_id AND d.escalado = 1
                ORDER BY h.data_inicio ASC";
    $res_hor = mysqli_query($conexao, $sql_hor);
    
    $participacoes = "";
    $total_horas = 0;
    while ($h = mysqli_fetch_assoc($res_hor)) {
        $data = date('d/m/Y', strtotime($h['data_inicio']));
        $hora_ini = date('H:i', strtotime($h['data_inicio']));
        $hora_fim = date('H:i', strtotime($h['data_final']));
        $participacoes .= "<li>Dia $data, das $hora_ini às $hora_fim</li>";
        
        $segundos = strtotime($h['data_final']) - strtotime($h['data_inicio']);
        $total_horas += ($segundos / 3600);
    }
    
    if ($participacoes === "") {
        $texto = "Não constam registros de participação efetiva (escalonamento) para o(a) atendente <b>" . $candidato['nome'] . "</b> no evento <b>" . $evento['nome'] . "</b> até a presente data.";
    } else {
        $texto = "Atestamos para os devidos fins que <b>" . $candidato['nome'] . "</b>, portador(a) do CPF nº <b>" . $candidato['CPF'] . "</b>, participou das atividades do projeto <b>" . $evento['nome'] . "</b>, conforme cronograma abaixo:
        <br><ul>" . $participacoes . "</ul>
        <br>Perfocendo uma carga horária total de aproximadamente <b>" . round($total_horas, 1) . " horas</b>.
        <br><br>
        O presente atestado é emitido para fins de justificativa de ausência em outras atividades no referido período.
        <br><br>
        Por ser verdade, firmamos a presente.";
    }
}

// Converte a variável global $title para ISO-8859-1 para o Header()
$title_utf8 = $title;
$title = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $title);

$pdf->SetTitle($title);
$pdf->PrintChapter(1, $title_utf8, $texto);

$pdf->Ln(20);
$cidade_data = iconv("UTF-8", "ISO-8859-1//TRANSLIT", "São Paulo, " . $hoje_extenso);
$pdf->Cell(0, 10, $cidade_data, 0, 1, 'R');

$pdf->Ln(20);
if (file_exists('img/assinatura.png')) {
    $pdf->Image('img/assinatura.png', 85, $pdf->GetY(), 40);
}
$pdf->Ln(15);
$pdf->Cell(0, 0, '', 'T'); 
$pdf->Ln(2);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, iconv("UTF-8", "ISO-8859-1//TRANSLIT", "Paulo Addair Daniel Filho - Presidente"), 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, iconv("UTF-8", "ISO-8859-1//TRANSLIT", "PROJETO AME - Atendentes Muito Especiais"), 0, 1, 'C');

$diretorio = "docs/atestados/";
if (!is_dir($diretorio)) {
    mkdir($diretorio, 0777, true);
}
$caminho_completo = $diretorio . $arquivo_nome;
$pdf->Output("F", $caminho_completo);

$url_db = "docs/atestados/" . $arquivo_nome;
$sql_doc = "INSERT INTO documentos (candidato_id, url, descritivo) VALUES ($candidato_id, '$url_db', '$descritivo')";
mysqli_query($conexao, $sql_doc);

// Limpa o buffer de saída do PHP para garantir que NADA do index.php interfira no binário do PDF
ob_end_clean();

$pdf->Output("I", $arquivo_nome);
exit; // Interrompe o script para que o index.php não imprima o rodapé
?>
