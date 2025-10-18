<?php
header('Content-Type: application/json');

require_once '../config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $query = "SELECT nama_provinsi FROM provinsi ORDER BY nama_provinsi ASC";
    $result = $conn->query($query);
    
    $provinsi = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $provinsi[] = $row['nama_provinsi'];
        }
    }
    
    echo json_encode([
        'success' => true,
        'provinsi' => $provinsi
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}