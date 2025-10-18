<?php
require_once "Model/login_registerModel.php";
require_once "config/database.php";

class LoginController {
    public function index() {
        require_once "View/login.php";
    }

    public function login() {
        session_start();
        if (isset($_POST["login"])) {
            $username = $_POST["username"];
            $password = $_POST["password"];

            $database = new Database();
            $conn = $database->getConnection();

            $model = new login_registerModel($username, $password);
            $result = $model->login($conn);

            if ($result && $result->num_rows === 1) {
                $data = $result->fetch_assoc();
                
                $hash_password = hash("sha256", $password);
                
                if ($hash_password === $data['password']) {
                    $_SESSION['user_id'] = $data['id'];
                    $_SESSION['username'] = $data['username'];
                    $_SESSION['user'] = $data;
                    
                    if (empty($data['nama_lengkap'])) {
                        header("Location: index.php?action=profil");
                    } else {
                        header("Location: index.php?action=dashboard");
                    }
                    exit;
                } else {
                    echo "<script>alert('Password salah!'); window.location='index.php?action=login';</script>";
                }
            } else {
                echo "<script>alert('Username tidak ditemukan!'); window.location='index.php?action=login';</script>";
            }
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        header("Location: index.php?action=login");
        exit;
    }
}