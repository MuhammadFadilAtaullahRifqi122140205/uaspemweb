<?php
require_once __DIR__ . '/../database/db.php';


class BaseController extends Connection {
    public function __construct() {
        // Memanggil constructor dari parent class
        parent::__construct();
        // Memanggil method initSession
        $this->initSession();
    }

    public function initSession() {
        // Memulai session jika belum dimulai
        session_start();
    }

    public function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            // Membuat token CSRF jika belum ada
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        // Mengembalikan token CSRF
        return $_SESSION['csrf_token'];
    }

    public function verifyCsrfToken($token) {
        if (!$token) {
            // Jika token kosong maka akan mengembalikan false
            return false;
        }
        // Membandingkan token yang diterima dengan token yang ada di session
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
