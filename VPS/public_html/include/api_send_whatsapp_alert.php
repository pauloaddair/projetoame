<?php
// F:/01_Projetos/Ativos/PROJETO_AME/VPS/public_html/include/api_send_whatsapp_alert.php
// API endpoint to dispatch WhatsApp alerts via Evolution API

// Prevent PHP notices/warnings from breaking the JSON response header
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Configuration
$api_key = "3ACDA4BD8811-4B32-A4E2-8A43367E1DAB";
$evolution_url = "https://evogo.netmailing.com.br/message/sendText/GONET1200";

// Start session to access session variables
session_start();

// Authenticate the admin session
if (empty($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Restricted access']);
    exit;
}

// Retrieve input parameters (phone and message)
$phone = '';
$message = '';

// Check standard POST variables
if (isset($_POST['phone'])) {
    $phone = $_POST['phone'];
}
if (isset($_POST['message'])) {
    $message = $_POST['message'];
}

// Fallback to JSON payload if parameters are empty
if (empty($phone) || empty($message)) {
    $input_source = (php_sapi_name() === 'cli') ? 'php://stdin' : 'php://input';
    $raw_input = file_get_contents($input_source);
    if (!empty($raw_input)) {
        $json_data = json_decode($raw_input, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            if (empty($phone) && isset($json_data['phone'])) {
                $phone = $json_data['phone'];
            }
            if (empty($message) && isset($json_data['message'])) {
                $message = $json_data['message'];
            }
        }
    }
}

// Validate that we got both inputs
if (empty($phone) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Missing phone or message parameter']);
    exit;
}

// Clean non-digit characters from the phone number
$cleaned_phone = preg_replace('/\D/', '', $phone);

// Brazilian standard phone adjustment:
// If the cleaned number has 10 or 11 digits, ensure it starts with country code 55
$phone_len = strlen($cleaned_phone);
if ($phone_len === 10 || $phone_len === 11) {
    $cleaned_phone = '55' . $cleaned_phone;
}

// Build body payload for Evolution API
$payload = [
    'number' => $cleaned_phone,
    'text' => $message
];

// Initialize cURL session
$ch = curl_init($evolution_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'apikey: ' . $api_key
]);

// Execute and capture response
$response_body = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

// Evaluate response and return JSON
if ($curl_error) {
    echo json_encode([
        'success' => false,
        'message' => 'cURL Error: ' . $curl_error
    ]);
    exit;
}

if ($http_code === 200 || $http_code === 201) {
    echo json_encode([
        'success' => true,
        'message' => 'Message sent successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Evolution API Error: ' . $http_code
    ]);
}
