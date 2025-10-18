<?php
require_once "Model/formModel.php";
require_once "config/database.php";

class ProfilController {
    // Tampilkan halaman form profil
    public function index() {
        session_start();
        
        // Cek apakah user sudah login
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }

        $database = new Database();
        $conn = $database->getConnection();

        // Ambil data user terbaru dari database
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows === 1) {
            $_SESSION['user'] = $result->fetch_assoc();
        }

        // Cek apakah profil sudah lengkap
        if (!empty($_SESSION['user']['nama_lengkap'])) {
            // Jika sudah lengkap, redirect ke dashboard
            header("Location: index.php?action=dashboard");
            exit;
        }

        // Tampilkan form profil
        require_once "View/form.php";
    }

    // Proses submit form profil
    public function submit() {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validasi input
            $errors = $this->validateInput($_POST, $_FILES);
            
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header("Location: index.php?action=profil");
                exit;
            }

            $database = new Database();
            $conn = $database->getConnection();

            // Upload foto profil
            $uploadFoto = ProfilModel::uploadFoto($_FILES['image']);
            if (!$uploadFoto['success']) {
                echo "<script>alert('" . $uploadFoto['message'] . "'); window.location='index.php?action=profil';</script>";
                exit;
            }

            // Simpan tanda tangan
            $uploadSignature = ProfilModel::simpanTandaTangan($_POST['signature']);
            if (!$uploadSignature['success']) {
                echo "<script>alert('" . $uploadSignature['message'] . "'); window.location='index.php?action=profil';</script>";
                exit;
            }

            // Gabungkan hobi menjadi string (comma separated)
            $hobi = isset($_POST['hobi']) ? implode(", ", $_POST['hobi']) : "";

            // Buat object model
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

            // Simpan ke database
            if ($model->simpanProfil($conn)) {
                // Update session dengan data user terbaru
                $query = "SELECT * FROM users WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $_SESSION['user_id']);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result && $result->num_rows === 1) {
                    $_SESSION['user'] = $result->fetch_assoc();
                }
                
                echo "<script>alert('Profil berhasil disimpan! 💖'); window.location='index.php?action=dashboard';</script>";
            } else {
                echo "<script>alert('Gagal menyimpan profil. Silakan coba lagi.'); window.location='index.php?action=profil';</script>";
            }
        }
    }

    // Validasi input form
    private function validateInput($post, $files) {
        $errors = [];

        // Validasi nama
        if (empty($post['nama']) || strlen($post['nama']) < 3) {
            $errors[] = "Nama lengkap minimal 3 karakter";
        }

        // Validasi gender
        if (empty($post['gender']) || !in_array($post['gender'], ['Laki-laki', 'Perempuan'])) {
            $errors[] = "Pilih jenis kelamin yang valid";
        }

        // Validasi usia
        if (empty($post['usia']) || $post['usia'] < 17 || $post['usia'] > 100) {
            $errors[] = "Usia harus antara 17-100 tahun";
        }

        // Validasi provinsi dan kota
        if (empty($post['provinsi']) || empty($post['kota'])) {
            $errors[] = "Pilih provinsi dan kota";
        }

        // Validasi pekerjaan
        if (empty($post['pekerjaan'])) {
            $errors[] = "Pekerjaan harus diisi";
        }

        // Validasi hobi
        if (empty($post['hobi']) || !is_array($post['hobi'])) {
            $errors[] = "Pilih minimal 1 hobi";
        }

        // Validasi bio
        if (empty($post['bio']) || strlen($post['bio']) < 20) {
            $errors[] = "Bio singkat minimal 20 karakter";
        }

        // Validasi foto
        if (empty($files['image']['name'])) {
            $errors[] = "Foto profil harus diupload";
        }

        // Validasi tanda tangan
        if (empty($post['signature'])) {
            $errors[] = "Tanda tangan digital harus diisi";
        }

        return $errors;
    }
}