
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
}
