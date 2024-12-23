<?php
// Memuat file env_loader.php untuk memuat variabel lingkungan dari file .env
require_once __DIR__ . '/../config/env_loader.php';
// Memuat file auth_controller.php yang berisi kelas AuthController
require_once __DIR__ . '/../controller/auth/auth_controller.php';

// Memuat variabel lingkungan dari file .env
loadEnv(__DIR__ . '/../../.env');

// Membuat instance dari AuthController
$auth = new AuthController();

// Memulai session
// $auth->initSession();

// Middleware untuk memeriksa apakah pengguna adalah admin
function adminMiddleware() {
    if ($_SESSION['user']['role_id'] !== 1) {
        // Jika pengguna bukan admin, arahkan ke halaman dashboard pengguna
        header("Location: " . getenv('APP_URL') . "/user/dashboard");
    }
}

// Middleware untuk memeriksa apakah pengguna adalah user biasa
function userMiddleware() {
    if ($_SESSION['user']['role_id'] !== 2) {
        // Jika pengguna bukan user biasa, arahkan ke halaman dashboard admin
        header("Location: " . getenv('APP_URL') . "/admin/dashboard");
    }
}
?>
