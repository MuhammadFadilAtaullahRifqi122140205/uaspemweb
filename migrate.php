<?php
// Cegah akses langsung dari URL
if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.");
}

require_once __DIR__ . '/app/database/db.php';


try {
    $connection = new Connection();
    $pdo = $connection->getDb();

    echo "Connected successfully to the database.\n";

    // Migration: Create tables
    $pdo->exec("CREATE TABLE IF NOT EXISTS roles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) UNIQUE
    );");

    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        role_id INT DEFAULT 2,
        username VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        gender ENUM('Male', 'Female'),
        image VARCHAR(255) DEFAULT NULL,
        city VARCHAR(255) NOT NULL,
        ip_address VARCHAR(50) UNIQUE NOT NULL,
        browser TEXT NOT NULL,
        FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
    );");

    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        image VARCHAR(255) DEFAULT NULL,
        description TEXT NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );");

    echo "Tables created successfully.\n";

    // Insert roles if not exists
    $pdo->exec("INSERT IGNORE INTO roles (name) VALUES ('Admin'), ('User');");

    // Create an admin user
    $adminUsername = getenv('ADMIN_USERNAME');
    $adminPassword = getenv('ADMIN_PASSWORD');
    $hashedPassword = password_hash($adminPassword, PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("INSERT INTO users (role_id, username, password, gender, city, ip_address, browser) VALUES
        (1, :username, :password, 'Male', 'Default City', '127.0.0.1', 'Default Browser')");
    $stmt->execute([
        ':username' => $adminUsername,
        ':password' => $hashedPassword,
    ]);

    echo "Admin user created successfully.\n";

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
} catch (Exception $e) {
    die($e->getMessage());
}
