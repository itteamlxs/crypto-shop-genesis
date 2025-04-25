
<?php
namespace App\Models;

use App\Database;

/**
 * Order model class
 * Handles all order-related database operations
 */
class Order {
    /**
     * Create a new order
     * 
     * @param array $orderData Order data
     * @return int|false ID of inserted order or false on failure
     */
    public static function create($orderData) {
        $db = Database::getInstance();
        $stmt = $db->query(
            "INSERT INTO orders (product_id, amount, crypto_address, status, email) 
             VALUES (?, ?, ?, ?, ?)",
            [
                $orderData['product_id'], 
                $orderData['amount'], 
                $orderData['crypto_address'] ?? null, 
                $orderData['status'] ?? 'pending',
                $orderData['email'] ?? null
            ]
        );
        
        if ($stmt->rowCount() > 0) {
            return $db->getConnection()->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Update order status
     * 
     * @param int $orderId Order ID
     * @param string $status New status
     * @return bool Success status
     */
    public static function updateStatus($orderId, $status) {
        $db = Database::getInstance();
        $stmt = $db->query(
            "UPDATE orders SET status = ? WHERE id = ?",
            [$status, $orderId]
        );
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Get order by ID
     * 
     * @param int $orderId Order ID
     * @return array|false Order data or false if not found
     */
    public static function getById($orderId) {
        $db = Database::getInstance();
        $stmt = $db->query(
            "SELECT o.*, p.name as product_name, p.price as product_price
             FROM orders o
             JOIN products p ON o.product_id = p.id
             WHERE o.id = ?",
            [$orderId]
        );
        return $stmt->fetch();
    }
    
    /**
     * Get order by crypto address
     * 
     * @param string $address Cryptocurrency address
     * @return array|false Order data or false if not found
     */
    public static function getByAddress($address) {
        $db = Database::getInstance();
        $stmt = $db->query(
            "SELECT * FROM orders WHERE crypto_address = ?",
            [$address]
        );
        return $stmt->fetch();
    }
}
