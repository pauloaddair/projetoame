<?php
/**
 * API to Search Exhibitors using AI (Python + DuckDuckGo + LiteLLM)
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
$query = "SELECT Evento FROM eventos WHERE evento_id = ?";
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

$evento_nome = $event['Evento'];

$is_windows = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
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

// Execute the Python script to extract exhibitors
$python_script = $base_path . 'include/find_exhibitors.py';
$command = $python_cmd . " " . escapeshellarg($python_script) . " " . escapeshellarg($evento_nome);
$output = shell_exec($command);

if ($output === null) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["success" => false, "message" => "Failed to run search script or output is empty"]);
    exit;
}

// Parse Python output
// Since Python outputs warnings first sometimes, let's extract only the JSON part
$json_start = strpos($output, '[');
if ($json_start === false) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["success" => false, "message" => "Python output contains no JSON structure", "raw" => $output]);
    exit;
}

$json_content = substr($output, $json_start);
$exhibitors = json_decode($json_content, true);

if (!is_array($exhibitors)) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["success" => false, "message" => "Invalid JSON from search script", "raw_output" => $output]);
    exit;
}

$inserted_count = 0;
$duplicates_count = 0;
$saved_list = [];

$u_id = intval($_SESSION['id']); // Current admin user ID

foreach ($exhibitors as $ex) {
    $nome = trim($ex['nome'] ?? '');
    if ($nome === '') continue;
    
    $contato = $ex['contato'] ?? null;
    $telefone = $ex['telefone'] ?? null;
    $site = $ex['site'] ?? null;
    $email = $ex['email'] ?? null;
    $whatsapp = $ex['whatsapp'] ?? null;
    $instagram = $ex['instagram'] ?? null;
    
    // Check if duplicate for this event_id
    $dup_query = "SELECT id FROM leads_expositores WHERE evento_id = ? AND nome = ?";
    $dup_stmt = mysqli_prepare($conexao, $dup_query);
    if ($dup_stmt) {
        mysqli_stmt_bind_param($dup_stmt, "is", $evento_id, $nome);
        mysqli_stmt_execute($dup_stmt);
        mysqli_stmt_store_result($dup_stmt);
        $is_dup = mysqli_stmt_num_rows($dup_stmt) > 0;
        mysqli_stmt_close($dup_stmt);
        
        if ($is_dup) {
            $duplicates_count++;
            continue;
        }
    }
    
    // Insert into database
    $ins_query = "INSERT INTO leads_expositores (evento_id, nome, contato, telefone, site, email, whatsapp, instagram, u_id) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $ins_stmt = mysqli_prepare($conexao, $ins_query);
    if ($ins_stmt) {
        mysqli_stmt_bind_param($ins_stmt, "isssssssi", 
            $evento_id, $nome, $contato, $telefone, $site, $email, $whatsapp, $instagram, $u_id
        );
        $success = mysqli_stmt_execute($ins_stmt);
        mysqli_stmt_close($ins_stmt);
        
        if ($success) {
            $inserted_count++;
            $saved_list[] = $nome;
        }
    }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    "success" => true,
    "evento" => $evento_nome,
    "extracted_count" => count($exhibitors),
    "inserted_count" => $inserted_count,
    "duplicates_count" => $duplicates_count,
    "list" => $saved_list
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
