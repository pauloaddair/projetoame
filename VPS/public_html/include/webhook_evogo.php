<?php
// F:/01_Projetos/Ativos/PROJETO_AME/VPS/public_html/include/webhook_evogo.php

header('Content-Type: application/json');

// Include database connection relative to this file
require_once dirname(__DIR__) . '/database/conexao.php';

if (!$conexao) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Read raw POST input and decode JSON
$raw_input = file_get_contents('php://input');
if (empty($raw_input) && php_sapi_name() === 'cli') {
    $raw_input = file_get_contents('php://stdin');
}
$data = json_decode($raw_input, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON payload']);
    exit;
}

// Parse sender JID or phone number
$sender_jid = '';
if (isset($data['data']['key']['remoteJid'])) {
    $sender_jid = $data['data']['key']['remoteJid'];
} elseif (isset($data['data']['key']['participant'])) {
    $sender_jid = $data['data']['key']['participant'];
} elseif (isset($data['sender'])) {
    $sender_jid = $data['sender'];
} elseif (isset($data['key']['remoteJid'])) {
    $sender_jid = $data['key']['remoteJid'];
}

$phone = '';
if (!empty($sender_jid)) {
    // Keep only digits (e.g. 5511986156206)
    $phone = preg_replace('/\D/', '', $sender_jid);
}

// Parse message text with fallbacks
$message_text = '';
if (isset($data['data']['message']['conversation'])) {
    $message_text = $data['data']['message']['conversation'];
} elseif (isset($data['data']['message']['extendedTextMessage']['text'])) {
    $message_text = $data['data']['message']['extendedTextMessage']['text'];
} elseif (isset($data['data']['message']['text'])) {
    $message_text = $data['data']['message']['text'];
} elseif (isset($data['text'])) {
    $message_text = $data['text'];
} elseif (isset($data['data']['message']['imageMessage']['caption'])) {
    $message_text = $data['data']['message']['imageMessage']['caption'];
} elseif (isset($data['data']['message']['videoMessage']['caption'])) {
    $message_text = $data['data']['message']['videoMessage']['caption'];
}

// Phone is required. If empty, discard the payload.
if (empty($phone)) {
    echo json_encode(['success' => false, 'message' => 'Invalid payload']);
    exit;
}

// Text is optional (media fallbacks). If empty, set a fallback message description.
if (empty($message_text)) {
    $message_text = '[Mídia/Mensagem sem texto]';
}

// Extract last 10 digits for DDD + number query matching
$phone_suffix = strlen($phone) >= 10 ? substr($phone, -10) : $phone;

// Search for matching lead in leads_expositores
$sql = "SELECT id, nome, u_id FROM leads_expositores WHERE 
  REPLACE(REPLACE(REPLACE(REPLACE(whatsapp, ' ', ''), '-', ''), '+', ''), '(', '') LIKE ?
  OR REPLACE(REPLACE(REPLACE(REPLACE(telefone, ' ', ''), '-', ''), '+', ''), '(', '') LIKE ?
LIMIT 1";

$stmt = $conexao->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'SQL preparation failed', 'error' => $conexao->error]);
    exit;
}

$like_suffix = '%' . $phone_suffix;
$stmt->bind_param("ss", $like_suffix, $like_suffix);
$stmt->execute();
$result = $stmt->get_result();
$lead = $result->fetch_assoc();
$stmt->close();

if (!$lead) {
    echo json_encode(['success' => false, 'message' => 'No matching lead found for phone']);
    $conexao->close();
    exit;
}

$expositor_id = $lead['id'];
$lead_name = $lead['nome'];
$u_id = !empty($lead['u_id']) ? intval($lead['u_id']) : 1;
$observacao = "[Recebido via EVOGO]: " . $message_text;
$data_contato = date('Y-m-d');
$tipo_contato = 'whatsapp';
$status = 'em andamento';

// Deduplication check: same expositor_id and observacao in the last 2 minutes
$dedup_sql = "SELECT id FROM leads_contatos 
              WHERE expositor_id = ? 
                AND observacao = ? 
                AND data_update >= NOW() - INTERVAL 2 MINUTE 
              LIMIT 1";
$dedup_stmt = $conexao->prepare($dedup_sql);
if ($dedup_stmt) {
    $dedup_stmt->bind_param("is", $expositor_id, $observacao);
    $dedup_stmt->execute();
    $dedup_result = $dedup_stmt->get_result();
    $is_duplicate = $dedup_result->fetch_assoc();
    $dedup_stmt->close();

    if ($is_duplicate) {
        echo json_encode([
            'success' => true,
            'message' => 'Message registered in CRM (duplicate ignored)',
            'lead' => $lead_name
        ]);
        $conexao->close();
        exit;
    }
}

// Log new message in CRM history (leads_contatos)
$insert_sql = "INSERT INTO leads_contatos (expositor_id, tipo_contato, data_contato, observacao, status, u_id) 
               VALUES (?, ?, ?, ?, ?, ?)";
$insert_stmt = $conexao->prepare($insert_sql);
if ($insert_stmt) {
    $insert_stmt->bind_param("issssi", $expositor_id, $tipo_contato, $data_contato, $observacao, $status, $u_id);
    if ($insert_stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Message registered in CRM',
            'lead' => $lead_name
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to insert contact log into CRM',
            'error' => $insert_stmt->error
        ]);
    }
    $insert_stmt->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Insert statement preparation failed',
        'error' => $conexao->error
    ]);
}

$conexao->close();
