CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) UNIQUE
);

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT,
    username VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    gender ENUM('Male', 'Female'),
    city VARCHAR(255),
    ip_address VARCHAR(50) UNIQUE,
    browser TEXT

    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS bukus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    judul VARCHAR(255),
    pengarang VARCHAR(255),
    penerbit VARCHAR(255),
    tahun_terbit INT,
    jumlah INT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
