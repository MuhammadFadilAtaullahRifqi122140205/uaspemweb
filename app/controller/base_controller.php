<?php
require_once __DIR__ . '/../database/db.php';

class BaseController extends Connection {
    public function __construct() {
        parent::__construct();
        session_start();
    }

    public function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public function verifyCsrfToken($token) {
        if(!$token) {
            return false;
        }
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
