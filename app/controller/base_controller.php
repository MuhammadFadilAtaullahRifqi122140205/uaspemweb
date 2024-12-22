<?php
require_once __DIR__ . '/../database/db.php';

class BaseController extends Connection {
    private $redis;

    public function __construct() {
        parent::__construct();
        $this->initRedisSession();
    }

    public function initRedisSession() {
        $this->redis = new Redis();
        $this->redis->connect(getenv("REDIS_HOST"), getenv("REDIS_PORT"));
        // $this->redis->auth(getenv("REDIS_PASSWORD"));

        $sessionPrefix = "PHPREDIS_SESSION:";
        session_set_save_handler(
            function ($savePath, $sessionName) {
                return true;
            },
            // Close handler
            function () {
                return true;
            },
            // Read handler
            function ($sessionId) use ($sessionPrefix) {
                return $this->redis->get($sessionPrefix . $sessionId) ?: '';
            },
            // Write handler
            function ($sessionId, $sessionData) use ($sessionPrefix) {
                $lifetime = ini_get('session.gc_maxlifetime');
                return $this->redis->setex($sessionPrefix . $sessionId, $lifetime, $sessionData);
            },
            // Destroy handler
            function ($sessionId) use ($sessionPrefix) {
                return $this->redis->del($sessionPrefix . $sessionId) > 0;
            },
            // Garbage collection handler
            function ($maxLifetime) {
                return true;
            }
        );
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
