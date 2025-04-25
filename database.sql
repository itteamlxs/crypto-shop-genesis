
-- Create database
CREATE DATABASE IF NOT EXISTS crypto_shop;

USE crypto_shop;

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    stock INT NOT NULL DEFAULT 0,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    crypto_address VARCHAR(255),
    status VARCHAR(50) DEFAULT 'pending',
    email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Admin users table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Login attempts table for rate limiting
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample products
INSERT INTO products (name, price, description, stock, image_url) VALUES 
('Bitcoin T-Shirt', 25.99, 'High-quality cotton t-shirt with Bitcoin logo.', 100, 'img/products/bitcoin-tshirt.jpg'),
('Ethereum Mug', 15.50, 'Ceramic mug with Ethereum logo, perfect for your morning coffee.', 75, 'img/products/ethereum-mug.jpg'),
('Crypto Hardware Wallet', 89.99, 'Secure hardware wallet for storing your cryptocurrency assets.', 30, 'img/products/hardware-wallet.jpg');

-- Insert a default admin user (username: admin, password: admin123)
-- Note: The password is hashed using password_hash() with PASSWORD_DEFAULT
INSERT INTO admins (username, password) VALUES 
('admin', '$2y$10$HKgXlAGF3pJSToEH3WF2LeLwQR.MHI0B9Sm4srQufnK5tDdFQ5n4.');
