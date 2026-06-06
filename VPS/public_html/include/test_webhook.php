<?php
// F:/01_Projetos/Ativos/PROJETO_AME/VPS/public_html/include/test_webhook.php

include_once dirname(__DIR__) . '/database/conexao.php';

if (!$conexao) {
    die("Database connection failed in test script\n");
}

// Fetch a real lead to get its phone/WhatsApp for testing
$query = "SELECT id, nome, whatsapp, telefone FROM leads_expositores WHERE whatsapp IS NOT NULL AND whatsapp != '' LIMIT 1";
$res = mysqli_query($conexao, $query);
$lead = mysqli_fetch_assoc($res);

if (!$lead) {
    $query = "SELECT id, nome, whatsapp, telefone FROM leads_expositores WHERE telefone IS NOT NULL AND telefone != '' LIMIT 1";
    $res = mysqli_query($conexao, $query);
    $lead = mysqli_fetch_assoc($res);
}

if (!$lead) {
    die("No leads found in leads_expositores to test with.\n");
}

$phone_to_use = !empty($lead['whatsapp']) ? $lead['whatsapp'] : $lead['telefone'];
$clean_phone = preg_replace('/\D/', '', $phone_to_use);
$jid = $clean_phone . "@s.whatsapp.net";

function run_webhook_with_payload($payload) {
    $descriptorspec = [
        0 => ["pipe", "r"], // stdin is a pipe that the child will read from
        1 => ["pipe", "w"], // stdout is a pipe that the child will write to
        2 => ["pipe", "w"]  // stderr
    ];

    $php_bin = 'F:\xampp\php\php.exe';
    $script_path = __DIR__ . '/webhook_evogo.php';
    
    $process = proc_open("$php_bin -d display_errors=0 $script_path", $descriptorspec, $pipes);
    
    if (is_resource($process)) {
        fwrite($pipes[0], json_encode($payload));
        fclose($pipes[0]);
        
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        
        proc_close($process);
        
        return [
            'stdout' => $stdout,
            'stderr' => $stderr
        ];
    }
    return null;
}

echo "==================================================\n";
echo "TEST 1: Normal message receipt\n";
echo "==================================================\n";

$msg_text = "Automated test message " . uniqid();
$payload1 = [
    "data" => [
        "key" => [
            "remoteJid" => $jid
        ],
        "message" => [
            "conversation" => $msg_text
        ]
    ]
];

$res = run_webhook_with_payload($payload1);
$response_json = $res['stdout'];
$response = json_decode($response_json, true);

if ($response && isset($response['success']) && $response['success'] === true && $response['message'] === 'Message registered in CRM') {
    echo "PASS: Normal message successfully logged. Lead: " . $response['lead'] . "\n";
} else {
    echo "FAIL: Normal message test failed. Response: $response_json. Error: " . $res['stderr'] . "\n";
}

// Get the inserted contact ID
$last_id_query = "SELECT id FROM leads_contatos WHERE expositor_id = " . $lead['id'] . " ORDER BY id DESC LIMIT 1";
$last_res = mysqli_query($conexao, $last_id_query);
$last_row = mysqli_fetch_assoc($last_res);
$first_inserted_id = $last_row['id'] ?? null;

echo "\n==================================================\n";
echo "TEST 2: Deduplication (duplicate message within 2 minutes)\n";
echo "==================================================\n";

$res2 = run_webhook_with_payload($payload1);
$response_json2 = $res2['stdout'];
$response2 = json_decode($response_json2, true);

if ($response2 && isset($response2['success']) && $response2['success'] === true && strpos($response2['message'], 'duplicate ignored') !== false) {
    echo "PASS: Duplicate message successfully ignored and returned success.\n";
} else {
    echo "FAIL: Deduplication test failed. Response: $response_json2. Error: " . $res2['stderr'] . "\n";
}

echo "\n==================================================\n";
echo "TEST 3: Fallback message for missing text (media)\n";
echo "==================================================\n";

$payload3 = [
    "data" => [
        "key" => [
            "remoteJid" => $jid
        ],
        "message" => [
            "imageMessage" => [
                "caption" => ""
            ]
        ]
    ]
];

$res3 = run_webhook_with_payload($payload3);
$response_json3 = $res3['stdout'];
$response3 = json_decode($response_json3, true);

if ($response3 && isset($response3['success']) && $response3['success'] === true) {
    echo "PASS: Webhook with empty message text successfully fell back and logged.\n";
} else {
    echo "FAIL: Missing text fallback test failed. Response: $response_json3. Error: " . $res3['stderr'] . "\n";
}

// Get second inserted ID
$last_res = mysqli_query($conexao, $last_id_query);
$last_row = mysqli_fetch_assoc($last_res);
$second_inserted_id = $last_row['id'] ?? null;

if ($second_inserted_id && $second_inserted_id != $first_inserted_id) {
    $verify_res = mysqli_query($conexao, "SELECT observacao FROM leads_contatos WHERE id = " . $second_inserted_id);
    $verify_row = mysqli_fetch_assoc($verify_res);
    if (strpos($verify_row['observacao'], '[Mídia/Mensagem sem texto]') !== false) {
        echo "PASS: Log contains the expected fallback text: " . $verify_row['observacao'] . "\n";
    } else {
        echo "FAIL: Log did not contain the fallback text. Observacao: " . $verify_row['observacao'] . "\n";
    }
}

echo "\n==================================================\n";
echo "TEST 4: Missing JID/phone (invalid payload)\n";
echo "==================================================\n";

$payload4 = [
    "data" => [
        "message" => [
            "conversation" => "Hello without JID"
        ]
    ]
];

$res4 = run_webhook_with_payload($payload4);
$response_json4 = $res4['stdout'];
$response4 = json_decode($response_json4, true);

if ($response4 && isset($response4['success']) && $response4['success'] === false && $response4['message'] === 'Invalid payload') {
    echo "PASS: Webhook rejected payload without phone number.\n";
} else {
    echo "FAIL: Missing phone test failed. Response: $response_json4. Error: " . $res4['stderr'] . "\n";
}

// Cleanup the test data
if ($first_inserted_id) {
    mysqli_query($conexao, "DELETE FROM leads_contatos WHERE id = " . $first_inserted_id);
    echo "\nCleaned up first test record (ID: $first_inserted_id)\n";
}
if ($second_inserted_id && $second_inserted_id != $first_inserted_id) {
    mysqli_query($conexao, "DELETE FROM leads_contatos WHERE id = " . $second_inserted_id);
    echo "Cleaned up second test record (ID: $second_inserted_id)\n";
}

$conexao->close();
?>
