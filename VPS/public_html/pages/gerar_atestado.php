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

// Classe especializada para Atestados e Certificados com Timbrado Oficial e Assinatura Formal
class AtestadoPDF extends PDF
{
    public $documentTitle = '';

    function Header()
    {
        // Logotipo Oficial (22mm de largura)
        if (file_exists('img/ame2023.jpg')) {
            $this->Image('img/ame2023.jpg', 20, 13, 22);
        } elseif (file_exists('img/card-2x1nobg.png')) {
            $this->Image('img/card-2x1nobg.png', 20, 13, 28);
        }

        // Cabeçalho Institucional Oficial
        $this->SetXY(46, 13);
        $this->SetFont('Arial', 'B', 10.5);
        $this->SetTextColor(30, 41, 59);
        $this->Cell(0, 4.5, iconv("UTF-8", "ISO-8859-1//TRANSLIT", "ASSOCIAÇÃO BRASILEIRA DE INCLUSÃO PELO TRABALHO"), 0, 1, 'L');

        $this->SetX(46);
        $this->SetFont('Arial', 'B', 9.5);
        $this->SetTextColor(217, 119, 6); // Âmbar Projeto AME (#d97706)
        $this->Cell(0, 4.5, iconv("UTF-8", "ISO-8859-1//TRANSLIT", "ATENDENTES MUITO ESPECIAIS — PROJETO A.M.E."), 0, 1, 'L');

        $this->SetX(46);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(100, 116, 139);
        $this->Cell(0, 4, iconv("UTF-8", "ISO-8859-1//TRANSLIT", "CNPJ: 32.131.752/0001-88 | Entidade Civil Sem Fins Lucrativos"), 0, 1, 'L');

        $this->SetX(46);
        $this->SetFont('Arial', '', 7.5);
        $this->SetTextColor(100, 116, 139);
        $this->Cell(0, 3.5, iconv("UTF-8", "ISO-8859-1//TRANSLIT", "São Paulo - SP | contato@projetoame.org | https://projetoame.org"), 0, 1, 'L');

        // Linha divisória institucional dupla
        $this->SetY(40);
        $this->SetDrawColor(217, 119, 6);
        $this->SetLineWidth(0.8);
        $this->Line(20, 40, 190, 40);

        $this->SetDrawColor(30, 58, 138);
        $this->SetLineWidth(0.3);
        $this->Line(20, 41.2, 190, 41.2);

        $this->Ln(13);

        // Título do Documento com destaque
        if (!empty($this->documentTitle)) {
            $this->SetFont('Arial', 'B', 15);
            $this->SetTextColor(30, 41, 59);
            $this->Cell(0, 8, iconv("UTF-8", "ISO-8859-1//TRANSLIT", $this->documentTitle), 0, 1, 'C');
            $this->Ln(6);
        }
    }

    function Footer()
    {
        $this->SetY(-22);
        $this->SetDrawColor(203, 213, 225);
        $this->SetLineWidth(0.3);
        $this->Line(20, $this->GetY(), 190, $this->GetY());
        $this->Ln(2);

        $this->SetFont('Arial', '', 7.5);
        $this->SetTextColor(100, 116, 139);
        $txt_rodape = "Associação Brasileira de Inclusão pelo Trabalho - Atendentes Muito Especiais (AME) | CNPJ: 32.131.752/0001-88\nDocumento oficial emitido eletronicamente pelo Projeto AME — projetoame.org";
        $this->MultiCell(0, 3.5, iconv("UTF-8", "ISO-8859-1//TRANSLIT", $txt_rodape), 0, 'C');

        $this->SetY(-10);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(148, 163, 184);
        $this->Cell(0, 5, iconv("UTF-8", "ISO-8859-1//TRANSLIT", 'Página ' . $this->PageNo() . ' de {nb}'), 0, 0, 'R');
    }
}

$pdf = new AtestadoPDF();
$pdf->AliasNbPages();
$pdf->SetMargins(20, 20, 20);

$hoje_extenso = data_extenso_pt();

