<?php
require_once '../config/database.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['opportunity_id']) || !isset($data['stage'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE opportunities SET stage = ? WHERE id = ?");
    $stmt->bind_param('si', $data['stage'], $data['opportunity_id']);
    $success = $stmt->execute();
    
    echo json_encode(['success' => $success]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}