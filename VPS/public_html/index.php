<?php
// index.php - Front Controller do Projeto AME
session_start();

$base_path = __DIR__ . '/';
require_once $base_path . 'database/conexao.php';
require_once $base_path . 'include/funcoes.php';

$app_web_root = '/';
$GLOBALS['app_web_root'] = $app_web_root;

// Sanitiza a URI recebida
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri_limpa = trim($request_uri, '/');

$parametros = explode('/', $uri_limpa);

// Requisições diretas a arquivos da pasta /include/ (APIs, AJAX)
if ($parametros[0] === 'include' && !empty($parametros[1])) {
    $inc_file = $base_path . 'include/' . $parametros[1];
    if (file_exists($inc_file)) {
        require_once $inc_file;
        exit;
    }
}

// Páginas que dispensam o header/footer padrão
$paginas_sem_template = ['gerarcertificados', 'api_get_candidato', 'api_get_perfil', 'api_perfil_save', 'api_usuario_save', 'webhook_pagseguro'];

// Pré-carregamento de Meta Tags OpenGraph para rotas públicas específicas
if ($parametros[0] === 'escala') {
    $ev_id = isset($_GET['evento_id']) ? (int)$_GET['evento_id'] : 0;
    if ($ev_id > 0) {
        $q_ev = "SELECT e.*, i.url as imagem_url FROM eventos_marcados e LEFT JOIN imagens i ON e.imagem_id = i.imagem_id WHERE e.id = '$ev_id' LIMIT 1";
        $r_ev = mysqli_query($conexao, $q_ev);
        if ($r_ev && $row_ev = mysqli_fetch_assoc($r_ev)) {
            $dt_i = date('d/m/Y', strtotime($row_ev['inicio']));
            $dt_f = date('d/m/Y', strtotime($row_ev['final']));
            $dt_ex = ($dt_i === $dt_f) ? $dt_i : "$dt_i a $dt_f";
            $subt = (!empty($row_ev['escala_fechada']) && $row_ev['escala_fechada'] == 1) ? "Escala Definitiva" : "Rodízio e Disponibilidade Declarada";
            $raw_img = !empty($row_ev['imagem_url']) ? $row_ev['imagem_url'] : 'img/ame2023.jpg';
            
            $titulo = $row_ev['nome'] . " — " . $subt;
            $og_title = $row_ev['nome'] . " (" . $subt . ")";
            $og_description = "📅 Período: {$dt_ex} | 📍 Local: " . $row_ev['local'] . ". Acompanhamento de Disponibilidade e Escala dos Atendentes Muito Especiais (Projeto AME).";
            $og_image = (strpos($raw_img, 'http') === 0) ? $raw_img : 'https://projetoame.org/' . ltrim($raw_img, '/');
            $og_url = "https://projetoame.org/escala?evento_id={$ev_id}";
        }
    }
}

if (in_array($parametros[0], $paginas_sem_template)) {
    if (file_exists($base_path . 'pages/' . $parametros[0] . '.php')) {
        include_once($base_path . 'pages/' . $parametros[0] . '.php');
    } else {
        include_once($base_path . 'pages/inexiste.php');
    }
} else {
    // Carrega o cabeçalho global (com OpenGraph pré-carregado se existente)
    include_once($base_path . 'include/html_head.php');

    $page_to_load = '';

    if ($parametros[0] === '' || $parametros[0] === 'index') {
        $page_to_load = $base_path . 'pages/base.php';
    } elseif ($parametros[0] === 'admin') {
        // Roteamento de Administração (/admin/escala, /admin/novoevento, etc)
        $sub_rota = isset($parametros[1]) ? $parametros[1] : 'index';
        switch ($sub_rota) {
            case 'index':
                $page_to_load = $base_path . 'pages/admin.php';
                break;
            case 'novoevento':
                $page_to_load = $base_path . 'pages/admin_novo_evento.php';
                break;
            case 'escala':
                $page_to_load = $base_path . 'pages/adminescala.php';
                break;
            case 'atividades':
                $page_to_load = $base_path . 'pages/admin_atividades.php';
                break;
            case 'novofolheto':
                $page_to_load = $base_path . 'pages/admin_novo_folheto.php';
                break;
            case 'prospeccao':
                $page_to_load = $base_path . 'pages/prospeccao.php';
                break;
            case 'atendentes':
                $page_to_load = $base_path . 'pages/atendentes.php';
                break;
            case 'candidatos':
                $page_to_load = $base_path . 'pages/candidatos.php';
                break;
            default:
                $page_to_load = $base_path . 'pages/inexiste.php';
                break;
        }
    } else {
        // Roteamento de Páginas Públicas & Portal do Responsável
        switch ($parametros[0]) {
            case 'painel':
            case 'meuperfil':
                $page_to_load = $base_path . 'pages/meuperfil.php';
                break;
            case 'atividades':
                $page_to_load = $base_path . 'pages/atividades.php';
                break;
            case 'atendentes':
                $page_to_load = $base_path . 'pages/atendentes.php';
                break;
            case 'disponibilidade':
                $page_to_load = $base_path . 'pages/disponibilidade.php';
                break;
            case 'escala':
                $page_to_load = $base_path . 'pages/escala.php';
                break;
            case 'nossaatuacao':
                $page_to_load = $base_path . 'pages/nossaatuacao.php';
                break;
            case 'transparencia':
                $page_to_load = $base_path . 'pages/transparencia.php';
                break;
            case 'inscrever':
                $page_to_load = $base_path . 'pages/inscrever.php';
                break;
            case 'curriculo':
                $page_to_load = $base_path . 'pages/curriculo.php';
                break;
            case 'login':
                $page_to_load = $base_path . 'pages/login.php';
                break;
            case 'logout':
                $page_to_load = $base_path . 'pages/logout.php';
                break;
            case 'trocafoto':
                $page_to_load = $base_path . 'pages/trocafoto.php';
                break;
            case 'trocafoto-usuario':
                $page_to_load = $base_path . 'pages/trocafoto-usuario.php';
                break;
            default:
                $page_to_load = $base_path . 'pages/inexiste.php';
                break;
        }
    }

    if (file_exists($page_to_load)) {
        include_once($page_to_load);
    } else {
        include_once($base_path . 'pages/inexiste.php');
    }
}
?>
