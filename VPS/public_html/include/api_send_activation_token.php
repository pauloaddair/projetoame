<?php
// F:/01_Projetos/Ativos/PROJETO_AME/VPS/public_html/include/api_send_activation_token.php
// Endpoint para gerar e enviar o link/token de primeiro acesso (WhatsApp ou E-mail)

ini_set('display_errors', 0);
error_reporting(E_ALL);
header('Content-Type: application/json');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o ID do usuário para ativação está setado na sessão
if (!isset($_SESSION['ativacao_usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Acesso não autorizado ou sessão expirada.']);
    exit;
}

$usuario_id = (int)$_SESSION['ativacao_usuario_id'];
$method = isset($_POST['method']) ? trim($_POST['method']) : '';

if ($method !== 'whatsapp' && $method !== 'email') {
    echo json_encode(['success' => false, 'message' => 'Método de envio inválido.']);
    exit;
}

// Conexão com o Banco de Dados
$base_path = dirname(__DIR__) . '/';
include_once($base_path . 'database/conexao.php');

if (!$conexao) {
    echo json_encode(['success' => false, 'message' => 'Falha na conexão com o banco de dados.']);
    exit;
}

// Busca dados do usuário
$q_user = "SELECT * FROM usuarios WHERE usuario_ID = $usuario_id";
$res_user = mysqli_query($conexao, $q_user);
if (!$res_user || mysqli_num_rows($res_user) == 0) {
    echo json_encode(['success' => false, 'message' => 'Usuário não encontrado.']);
    exit;
}
$user_data = mysqli_fetch_assoc($res_user);

$nome = $user_data['nome'];
$email = $user_data['email'];
$telefone = $user_data['telefone'];

// Se telefone estiver vazio, busca nos candidatos vinculados
if (empty($telefone)) {
    $q_cand = "SELECT c.telefone, c.celular FROM candidatos c
               JOIN candidatos_usuarios cu ON c.candidato_id = cu.candidato_id
               WHERE cu.usuario_id = $usuario_id LIMIT 1";
    $res_cand = mysqli_query($conexao, $q_cand);
    if ($res_cand && mysqli_num_rows($res_cand) > 0) {
        $cand_data = mysqli_fetch_assoc($res_cand);
        $telefone = !empty($cand_data['celular']) ? $cand_data['celular'] : $cand_data['telefone'];
    }
}

// Gera o token randômico único
$token = bin2hex(random_bytes(24)); // 48 caracteres hexadecimais

// Grava o token e sua validade (2 horas)
$q_update = "UPDATE usuarios SET token_ativacao = '$token', token_expira = DATE_ADD(NOW(), INTERVAL 2 HOUR) WHERE usuario_ID = $usuario_id";
if (!mysqli_query($conexao, $q_update)) {
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar o token de segurança no servidor.']);
    exit;
}

// Constrói o link de ativação
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$app_web_root = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
// Remove '/include/' do path para a raiz da rota pública
$app_web_root = str_replace('include/', '', $app_web_root);
$link = $protocol . $host . $app_web_root . 'ativar-perfil/' . $token;

// Método de Envio: WhatsApp
if ($method === 'whatsapp') {
    if (empty($telefone)) {
        echo json_encode(['success' => false, 'message' => 'Telefone/WhatsApp não está cadastrado na sua conta.']);
        exit;
    }
    
    // Limpa telefone e garante código de país do Brasil (55) se DDD + Número
    $cleaned_phone = preg_replace('/\D/', '', $telefone);
    $phone_len = strlen($cleaned_phone);
    if ($phone_len === 10 || $phone_len === 11) {
        $cleaned_phone = '55' . $cleaned_phone;
    }

    $message_text = "Olá, *{$nome}*!\n\nEste é o seu link para ativar sua conta e cadastrar sua senha no *Portal do Associado do Projeto AME*:\n\n" . $link . "\n\nEste link é de uso único e expira em 2 horas. Caso não tenha solicitado, por favor ignore esta mensagem.";

    // Configuração Evolution API (do arquivo api_send_whatsapp_alert.php)
    $api_key = "1b4b590c-cd88-493a-99bf-2c5710e542b3";
    $evolution_url = "https://evogo.netmailing.com.br/message/sendText/GONET1200";

    $payload = [
        'number' => $cleaned_phone,
        'text' => $message_text
    ];

    $ch = curl_init($evolution_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'apikey: ' . $api_key
    ]);

    $response_body = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($curl_error) {
        echo json_encode(['success' => false, 'message' => 'Erro de conexão no WhatsApp (cURL): ' . $curl_error]);
        exit;
    }

    if ($http_code === 200 || $http_code === 201) {
        echo json_encode(['success' => true, 'message' => 'Link de ativação enviado com sucesso para o seu WhatsApp!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao enviar via WhatsApp (API Código: ' . $http_code . ').']);
    }
    exit;
}

// Método de Envio: E-mail
if ($method === 'email') {
    if (empty($email)) {
        echo json_encode(['success' => false, 'message' => 'E-mail não está cadastrado na sua conta.']);
        exit;
    }

    $subject = "Primeiro Acesso - Ativacao de Conta (Projeto AME)";
    
    // Corpo HTML da mensagem
    $html_message = '
    <html>
    <head>
      <title>Ativação de Conta - Projeto AME</title>
    </head>
    <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
      <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h2 style="color: #007bff; text-align: center;">Portal do Associado - Projeto AME</h2>
        <p>Olá, <strong>' . htmlspecialchars($nome) . '</strong>!</p>
        <p>Identificamos que este é o seu primeiro acesso ao nosso portal. Para sua segurança, você precisa criar uma senha de acesso.</p>
        <p style="text-align: center; margin: 30px 0;">
          <a href="' . $link . '" style="background-color: #28a745; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">Cadastrar Minha Senha</a>
        </p>
        <p style="font-size: 12px; color: #666; text-align: center;">
          Ou copie e cole o seguinte link no seu navegador:<br>
          <a href="' . $link . '">' . $link . '</a>
        </p>
        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
        <p style="font-size: 11px; color: #999;">Este link é seguro, de uso único e expira em 2 horas. Caso não tenha solicitado este e-mail, apenas ignore esta mensagem.</p>
      </div>
    </body>
    </html>
    ';

    $from = "pauloadd@projetoame.org";
    $headers = [];
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=UTF-8';
    $headers[] = 'From: Portal Projeto AME <' . $from . '>';

    if (mail($email, $subject, $html_message, implode("\r\n", $headers))) {
        echo json_encode(['success' => true, 'message' => 'Link de ativação enviado com sucesso para o seu e-mail!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro interno ao enviar o e-mail via servidor PHP.']);
    }
    exit;
}
