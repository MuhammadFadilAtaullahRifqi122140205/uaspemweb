<?php
// Memuat file auth_controller.php yang berisi kelas AuthController
require_once __DIR__ . '/../controller/auth/auth_controller.php';
// Memuat file env_loader.php untuk memuat variabel lingkungan dari file .env
require_once __DIR__ . '/../config/env_loader.php';

// Memuat variabel lingkungan dari file .env
loadEnv(__DIR__ . '/../../.env');

// Membuat instance dari AuthController
$auth = new AuthController();

// Memulai session
// $auth->initSession();

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['user'])) {
    // Jika pengguna belum login, arahkan ke halaman utama
    header("Location: " . getenv('APP_URL'));
}
?>
