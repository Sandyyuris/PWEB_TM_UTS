<?php

require_once 'Controller/loginController.php';
require_once 'Controller/registerController.php';
require_once 'Controller/profilController.php';
require_once 'Controller/hasilController.php';

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
        
    case 'edit_profil':
        $controller = new ProfilController();
        $controller->editIndex();
        break;

    case 'update':
        $controller = new ProfilController();
        $controller->update();
        break;

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