<?php

require_once "Model/formModel.php";
require_once "config/database.php";
require_once "Model/hasilModel.php"; 

class ProfilController {
    
    public function index() {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }

        $database = new Database();
        $conn = $database->getConnection();

        $hasilModel = new HasilModel();
        $dataUser = $hasilModel->getUserById($conn, $_SESSION['user_id']);
        $_SESSION['user'] = $dataUser;


        if (!empty($_SESSION['user']['nama_lengkap'])) {
            header("Location: index.php?action=dashboard");
            exit;
        }

        require_once "View/form.php";
    }

    public function submit() {
        session_start();
        
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=login");
            exit;
        }

        $errors = $this->validateInput($_POST, $_FILES);
        if (!empty($errors)) {
            $error_message = implode("\\n", $errors);
            echo "<script>alert('Gagal Validasi:\\n$error_message'); window.location='index.php?action=profil';</script>";
            exit;
        }

        $database = new Database();
        $conn = $database->getConnection();

        $uploadFoto = ProfilModel::uploadFoto($_FILES['image']);
        if (!$uploadFoto['success']) {
            echo "<script>alert('Gagal Upload Foto: " . $uploadFoto['message'] . "'); window.location='index.php?action=profil';</script>";
            exit;
        }

        $uploadSignature = ProfilModel::simpanTandaTangan($_POST['signature']);
        if (!$uploadSignature['success']) {
            echo "<script>alert('Gagal Simpan Tanda Tangan: " . $uploadSignature['message'] . "'); window.location='index.php?action=profil';</script>";
            exit;
        }

        $hobi = isset($_POST['hobi']) ? implode(", ", $_POST['hobi']) : "";
        $model = new ProfilModel(
            $_SESSION['user_id'],
            htmlspecialchars($_POST['nama']),
            htmlspecialchars($_POST['gender']),
            intval($_POST['usia']),
            htmlspecialchars($_POST['provinsi']),
            htmlspecialchars($_POST['kota']),
            htmlspecialchars($_POST['pekerjaan']),
            $hobi,
            htmlspecialchars($_POST['bio']),
            'uploads/profiles/' . $uploadFoto['filename'],
            'uploads/signatures/' . $uploadSignature['filename']
        );

        if ($model->simpanProfil($conn)) {

            echo "<script>alert('Profil berhasil disimpan! 💖'); window.location='index.php?action=dashboard';</script>";
        } else {
            echo "<script>alert('Gagal menyimpan profil. (Database Error/Kota tidak ditemukan).'); window.location='index.php?action=profil';</script>";
        }
    }
    
    public function editIndex() {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }

        $database = new Database();
        $conn = $database->getConnection();

        $hasilModel = new HasilModel();
        $dataProfil = $hasilModel->getUserById($conn, $_SESSION['user_id']);
        
        if (!$dataProfil) {
            header("Location: index.php?action=profil");
            exit;
        }

        $user = $dataProfil; 

        require_once "View/edit_profil.php";
    }

    public function update() {
        session_start();
        
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=login");
            exit;
        }

        $database = new Database();
        $conn = $database->getConnection();
        $hasilModel = new HasilModel();
        $dataLama = $hasilModel->getUserById($conn, $_SESSION['user_id']);
        
        if (!$dataLama) {
            echo "<script>alert('Data profil lama tidak ditemukan.'); window.location='index.php?action=edit_profil';</script>";
            exit;
        }

        $fotoProfilBaru = $dataLama['foto_profil'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $uploadFoto = ProfilModel::uploadFoto($_FILES['image']);
            if (!$uploadFoto['success']) {
                echo "<script>alert('Gagal Upload Foto: " . $uploadFoto['message'] . "'); window.location='index.php?action=edit_profil';</script>";
                exit;
            }
            $fotoProfilBaru = 'uploads/profiles/' . $uploadFoto['filename'];
        }

        $tandaTanganBaru = $dataLama['tanda_tangan'];
        if (!empty($_POST['signature'])) { 
            $uploadSignature = ProfilModel::simpanTandaTangan($_POST['signature']);
            if (!$uploadSignature['success']) {
                echo "<script>alert('Gagal Simpan Tanda Tangan: " . $uploadSignature['message'] . "'); window.location='index.php?action=edit_profil';</script>";
                exit;
            }
            $tandaTanganBaru = 'uploads/signatures/' . $uploadSignature['filename'];
        }

        $hobi = isset($_POST['hobi']) ? implode(", ", $_POST['hobi']) : "";
        $model = new ProfilModel(
            $_SESSION['user_id'],
            htmlspecialchars($_POST['nama']),
            htmlspecialchars($_POST['gender']),
            intval($_POST['usia']),
            htmlspecialchars($_POST['provinsi']),
            htmlspecialchars($_POST['kota']),
            htmlspecialchars($_POST['pekerjaan']),
            $hobi,
            htmlspecialchars($_POST['bio']),
            $fotoProfilBaru, 
            $tandaTanganBaru 
        );
        
        if ($model->simpanProfil($conn)) {
            echo "<script>alert('Profil berhasil diupdate! 🌟'); window.location='index.php?action=dashboard';</script>";
        } else {
            echo "<script>alert('Gagal mengupdate profil. (Database Error/Kota tidak ditemukan).'); window.location='index.php?action=edit_profil';</script>";
        }
    }
    

    private function validateInput($post, $files) {
        $errors = [];
        if (empty($post['nama']) || strlen($post['nama']) < 3) {
            $errors[] = "Nama lengkap minimal 3 karakter";
        }
        if (empty($post['gender']) || !in_array($post['gender'], ['Laki-laki', 'Perempuan'])) {
            $errors[] = "Pilih jenis kelamin yang valid";
        }
        if (empty($post['usia']) || $post['usia'] < 17 || $post['usia'] > 100) {
            $errors[] = "Usia harus antara 17-100 tahun";
        }
        if (empty($post['provinsi']) || empty($post['kota'])) {
            $errors[] = "Pilih provinsi dan kota";
        }
        if (empty($post['pekerjaan'])) {
            $errors[] = "Pekerjaan harus diisi";
        }
        if (empty($post['hobi']) || !is_array($post['hobi'])) {
            $errors[] = "Pilih minimal 1 hobi";
        }
        if (empty($post['bio']) || strlen($post['bio']) < 20) {
            $errors[] = "Bio singkat minimal 20 karakter";
        }

        if (($_GET['action'] ?? 'submit') === 'submit' && empty($files['image']['name'])) {
            $errors[] = "Foto profil harus diupload";
        }
        if (($_GET['action'] ?? 'submit') === 'submit' && empty($post['signature'])) {
            $errors[] = "Tanda tangan digital harus diisi";
        }
        return $errors;
    }
}