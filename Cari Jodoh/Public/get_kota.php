<?php
header('Content-Type: application/json');

require_once '../config/database.php';

try {
    if (!isset($_GET['provinsi'])) {
        throw new Exception('Provinsi tidak ditemukan');
    }
    
    $provinsi = $_GET['provinsi'];
    
    $database = new Database();
    $conn = $database->getConnection();
    
    // Cari id_provinsi berdasarkan nama provinsi
    $queryProvinsi = "SELECT id_provinsi FROM provinsi WHERE nama_provinsi = ?";
    $stmtProvinsi = $conn->prepare($queryProvinsi);
    $stmtProvinsi->bind_param("s", $provinsi);
    $stmtProvinsi->execute();
    $resultProvinsi = $stmtProvinsi->get_result();
    
    if ($resultProvinsi->num_rows === 0) {
        throw new Exception('Provinsi tidak valid');
    }
    
    $dataProvinsi = $resultProvinsi->fetch_assoc();
    $idProvinsi = $dataProvinsi['id_provinsi'];
    
    // Ambil data kota berdasarkan id_provinsi
    $queryKota = "SELECT nama_kota FROM kota WHERE id_provinsi = ? ORDER BY nama_kota ASC";
    $stmtKota = $conn->prepare($queryKota);
    $stmtKota->bind_param("i", $idProvinsi);
    $stmtKota->execute();
    $resultKota = $stmtKota->get_result();
    
    $kota = [];
    if ($resultKota) {
        while ($row = $resultKota->fetch_assoc()) {
            $kota[] = $row['nama_kota'];
        }
    }
    
    echo json_encode([
        'success' => true,
        'kota' => $kota
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}