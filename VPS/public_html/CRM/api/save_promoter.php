<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST;
    
    $sql = "INSERT INTO promoters (company_name, cnpj, website, main_phone, email, address, segment) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", 
        $data['company_name'],
        $data['cnpj'],
        $data['website'],
        $data['main_phone'],
        $data['email'],
        $data['address'],
        $data['segment']
    );
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}