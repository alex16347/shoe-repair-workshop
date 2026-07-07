-- ============================================================
-- БАЗА ДАННЫХ: shoe_repair_workshop
-- Модуль 3: Чистая версия (только структура + админ)
-- ============================================================

-- Создание базы данных
CREATE DATABASE IF NOT EXISTS shoe_repair_workshop
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE shoe_repair_workshop;

-- ============================================================
-- 1. ТАБЛИЦА: users (Пользователи)
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- 2. ТАБЛИЦА: order_statuses (Статусы заказов)
-- ============================================================
CREATE TABLE IF NOT EXISTS order_statuses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    status_name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    color VARCHAR(20) DEFAULT '#3498db',
    sort_order TINYINT DEFAULT 0
) ENGINE=InnoDB;

-- ============================================================
-- 3. ТАБЛИЦА: orders (Заказы)
-- ============================================================
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    status_id INT DEFAULT 1,
    shoe_type VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    repair_date DATE NOT NULL,
    estimated_cost DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Внешние ключи
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (status_id) REFERENCES order_statuses(id)
) ENGINE=InnoDB;

-- ============================================================
-- 4. ИНДЕКСЫ (для ускорения запросов)
-- ============================================================

-- Индексы для таблицы orders
CREATE INDEX idx_orders_user_id ON orders(user_id);
CREATE INDEX idx_orders_status_id ON orders(status_id);
CREATE INDEX idx_orders_created_at ON orders(created_at);
CREATE INDEX idx_orders_repair_date ON orders(repair_date);

-- Индексы для таблицы users
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role);

-- ============================================================
-- 5. ОГРАНИЧЕНИЯ
-- ============================================================

-- Стоимость не может быть отрицательной
ALTER TABLE orders MODIFY estimated_cost DECIMAL(10,2) NOT NULL;
ALTER TABLE orders ADD CONSTRAINT chk_estimated_cost CHECK (estimated_cost >= 0);

-- ============================================================
-- 6. ТРИГГЕР: автоматическая установка статуса "Новый" (id=1)
-- ============================================================

DELIMITER //
CREATE TRIGGER set_default_status
BEFORE INSERT ON orders
FOR EACH ROW
BEGIN
    IF NEW.status_id IS NULL THEN
        SET NEW.status_id = 1;
    END IF;
END;//
DELIMITER ;

-- ============================================================
-- 7. НАЧАЛЬНЫЕ ДАННЫЕ (статусы + администратор)
-- ============================================================

-- Статусы заказов (5 штук)
INSERT INTO order_statuses (status_name, description, color, sort_order) VALUES
('Новый', 'Заказ только создан, ожидает обработки', '#3498db', 1),
('В работе', 'Мастер приступил к ремонту', '#f39c12', 2),
('Готов к выдаче', 'Ремонт завершен, можно забрать', '#27ae60', 3),
('Выдан', 'Заказ получен клиентом', '#95a5a6', 4),
('Отменен', 'Заказ отменен', '#e74c3c', 5);

-- Администратор (пароль: admin123)
INSERT INTO users (username, password_hash, full_name, phone, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'Администратор', '+7 (999) 111-11-11', 'admin@example.com', 'admin');

-- ============================================================
-- 8. ПРОВЕРОЧНЫЙ ЗАПРОС (для отладки)
-- ============================================================

-- SELECT * FROM users;
-- SELECT * FROM order_statuses;
-- SELECT * FROM orders;

-- ============================================================
-- КОНЕЦ ФАЙЛА
-- ============================================================
