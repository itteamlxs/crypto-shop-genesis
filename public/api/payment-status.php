
<?php
/**
 * API endpoint to check payment status
 */

// Load bootstrap configuration
require_once dirname(dirname(__DIR__)) . '/config/bootstrap.php';

use App\Models\Order;
use App\Services\PaymentService;

// Set JSON content type
header('Content-Type: application/json');

// Get order ID from request
$orderId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$orderId) {
    echo json_encode(['error' => 'Invalid order ID']);
    exit;
}

// Get order from database
$order = Order::getById($orderId);

if (!$order) {
    echo json_encode(['error' => 'Order not found']);
    exit;
}

// Check payment status with BTCPay Server
$paymentService = new PaymentService();
$status = $paymentService->checkPaymentStatus($orderId);

// Update order status if changed
if ($status !== $order['status']) {
    Order::updateStatus($orderId, $status);
}

// Return current status
echo json_encode([
    'id' => $orderId,
    'status' => $status
]);
