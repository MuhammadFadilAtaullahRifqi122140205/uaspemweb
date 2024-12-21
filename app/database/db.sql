CREATE DATABASE IF NOT EXISTS uas;

USE uas;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    gender ENUM('Male', 'Female'),
    city VARCHAR(255),
    ip_address VARCHAR(50) UNIQUE,
    browser TEXT
);