if ($tipo === 'matricula') {
    $title = "CONFIRMAÇÃO DE MATRÍCULA";
    $descritivo = "Confirmação de Matrícula - " . $evento['nome'];
    $arquivo_nome = "matricula_" . $candidato_id . "_" . $evento_id . "_" . time() . ".pdf";
    
    $texto = "A <b>ASSOCIAÇÃO BRASILEIRA DE INCLUSÃO PELO TRABALHO – ATENDENTES MUITO ESPECIAIS (AME)</b>, pessoa jurídica de direito privado sem fins lucrativos, inscrita no CNPJ/MF sob o nº <b>32.131.752/0001-88</b>, com sede na Cidade de São Paulo - SP, declara para os devidos fins e a quem possa interessar que o(a) aluno(a) / atendente <b>" . $candidato['nome'] . "</b>, inscrito(a) no CPF sob o nº <b>" . $candidato['CPF'] . "</b>, encontra-se devidamente matriculado(a) e com participação ativa nas atividades de capacitação e inclusão produtiva referentes ao projeto: <b>" . $evento['nome'] . "</b>, ministrado no período de " . date('d/m/Y', strtotime($evento['inicio'])) . " a " . date('d/m/Y', strtotime($evento['final'])) . ".
    <br><br>
    O programa tem por objetivo o desenvolvimento de competências socioemocionais, técnicas e comportamentais para o trabalho e a autonomia social, em conformidade com as diretrizes estatutárias da entidade.
    <br><br>
    Por ser a expressão da verdade, firmamos a presente declaração.";
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
            $texto = "A <b>ASSOCIAÇÃO BRASILEIRA DE INCLUSÃO PELO TRABALHO – ATENDENTES MUITO ESPECIAIS (AME)</b>, pessoa jurídica de direito privado sem fins lucrativos, inscrita no CNPJ sob o nº <b>32.131.752/0001-88</b>, com sede na Cidade de São Paulo - SP, declara que o(a) participante <b>" . $candidato['nome'] . "</b>, CPF nº <b>" . $candidato['CPF'] . "</b>, esteve inscrito(a) na atividade <b>" . $evento['nome'] . "</b>, não constando presenças confirmadas no sistema até a presente data.";
        } else {
            $texto = "A <b>ASSOCIAÇÃO BRASILEIRA DE INCLUSÃO PELO TRABALHO – ATENDENTES MUITO ESPECIAIS (AME)</b>, pessoa jurídica de direito privado sem fins lucrativos, inscrita no CNPJ sob o nº <b>32.131.752/0001-88</b>, com sede na Cidade de São Paulo - SP, declara para os devidos fins que o(a) participante <b>" . $candidato['nome'] . "</b>, portador(a) do CPF nº <b>" . $candidato['CPF'] . "</b>, participou de <b>{$tot_presencas}</b> de um total de <b>{$tot_aulas}</b> aulas/turnos programados na atividade <b>" . $evento['nome'] . "</b>, perfazendo uma frequência de <b>{$frequencia_pct}%</b>, conforme cronograma cumprido discriminado abaixo:
            <br><ul>" . $participacoes . "</ul>
            <br>Perfazendo uma carga horária efetivamente cumprida de <b>" . $total_horas_fmt . " horas</b>.
            <br><br>
            <b>Ressalva Institucional:</b> O presente documento atesta formalmente as horas e atividades efetivamente cursadas pelo(a) participante. Por não ter atingido a linha de corte mínima regulamentar de frequência exigida pela coordenação institucional ({$linha_corte}%), esta declaração <b>não confere o Certificado de Conclusão Integral</b> da referida atividade, servindo como comprovação legal de frequência e horas parciais.
            <br><br>
            Por ser a expressão da verdade, firmamos a presente declaração.";
        }
    } else {
        // CERTIFICADO DE CONCLUSÃO / ATESTADO DE PARTICIPAÇÃO PLENA
        $title = $is_curso ? "CERTIFICADO DE CONCLUSÃO" : "ATESTADO DE PARTICIPAÇÃO";
        $descritivo = ($is_curso ? "Certificado de Conclusão - " : "Atestado de Participação - ") . $evento['nome'];
        $arquivo_nome = "certificado_" . $candidato_id . "_" . $evento_id . "_" . time() . ".pdf";

        if ($participacoes === "") {
            $texto = "A <b>ASSOCIAÇÃO BRASILEIRA DE INCLUSÃO PELO TRABALHO – ATENDENTES MUITO ESPECIAIS (AME)</b>, inscrita no CNPJ sob o nº <b>32.131.752/0001-88</b>, informa que não constam registros de participação efetiva confirmada para o(a) atendente/aluno(a) <b>" . $candidato['nome'] . "</b> no evento <b>" . $evento['nome'] . "</b> até a presente data.";
        } else {
            $texto = "A <b>ASSOCIAÇÃO BRASILEIRA DE INCLUSÃO PELO TRABALHO – ATENDENTES MUITO ESPECIAIS (AME)</b>, pessoa jurídica de direito privado sem fins lucrativos, inscrita no CNPJ sob o nº <b>32.131.752/0001-88</b>, com sede na Cidade de São Paulo - SP, certifica e atesta para os devidos fins que <b>" . $candidato['nome'] . "</b>, portador(a) do CPF nº <b>" . $candidato['CPF'] . "</b>, concluiu com êxito todas as etapas e atividades do projeto <b>" . $evento['nome'] . "</b>, ministrado no período de " . date('d/m/Y', strtotime($evento['inicio'])) . " a " . date('d/m/Y', strtotime($evento['final'])) . ", atingindo frequência de <b>{$frequencia_pct}%</b> (linha de corte regulamentar de {$linha_corte}%), com o seguinte cronograma cumprido:
            <br><ul>" . $participacoes . "</ul>
            <br>Perfazendo uma carga horária total de <b>" . $total_horas_fmt . " horas</b>.
            <br><br>
            O(A) participante demonstrou pleno comprometimento, pontualidade, assiduidade e excelente desenvolvimento nas práticas e conteúdos ministrados.
            <br><br>
            Por ser a expressão da verdade, firmamos o presente documento.";
        }
    }
}

