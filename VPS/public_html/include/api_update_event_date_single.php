<?php
/**
 * API to Update a Single Event's Dates using AI (Python + duckduckgo_search + LiteLLM)
 */
session_start();

// Check if user is logged in
if (!isset($_SESSION['id']) || $_SESSION['id'] === "") {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["success" => false, "message" => "Restricted access. Session invalid."]);
    exit;
}

$base_path = dirname(__DIR__) . '/';
include_once($base_path . 'database/conexao.php');

if (!$conexao) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["success" => false, "message" => "Invalid request method. Use POST."]);
    exit;
}

$evento_id = isset($_POST['evento_id']) ? intval($_POST['evento_id']) : 0;
if ($evento_id <= 0) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["success" => false, "message" => "Invalid Event ID"]);
    exit;
}

// Fetch event details
$query = "SELECT evento_id, Evento, Inicio, Final FROM eventos WHERE evento_id = ?";
$stmt = mysqli_prepare($conexao, $query);
if (!$stmt) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["success" => false, "message" => "Failed to prepare select query"]);
    exit;
}

mysqli_stmt_bind_param($stmt, "i", $evento_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$event = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$event) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["success" => false, "message" => "Event not found"]);
    exit;
}

$events_payload = [[
    'evento_id' => (int)$event['evento_id'],
    'Evento' => $event['Evento'],
    'Inicio' => $event['Inicio'],
    'Final' => $event['Final']
]];

$json_events = json_encode($events_payload, JSON_UNESCAPED_UNICODE);

// Determine execution argument to avoid Windows escapeshellarg JSON corruption
$is_windows = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
$arg = $is_windows ? base64_encode($json_events) : $json_events;

$python_cmd = 'python';
if (!$is_windows) {
    $venv_python = $base_path . 'include/venv/bin/python';
    if (file_exists($venv_python)) {
        $python_cmd = $venv_python;
    } else {
        $has_python3 = shell_exec('which python3');
        if (!empty(trim($has_python3))) {
            $python_cmd = 'python3';
        }
    }
}

$python_script = $base_path . 'include/update_events_dates.py';
$command = $python_cmd . " " . escapeshellarg($python_script) . " " . escapeshellarg($arg);

// Execute command
$output = shell_exec($command);

if ($output === null) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["success" => false, "message" => "Failed to execute Python script or empty output"]);
    exit;
}

// Find JSON start
$json_start = strpos($output, '[');
if ($json_start === false) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["success" => false, "message" => "Python output contains no JSON structure", "raw" => $output]);
    exit;
}

$json_content = substr($output, $json_start);
$response = json_decode($json_content, true);

if (!is_array($response) || empty($response)) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["success" => false, "message" => "Invalid response from Python script", "raw_output" => $output]);
    exit;
}

$item = $response[0];
$inicio = $item['inicio'] ?? null;
$final = $item['final'] ?? null;
$error = $item['error'] ?? null;

if ($inicio && $final) {
    // Update database columns Inicio, Final, and Data Confirmada
    $update_query = "UPDATE eventos 
                     SET Inicio = ?, Final = ?, `Data Confirmada` = 1 
                     WHERE evento_id = ?";
    $up_stmt = mysqli_prepare($conexao, $update_query);
    if ($up_stmt) {
        mysqli_stmt_bind_param($up_stmt, "ssi", $inicio, $final, $evento_id);
        $success = mysqli_stmt_execute($up_stmt);
        mysqli_stmt_close($up_stmt);
        
        if ($success) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                "success" => true,
                "evento" => $event['Evento'],
                "inicio" => $inicio,
                "final" => $final,
                "message" => "Dates updated successfully"
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["success" => false, "message" => "Failed to update database record"]);
} else {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["success" => false, "message" => "No dates found for the event", "error" => $error]);
}
?>
