<?php
require_once '../config/database.php';

header('Content-Type: application/json');

try {
    $sql = "SELECT id, name, start_date, end_date FROM events ORDER BY start_date DESC";
    $result = $conn->query($sql);
    
    $events = array();
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
    
    echo json_encode($events);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}