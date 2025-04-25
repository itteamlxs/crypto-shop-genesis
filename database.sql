
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

-- Insert sample products
INSERT INTO products (name, price, description, stock, image_url) VALUES 
('Bitcoin T-Shirt', 25.99, 'High-quality cotton t-shirt with Bitcoin logo.', 100, 'img/products/bitcoin-tshirt.jpg'),
('Ethereum Mug', 15.50, 'Ceramic mug with Ethereum logo, perfect for your morning coffee.', 75, 'img/products/ethereum-mug.jpg'),
('Crypto Hardware Wallet', 89.99, 'Secure hardware wallet for storing your cryptocurrency assets.', 30, 'img/products/hardware-wallet.jpg');
