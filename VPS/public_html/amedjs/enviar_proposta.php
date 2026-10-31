<?php
/**
 * Handler de Processamento de Propostas B2B - AME DJs
 * Endpoint: POST /api/proposta.php
 */

header('Content-Type: application/json; charset=utf-8');

// Apenas POST permitido
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método não permitido']);
    exit;
}

// 1. Sanitização e Captura dos Campos
$nome        = trim(filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
$empresa     = trim(filter_input(INPUT_POST, 'empresa', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
$email       = trim(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) ?? '');
$telefone    = trim(filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
$tipo_evento = trim(filter_input(INPUT_POST, 'tipo_evento', FILTER_SANITIZE_SPECIAL_CHARS) ?? 'Geral');
$data_evento = trim(filter_input(INPUT_POST, 'data_evento', FILTER_SANITIZE_SPECIAL_CHARS) ?? 'A definir');
$mensagem    = trim(filter_input(INPUT_POST, 'mensagem', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');

// Honeypot anti-spam (campo invisível)
$honeypot    = trim($_POST['website'] ?? '');
if (!empty($honeypot)) {
    // Se o bot preencheu o honeypot, finge sucesso silencioso
    echo json_encode(['success' => true]);
    exit;
}

// Validação dos obrigatórios
if (empty($nome) || empty($email) || empty($telefone)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Por favor, preencha todos os campos obrigatórios (Nome, E-mail e Telefone).']);
    exit;
}

// 2. Envio de E-mail para contato@projetoame.org
$destinatario = 'contato@projetoame.org';
$assunto = "=?UTF-8?B?" . base64_encode("🎧 Novo Interesse Comercial - AME DJs: " . ($empresa ?: $nome)) . "?=";

$corpoEmail = "========================================================\n";
$corpoEmail .= "  NOVA SOLICITAÇÃO DE CONTRATAÇÃO - AME DJs\n";
$corpoEmail .= "========================================================\n\n";
$corpoEmail .= "Solicitante:   {$nome}\n";
$corpoEmail .= "Empresa:       " . ($empresa ?: 'Não informada') . "\n";
$corpoEmail .= "E-mail:        {$email}\n";
$corpoEmail .= "WhatsApp:      {$telefone}\n";
$corpoEmail .= "Tipo Evento:   {$tipo_evento}\n";
$corpoEmail .= "Data Prevista: {$data_evento}\n\n";
$corpoEmail .= "Detalhes e Mensagem:\n";
$corpoEmail .= "--------------------------------------------------------\n";
$corpoEmail .= ($mensagem ?: 'Sem observações adicionais.') . "\n";
$corpoEmail .= "--------------------------------------------------------\n\n";
$corpoEmail .= "Origem: https://amedjs.ong.br/\n";
$corpoEmail .= "Data/Hora: " . date('d/m/Y H:i:s') . "\n";

$headers = [];
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-type: text/plain; charset=UTF-8';
$headers[] = 'From: AME DJs <noreply@amedjs.ong.br>';
$headers[] = "Reply-To: {$nome} <{$email}>";
$headers[] = 'X-Mailer: PHP/' . phpversion();

$emailEnviado = @mail($destinatario, $assunto, $corpoEmail, implode("\r\n", $headers));

// 3. Notificação Webhook (iController / n8n / Evolution API)
// Dispara evento assíncrono para o orquestrador n8n na VPS2
$webhookUrl = 'https://n8n.netmailing.com.br/webhook/amedjs-novo-lead';
$payloadWebhook = json_encode([
    'origem'      => 'Landing Page AMEDJs',
    'tenant'      => 'projeto_ame',
    'nome'        => $nome,
    'empresa'     => $empresa,
    'email'       => $email,
    'telefone'    => $telefone,
    'tipo_evento' => $tipo_evento,
    'data_evento' => $data_evento,
    'mensagem'    => $mensagem,
    'timestamp'   => date('c')
]);

$ch = curl_init($webhookUrl);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadWebhook);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($payloadWebhook)
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 3); // Timeout rápido para não prender o usuário
$webhookResponse = @curl_exec($ch);
curl_close($ch);

// 4. Retorno JSON
echo json_encode([
    'success' => true,
    'message' => 'Solicitação recebida com sucesso! Em instantes nossa equipe entrará em contato.'
]);
