<?php
require_once __DIR__ . '/../../database/db.php';

class UserController extends Connection {
    public function __construct() {
        parent::__construct();
    }

    public function createUser($username, $gender, $city, $ip, $browser) {
        $stmt = $this->db->prepare("INSERT INTO users (username, gender, city, ip_address, browser) VALUES (:username, :gender, :city, :ip, :browser)");
        $stmt->execute([
            ':username' => $username,
            ':gender' => $gender,
            ':city' => $city,
            ':ip' => $ip,
            ':browser' => $browser
        ]);
    }

    public function getUser($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllUsers() {
        $stmt = $this->db->query("SELECT * FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateUser($id, $username, $gender, $city, $ip, $browser) {
        $stmt = $this->db->prepare("UPDATE users SET username = :username, gender = :gender, city = :city, ip_address = :ip, browser = :browser WHERE id = :id");
        $stmt->execute([
            ':id' => $id,
            ':username' => $username,
            ':gender' => $gender,
            ':city' => $city,
            ':ip' => $ip,
            ':browser' => $browser
        ]);
    }

    public function deleteUser($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }
}
