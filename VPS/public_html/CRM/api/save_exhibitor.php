<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST;
    
    $sql = "INSERT INTO exhibitors (company_name, cnpj, website, main_phone, email, address, segment, lead_source) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", 
        $data['company_name'],
        $data['cnpj'],
        $data['website'],
        $data['main_phone'],
        $data['email'],
        $data['address'],
        $data['segment'],
        $data['lead_source']
    );
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}