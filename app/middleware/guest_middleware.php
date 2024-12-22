<?php
require_once __DIR__ . '/../config/env_loader.php';
require_once __DIR__ . '/../controller/auth/auth_controller.php';
loadEnv(__DIR__ . '/../../.env');

$auth = new AuthController();

$auth->initRedisSession();

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['user'])) {
    header("Location: " . getenv('APP_URL') . "/user/dashboard");
    exit();
}
?>
