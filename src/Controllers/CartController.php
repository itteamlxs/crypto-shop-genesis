
<?php
namespace App\Controllers;

use App\Models\Product;

/**
 * Controller for cart-related actions
 */
class CartController {
    /**
     * Display shopping cart page
     */
    public function index() {
        $cart = $this->getCart();
        $cartItems = [];
        $total = 0;
        
        if (!empty($cart)) {
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
        }
        
        require APP_ROOT . '/views/cart/index.php';
    }
    
    /**
     * Add product to cart
     */
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /");
            exit;
        }
        
        $productId = filter_input(INPUT_POST, 'product_id', FILTER_SANITIZE_NUMBER_INT);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_NUMBER_INT);
        $quantity = $quantity > 0 ? $quantity : 1;
        
        // Check if product exists
        $product = Product::findById($productId);
        if (!$product) {
            header("Location: /");
            exit;
        }
        
        // Add to cart
        $cart = $this->getCart();
        
        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }
        
        $_SESSION['cart'] = $cart;
        
        header("Location: /cart");
        exit;
    }
    
    /**
     * Get current cart from session
     * 
     * @return array Cart items
     */
    private function getCart() {
        session_start();
        
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        return $_SESSION['cart'];
    }
}
