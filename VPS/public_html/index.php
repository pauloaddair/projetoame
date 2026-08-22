<?php
// index.php - Front Controller do Projeto AME
session_start();

$base_path = __DIR__ . '/';
require_once $base_path . 'database/conexao.php';
require_once $base_path . 'include/funcoes.php';

// Sanitiza a URI recebida
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri_limpa = trim($request_uri, '/');
$parametros = explode('/', $uri_limpa);

// Suporte automático para execução em subpasta local (ex: http://localhost/projetoame/...)
if (!empty($parametros[0]) && strtolower($parametros[0]) === 'projetoame') {
    $app_web_root = '/projetoame/';
    array_shift($parametros);
    if (empty($parametros)) {
        $parametros = [''];
    }
} else {
    $app_web_root = '/';
}
$GLOBALS['app_web_root'] = $app_web_root;

// Redirecionamento da raiz/index antes de qualquer saída de cabeçalho (evita 'headers already sent')
if ($parametros[0] === '' || $parametros[0] === 'index') {
    header('Location: ' . $app_web_root . 'home/');
    exit;
}

// Requisições diretas a arquivos da pasta /include/ (APIs, AJAX)
if ($parametros[0] === 'include' && !empty($parametros[1])) {
    $inc_file = $base_path . 'include/' . $parametros[1];
    if (file_exists($inc_file)) {
        require_once $inc_file;
        exit;
    }
}

// Páginas que dispensam o header/footer padrão
$paginas_sem_template = ['gerarcertificados', 'api_get_candidato', 'api_get_perfil', 'api_perfil_save', 'api_usuario_save', 'webhook_pagseguro', 'gerar_atestado'];

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

$rota_limpa = preg_replace('/[^a-zA-Z0-9_\-]/', '', $parametros[0]);

if (in_array($rota_limpa, $paginas_sem_template) || ($rota_limpa === 'curriculo' && isset($_GET['gerar_pdf']))) {
    $target = $base_path . 'pages/' . $rota_limpa . '.php';
    if (file_exists($target)) {
        include_once($target);
    } else {
        include_once($base_path . 'pages/inexiste.php');
    }
} else {
    // Carrega o cabeçalho global (com OpenGraph pré-carregado se existente)
    include_once($base_path . 'include/html_head.php');

    $page_to_load = '';

    if ($rota_limpa === 'admin') {
        // Roteamento dinâmico de Administração (/admin, /admin/atividades, /admin/escala, etc.)
        $sub_rota = isset($parametros[1]) ? preg_replace('/[^a-zA-Z0-9_\-]/', '', $parametros[1]) : '';
        
        if ($sub_rota === '' || $sub_rota === 'index') {
            $page_to_load = $base_path . 'pages/adminindex.php';
        } else {
            $candidatos_admin = [
                $base_path . 'pages/admin' . $sub_rota . '.php',
                $base_path . 'pages/admin_' . $sub_rota . '.php',
                $base_path . 'pages/' . $sub_rota . '.php',
                $base_path . 'pages/admin' . str_replace('-', '_', $sub_rota) . '.php',
                $base_path . 'pages/admin_' . str_replace('-', '_', $sub_rota) . '.php'
            ];
            
            // Atalhos convenientes
            if ($sub_rota === 'novoevento' || $sub_rota === 'novaatividade') {
                array_unshift($candidatos_admin, $base_path . 'pages/adminnovaatividade.php');
            } elseif ($sub_rota === 'candidatos') {
                array_unshift($candidatos_admin, $base_path . 'pages/casting.php');
            } elseif ($sub_rota === 'atividade') {
                array_unshift($candidatos_admin, $base_path . 'pages/adminatividade.php');
            }
            
            foreach ($candidatos_admin as $arq) {
                if (file_exists($arq)) {
                    $page_to_load = $arq;
                    break;
                }
            }
        }
    } else {
        // Roteamento dinâmico de Páginas Públicas & Portal do Responsável
        $rota_alvo = $rota_limpa;
        if ($rota_alvo === 'painel') {
            $rota_alvo = 'meuperfil';
        } elseif ($rota_alvo === 'novoevento') {
            $rota_alvo = 'adminnovaatividade';
        }
        
        $candidatos_publico = [
            $base_path . 'pages/' . $rota_alvo . '.php',
            $base_path . 'pages/' . str_replace('-', '_', $rota_alvo) . '.php',
            $base_path . 'pages/' . str_replace('-', '', $rota_alvo) . '.php'
        ];
        
        foreach ($candidatos_publico as $arq) {
            if (file_exists($arq)) {
                $page_to_load = $arq;
                break;
            }
        }
    }

    if (!empty($page_to_load) && file_exists($page_to_load)) {
        include_once($page_to_load);
    } else {
        include_once($base_path . 'pages/inexiste.php');
    }
}
?>
