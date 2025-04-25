
<?php
namespace App\Models;

use App\Database;

/**
 * OrderAdmin model class
 * Handles order-related operations for the admin panel
 */
class OrderAdmin {
    /**
     * Get all orders with product details
     * 
     * @param string|null $status Filter by status (optional)
     * @return array Array of orders
     */
    public static function getAllOrders($status = null) {
        $db = Database::getInstance();
        
        $query = "SELECT o.*, p.name as product_name 
                 FROM orders o
                 JOIN products p ON o.product_id = p.id";
        
        $params = [];
        
        if ($status !== null) {
            $query .= " WHERE o.status = ?";
            $params[] = $status;
        }
        
        $query .= " ORDER BY o.created_at DESC";
        
        return $db->query($query, $params)->fetchAll();
    }
    
    /**
     * Get paid orders with product details
     * 
     * @return array Array of paid orders
     */
    public static function getPaidOrders() {
        return self::getAllOrders('paid');
    }
    
    /**
     * Get pending orders with product details
     * 
     * @return array Array of pending orders
     */
    public static function getPendingOrders() {
        return self::getAllOrders('pending');
    }
    
    /**
     * Get orders count by status
     * 
     * @return array Associative array with counts by status
     */
    public static function getOrdersCountByStatus() {
        $db = Database::getInstance();
        
        $stmt = $db->query(
            "SELECT status, COUNT(*) as count FROM orders GROUP BY status"
        );
        
        $result = [];
        $rows = $stmt->fetchAll();
        
        foreach ($rows as $row) {
            $result[$row['status']] = $row['count'];
        }
        
        return $result;
    }
    
    /**
     * Get total revenue from paid orders
     * 
     * @return float Total revenue
     */
    public static function getTotalRevenue() {
        $db = Database::getInstance();
        
        $stmt = $db->query(
            "SELECT SUM(amount) as total FROM orders WHERE status = 'paid'"
        );
        
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
}
