
<?php
require_once dirname(dirname(dirname(__DIR__))) . '/src/Models/OrderAdmin.php';

class OrderManager {
    private $paidOrders;
    private $orderStats;
    private $totalRevenue;

    public function __construct() {
        $this->paidOrders = OrderAdmin::getPaidOrders();
        $this->orderStats = OrderAdmin::getOrdersCountByStatus();
        $this->totalRevenue = OrderAdmin::getTotalRevenue();
    }

    public function getPaidOrders() {
        return $this->paidOrders;
    }

    public function getOrderStats() {
        return $this->orderStats;
    }

    public function getTotalRevenue() {
        return $this->totalRevenue;
    }
}
