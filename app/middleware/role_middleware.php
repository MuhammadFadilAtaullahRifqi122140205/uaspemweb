<?php
require_once __DIR__ . '/../config/env_loader.php';
require_once __DIR__ . '/../controller/auth/auth_controller.php';
loadEnv(__DIR__ . '/../../.env');

$auth = new AuthController();

$auth->initSession();

function adminMiddleware() {
    if ($_SESSION['user']['role_id'] !== 1) {
        header("Location: " . getenv('APP_URL') . "/user/dashboard");
        exit();
    }
}

function userMiddleware() {
    if ($_SESSION['user']['role_id'] !== 2) {
        header("Location: " . getenv('APP_URL') . "/admin/dashboard");
        exit();
    }
}
?>
