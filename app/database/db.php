<?php
require_once __DIR__ . '/../config/env_loader.php';
// Load .env file
loadEnv(__DIR__ . '/../../.env');

class Connection {
    protected $db;
    protected $host;
    protected $port;
    protected $dbname;
    protected $username;
    protected $password;

    public function __construct() {
        // Set attribute dari environment variables
        $this->host = getenv('DB_HOST');
        $this->port = getenv('DB_PORT');
        $this->dbname = getenv('DB_NAME');
        $this->username = getenv('DB_USER');
        $this->password = getenv('DB_PASSWORD');

        try {
            // Pastikan environment variables sudah di-set
            if (!$this->host || !$this->port || !$this->dbname || !$this->username) {
                throw new Exception("Missing required database environment variables.");
            }

            // membuat Data Source Name (DSN) untuk PDO
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8mb4";

            // Membuat koneksi ke database
            $this->db = new PDO($dsn, $this->username, $this->password);

            // Set error mode ke exception
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Jika terjadi error saat koneksi ke database maka akan menampilkan pesan error
            die("Database connection failed: " . $e->getMessage());
        }
    }

    // Method untuk mendapatkan koneksi database
    public function getDb() {
        return $this->db;
    }
}
