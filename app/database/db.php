<?php
require_once __DIR__ . '/../config/env_loader.php';
loadEnv(__DIR__ . '/../../.env');
class Connection {
    protected $db;

    public function __construct() {
        try {
            $this->db = new PDO(
                getenv("DB_CONNECTION") . ':host=' . getenv("DB_HOST") . ';dbname=' . getenv("DB_NAME"),
                getenv("DB_USER"),
                getenv("DB_PASSWORD")
            );
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
