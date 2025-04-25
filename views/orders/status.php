
<?php require APP_ROOT . '/views/layout/header.php'; ?>

<div class="text-center mb-4">
    <h1 class="mb-3">Order #<?php echo $order['id']; ?></h1>
    
    <?php if ($order['status'] == 'pending'): ?>
    <div class="alert alert-warning">
        <h4>Payment Pending</h4>
        <p>Your order has been received and is waiting for payment.</p>
    </div>
    <?php elseif ($order['status'] == 'paid'): ?>
    <div class="alert alert-success">
        <h4>Payment Confirmed</h4>
        <p>Thank you! Your payment has been received.</p>
    </div>
    <?php else: ?>
    <div class="alert alert-danger">
        <h4>Payment Failed or Expired</h4>
        <p>Your payment has not been received or has expired.</p>
    </div>
    <?php endif; ?>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Order Details</h5>
            </div>
            <div class="card-body">
                <p><strong>Product:</strong> <?php echo htmlspecialchars($order['product_name']); ?></p>
                <p><strong>Amount:</strong> $<?php echo number_format($order['amount'], 2); ?></p>
                <p><strong>Order Date:</strong> <?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?></p>
                <p><strong>Status:</strong> <span class="badge <?php echo $order['status'] == 'paid' ? 'bg-success' : ($order['status'] == 'pending' ? 'bg-warning' : 'bg-danger'); ?>"><?php echo ucfirst($order['status']); ?></span></p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Payment Instructions</h5>
            </div>
            <div class="card-body">
                <?php if ($order['status'] == 'pending' && !empty($order['crypto_address'])): ?>
                <div class="text-center mb-3">
                    <div class="qr-code bg-light p-3 d-inline-block">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo urlencode('bitcoin:' . $order['crypto_address']); ?>" 
                             alt="Payment QR Code" class="img-fluid">
                    </div>
                </div>
                
                <div class="alert alert-info">
                    <p class="mb-1"><strong>Send payment to this address:</strong></p>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($order['crypto_address']); ?>" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('<?php echo htmlspecialchars($order['crypto_address']); ?>')">Copy</button>
                    </div>
                    <small class="d-block mb-2">The payment will be automatically detected once confirmed on the blockchain.</small>
                    <p class="mb-0"><strong>Amount:</strong> <?php echo number_format($btcAmount, 8); ?> BTC</p>
                </div>
                <?php elseif ($order['status'] == 'paid'): ?>
                <div class="text-center">
                    <div class="mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                        </svg>
                    </div>
                    <p>Your payment has been confirmed. Thank you for your purchase!</p>
                    <a href="/" class="btn btn-primary">Continue Shopping</a>
                </div>
                <?php else: ?>
                <div class="text-center">
                    <p>This payment has expired or was cancelled.</p>
                    <a href="/" class="btn btn-primary">Return to Shop</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if ($order['status'] == 'pending'): ?>
<div class="d-flex justify-content-center mt-4">
    <button id="refresh-status" class="btn btn-outline-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-repeat me-1" viewBox="0 0 16 16">
            <path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41zm-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9z"/>
            <path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5.002 5.002 0 0 0 8 3M3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9H3.1z"/>
        </svg>
        Refresh Status
    </button>
</div>

<script>
// Polling mechanism to check payment status
(function() {
    const orderId = <?php echo $order['id']; ?>;
    let checkInterval;

    // Function to check payment status
    function checkStatus() {
        fetch(`/api/payment-status.php?id=${orderId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status !== 'pending') {
                    clearInterval(checkInterval);
                    window.location.reload();
                }
            })
            .catch(error => console.error('Error checking payment status:', error));
    }

    // Start polling every 10 seconds
    checkInterval = setInterval(checkStatus, 10000);
    
    // Manual refresh
    document.getElementById('refresh-status').addEventListener('click', function() {
        location.reload();
    });
    
    // Copy to clipboard function
    window.copyToClipboard = function(text) {
        navigator.clipboard.writeText(text).then(function() {
            alert('Address copied to clipboard');
        }, function(err) {
            console.error('Could not copy text: ', err);
        });
    };
})();
</script>
<?php endif; ?>

<?php require APP_ROOT . '/views/layout/footer.php'; ?>
