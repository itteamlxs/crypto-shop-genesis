
<?php
/**
 * API endpoint to check payment status
 */

// Load bootstrap configuration
require_once dirname(__DIR__) . '/config/bootstrap.php';

use App\Services\PaymentService;
use App\Models\Order;

// Set JSON content type
header('Content-Type: application/json');

// Set security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

try {
    // Get order ID from request with proper validation
    $orderId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$orderId) {
        throw new \Exception('Invalid order ID');
    }

    // Get order from database
    $order = Order::getById($orderId);

    if (!$order) {
        throw new \Exception('Order not found');
    }

    // Check payment status with BTCPay Server
    $paymentService = new PaymentService();
    $confirmed = $paymentService->confirmPayment($orderId);

    // Return current status
    echo json_encode([
        'id' => $orderId,
        'status' => $order['status'],
        'updated' => $confirmed
    ]);
    
} catch (\Exception $e) {
    // Log the actual error for debugging
    error_log('Payment check error: ' . $e->getMessage());
    
    // Return a generic error message to the user
    http_response_code(400);
    echo json_encode([
        'error' => 'Unable to process payment status check. Please try again later.'
    ]);
}
