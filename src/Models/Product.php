<?php
namespace App\Models;

use App\Database;

/**
 * Product model class
 * Handles all product-related database operations
 */
class Product {
    /**
     * Get all active products (stock > 0)
     * 
     * @return array Array of products
     */
    public static function getAllProducts() {
        $db = Database::getInstance();
        return $db->query("SELECT * FROM products WHERE stock > 0 ORDER BY id DESC")->fetchAll();
    }
    
    /**
     * Get all products regardless of stock status (for admin panel)
     * 
     * @return array Array of products
     */
    public static function getAll() {
        $db = Database::getInstance();
        return $db->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
    }
    
    /**
     * Get a single product by ID
     * 
     * @param int $id Product ID
     * @return array|false Product data or false if not found
     */
    public static function getById($id) {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM products WHERE id = ? AND stock > 0", [$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get a product by ID regardless of stock status (for admin panel)
     * 
     * @param int $id Product ID
     * @return array|false Product data or false if not found
     */
    public static function getByIdAdmin($id) {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM products WHERE id = ?", [$id]);
        return $stmt->fetch();
    }
    
    /**
     * Create a new product
     * 
     * @param string $name Product name
     * @param float $price Product price
     * @param string $description Product description
     * @param int $stock Product stock
     * @param string|null $imageUrl Product image URL
     * @return bool Success status
     */
    public static function createProduct($name, $price, $description, $stock, $imageUrl = null) {
        $db = Database::getInstance();
        $stmt = $db->query(
            "INSERT INTO products (name, price, description, stock, image_url) VALUES (?, ?, ?, ?, ?)",
            [$name, $price, $description, $stock, $imageUrl]
        );
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Update an existing product
     * 
     * @param int $id Product ID
     * @param string $name Product name
     * @param float $price Product price
     * @param string $description Product description
     * @param int $stock Product stock
     * @param string|null $imageUrl Product image URL
     * @return bool Success status
     */
    public static function updateProduct($id, $name, $price, $description, $stock, $imageUrl = null) {
        $db = Database::getInstance();
        
        // If image URL is provided, update it too; otherwise, leave it unchanged
        if ($imageUrl !== null) {
            $stmt = $db->query(
                "UPDATE products SET name = ?, price = ?, description = ?, stock = ?, image_url = ? WHERE id = ?",
                [$name, $price, $description, $stock, $imageUrl, $id]
            );
        } else {
            $stmt = $db->query(
                "UPDATE products SET name = ?, price = ?, description = ?, stock = ? WHERE id = ?",
                [$name, $price, $description, $stock, $id]
            );
        }
        
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Delete a product
     * 
     * @param int $id Product ID
     * @return bool Success status
     */
    public static function deleteProduct($id) {
        $db = Database::getInstance();
        
        // Check if there are any orders for this product
        $ordersCheck = $db->query("SELECT COUNT(*) as count FROM orders WHERE product_id = ?", [$id])->fetch();
        
        if ($ordersCheck['count'] > 0) {
            // If orders exist, set stock to 0 instead of deleting
            $stmt = $db->query("UPDATE products SET stock = 0 WHERE id = ?", [$id]);
        } else {
            // If no orders exist, delete the product
            $stmt = $db->query("DELETE FROM products WHERE id = ?", [$id]);
        }
        
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Decrease product stock after purchase
     * 
     * @param int $id Product ID
     * @param int $quantity Quantity to decrease
     * @return bool Success status
     */
    public static function decreaseStock($id, $quantity = 1) {
        $db = Database::getInstance();
        $stmt = $db->query(
            "UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?",
            [$quantity, $id, $quantity]
        );
        return $stmt->rowCount() > 0;
    }
}
