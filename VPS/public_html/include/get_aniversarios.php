<?php
include_once('conexao.php');
header('Content-Type: application/json');

// Verifica se o parâmetro 'mes' foi enviado e é válido
if (isset($_GET['mes']) && is_numeric($_GET['mes'])) {
    $mes = (int)$_GET['mes'];
    if ($mes < 1 || $mes > 12) {
        echo json_encode(['success' => false, 'message' => 'Mês inválido.('.$mes.")"]);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Parâmetro "mes" não fornecido ou inválido.']);
    exit;
}

// Define o charset para evitar problemas com caracteres especiais
$conexao->set_charset('utf8');

// Consulta SQL para obter os aniversariantes do mês
$sql = "SELECT c.nome, c.Nascimento, i.url
        FROM candidatos c
        JOIN imagens i ON c.imagem_id = i.imagem_id
        WHERE MONTH(c.Nascimento) = ?
		AND c.ativo = 1 
        ORDER BY DAY(c.Nascimento) ASC";
$labelmes = array (
'janeiro',
'fevereiro',
'março',
'abril',
'maio',
'junho',
'julho',
'agosto',
'setembro',
'outubro',
'novembro',
'dezembro');
if ($stmt = $conexao->prepare($sql)) {
    $stmt->bind_param('i', $mes);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $html = '';
        $active = 'active';
        while ($row = $resultado->fetch_assoc()) {
            $nome = htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8');
            $dia = date('d', strtotime($row['Nascimento']));
            $imagem = htmlspecialchars($row['url'], ENT_QUOTES, 'UTF-8');

            $html .= '<div class="carousel-item ' . $active . '">';
            $html .= '<img src="' . $imagem . '" alt="Foto de ' . $nome . '">';
            $html .= '<div class="carousel-caption">';
            $html .= '<h5>' . $nome . '</h5>';
            $html .= '<p>Dia: ' . $dia . '</p>';
            $html .= '</div></div>';

            $active = ''; // Apenas o primeiro item deve ser ativo
        }

        // Obtém o nome do mês por extenso em português
        setlocale(LC_TIME, 'pt_BR.UTF-8');
//        $nome_mes = strftime('%B', mktime(0, 0, 0, $mes, 1));
        $nome_mes = strftime('%B', mktime(0, 0, 0, $mes, 1));

        echo json_encode(['success' => true, 'html' => $html, 'monthName' => ucfirst($nome_mes)]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nenhum aniversariante encontrado para este mês.', 'monthName' => ucfirst($nome_mes)]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Erro na preparação da consulta: ' . $mysqli->error]);
}

$conexao->close();
?>
