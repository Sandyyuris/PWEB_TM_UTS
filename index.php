<?php
// FILE: Cari Jodoh/index.php

// Panggil semua Controller yang dibutuhkan
require_once 'Controller/loginController.php';
require_once 'Controller/registerController.php';
require_once 'Controller/profilController.php'; // 👈 Menggunakan nama baru ProfilController
require_once 'Controller/hasilController.php';

// Ambil parameter action dari URL, default ke 'login'
$action = $_GET['action'] ?? 'login';

switch ($action) {
    case 'register':
        $controller = new RegisterController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
            $controller->register();
        } else {
            $controller->index();
        }
        break;

    case 'login':
        $controller = new LoginController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
            $controller->login();
        } else {
            $controller->index();
        }
        break;

    // --- LOGIKA PROFIL BARU ---
    case 'profil':
        // Halaman pengisian profil awal (form.php)
        $controller = new ProfilController();
        $controller->index();
        break;

    case 'submit':
        // Proses pengiriman form profil awal
        $controller = new ProfilController();
        $controller->submit();
        break;
        
    case 'edit_profil':
        // Halaman tampilan edit profil (edit_profil.php)
        $controller = new ProfilController();
        $controller->editIndex();
        break;

    case 'update':
        // Proses pengiriman form edit profil
        $controller = new ProfilController();
        $controller->update();
        break;
    // --- AKHIR LOGIKA PROFIL BARU ---

    case 'dashboard':
        $controller = new HasilController();
        $controller->index();
        break;

    case 'logout':
        $controller = new LoginController();
        $controller->logout();
        break;

    default:
        $controller = new LoginController();
        $controller->index();
        break;
}