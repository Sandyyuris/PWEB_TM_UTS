<?php
require_once 'Controller/loginController.php';
require_once 'Controller/registerController.php';
require_once 'Controller/formController.php';

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

    case 'profil':
        $controller = new ProfilController();
        $controller->index();
        break;

    case 'submit':
        $controller = new ProfilController();
        $controller->submit();
        break;

    case 'dashboard':
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }
        require_once "View/form.php";
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