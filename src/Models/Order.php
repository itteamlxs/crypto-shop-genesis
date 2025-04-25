
<?php
namespace App\Models;

use App\Database;

/**
 * Order model class
 */
class Order {
    private $id;
    private $productId;
    private $amount;
    private $cryptoAddress;
    private $status;
    private $email;
    
    /**
     * Create a new order
     * 
     * @param array $orderData Order data
     * @return int|false ID of inserted order or false on failure
     */
    public static function create($orderData) {
        $db = Database::getInstance();
        $stmt = $db->query(
            "INSERT INTO orders (product_id, amount, email) VALUES (?, ?, ?)",
            [$orderData['product_id'], $orderData['amount'], $orderData['email']]
        );
        
        if ($stmt->rowCount() > 0) {
            return $db->getConnection()->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Update order with crypto payment information
     * 
     * @param int $orderId Order ID
     * @param string $cryptoAddress Cryptocurrency address
     * @return bool Success status
     */
    public static function updatePaymentInfo($orderId, $cryptoAddress) {
        $db = Database::getInstance();
        $stmt = $db->query(
            "UPDATE orders SET crypto_address = ? WHERE id = ?",
            [$cryptoAddress, $orderId]
        );
        return $stmt->rowCount() > 0;
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
     * Find an order by ID
     * 
     * @param int $id Order ID
     * @return array|false Order data or false if not found
     */
    public static function findById($id) {
        $db = Database::getInstance();
        $stmt = $db->query(
            "SELECT o.*, p.name as product_name, p.image_url 
             FROM orders o
             JOIN products p ON o.product_id = p.id
             WHERE o.id = ?",
            [$id]
        );
        return $stmt->fetch();
    }
}
