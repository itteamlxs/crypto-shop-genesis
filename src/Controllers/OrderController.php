
<?php
namespace App\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\PaymentService;
use App\Services\MailService;

/**
 * Controller for order-related actions
 */
class OrderController {
    /**
     * Display checkout page
     */
    public function checkout() {
        // Start session to get cart
        session_start();
        
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            header("Location: /cart");
            exit;
        }
        
        // Get cart items
        $cart = $_SESSION['cart'];
        $cartItems = [];
        $total = 0;
        
        foreach ($cart as $productId => $quantity) {
            $product = Product::getById($productId);
            if ($product) {
                $subtotal = $product['price'] * $quantity;
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal
                ];
                $total += $subtotal;
            }
        }
        
        require APP_ROOT . '/views/orders/checkout.php';
    }
    
    /**
     * Create a new order
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /");
            exit;
        }
        
        // Start session to get cart
        session_start();
        
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            header("Location: /cart");
            exit;
        }
        
        // Get form data
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        
        // Process each cart item as an order
        $cart = $_SESSION['cart'];
        $orderId = null;
        
        // For simplicity in this example, we'll just process the first item
        // In a real app, you might want to create an "order_items" table
        foreach ($cart as $productId => $quantity) {
            $product = Product::getById($productId);
            
            if ($product) {
                // Create payment
                $paymentService = new PaymentService();
                $amount = $product['price'] * $quantity;
                
                $paymentData = $paymentService->createInvoice(
                    $amount, 
                    $email, 
                    $product['name']
                );
                
                if ($paymentData) {
                    // Create order
                    $orderData = [
                        'product_id' => $productId,
                        'amount' => $amount,
                        'crypto_address' => $paymentData['address'],
                        'status' => 'pending',
                        'email' => $email
                    ];
                    
                    $orderId = Order::create($orderData);
                    
                    if ($orderId) {
                        // Decrease product stock
                        Product::decreaseStock($productId, $quantity);
                        
                        // Send confirmation email (if available)
                        if (class_exists('\\App\\Services\\MailService') && !empty($email)) {
                            $mailService = new MailService();
                            $mailService->sendOrderConfirmation($email, $orderId, $product['name'], $paymentData);
                        }
                        
                        // Clear cart and redirect to status page
                        $_SESSION['cart'] = [];
                        header("Location: /order/status/$orderId");
                        exit;
                    }
                }
            }
        }
        
        // If we get here, something went wrong
        header("Location: /checkout?error=1");
        exit;
    }
    
    /**
     * Display order status page
     * 
     * @param int $id Order ID
     */
    public function status($id) {
        $order = Order::getById($id);
        
        if (!$order) {
            header("Location: /");
            exit;
        }
        
        // Calculate BTC amount (for display purposes)
        $paymentService = new PaymentService();
        $btcAmount = $paymentService->convertToBtc($order['amount']);
        
        require APP_ROOT . '/views/orders/status.php';
    }
    
    /**
     * Display order success page
     */
    public function success() {
        require APP_ROOT . '/views/orders/success.php';
    }
    
    /**
     * API endpoint to check payment status
     * 
     * @param int $id Order ID
     */
    public function checkPaymentStatus($id) {
        header('Content-Type: application/json');
        
        $order = Order::getById($id);
        
        if (!$order) {
            echo json_encode(['error' => 'Order not found']);
            exit;
        }
        
        // In a production environment, we would check with BTCPay Server
        $paymentService = new PaymentService();
        $status = $paymentService->checkPaymentStatus($id);
        
        // Update order status if payment is confirmed
        if ($status !== $order['status']) {
            Order::updateStatus($id, $status);
        }
        
        echo json_encode(['status' => $status]);
        exit;
    }
}
