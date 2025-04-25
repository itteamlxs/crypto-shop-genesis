
<?php
namespace App\Models;

use App\Database;

/**
 * Product model class
 */
class Product {
    private $id;
    private $name;
    private $price;
    private $description;
    private $stock;
    private $imageUrl;
    
    /**
     * Find all products
     * 
     * @return array Array of products
     */
    public static function findAll() {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM products");
        return $stmt->fetchAll();
    }
    
    /**
     * Find a product by ID
     * 
     * @param int $id Product ID
     * @return array|false Product data or false if not found
     */
    public static function findById($id) {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM products WHERE id = ?", [$id]);
        return $stmt->fetch();
    }
    
    /**
     * Decrease product stock after purchase
     * 
     * @param int $id Product ID
     * @param int $quantity Quantity purchased
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
