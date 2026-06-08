<?php
/**
 * Background Event Date Updater
 * Hybrid PHP + Python solution to update event dates.
 */

// Safe path inclusion
$base_path = dirname(__DIR__) . '/';
include_once($base_path . 'database/conexao.php');

if (!$conexao) {
    echo json_encode(["success" => false, "error" => "Database connection failed"]);
    exit(1);
}

// Select events from the 'eventos' table that need updating
// Order by oldest or NULL dates first
$query = "SELECT evento_id, Evento, Inicio, Final 
          FROM eventos 
          WHERE Inicio < NOW() OR Inicio IS NULL 
          ORDER BY Inicio IS NULL DESC, Inicio ASC 
          LIMIT 5";

$result = mysqli_query($conexao, $query);
if (!$result) {
    echo json_encode(["success" => false, "error" => "Database query failed: " . mysqli_error($conexao)]);
    exit(1);
}

$events = [];
while ($row = mysqli_fetch_assoc($result)) {
    $events[] = [
        'evento_id' => (int)$row['evento_id'],
        'Evento' => $row['Evento'],
        'Inicio' => $row['Inicio'],
        'Final' => $row['Final']
    ];
}

if (empty($events)) {
    echo json_encode(["success" => true, "message" => "No events need updating", "updated" => []], JSON_UNESCAPED_UNICODE);
    exit(0);
}

$json_events = json_encode($events, JSON_UNESCAPED_UNICODE);

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
    echo json_encode(["success" => false, "error" => "Failed to execute Python script or empty output"]);
    exit(1);
}

$response = json_decode($output, true);

// Check if Python script returned error structure
if (isset($response['success']) && $response['success'] === false) {
    echo json_encode(["success" => false, "error" => "Python script error: " . ($response['error'] ?? 'Unknown error')]);
    exit(1);
}

if (!is_array($response)) {
    echo json_encode(["success" => false, "error" => "Invalid response from Python script", "raw_output" => $output]);
    exit(1);
}

$updated_count = 0;
$summary = [];

foreach ($response as $item) {
    $evento_id = isset($item['evento_id']) ? (int)$item['evento_id'] : 0;
    $inicio = $item['inicio'] ?? null;
    $final = $item['final'] ?? null;
    $error = $item['error'] ?? null;
    
    // Find original event name
    $event_name = '';
    foreach ($events as $ev) {
        if ($ev['evento_id'] === $evento_id) {
            $event_name = $ev['Evento'];
            break;
        }
    }

    if ($inicio && $final) {
        // Update database columns Inicio, Final, and Data Confirmada
        $update_query = "UPDATE eventos 
                         SET Inicio = ?, Final = ?, `Data Confirmada` = 1 
                         WHERE evento_id = ?";
        $stmt = mysqli_prepare($conexao, $update_query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssi", $inicio, $final, $evento_id);
            $success = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            
            if ($success) {
                $updated_count++;
                $summary[] = [
                    "evento_id" => $evento_id,
                    "evento" => $event_name,
                    "inicio" => $inicio,
                    "final" => $final,
                    "status" => "Updated successfully"
                ];
            } else {
                $summary[] = [
                    "evento_id" => $evento_id,
                    "evento" => $event_name,
                    "status" => "Database update failed"
                ];
            }
        } else {
            $summary[] = [
                "evento_id" => $evento_id,
                "evento" => $event_name,
                "status" => "Failed to prepare update query"
            ];
        }
    } else {
        $summary[] = [
            "evento_id" => $evento_id,
            "evento" => $event_name,
            "status" => "No dates found",
            "error" => $error
        ];
    }
}

// Return output summary
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    "success" => true,
    "total_processed" => count($events),
    "updated_count" => $updated_count,
    "summary" => $summary
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
