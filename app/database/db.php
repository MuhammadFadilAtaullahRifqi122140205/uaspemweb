<?php
class Connection {
    protected $db;

    public function __construct() {
        try {
            $this->db = new PDO('mysql:host=127.0.0.1', 'root', '');
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sqlFile = __DIR__ . '/db.sql';
            if (file_exists($sqlFile)) {
                $sql = file_get_contents($sqlFile);
                $this->db->exec($sql);
            } else {
                throw new Exception("SQL file not found: $sqlFile");
            }

            $this->db->exec("USE uas");
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
