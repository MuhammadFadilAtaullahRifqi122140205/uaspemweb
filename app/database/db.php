<?php
require_once __DIR__ . '/../config/env_loader.php';
loadEnv(__DIR__ . '/../../.env');

class Connection {
    protected $db;
    protected $host;
    protected $port;
    protected $dbname;
    protected $username;
    protected $password;

    public function __construct() {
        // Load environment variables
        $this->host = getenv('DB_HOST');
        $this->port = getenv('DB_PORT');
        $this->dbname = getenv('DB_NAME');
        $this->username = getenv('DB_USER');
        $this->password = getenv('DB_PASSWORD');

        try {
            // Ensure environment variables are set
            if (!$this->host || !$this->port || !$this->dbname || !$this->username || !$this->password) {
                throw new Exception("Missing required database environment variables.");
            }

            // Create the DSN
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8mb4";

            // Initialize PDO
            $this->db = new PDO($dsn, $this->username, $this->password);

            // Set PDO attributes
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    public function getDb() {
        return $this->db;
    }
}
