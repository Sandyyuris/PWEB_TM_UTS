<?php
require_once "Model/hasilModel.php";
require_once "config/database.php";

class HasilController {
    
    public function index() {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }

        $database = new Database();
        $conn = $database->getConnection();

        $hasilModel = new HasilModel();
        $currentUser = $hasilModel->getUserById($conn, $_SESSION['user_id']);
        
        if (!$currentUser || empty($currentUser['nama_lengkap'])) {
            header("Location: index.php?action=profil");
            exit;
        }

        $_SESSION['user'] = $currentUser;

        $allUsers = $hasilModel->getAllUsersExcept($conn, $_SESSION['user_id']);

        require_once "View/hasil.php";
    }

    public function getDataTable() {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $database = new Database();
        $conn = $database->getConnection();

        $hasilModel = new HasilModel();
        $users = $hasilModel->getAllUsersExcept($conn, $_SESSION['user_id']);

        $data = [];
        $no = 1;
        foreach ($users as $user) {
            $data[] = [
                'no' => $no++,
                'foto' => $user['foto_profil'],
                'nama' => $user['nama_lengkap'],
                'gender' => $user['jenis_kelamin'],
                'usia' => $user['umur'],
                'kota' => $user['nama_kota'] ?? 'N/A',
                'provinsi' => $user['nama_provinsi'] ?? 'N\/A',
                'pekerjaan' => $user['pekerjaan'],
                'hobi' => $user['hobi'],
                'bio' => $user['catatan'],
                'id' => $user['id']
            ];
        }

        echo json_encode(['data' => $data]);
    }
}