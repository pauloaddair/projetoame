<?php
require_once '../config/database.php';

header('Content-Type: application/json');

$type = $_GET['type'] ?? '';

if (!in_array($type, ['promoter', 'exhibitor'])) {
    echo json_encode(['error' => 'Invalid entity type']);
    exit;
}

try {
    $table = $type . 's';
    $sql = "SELECT id, company_name FROM $table ORDER BY company_name";
    $result = $conn->query($sql);
    
    $entities = array();
    while ($row = $result->fetch_assoc()) {
        $entities[] = $row;
    }
    
    echo json_encode($entities);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}