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
    $linha_corte = !empty($evento['linha_corte_presenca']) ? intval($evento['linha_corte_presenca']) : 75;
    $is_curso = (in_array(strtolower($evento['tipo'] ?? ''), ['curso', 'treinamento']) || in_array(strtolower($evento['tipo_evento'] ?? ''), ['curso', 'treinamento']));

    // Total de aulas programadas/escaladas
    $sql_tot = "SELECT COUNT(*) as total FROM horarios h 
                JOIN disponibilidade d ON h.horario_id = d.atividade_id 
                WHERE d.candidato_id = $candidato_id AND h.evento_id = $evento_id AND d.escalado = 1";
    $res_tot = mysqli_query($conexao, $sql_tot);
    $tot_aulas = ($res_tot && $row_tot = mysqli_fetch_assoc($res_tot)) ? intval($row_tot['total']) : 0;

    // Presenças confirmadas
    $sql_pres = "SELECT h.* 
                 FROM presenca p
                 JOIN horarios h ON p.horario_id = h.horario_id
                 WHERE p.candidato_id = $candidato_id AND p.evento_id = $evento_id AND p.presente = 1
                 ORDER BY h.data_inicio ASC";
    $res_pres = mysqli_query($conexao, $sql_pres);
    $presencas_rows = [];
    if ($res_pres) {
        while ($r = mysqli_fetch_assoc($res_pres)) {
            $presencas_rows[] = $r;
        }
    }
    $tot_presencas = count($presencas_rows);

    // Fallback para eventos em modelo legado (onde presenca não gravava horario_id)
    if ($tot_presencas === 0 && $tot_aulas > 0) {
        $sql_leg = "SELECT presente FROM presenca WHERE candidato_id = $candidato_id AND evento_id = $evento_id AND (horario_id IS NULL OR horario_id = 0) AND presente = 1 LIMIT 1";
        $res_leg = mysqli_query($conexao, $sql_leg);
        if ($res_leg && mysqli_num_rows($res_leg) > 0) {
            $res_all_hor = mysqli_query($conexao, "SELECT h.* FROM horarios h JOIN disponibilidade d ON h.horario_id = d.atividade_id WHERE d.candidato_id = $candidato_id AND h.evento_id = $evento_id AND d.escalado = 1 ORDER BY h.data_inicio ASC");
            while ($rh = mysqli_fetch_assoc($res_all_hor)) {
                $presencas_rows[] = $rh;
            }
            $tot_presencas = count($presencas_rows);
        }
    }

    $frequencia_pct = ($tot_aulas > 0) ? round(($tot_presencas / $tot_aulas) * 100) : 0;
    $apto_pleno = ($frequencia_pct >= $linha_corte && $tot_presencas > 0);

    $participacoes = "";
    $total_horas = 0;
    foreach ($presencas_rows as $h) {
        $data = date('d/m/Y', strtotime($h['data_inicio']));
        $hora_ini = date('H:i', strtotime($h['data_inicio']));
        $hora_fim = date('H:i', strtotime($h['data_final']));
        $participacoes .= "<li>Dia $data, das $hora_ini às $hora_fim</li>";
        
        $segundos = strtotime($h['data_final']) - strtotime($h['data_inicio']);
        $total_horas += ($segundos / 3600);
    }
    $total_horas_fmt = number_format($total_horas, 1, ',', '.');

    if ($tipo === 'parcial' || (!$apto_pleno && $tipo === 'participacao')) {
        // DECLARAÇÃO DE HORAS PARCIAIS
        $title = "DECLARAÇÃO DE HORAS PARCIAIS";
        $descritivo = "Declaração de Horas Parciais - " . $evento['nome'];
        $arquivo_nome = "parcial_" . $candidato_id . "_" . $evento_id . "_" . time() . ".pdf";

        if ($participacoes === "") {
            $texto = "Declaramos que o(a) participante <b>" . $candidato['nome'] . "</b>, CPF nº <b>" . $candidato['CPF'] . "</b>, esteve inscrito(a) na atividade <b>" . $evento['nome'] . "</b>, não constando presenças confirmadas no sistema até a presente data.";
        } else {
            $texto = "Declaramos para os devidos fins que o(a) participante <b>" . $candidato['nome'] . "</b>, portador(a) do CPF nº <b>" . $candidato['CPF'] . "</b>, participou de <b>{$tot_presencas}</b> de um total de <b>{$tot_aulas}</b> aulas/turnos programados na atividade <b>" . $evento['nome'] . "</b>, perfazendo uma frequência de <b>{$frequencia_pct}%</b>, conforme cronograma cumprido abaixo:
            <br><ul>" . $participacoes . "</ul>
            <br>Perfazendo uma carga horária efetivamente cumprida de <b>" . $total_horas_fmt . " horas</b>.
            <br><br>
            <b>Ressalva Institucional:</b> O presente documento atesta formalmente as horas e atividades efetivamente cursadas pelo(a) participante. Por não ter atingido a linha de corte mínima regulamentar de frequência exigida pela coordenação ({$linha_corte}%), esta declaração <b>não confere o Certificado de Conclusão Integral</b> da referida atividade, servindo como comprovação de horas parciais.
            <br><br>
            Por ser verdade, firmamos a presente.";
        }
    } else {
        // CERTIFICADO DE CONCLUSÃO / ATESTADO DE PARTICIPAÇÃO PLENA
        $title = $is_curso ? "CERTIFICADO DE CONCLUSÃO" : "ATESTADO DE PARTICIPAÇÃO";
        $descritivo = ($is_curso ? "Certificado de Conclusão - " : "Atestado de Participação - ") . $evento['nome'];
        $arquivo_nome = "certificado_" . $candidato_id . "_" . $evento_id . "_" . time() . ".pdf";

        if ($participacoes === "") {
            $texto = "Não constam registros de participação efetiva confirmada para o(a) atendente/aluno(a) <b>" . $candidato['nome'] . "</b> no evento <b>" . $evento['nome'] . "</b> até a presente data.";
        } else {
            $texto = "Certificamos e atestamos para os devidos fins que <b>" . $candidato['nome'] . "</b>, portador(a) do CPF nº <b>" . $candidato['CPF'] . "</b>, concluiu com êxito as atividades do projeto <b>" . $evento['nome'] . "</b>, realizadas no período de " . date('d/m/Y', strtotime($evento['inicio'])) . " a " . date('d/m/Y', strtotime($evento['final'])) . ", atingindo frequência de <b>{$frequencia_pct}%</b> (linha de corte regulamentar de {$linha_corte}%), com o seguinte cronograma cumprido:
            <br><ul>" . $participacoes . "</ul>
            <br>Perfazendo uma carga horária total de aproximadamente <b>" . $total_horas_fmt . " horas</b>.
            <br><br>
            O(A) participante demonstrou pleno comprometimento, pontualidade e aptidão nas práticas e conteúdos ministrados.
            <br><br>
            Por ser verdade, firmamos a presente.";
        }
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
