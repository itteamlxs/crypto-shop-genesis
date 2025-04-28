
<?php
/**
 * Test Payment Page
 * Simulates a payment in Bitcoin Testnet using BTCPay Server
 */

// Load bootstrap configuration
require_once dirname(__DIR__) . '/config/bootstrap.php';

use App\Models\Product;
use App\Services\PaymentService;

// Set security headers
header("Content-Security-Policy: default-src 'self'; style-src 'self' https://cdn.jsdelivr.net; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; img-src 'self' https://api.qrserver.com data:;");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

try {
    // Get product ID from request with proper validation
    $productId = filter_input(INPUT_GET, 'product_id', FILTER_VALIDATE_INT);

    // If no product_id is provided or it's invalid, find a product with stock
    if (!$productId) {
        // Find first product with stock > 0
        $db = \App\Database::getInstance();
        $stmt = $db->query("SELECT id FROM products WHERE stock > 0 ORDER BY id ASC LIMIT 1");
        $product = $stmt->fetch();
        
        if ($product) {
            $productId = $product['id'];
        } else {
            throw new \Exception('No products available with stock');
        }
    }

    // Get product details
    $product = Product::getById($productId);

    // Check if product exists and has stock
    if (!$product || $product['stock'] <= 0) {
        throw new \Exception('Product not available or out of stock');
    }

    // Initialize PaymentService with testnet mode
    $paymentService = new PaymentService();

    // Create test invoice
    $paymentData = $paymentService->createInvoice(
        $product['price'],
        'test@example.com',
        $product['name']
    );

    // Store test order in database
    $orderId = \App\Models\Order::create([
        'product_id' => $productId,
        'amount' => $product['price'],
        'crypto_address' => $paymentData['address'],
        'status' => 'pending',
        'email' => 'test@example.com'
    ]);
} catch (\Exception $e) {
    // Log the actual error for debugging
    error_log('Test payment error: ' . $e->getMessage());
    
    // Display a generic error message
    $error = 'Unable to process test payment. Please try again later.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Payment - Crypto Shop</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($error) ?>
                        <p><a href="/" class="btn btn-primary mt-3">Return to Shop</a></p>
                    </div>
                <?php else: ?>
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Test Payment (Bitcoin Testnet)</h4>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <strong>This is a test payment page using Bitcoin Testnet.</strong> 
                                <p>Use this page to test the payment flow without using real cryptocurrency.</p>
                            </div>
                            
                            <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                            <p class="card-text">Price: $<?= number_format($product['price'], 2) ?></p>
                            
                            <hr>
                            
                            <h5>Payment Information</h5>
                            <div class="text-center mb-3">
                                <div class="qr-code bg-light p-3 d-inline-block">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode('bitcoin:' . $paymentData['address'] . '?amount=' . $paymentData['amount_btc']) ?>" 
                                         alt="Payment QR Code" class="img-fluid">
                                </div>
                            </div>
                            
                            <div class="alert alert-warning">
                                <p class="mb-1"><strong>Send Testnet Bitcoin to this address:</strong></p>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($paymentData['address']) ?>" readonly>
                                    <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('<?= htmlspecialchars($paymentData['address']) ?>')">Copy</button>
                                </div>
                                <p class="mb-0"><strong>Amount:</strong> <?= $paymentData['amount_btc'] ?> BTC</p>
                            </div>
                            
                            <div class="alert alert-success">
                                <p class="mb-0"><strong>Order ID:</strong> <?= $orderId ?></p>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <button id="check-status" class="btn btn-primary" data-order-id="<?= $orderId ?>">Check Payment Status</button>
                                <a href="/" class="btn btn-outline-secondary">Back to Shop</a>
                            </div>
                            
                            <div id="status-message" class="mt-3"></div>
                        </div>
                    </div>
                    
                    <div class="card mt-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">How to Test with Bitcoin Testnet</h5>
                        </div>
                        <div class="card-body">
                            <ol>
                                <li>Install a Bitcoin Testnet wallet (e.g., <a href="https://electrum.org/" target="_blank">Electrum</a> in testnet mode)</li>
                                <li>Get free testnet bitcoins from a faucet like <a href="https://testnet-faucet.mempool.co/" target="_blank">https://testnet-faucet.mempool.co/</a></li>
                                <li>Send the exact amount to the address shown above</li>
                                <li>Click "Check Payment Status" to verify your payment</li>
                            </ol>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        // Function to copy address to clipboard
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Address copied to clipboard');
            }, function(err) {
                console.error('Could not copy text: ', err);
            });
        }
        
        // Function to check payment status
        document.getElementById('check-status')?.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            const statusMessage = document.getElementById('status-message');
            
            statusMessage.innerHTML = '<div class="alert alert-info">Checking payment status...</div>';
            
            fetch('/check_payment.php?id=' + orderId)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        statusMessage.innerHTML = '<div class="alert alert-danger">Error checking payment status. Please try again.</div>';
                    } else if (data.status === 'paid') {
                        statusMessage.innerHTML = '<div class="alert alert-success">Payment confirmed! Order is now paid.</div>';
                    } else if (data.status === 'pending') {
                        statusMessage.innerHTML = '<div class="alert alert-warning">Payment still pending. Please wait for confirmation.</div>';
                    } else {
                        statusMessage.innerHTML = '<div class="alert alert-danger">Payment expired or failed.</div>';
                    }
                })
                .catch(error => {
                    statusMessage.innerHTML = '<div class="alert alert-danger">Error checking payment status.</div>';
                    console.error('Error:', error);
                });
        });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
