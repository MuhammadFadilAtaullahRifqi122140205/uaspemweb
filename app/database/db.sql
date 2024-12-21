CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) UNIQUE
);

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT DEFAULT 2,
    username VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    gender ENUM('Male', 'Female'),
    image VARCHAR(255) DEFAULT NULL,
    city VARCHAR(255),
    ip_address VARCHAR(50) UNIQUE,
    browser TEXT,

    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);

INSERT INTO roles (name) VALUES ('Admin'), ('User');


CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(255),
    price DECIMAL(10, 2),
    image VARCHAR(255) DEFAULT NULL,
    description TEXT,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
