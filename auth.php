<?php
require_once __DIR__ . '/app/controller/auth/auth_controller.php';


$authController = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_GET['action'];
    // Ketika request method adalah POST maka akan memanggil method handleRequest dari AuthController
    $authController->handleRequest($action, $_POST);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Ketika request method adalah GET dan action adalah logout maka akan memanggil method logout dari AuthController
    $authController->logout();
    header("Location: " . getenv('APP_URL'));
}
