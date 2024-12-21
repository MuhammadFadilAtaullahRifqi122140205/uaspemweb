<?php
require_once __DIR__ . '/../config/env_loader.php';
loadEnv(__DIR__ . '/../../.env');
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: " . getenv('APP_URL'));
    exit();
}
?>
