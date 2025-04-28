
<?php
/**
 * Thank You Page
 * Displayed after a successful order
 */

// Load bootstrap configuration
require_once dirname(__DIR__) . '/config/bootstrap.php';

// Check for order ID parameter
$orderId = filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT);

// Get order details if ID is provided
$order = null;
if ($orderId) {
    $order = \App\Models\Order::getById($orderId);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - Crypto Shop</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">Thank You for Your Order!</h4>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                            </svg>
                        </div>
                        
                        <h5 class="card-title mb-3">Your payment has been confirmed!</h5>
                        
                        <?php if ($order): ?>
                        <p class="mb-3">Order #<?php echo $orderId; ?> has been successfully processed.</p>
                        <?php else: ?>
                        <p class="mb-3">Your order has been successfully processed.</p>
                        <?php endif; ?>
                        
                        <p class="mb-4">A confirmation email has been sent if you provided an email address.</p>
                        
                        <div class="d-flex justify-content-center gap-3">
                            <a href="/" class="btn btn-primary">Continue Shopping</a>
                            <?php if ($orderId): ?>
                            <a href="/order/status/<?php echo $orderId; ?>" class="btn btn-outline-secondary">View Order Details</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
