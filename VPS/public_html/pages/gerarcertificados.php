<?php
// pages/gerarcertificados.php - Gerador de Certificados em PDF (Layout AlphaGraphics)
if (!isset($conexao)) {
    require_once __DIR__ . '/../database/conexao.php';
    require_once __DIR__ . '/../include/funcoes.php';
}
require_once __DIR__ . '/../fpdf/fpdf.php';

$db = isset($conexao) ? $conexao : (isset($conn) ? $conn : null);

// Segurança básica de acesso admin
if (!isset($_SESSION['usuario_id']) && !isset($_GET['token_auth'])) {
    die("Acesso não autorizado.");
}

$evento_id = isset($_GET['evento_id']) ? (int)$_GET['evento_id'] : 0;
if (!$evento_id) {
    die("Evento não especificado.");
}

// Busca evento
$query_ev = "SELECT e.*, i.url as imagem_url FROM eventos_marcados e LEFT JOIN imagens i ON e.imagem_id = i.imagem_id WHERE e.id = '$evento_id'";
$res_ev = mysqli_query($db, $query_ev);
$evento = $res_ev ? mysqli_fetch_assoc($res_ev) : null;

if (!$evento) {
    die("Evento inválido.");
}

// Busca atedentes escalados
$query_esc = "SELECT DISTINCT c.nome as atendente_nome 
              FROM disponibilidade d 
              JOIN candidatos c ON d.candidato_id = c.candidato_id 
              JOIN horarios h ON d.atividade_id = h.horario_id 
              WHERE h.evento_id = '$evento_id' AND d.escalado = 1 
              ORDER BY c.nome ASC";
$res_esc = mysqli_query($db, $query_esc);

$atendentes = [];
if ($res_esc) {
    while ($r = mysqli_fetch_assoc($res_esc)) {
        $atendentes[] = $r['atendente_nome'];
    }
}

if (count($atendentes) === 0) {
    die("Nenhum atendente escalado neste evento.");
}

// Classe FPDF para Certificado Horizontal (L - Landscape)
class PDF_Certificado extends FPDF {
    function Header() {
        // Moldura externa elegante
        $this->SetLineWidth(1.5);
        $this->SetDrawColor(120, 80, 40); // Marrom metálico
        $this->Rect(10, 10, 277, 190);
        $this->SetLineWidth(0.5);
        $this->Rect(13, 13, 271, 184);
    }
}

$pdf = new PDF_Certificado('L', 'mm', 'A4');
$pdf->SetAutoPageBreak(false);

$data_inicio_fmt = date('d/m/Y', strtotime($evento['inicio']));
$data_final_fmt = date('d/m/Y', strtotime($evento['final']));
$datas_str = ($data_inicio_fmt === $data_final_fmt) ? "no dia $data_inicio_fmt" : "entre os dias $data_inicio_fmt e $data_final_fmt";
$local_str = utf8_decode($evento['local']);
$nome_evento_str = utf8_decode(strtoupper($evento['nome']));

foreach ($atendentes as $atendente_nome) {
    $pdf->AddPage();
    
    // Cabeçalho institucional AME
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->SetTextColor(80, 50, 20);
    $pdf->SetXY(30, 25);
    $pdf->Cell(237, 8, utf8_decode('ATENDENTES MUITO ESPECIAIS – A.M.E.'), 0, 1, 'C');
    
    $pdf->SetFont('Arial', 'I', 13);
    $pdf->SetTextColor(100, 70, 40);
    $pdf->Cell(0, 6, utf8_decode('Associação Brasileira de Inclusão Através do Trabalho'), 0, 1, 'C');
    
    $pdf->Ln(8);

    // Título do Certificado
    $pdf->SetFont('Arial', '', 28);
    $pdf->SetTextColor(60, 40, 20);
    $pdf->Cell(0, 12, utf8_decode('Certificado de participação'), 0, 1, 'C');
    
    $pdf->SetFont('Arial', '', 12);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->Cell(0, 6, utf8_decode('é aqui concedido a'), 0, 1, 'C');
    
    $pdf->Ln(6);

    // NOME DO ATENDENTE (Destaque Principal)
    $pdf->SetFont('Arial', 'B', 24);
    $pdf->SetTextColor(30, 30, 30);
    $pdf->Cell(0, 12, utf8_decode(mb_strtoupper($atendente_nome, 'UTF-8')), 0, 1, 'C');
    
    $pdf->SetFont('Arial', '', 11);
    $pdf->SetTextColor(90, 90, 90);
    $pdf->Cell(0, 6, utf8_decode('por seu destacado desempenho e contribuição no evento'), 0, 1, 'C');
    
    $pdf->Ln(4);

    // Nome do Evento
    $pdf->SetFont('Arial', 'B', 18);
    $pdf->SetTextColor(180, 60, 20); // Destaque na cor do evento
    $pdf->Cell(0, 10, $nome_evento_str, 0, 1, 'C');
    
    // Texto do Período e Local
    $pdf->SetFont('Arial', '', 11);
    $pdf->SetTextColor(70, 70, 70);
    $pdf->Cell(0, 6, utf8_decode("Evento ocorrido $datas_str no $local_str"), 0, 1, 'C');
    
    $pdf->Ln(12);

    // Linha de Assinatura
    $pdf->SetDrawColor(150, 150, 150);
    $pdf->Line(100, 160, 197, 160);
    $pdf->SetY(162);
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->Cell(0, 5, utf8_decode('Projeto Atendentes Muito Especiais'), 0, 1, 'C');
}

// Saída do PDF para o Navegador / Download
$nome_arquivo = 'Certificados_AlphaGraphics_' . preg_replace('/[^A-Za-z0-9]/', '_', $evento['nome']) . '.pdf';
$pdf->Output('I', $nome_arquivo);