$title_utf8 = $title;
$pdf->SetTitle(iconv("UTF-8", "ISO-8859-1//TRANSLIT", $title));
$pdf->documentTitle = $title_utf8;

$pdf->AddPage();
$pdf->SetFont('Arial', '', 11.5);
$pdf->SetTextColor(30, 41, 59);
$pdf->WriteHTML(iconv("UTF-8", "ISO-8859-1//TRANSLIT", $texto));

$pdf->Ln(12);
$cidade_data = iconv("UTF-8", "ISO-8859-1//TRANSLIT", "São Paulo, " . $hoje_extenso . ".");
$pdf->Cell(0, 8, $cidade_data, 0, 1, 'R');

$pdf->Ln(6);
if ($pdf->GetY() > 215) {
    $pdf->AddPage();
}

$sig_y = $pdf->GetY();
if (file_exists('img/assinatura.png')) {
    $pdf->Image('img/assinatura.png', 80, $sig_y - 6, 50);
}

// Linha de Assinatura Centralizada (largura 120mm)
$pdf->SetDrawColor(100, 116, 139);
$pdf->SetLineWidth(0.4);
$pdf->Line(45, $sig_y + 14, 165, $sig_y + 14);

$pdf->SetY($sig_y + 16);
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetTextColor(30, 41, 59);
$pdf->Cell(0, 5, iconv("UTF-8", "ISO-8859-1//TRANSLIT", "Paulo Addair Daniel Filho"), 0, 1, 'C');

$pdf->SetFont('Arial', '', 9.5);
$pdf->SetTextColor(71, 85, 105);
$pdf->Cell(0, 4.5, iconv("UTF-8", "ISO-8859-1//TRANSLIT", "Presidente"), 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(30, 41, 59);
$pdf->Cell(0, 4.5, iconv("UTF-8", "ISO-8859-1//TRANSLIT", "Associação Brasileira de Inclusão pelo Trabalho - Atendentes Muito Especiais"), 0, 1, 'C');

$pdf->SetFont('Arial', '', 8.5);
$pdf->SetTextColor(100, 116, 139);
$pdf->Cell(0, 4, iconv("UTF-8", "ISO-8859-1//TRANSLIT", "CNPJ nº 32.131.752/0001-88 | Projeto AME"), 0, 1, 'C');

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
