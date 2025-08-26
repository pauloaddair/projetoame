<?php
//	echo "chegou aqui 1!<br>";
//	echo($request_uri);
//	exit;
//INDEX
session_start();
// Define o fuso horário
date_default_timezone_set('America/Sao_Paulo');

// --- Configurações Centralizadas de Caminho ---
$base_path = __DIR__ . '/'; 
$GLOBALS['app_web_root'] = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

// --- Roteamento ---
// $request_uri = strtok($_SERVER['REQUEST_URI'], '?');
$request_uri =$_SERVER['REQUEST_URI'];

if (substr($request_uri, 0, strlen($GLOBALS['app_web_root'])) == $GLOBALS['app_web_root']) {
    $route_path = substr($request_uri, strlen($GLOBALS['app_web_root']));
} else {
    $route_path = $request_uri;
}
$route_path = ltrim($route_path, '/');
$parametros = explode('/', $route_path);


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

    

    // DEBUG: Verificando o caminho do arquivo
    echo "<!-- DEBUG: Tentando carregar o arquivo: " . htmlspecialchars($page_to_load) . " -->";

    if (!empty($page_to_load) && file_exists($page_to_load)) {
        // Inclui os arquivos essenciais para as páginas do sistema
        include_once($base_path . 'include/conexao.php');
        include_once($base_path . 'include/funcoes.php');
        include_once($base_path . 'include/head.php');
        include_once($base_path . 'include/nav.php');

        include_once($page_to_load);

        // Inclui o rodapé da página
        include_once($base_path . 'include/footer.php');
        include_once($base_path . 'include/scripts.php');
        include_once($base_path . 'include/end.php');
    } else {
        // Se a página não existe, inclui a página de erro 404
        include_once($base_path . 'pages/inexiste.php');
    }
} else {
    // Se for a raiz, carrega o home/index.html e para o script
    if (file_exists($base_path . 'home/index.html')) {
        readfile($base_path . 'home/index.html');
        exit; // Para de executar o resto do PHP
    } else {
        // Fallback se o home/index.html não existir
        echo "Página inicial não encontrada.";
    }
}
?>