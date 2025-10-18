<?php
require_once "Model/login_registerModel.php";
require_once "config/database.php";

class RegisterController {

    public function index() {
        require_once "View/register.php";
    }

    public function register() {
        session_start();

        if (isset($_POST["register"])) {
            $username = $_POST["username"];
            $password = $_POST["password"];

            $database = new Database();
            $conn = $database->getConnection();

            $model = new login_registerModel($username, $password);

            $result = $model->cekUsername($conn);

            if ($result && $result->num_rows > 0) {
                $pesan_error = "Username sudah digunakan!";
                echo "<script>alert('$pesan_error'); window.location='index.php?action=register';</script>";
                exit;
            } else {
                // 🔹 Simpan akun baru
                $model->register($conn);
                $pesan_success = "Akun berhasil dibuat! Silakan login.";
                echo "<script>alert('$pesan_success'); window.location='index.php?action=login';</script>";
                exit;
            }
        }
    }
}
