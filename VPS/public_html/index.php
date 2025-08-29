<?php
//if (session_status() === PHP_SESSION_NONE) {
    session_start();
// }
// Define o fuso horário
date_default_timezone_set('America/Sao_Paulo');

// --- Configurações Centralizadas de Caminho ---
$base_path = __DIR__ . '/'; 
$GLOBALS['app_web_root'] = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

// --- Roteamento ---
$request_uri =$_SERVER['REQUEST_URI'];

if (substr($request_uri, 0, strlen($GLOBALS['app_web_root'])) == $GLOBALS['app_web_root']) {
    $route_path = substr($request_uri, strlen($GLOBALS['app_web_root']));
} else {
    $route_path = $request_uri;
}
$route_path = ltrim($route_path, '/');
$parametros = explode('/', $route_path);

// Inclui os arquivos essenciais para as páginas do sistema
include_once($base_path . 'include/conexao.php');
include_once($base_path . 'include/funcoes.php');

// Output HTML structure
include_once($base_path . 'include/html_head.php'); // New include for <head> content
?>
<body>
<?php
// Lógica de roteamento principal
if (!empty($parametros[0])) {
    $page_to_load = '';

    // Rota específica para /admin/atividades
    if (($parametros[0] === 'admin' && isset($parametros[1]) && $parametros[1] === 'atividades')) {
        $page_to_load = $base_path . 'pages/adminatividades.php';
    } elseif ($parametros[0] === 'admin' && isset($parametros[1]) && $parametros[1] === 'ver_escala') {
        $page_to_load = $base_path . 'pages/adminescala.php';
    } else {
        // Roteamento padrão para outras páginas
        $page_to_load = $base_path . 'pages/' . $parametros[0] . '.php';
    }

    if (!empty($page_to_load) && file_exists($page_to_load)) {
        include_once($page_to_load);
    } else {
        // Se a página não existe, inclui a página de erro 404
        include_once($base_path . 'pages/inexiste.php');
    }
} else {
    // Se for a raiz, carrega o home/index.html e para o script
    if (file_exists($base_path . 'home/index.html')) {
        readfile($base_path . 'home/index.html');
    } else {
        // Fallback se o home/index.html não existir
        echo "Página inicial não encontrada.";
    }
}
?>
<?php include_once($base_path . 'include/html_footer_scripts.php'); // New include for footer and scripts ?>
</body>
</html>