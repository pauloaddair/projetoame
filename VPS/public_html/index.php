<?php
// index.php - Front Controller do Projeto AME
session_start();

// Define o fuso horário
date_default_timezone_set('America/Sao_Paulo');

// --- Configurações Centralizadas de Caminho ---
$base_path = __DIR__ . '/';
$GLOBALS['app_web_root'] = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

// --- Roteamento ---
$request_uri = $_SERVER['REQUEST_URI'];

// Remove query strings para o roteamento
$request_uri = strtok($request_uri, '?');

if (substr($request_uri, 0, strlen($GLOBALS['app_web_root'])) == $GLOBALS['app_web_root']) {
    $route_path = substr($request_uri, strlen($GLOBALS['app_web_root']));
} else {
    $route_path = $request_uri;
}
$route_path = ltrim($route_path, '/');
$parametros = explode('/', $route_path);

// Inclui os arquivos essenciais para as páginas do sistema
include_once($base_path . 'database/conexao.php');
include_once($base_path . 'include/funcoes.php');

// Se for a raiz, carrega o home/index.php direto e para o script
if (empty($parametros[0])) {
    if (file_exists($base_path . 'home/index.php')) {
        include($base_path . 'home/index.php');
        exit;
    } else {
        echo "Página inicial não encontrada.";
        exit;
    }
}

// Output HTML structure (apenas para páginas do sistema interno)
include_once($base_path . 'include/html_head.php');
?>
<body>
<?php
// Lógica de roteamento principal
if (!empty($parametros[0])) {
    $page_to_load = '';

    // Roteamento para páginas administrativas
    if ($parametros[0] === 'admin') {
        $sub_rota = isset($parametros[1]) ? $parametros[1] : 'index';
        
        switch ($sub_rota) {
            case 'atividades':
                $page_to_load = $base_path . 'pages/adminatividades.php';
                break;
            case 'escala':
            case 'ver_escala':
                $page_to_load = $base_path . 'pages/adminescala.php';
                break;
            case 'novoevento':
                $page_to_load = $base_path . 'pages/adminnovoevento.php';
                break;
            case 'evento':
                $page_to_load = $base_path . 'pages/adminevento.php';
                break;
            case 'rodizio':
                $page_to_load = $base_path . 'pages/adminrodizio.php';
                break;
            default:
                // Tenta carregar admin{sub_rota}.php se existir
                if (file_exists($base_path . 'pages/admin' . $sub_rota . '.php')) {
                    $page_to_load = $base_path . 'pages/admin' . $sub_rota . '.php';
                } else {
                    $page_to_load = $base_path . 'pages/adminindex.php';
                }
                break;
        }
    } elseif ($parametros[0] === 'ativar-perfil') {
        $page_to_load = $base_path . 'pages/ativarperfil.php';
    } else {
        // Roteamento padrão para outras páginas (/atendentes, /login, etc)
        if (file_exists($base_path . 'pages/' . $parametros[0] . '.php')) {
            $page_to_load = $base_path . 'pages/' . $parametros[0] . '.php';
        } else {
            $page_to_load = $base_path . 'pages/inexiste.php';
        }
    }

    if (file_exists($page_to_load)) {
        include_once($page_to_load);
    } else {
        include_once($base_path . 'pages/inexiste.php');
    }
}
?>
<?php include_once($base_path . 'include/html_footer_scripts.php'); ?>
</body>
</html>
