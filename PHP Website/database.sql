CREATE DATABASE IF NOT EXISTS demoshop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE demoshop;

DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    full_name VARCHAR(255) NOT NULL,
    role ENUM('admin','user') NOT NULL DEFAULT 'user',
    address VARCHAR(255) NULL,
    phone VARCHAR(50) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL UNIQUE,
    description TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    sku VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    price DECIMAL(12,2) NOT NULL CHECK (price >= 0),
    stock_quantity INT NOT NULL DEFAULT 0 CHECK (stock_quantity >= 0),
    image_url VARCHAR(255) NULL,
    category_id INT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(12,2) NOT NULL,
    status ENUM('pending','paid','shipped','delivered','cancelled') DEFAULT 'pending',
    shipping_address VARCHAR(255) NOT NULL,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price_at_time DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

INSERT INTO users (username, password, email, full_name, role, address, phone) VALUES
('admin01', '$2y$10$Sag840oSS6jDLh6XyE/NNewFsyTUqJXJ2lmJy7I1yT.fSl75mztVS', 'admin@example.com', 'System Administrator', 'admin', 'HCMC', '0900000001'),
('user01', '$2y$10$dIvVBZlZmHr5U4ShMWl/euuy7//2eSWKBrVq/UdTmIVz/HfrwCst.', 'user@example.com', 'Demo Customer', 'user', 'HCMC', '0900000002');

INSERT INTO categories (name, description) VALUES
('Laptop', 'Laptop for study and office work'),
('Phone', 'Smartphone and accessories'),
('Book', 'Programming and technology books'),
('Accessory', 'Computer accessories');

INSERT INTO products (name, sku, description, price, stock_quantity, image_url, category_id) VALUES
('Laptop Basic 14', 'LAP-BASIC-14', 'A reliable laptop for programming practice and office work.', 12500000, 12, 'assets/img/laptop-basic.svg', 1),
('Laptop Pro 15', 'LAP-PRO-15', 'A stronger laptop for Docker, IDEs, and database development.', 18500000, 8, 'assets/img/laptop-pro.svg', 1),
('Smartphone Study A1', 'PHN-A1', 'A smartphone for online learning and communication.', 5500000, 20, 'assets/img/phone-a1.svg', 2),
('PHP and MySQL Handbook', 'BOOK-PHP-MYSQL', 'A practical guide for building web applications with PHP and MySQL.', 250000, 50, 'assets/img/book-php.svg', 3),
('Wireless Mouse', 'ACC-MOUSE-01', 'Ergonomic wireless mouse for students.', 220000, 40, 'assets/img/mouse.svg', 4),
('USB-C Hub', 'ACC-HUB-01', 'USB-C hub with HDMI and USB ports.', 450000, 25, 'assets/img/hub.svg', 4);