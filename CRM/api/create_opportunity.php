<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_POST['title']) || !isset($_POST['entity_type']) || !isset($_POST['entity_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    $stmt = $conn->prepare("
        INSERT INTO opportunities (
            title, 
            entity_type, 
            entity_id, 
            event_id, 
            stage
        ) VALUES (?, ?, ?, ?, 'backlog')
    ");
    
    $event_id = $_POST['event_id'] ?: null;
    $stmt->bind_param('ssii', $_POST['title'], $_POST['entity_type'], $_POST['entity_id'], $event_id);
    
    if ($stmt->execute()) {
        $opportunityId = $conn->insert_id;
        echo json_encode(['success' => true, 'id' => $opportunityId]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create opportunity']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}