CREATE DATABASE IF NOT EXISTS shoe_repair_workshop;
USE shoe_repair_workshop;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    shoe_type VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    repair_date DATE NOT NULL,
    estimated_cost DECIMAL(10,2) NOT NULL,
    status_id INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Добавляем тестового админа (пароль: admin123)
INSERT INTO users (username, password_hash, full_name, phone, email, role) VALUES
('admin', '$2y$10$YourHashedPasswordHere', 'Администратор', '+7 (999) 111-11-11', 'admin@example.com', 'admin');