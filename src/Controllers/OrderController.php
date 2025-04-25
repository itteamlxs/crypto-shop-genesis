
<?php
namespace App\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\BtcPayService;
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
            $product = Product::findById($productId);
            if ($product) {
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product['price'] * $quantity
                ];
                $total += $product['price'] * $quantity;
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
            $product = Product::findById($productId);
            
            if ($product) {
                $orderData = [
                    'product_id' => $productId,
                    'amount' => $product['price'] * $quantity,
                    'email' => $email
                ];
                
                // Create order
                $orderId = Order::create($orderData);
                
                if ($orderId) {
                    // Decrease product stock
                    Product::decreaseStock($productId, $quantity);
                    
                    // Generate crypto payment
                    $btcPayService = new BtcPayService();
                    $paymentData = $btcPayService->createInvoice($orderId, $orderData['amount']);
                    
                    if ($paymentData) {
                        // Update order with crypto address
                        Order::updatePaymentInfo($orderId, $paymentData['address']);
                        
                        // Send confirmation email
                        $mailService = new MailService();
                        $mailService->sendOrderConfirmation($email, $orderId, $product['name'], $paymentData);
                        
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
        $order = Order::findById($id);
        
        if (!$order) {
            header("Location: /");
            exit;
        }
        
        require APP_ROOT . '/views/orders/status.php';
    }
    
    /**
     * Display order success page
     */
    public function success() {
        require APP_ROOT . '/views/orders/success.php';
    }
}
