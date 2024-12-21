<?php
require_once __DIR__ . '/app/controller/auth/auth_controller.php';
session_start();

$authController = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_GET['action'];
    $authController->handleRequest($action, $_POST);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'logout') {
    $authController->logout();
    header("Location: http://localhost:8080/");
    exit();
}
