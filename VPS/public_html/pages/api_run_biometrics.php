<?php
// pages/api_run_biometrics.php - Aciona scripts python de biometria via Painel Admin
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

// Conexão com o banco de dados (se necessário)
if (!isset($conexao)) {
    require_once __DIR__ . '/../database/conexao.php';
}

// 1. Verifica se o usuário é administrador
if (!isset($_SESSION['id']) || (int)$_SESSION['nivel'] < 4) {
    echo json_encode(['success' => false, 'message' => 'Acesso negado. Apenas administradores podem executar esta ação.']);
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action === 'blog') {
    // Executa em segundo plano no servidor Linux (VPS)
    // Redireciona a saída para um arquivo de log local para evitar que o PHP bloqueie a resposta
    $cmd = "/home/projetoame/public_html/include/venv/bin/python /home/projetoame/scan_blog_biometrics.py > /home/projetoame/scan_blog.log 2>&1 &";
    exec($cmd);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Varredura e biometria dos posts do WordPress iniciada em segundo plano no servidor! Acompanhe as novas fotos reconhecidas na listagem.'
    ]);
    exit;
} elseif ($action === 'event') {
    $evento_id = isset($_POST['evento_id']) ? (int)$_POST['evento_id'] : 0;
    $pasta_fotos = isset($_POST['pasta_fotos']) ? trim($_POST['pasta_fotos']) : '';
    
    if ($evento_id <= 0 || empty($pasta_fotos)) {
        echo json_encode(['success' => false, 'message' => 'Por favor, informe um ID de evento válido e o caminho da pasta de fotos.']);
        exit;
    }
    
    // Sanitização básica do caminho para execução segura em shell
    $pasta_fotos_escaped = escapeshellarg($pasta_fotos);
    $cmd = "/home/projetoame/public_html/include/venv/bin/python /home/projetoame/scan_events.py $evento_id $pasta_fotos_escaped > /home/projetoame/scan_event_$evento_id.log 2>&1 &";
    exec($cmd);
    
    echo json_encode([
        'success' => true, 
        'message' => "Processamento da pasta de fotos para o Evento #$evento_id iniciado em segundo plano no servidor!"
    ]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Ação de execução inválida.']);
exit;
